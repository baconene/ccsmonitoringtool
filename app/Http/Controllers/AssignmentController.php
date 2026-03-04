<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\CourseEnrollment;
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
        Log::info('AssignmentController::show called', ['assignment_id' => $assignment->id]);
        
        $activity = $assignment->activity()->with('activityType')->first();
        
        if (!$activity) {
            Log::error('No activity found for assignment', ['assignment_id' => $assignment->id]);
            return Inertia::render('ActivityManagement/Assignment/AssignmentManagement', [
                'activity' => null,
                'assignment' => $assignment,
                'studentsProgress' => [],
            ]);
        }
        
        Log::info('Activity found', ['activity_id' => $activity->id, 'title' => $activity->title]);
        
        $assignmentData = Assignment::with([
            'questions.options',
            'document'
        ])->find($assignment->id);

        // Get student progress for this assignment
        $studentsProgress = $this->getStudentProgress($activity, $assignment);
        
        Log::info('Assignment page render', [
            'assignment_id' => $assignment->id,
            'activity_id' => $activity->id,
            'students_progress_count' => count($studentsProgress),
            'students_progress' => $studentsProgress,
        ]);

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
        Log::info('Assignment store request received', [
            'activity_id' => $request->input('activity_id'),
            'title' => $request->input('title'),
            'has_questions' => $request->has('questions'),
        ]);

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
            'questions.*.question_text' => 'nullable|string',
            'questions.*.question_type' => 'nullable|in:true_false,multiple_choice,enumeration,short_answer',
            'questions.*.points' => 'nullable|integer|min:1',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.acceptable_answers' => 'nullable|array',
            'questions.*.case_sensitive' => 'boolean',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.option_text' => 'nullable|string',
            'questions.*.options.*.is_correct' => 'boolean',
        ]);

        $questions = collect($validated['questions'] ?? [])->map(function ($question, $index) {
            $questionText = trim((string) ($question['question_text'] ?? ''));
            $questionType = $question['question_type'] ?? null;
            $points = (int) ($question['points'] ?? 0);

            if ($questionText === '' || !$questionType || $points <= 0) {
                return null;
            }

            $options = collect($question['options'] ?? [])->map(function ($option, $optionIndex) {
                $optionText = trim((string) ($option['option_text'] ?? ''));

                if ($optionText === '') {
                    return null;
                }

                return [
                    'option_text' => $optionText,
                    'is_correct' => (bool) ($option['is_correct'] ?? false),
                    'order' => $optionIndex + 1,
                ];
            })->filter()->values()->all();

            return [
                'question_text' => $questionText,
                'question_type' => $questionType,
                'points' => $points,
                'correct_answer' => $question['correct_answer'] ?? null,
                'acceptable_answers' => $question['acceptable_answers'] ?? null,
                'case_sensitive' => (bool) ($question['case_sensitive'] ?? false),
                'explanation' => $question['explanation'] ?? null,
                'order' => $index + 1,
                'options' => $options,
            ];
        })->filter()->values()->all();

        Log::info('Assignment validation passed', [
            'activity_id' => $validated['activity_id'],
                'questions_count' => count($questions),
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

            Log::info('Assignment created', [
                'assignment_id' => $assignment->id,
                'activity_id' => $assignment->activity_id,
            ]);

            // Create questions if provided
            if (!empty($questions)) {
                foreach ($questions as $index => $questionData) {
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

                    Log::info('Question created', [
                        'question_id' => $question->id,
                        'assignment_id' => $assignment->id,
                    ]);

                    // Create options for multiple choice questions
                    if ($questionData['question_type'] === 'multiple_choice' && !empty($questionData['options'])) {
                        foreach ($questionData['options'] as $optionIndex => $optionData) {
                            $option = $question->options()->create([
                                'option_text' => $optionData['option_text'],
                                'is_correct' => $optionData['is_correct'] ?? false,
                                'order' => $optionIndex + 1,
                            ]);
                            
                            Log::info('Option created', [
                                'option_id' => $option->id,
                                'question_id' => $question->id,
                            ]);
                        }
                    }
                }

                // Recalculate total points based on questions
                $totalPoints = $assignment->questions()->sum('points');
                $assignment->update(['total_points' => $totalPoints]);
                
                Log::info('Assignment total points updated', [
                    'assignment_id' => $assignment->id,
                    'total_points' => $totalPoints,
                ]);
            }

            // Initialize student progress for enrolled students
            $this->initializeStudentProgress($assignment);

            DB::commit();

            Log::info('Assignment creation completed successfully', [
                'assignment_id' => $assignment->id,
            ]);

            // Return JSON response for AJAX request
            return response()->json([
                'success' => true,
                'message' => 'Assignment created successfully!',
                'assignment_id' => $assignment->id,
                'redirect' => route('activities.show', $validated['activity_id']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create assignment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create assignment: ' . $e->getMessage(),
            ], 500);
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
            'questions.*.question_text' => 'nullable|string',
            'questions.*.question_type' => 'nullable|in:true_false,multiple_choice,enumeration,short_answer',
            'questions.*.points' => 'nullable|integer|min:1',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.acceptable_answers' => 'nullable|array',
            'questions.*.case_sensitive' => 'boolean',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*.id' => 'nullable|exists:assignment_question_options,id',
            'questions.*.options.*.option_text' => 'nullable|string',
            'questions.*.options.*.is_correct' => 'boolean',
            'deleted_question_ids' => 'nullable|array',
            'deleted_question_ids.*' => 'exists:assignment_questions,id',
        ]);

        $questions = collect($validated['questions'] ?? [])->map(function ($question, $index) {
            $questionText = trim((string) ($question['question_text'] ?? ''));
            $questionType = $question['question_type'] ?? null;
            $points = (int) ($question['points'] ?? 0);

            if ($questionText === '' || !$questionType || $points <= 0) {
                return null;
            }

            $options = collect($question['options'] ?? [])->map(function ($option) {
                $optionText = trim((string) ($option['option_text'] ?? ''));
                if ($optionText === '') {
                    return null;
                }

                return [
                    'id' => $option['id'] ?? null,
                    'option_text' => $optionText,
                    'is_correct' => (bool) ($option['is_correct'] ?? false),
                ];
            })->filter()->values()->all();

            return [
                'id' => $question['id'] ?? null,
                'question_text' => $questionText,
                'question_type' => $questionType,
                'points' => $points,
                'correct_answer' => $question['correct_answer'] ?? null,
                'acceptable_answers' => $question['acceptable_answers'] ?? null,
                'case_sensitive' => (bool) ($question['case_sensitive'] ?? false),
                'explanation' => $question['explanation'] ?? null,
                'order' => $index + 1,
                'options' => $options,
            ];
        })->filter()->values()->all();

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
            if (!empty($questions)) {
                foreach ($questions as $index => $questionData) {
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
            Log::warning('No course found for activity', ['activity_id' => $activity->id]);
            return;
        }

        // Get students enrolled in this course
        // Using course_enrollments pivot table
        $students = Student::whereHas('courseEnrollments', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->get();

        Log::info('Initializing progress for students', [
            'assignment_id' => $assignment->id,
            'course_id' => $courseId,
            'student_count' => $students->count(),
        ]);

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
        // Try to get course through module relationship
        $courseId = null;
        $module = $activity->modules()->first();
        
        if ($module) {
            $courseId = $module->course_id;
            Log::info('Course found through module', ['module_id' => $module->id, 'course_id' => $courseId]);
        }
        
        // Fallback: check if activity has direct course reference or through other means
        if (!$courseId) {
            // Try to get from activity's first module's course
            $modules = $activity->modules()->with('course')->get();
            if ($modules->isNotEmpty()) {
                $courseId = $modules->first()->course_id;
                Log::info('Course found through module with relation', ['course_id' => $courseId]);
            }
        }
        
        if (!$courseId) {
            Log::warning('No course found for activity', [
                'activity_id' => $activity->id,
                'activity_title' => $activity->title
            ]);
            return [];
        }

        Log::info('Getting students for course', ['course_id' => $courseId]);

        // Source of truth for roster: course_enrollments table
        $enrollments = CourseEnrollment::with(['student.user', 'user'])
            ->where('course_id', $courseId)
            ->get();

        Log::info('Enrollments found for assignment submissions roster', [
            'course_id' => $courseId,
            'enrollment_count' => $enrollments->count(),
            'student_ids' => $enrollments->pluck('student_id')->filter()->values()->toArray(),
        ]);

        $questionCount = $assignment->questions()->count();

        return $enrollments->map(function ($enrollment) use ($activity, $assignment, $courseId, $questionCount) {
            $student = $enrollment->student;

            // Backward compatibility: old enrollments may only have user_id
            if (!$student && $enrollment->user_id) {
                $student = Student::with('user')
                    ->where('user_id', $enrollment->user_id)
                    ->first();
            }

            $studentId = $student?->id;
            $studentUser = $student?->user ?? $enrollment->user;

            // Check activity participation by student_activity_progress (requested behavior)
            $progress = null;
            if ($studentId) {
                $progress = StudentActivityProgress::where('student_id', $studentId)
                    ->where('activity_id', $activity->id)
                    ->where('activity_type', 'assignment')
                    ->latest('updated_at')
                    ->first();
            }

            $studentActivity = null;
            if ($studentId) {
                $studentActivity = StudentActivity::where('student_id', $studentId)
                    ->where('activity_id', $activity->id)
                    ->where('course_id', $courseId)
                    ->first();
            }

            $takingActivity = $progress && (
                $progress->status !== 'not_started'
                || (int) ($progress->answered_questions ?? 0) > 0
                || $progress->started_at !== null
                || $progress->submitted_at !== null
            );

            $status = 'not_started';
            if ($progress) {
                if ($progress->graded_at !== null || in_array($progress->status, ['graded', 'completed'], true)) {
                    $status = 'graded';
                } elseif ($progress->submitted_at !== null || $progress->is_submitted || $progress->status === 'submitted') {
                    $status = 'submitted';
                } elseif ($takingActivity || $progress->status === 'in_progress') {
                    $status = 'in_progress';
                }
            } elseif ($studentActivity && in_array($studentActivity->status, ['in_progress', 'submitted', 'graded'], true)) {
                $status = $studentActivity->status;
            }

            $totalQuestions = (int) ($progress?->total_questions ?? $questionCount);
            $answeredQuestions = (int) ($progress?->answered_questions ?? 0);

            $progressPercentage = $progress?->progress_percentage;
            if ($progressPercentage === null) {
                $progressPercentage = $totalQuestions > 0
                    ? round(($answeredQuestions / $totalQuestions) * 100, 2)
                    : 0;
            }

            return [
                'student_id' => $studentId,
                'student_name' => $studentUser?->name ?? 'Unknown',
                'student_number' => $student?->student_id_text ?? 'N/A',
                'student_email' => $studentUser?->email ?? 'N/A',
                'is_taking_activity' => (bool) $takingActivity,
                'status' => $status,
                'submission_status' => $progress?->submission_status ?? 'not_started',
                'answered_questions' => $answeredQuestions,
                'total_questions' => $totalQuestions,
                'progress_percentage' => (float) $progressPercentage,
                'score' => $progress?->score ?? $studentActivity?->score ?? null,
                'max_score' => $progress?->max_score ?? $progress?->points_possible ?? $studentActivity?->max_score ?? $assignment->total_points,
                'percentage_score' => $progress?->percentage_score ?? $studentActivity?->percentage_score ?? null,
                'submitted_at' => $progress?->submitted_at?->format('Y-m-d H:i:s') ?? $studentActivity?->submitted_at?->format('Y-m-d H:i:s'),
                'graded_at' => $progress?->graded_at?->format('Y-m-d H:i:s') ?? $studentActivity?->graded_at?->format('Y-m-d H:i:s'),
                'requires_grading' => (bool) ($progress?->requires_grading ?? false),
                'can_review' => in_array($status, ['submitted', 'graded'], true) && ($progress?->student_activity_id || $studentActivity?->id),
                'student_activity_id' => $progress?->student_activity_id ?? $studentActivity?->id,
            ];
        })->values()->toArray();
    }
}
