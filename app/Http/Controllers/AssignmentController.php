<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirect to activities index since assignments are managed through activities
        return redirect()->route('activities.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Redirect to activities index since assignments are created through activities
        return redirect()->route('activities.index')
            ->with('info', 'Create an activity first, then add an assignment to it.');
    }

    /**
     * Show the assignment management page
     */
    public function show(Assignment $assignment)
    {
        $activity = $assignment->activity()->with('activityType')->first();
        
        $assignmentData = Assignment::with([
            'questions.options',
            'document'
        ])->find($assignment->id);

        // Get student progress for this assignment
        $studentsProgress = $this->getStudentProgress($activity, $assignment);

        return Inertia::render('ActivityManagement/Assignment/AssignmentManagement', [
            'activity' => $activity,
            'assignment' => $assignmentData,
            'studentsProgress' => $studentsProgress,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'assignment_type' => 'required|in:objective,file_upload,mixed',
            'total_points' => 'required|integer|min:1',
            'time_limit' => 'nullable|integer|min:1',
            'allow_late_submission' => 'boolean',
            'document_id' => 'nullable|exists:documents,id',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:true_false,multiple_choice,enumeration,short_answer',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.acceptable_answers' => 'nullable|array',
            'questions.*.case_sensitive' => 'boolean',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.options.*.is_correct' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create assignment
            $assignment = Assignment::create([
                'activity_id' => $validated['activity_id'],
                'created_by' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'assignment_type' => $validated['assignment_type'],
                'total_points' => $validated['total_points'],
                'time_limit' => $validated['time_limit'] ?? null,
                'allow_late_submission' => $validated['allow_late_submission'] ?? false,
                'document_id' => $validated['document_id'] ?? null,
            ]);

            // Create questions if provided
            if (!empty($validated['questions'])) {
                foreach ($validated['questions'] as $index => $questionData) {
                    $question = $assignment->questions()->create([
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'points' => $questionData['points'],
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'acceptable_answers' => $questionData['acceptable_answers'] ?? null,
                        'case_sensitive' => $questionData['case_sensitive'] ?? false,
                        'explanation' => $questionData['explanation'] ?? null,
                        'order' => $index + 1,
                    ]);

                    // Create options for multiple choice questions
                    if ($questionData['question_type'] === 'multiple_choice' && !empty($questionData['options'])) {
                        foreach ($questionData['options'] as $optionIndex => $optionData) {
                            $question->options()->create([
                                'option_text' => $optionData['option_text'],
                                'is_correct' => $optionData['is_correct'] ?? false,
                                'order' => $optionIndex + 1,
                            ]);
                        }
                    }
                }

                // Recalculate total points based on questions
                $totalPoints = $assignment->questions()->sum('points');
                $assignment->update(['total_points' => $totalPoints]);
            }

            // Initialize student progress for enrolled students
            $this->initializeStudentProgress($assignment);

            DB::commit();

            return redirect()->route('activities.show', $validated['activity_id'])
                ->with('success', 'Assignment created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create assignment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create assignment: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'total_points' => 'required|integer|min:1',
            'time_limit' => 'nullable|integer|min:1',
            'allow_late_submission' => 'boolean',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|exists:assignment_questions,id',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:true_false,multiple_choice,enumeration,short_answer',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.acceptable_answers' => 'nullable|array',
            'questions.*.case_sensitive' => 'boolean',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.id' => 'nullable|exists:assignment_question_options,id',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.options.*.is_correct' => 'boolean',
            'deleted_question_ids' => 'nullable|array',
            'deleted_question_ids.*' => 'exists:assignment_questions,id',
        ]);

        try {
            DB::beginTransaction();

            // Update assignment
            $assignment->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'total_points' => $validated['total_points'],
                'time_limit' => $validated['time_limit'] ?? null,
                'allow_late_submission' => $validated['allow_late_submission'] ?? false,
            ]);

            // Delete removed questions
            if (!empty($validated['deleted_question_ids'])) {
                AssignmentQuestion::whereIn('id', $validated['deleted_question_ids'])->delete();
            }

            // Update or create questions
            if (!empty($validated['questions'])) {
                foreach ($validated['questions'] as $index => $questionData) {
                    if (!empty($questionData['id'])) {
                        // Update existing question
                        $question = AssignmentQuestion::find($questionData['id']);
                        $question->update([
                            'question_text' => $questionData['question_text'],
                            'question_type' => $questionData['question_type'],
                            'points' => $questionData['points'],
                            'correct_answer' => $questionData['correct_answer'] ?? null,
                            'acceptable_answers' => $questionData['acceptable_answers'] ?? null,
                            'case_sensitive' => $questionData['case_sensitive'] ?? false,
                            'explanation' => $questionData['explanation'] ?? null,
                            'order' => $index + 1,
                        ]);
                    } else {
                        // Create new question
                        $question = $assignment->questions()->create([
                            'question_text' => $questionData['question_text'],
                            'question_type' => $questionData['question_type'],
                            'points' => $questionData['points'],
                            'correct_answer' => $questionData['correct_answer'] ?? null,
                            'acceptable_answers' => $questionData['acceptable_answers'] ?? null,
                            'case_sensitive' => $questionData['case_sensitive'] ?? false,
                            'explanation' => $questionData['explanation'] ?? null,
                            'order' => $index + 1,
                        ]);
                    }

                    // Handle options for multiple choice
                    if ($questionData['question_type'] === 'multiple_choice' && !empty($questionData['options'])) {
                        // Get existing option IDs
                        $existingOptionIds = $question->options()->pluck('id')->toArray();
                        $providedOptionIds = array_filter(array_column($questionData['options'], 'id'));
                        
                        // Delete options that are no longer present
                        $optionsToDelete = array_diff($existingOptionIds, $providedOptionIds);
                        AssignmentQuestionOption::whereIn('id', $optionsToDelete)->delete();

                        // Update or create options
                        foreach ($questionData['options'] as $optionIndex => $optionData) {
                            if (!empty($optionData['id'])) {
                                // Update existing option
                                AssignmentQuestionOption::where('id', $optionData['id'])->update([
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => $optionData['is_correct'] ?? false,
                                    'order' => $optionIndex + 1,
                                ]);
                            } else {
                                // Create new option
                                $question->options()->create([
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => $optionData['is_correct'] ?? false,
                                    'order' => $optionIndex + 1,
                                ]);
                            }
                        }
                    }
                }

                // Recalculate total points
                $totalPoints = $assignment->questions()->sum('points');
                $assignment->update(['total_points' => $totalPoints]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Assignment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update assignment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update assignment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        try {
            $activityId = $assignment->activity_id;
            $assignment->delete();
            return redirect()->route('activities.show', $activityId)
                ->with('success', 'Assignment deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete assignment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete assignment.');
        }
    }

    /**
     * Initialize student progress for all enrolled students
     */
    private function initializeStudentProgress(Assignment $assignment)
    {
        $activity = $assignment->activity;
        
        // Get all enrolled students for this course
        $courseId = $activity->modules()->first()->course_id ?? null;
        if (!$courseId) {
            return;
        }

        $students = Student::whereHas('courses', function ($query) use ($courseId) {
            $query->where('courses.id', $courseId)
                  ->where('course_student.status', 'enrolled');
        })->get();

        foreach ($students as $student) {
            // Find or create student activity record
            $studentActivity = StudentActivity::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'module_id' => $activity->modules()->first()->id ?? null,
                    'course_id' => $courseId,
                    'activity_type' => 'assignment',
                    'status' => 'not_started',
                    'max_score' => $assignment->total_points,
                ]
            );

            // Create progress record
            StudentActivityProgress::firstOrCreate(
                [
                    'student_activity_id' => $studentActivity->id,
                    'activity_type' => 'assignment'
                ],
                [
                    'student_id' => $student->id,
                    'activity_id' => $activity->id,
                    'status' => 'not_started',
                    'points_possible' => $assignment->total_points,
                    'total_questions' => $assignment->questions()->count(),
                    'answered_questions' => 0,
                    'requires_grading' => $assignment->acceptsFileUploads(),
                    'due_date' => $activity->due_date,
                    'assignment_data' => json_encode(['submission_status' => 'draft']),
                ]
            );
        }
    }

    /**
     * Get student progress for the assignment
     */
    private function getStudentProgress(Activity $activity, Assignment $assignment)
    {
        $courseId = $activity->modules()->first()->course_id ?? null;
        if (!$courseId) {
            return [];
        }

        $students = Student::whereHas('courses', function ($query) use ($courseId) {
            $query->where('courses.id', $courseId)
                  ->where('course_student.status', 'enrolled');
        })->with(['user'])->get();

        return $students->map(function ($student) use ($activity, $assignment) {
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $activity->id)
                ->first();

            $progress = null;
            if ($studentActivity) {
                $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                    ->where('activity_type', 'assignment')
                    ->first();
            }

            return [
                'student_id' => $student->id,
                'student_name' => $student->user->name,
                'student_number' => $student->student_number,
                'status' => $studentActivity->status ?? 'not_started',
                'submission_status' => $progress->submission_status ?? 'not_started',
                'answered_questions' => $progress->answered_questions ?? 0,
                'total_questions' => $progress->total_questions ?? 0,
                'progress_percentage' => $progress && $progress->total_questions > 0 
                    ? round(($progress->answered_questions / $progress->total_questions) * 100, 2)
                    : 0,
                'score' => $studentActivity->score ?? null,
                'max_score' => $studentActivity->max_score ?? $assignment->total_points,
                'percentage_score' => $studentActivity->percentage_score ?? null,
                'submitted_at' => $studentActivity->submitted_at ?? null,
                'graded_at' => $studentActivity->graded_at ?? null,
                'requires_grading' => $progress->requires_grading ?? false,
                'can_review' => $progress && $progress->submission_status === 'submitted',
            ];
        })->toArray();
    }
}
