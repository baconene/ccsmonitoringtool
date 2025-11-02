<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentAssignmentAnswer;
use App\Models\InstructorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StudentAssignmentController extends Controller
{
    /**
     * Show assignment by StudentActivity ID
     */
    public function showByStudentActivity(StudentActivity $studentActivity)
    {
        // Verify the student owns this activity
        $user = auth()->user();
        if (!$user->student || $studentActivity->student_id !== $user->student->id) {
            abort(403, 'Unauthorized access to this activity.');
        }

        // Load the assignment
        $assignment = Assignment::where('activity_id', $studentActivity->activity_id)->firstOrFail();
        
        // Redirect to the start method with activity_id
        return redirect()->route('student.assignment.start', $studentActivity->activity_id);
    }

    /**
     * Start an assignment (auto-creates StudentActivity and Progress)
     * Flow: activity_id -> student_id -> StudentActivity -> assignment
     */
    public function start($activityId)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        // Get the activity and assignment
        $activity = \App\Models\Activity::with(['assignment.questions.options'])->findOrFail($activityId);
        
        if (!$activity->assignment) {
            return redirect()->back()->with('error', 'Assignment not found for this activity.');
        }

        $assignment = $activity->assignment;

        // Check if StudentActivity already exists for this student and activity
        $studentActivity = StudentActivity::where('student_id', $student->id)
            ->where('activity_id', $activityId)
            ->first();

        // If assignment is already completed, redirect to results page
        if ($studentActivity && $studentActivity->status === 'completed') {
            return redirect()->route('student.activities.results', $studentActivity->id)
                ->with('info', 'Assignment already completed. Redirected to results.');
        }

        // If not exists, create it with module_id and course_id from module_activities
        if (!$studentActivity) {
            // Get module_id and course_id from module_activities pivot table
            $moduleActivity = \DB::table('module_activities')
                ->where('activity_id', $activityId)
                ->first();
                
            if (!$moduleActivity) {
                return redirect()->back()->with('error', 'Activity not associated with any module.');
            }

            $studentActivity = StudentActivity::create([
                'student_id' => $student->id,
                'activity_id' => $activityId,
                'module_id' => $moduleActivity->module_id,
                'course_id' => $moduleActivity->module_course_id,
                'status' => 'in_progress',
                'started_at' => now(),
                'max_score' => $assignment->total_points,
            ]);
        }

        // Update status to in_progress if not started
        if ($studentActivity->status === 'not_started') {
            $studentActivity->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Get or create progress record
        $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
            ->where('activity_id', $activityId)
            ->first();
            
        if (!$progress) {
            $progress = StudentActivityProgress::create([
                'student_activity_id' => $studentActivity->id,
                'student_id' => $student->id,
                'activity_id' => $activityId,
                'activity_type' => 'assignment',
                'status' => 'in_progress',
                'points_possible' => $assignment->total_points,
                'total_questions' => $assignment->questions()->count(),
                'answered_questions' => 0,
                'requires_grading' => $assignment->acceptsFileUploads(),
                'due_date' => $assignment->activity->due_date,
                'assignment_data' => json_encode(['submission_status' => 'draft']),
            ]);
        }

        // Load assignment with questions and options
        $assignmentData = Assignment::with([
            'questions.options',
            'activity'
        ])->findOrFail($assignment->id);

        // Get student's existing answers and merge with questions
        $answers = StudentAssignmentAnswer::where('student_activity_id', $studentActivity->id)
            ->get()
            ->keyBy('question_id');

        // Add student answers to questions
        $questions = $assignmentData->questions->map(function ($question) use ($answers) {
            $question->student_answer = $answers->get($question->id);
            return $question;
        });

        // Check if assignment is overdue
        $dueDate = $activity->due_date;
        $isOverdue = $dueDate && now()->isAfter($dueDate);

        // Check if student can submit (not submitted and not overdue)
        $canSubmit = $progress->status !== 'submitted' && $progress->status !== 'graded' && !$isOverdue;

        // Get file upload answer if exists
        $fileUploadAnswer = null;
        if ($assignment->acceptsFileUploads()) {
            $fileUploadAnswer = StudentAssignmentAnswer::where('student_activity_id', $studentActivity->id)
                ->whereNotNull('file_path')
                ->first();
        }

        return Inertia::render('Student/TakeAssignment', [
            'assignment' => $assignmentData,
            'questions' => $questions,
            'studentActivity' => $studentActivity,
            'progress' => $progress,
            'fileUploadAnswer' => $fileUploadAnswer,
            'canSubmit' => $canSubmit,
            'isOverdue' => $isOverdue,
        ]);
    }

    /**
     * Show assignment for student to take (OLD METHOD - keeping for backwards compatibility)
     */
    public function show(Assignment $assignment)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();
        
        // Redirect to start method instead
        return redirect()->route('student.assignment.start', $assignment->activity_id);
    }

    /**
     * Show assignment for student to take (LEGACY)
     */
    public function showLegacy(Assignment $assignment)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();
        
        // Get student activity record
        $studentActivity = StudentActivity::where('student_id', $student->id)
            ->where('activity_id', $assignment->activity_id)
            ->firstOrFail();

        // Get or create progress record
        $progress = StudentActivityProgress::firstOrCreate(
            [
                'student_activity_id' => $studentActivity->id,
                'activity_type' => 'assignment'
            ],
            [
                'student_id' => $student->id,
                'activity_id' => $assignment->activity_id,
                'status' => 'in_progress',
                'points_possible' => $assignment->total_points,
                'total_questions' => $assignment->questions()->count(),
                'answered_questions' => 0,
                'requires_grading' => $assignment->acceptsFileUploads(),
                'due_date' => $assignment->activity->due_date,
                'assignment_data' => json_encode(['submission_status' => 'draft']),
            ]
        );

        // Update status to in_progress if not started
        if ($studentActivity->status === 'not_started') {
            $studentActivity->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        // Load assignment with questions and options
        $assignmentData = Assignment::with([
            'questions.options',
            'activity'
        ])->find($assignment->id);

        // Get student's previous answers
        $studentAnswers = StudentAssignmentAnswer::where('student_id', $student->id)
            ->where('assignment_id', $assignment->id)
            ->get()
            ->keyBy('assignment_question_id');

        // Format questions with student answers
        $questionsWithAnswers = $assignmentData->questions->map(function ($question) use ($studentAnswers) {
            $answer = $studentAnswers->get($question->id);
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'points' => $question->points,
                'order' => $question->order,
                'options' => $question->options->map(fn($opt) => [
                    'id' => $opt->id,
                    'option_text' => $opt->option_text,
                    'order' => $opt->order,
                ]),
                'student_answer' => $answer ? [
                    'answer_text' => $answer->answer_text,
                    'selected_options' => $answer->selected_options,
                    'answered_at' => $answer->answered_at,
                ] : null,
            ];
        });

        // Get file upload answer if exists
        $fileUploadAnswer = StudentAssignmentAnswer::where('student_id', $student->id)
            ->where('assignment_id', $assignment->id)
            ->whereNotNull('file_path')
            ->first();

        return Inertia::render('Student/TakeAssignment', [
            'assignment' => $assignmentData,
            'questions' => $questionsWithAnswers,
            'progress' => $progress,
            'studentActivity' => $studentActivity,
            'fileUploadAnswer' => $fileUploadAnswer,
            'canSubmit' => $progress->answered_questions >= $progress->total_questions || $assignment->acceptsFileUploads(),
            'isOverdue' => $progress->isOverdue(),
        ]);
    }

    /**
     * Save answer for a question
     */
    public function saveAnswer(Request $request, Assignment $assignment)
    {
        try {
            $validated = $request->validate([
                'question_id' => 'required|exists:assignment_questions,id',
                'answer_text' => 'nullable|string',
                'selected_options' => 'nullable|array',
                'selected_options.*' => 'exists:assignment_question_options,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for saveAnswer', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            Log::error('Student not found for saveAnswer', [
                'user_id' => auth()->id(),
                'assignment_id' => $assignment->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Student record not found',
            ], 404);
        }
        
        try {
            DB::beginTransaction();

            $question = AssignmentQuestion::findOrFail($validated['question_id']);

            // Create or update answer
            $answer = StudentAssignmentAnswer::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'assignment_id' => $assignment->id,
                    'assignment_question_id' => $question->id,
                ],
                [
                    'answer_text' => $validated['answer_text'] ?? null,
                    'selected_options' => $validated['selected_options'] ?? null,
                    'answered_at' => now(),
                ]
            );

            // Auto-grade if possible
            if (in_array($question->question_type, ['true_false', 'multiple_choice', 'enumeration', 'short_answer'])) {
                $isCorrect = false;
                
                if ($question->question_type === 'multiple_choice') {
                    $isCorrect = $question->checkAnswer($validated['selected_options'] ?? []);
                } else {
                    $isCorrect = $question->checkAnswer($validated['answer_text'] ?? '');
                }

                $answer->update([
                    'is_correct' => $isCorrect,
                    'points_earned' => $isCorrect ? $question->points : 0,
                ]);
            }

            // Update progress
            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $assignment->activity_id)
                ->firstOrFail();

            $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                ->where('activity_id', $assignment->activity_id)
                ->first();
            
            // Create progress if it doesn't exist (for legacy data or direct access)
            if (!$progress) {
                $progress = StudentActivityProgress::create([
                    'student_activity_id' => $studentActivity->id,
                    'student_id' => $student->id,
                    'activity_id' => $assignment->activity_id,
                    'activity_type' => 'assignment',
                    'status' => 'in_progress',
                    'points_possible' => $assignment->total_points,
                    'total_questions' => $assignment->questions()->count(),
                    'answered_questions' => 0,
                    'requires_grading' => $assignment->acceptsFileUploads(),
                    'due_date' => $assignment->activity->due_date,
                    'assignment_data' => json_encode(['submission_status' => 'draft']),
                ]);
            }
            
            // Count answered questions
            $answeredCount = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->whereNotNull('assignment_question_id')
                ->count();

            // Calculate auto-graded score
            $autoGradedScore = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->whereNotNull('assignment_question_id')
                ->whereNotNull('is_correct')
                ->sum('points_earned');

            // Update progress and real-time scores in StudentActivity
            $progress->update([
                'answered_questions' => $answeredCount,
                'points_earned' => $autoGradedScore,
                'score' => $autoGradedScore,
                'percentage_score' => $assignment->total_points > 0 ? round(($autoGradedScore / $assignment->total_points) * 100, 2) : 0,
            ]);

            // Update StudentActivity with real-time scores
            $studentActivity->update([
                'score' => $autoGradedScore,
                'max_score' => $assignment->total_points,
                'percentage_score' => $assignment->total_points > 0 ? round(($autoGradedScore / $assignment->total_points) * 100, 2) : 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully',
                'is_correct' => $answer->is_correct ?? null,
                'points_earned' => $answer->points_earned ?? null,
                'answered_questions' => $answeredCount,
                'auto_graded_score' => $autoGradedScore,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save answer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => $student->id ?? null,
                'assignment_id' => $assignment->id,
                'question_id' => $validated['question_id'] ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save answer: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload file for assignment
     */
    public function uploadFile(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png',
        ]);

        $student = Student::where('user_id', auth()->id())->firstOrFail();

        try {
            DB::beginTransaction();

            // Store file
            $file = $request->file('file');
            $path = $file->store('assignment_submissions', 'public');
            $originalFilename = $file->getClientOriginalName();

            // Delete old file if exists
            $oldAnswer = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->whereNotNull('file_path')
                ->first();

            if ($oldAnswer && $oldAnswer->file_path) {
                Storage::disk('public')->delete($oldAnswer->file_path);
                $oldAnswer->delete();
            }

            // Create answer record
            $answer = StudentAssignmentAnswer::create([
                'student_id' => $student->id,
                'assignment_id' => $assignment->id,
                'file_path' => $path,
                'original_filename' => $originalFilename,
                'answered_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_url' => Storage::url($path),
                'original_filename' => $originalFilename,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to upload file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file',
            ], 500);
        }
    }

    /**
     * Submit assignment
     */
    public function submit(Assignment $assignment)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        try {
            DB::beginTransaction();

            $studentActivity = StudentActivity::where('student_id', $student->id)
                ->where('activity_id', $assignment->activity_id)
                ->firstOrFail();

            $progress = StudentActivityProgress::where('student_activity_id', $studentActivity->id)
                ->where('activity_id', $assignment->activity_id)
                ->firstOrFail();

            // Calculate final score
            $objectiveScore = StudentAssignmentAnswer::where('student_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->whereNotNull('assignment_question_id')
                ->sum('points_earned');

            $totalScore = $objectiveScore;
            $percentage = $assignment->total_points > 0 ? ($totalScore / $assignment->total_points) * 100 : 0;

            // Update progress
            $progressUpdateData = [
                'status' => $progress->requires_grading ? 'submitted' : 'completed',
                'submission_status' => 'submitted',
                'submitted_at' => now(),
                'submission_date' => now(),
                'is_submitted' => true,
                'points_earned' => $totalScore,
                'score' => $totalScore,
                'percentage_score' => $percentage,
                'assignment_data' => json_encode([
                    'submission_status' => 'submitted',
                    'auto_graded_score' => $objectiveScore,
                    'submitted_at' => now()->toDateTimeString(),
                ]),
            ];

            // If no manual grading required, mark as completed and graded
            if (!$progress->requires_grading) {
                $progressUpdateData['is_completed'] = true;
                $progressUpdateData['completed_at'] = now();
                $progressUpdateData['graded_at'] = now();
            }

            $progress->update($progressUpdateData);

            // Update student activity
            $updateData = [
                'status' => $progress->requires_grading ? 'submitted' : 'completed', // Use 'completed' for auto-graded
                'submitted_at' => now(),
                'score' => $totalScore,
                'percentage_score' => $percentage,
                'max_score' => $assignment->total_points,
            ];

            if (!$progress->requires_grading) {
                // Auto-graded assignments are immediately completed
                $updateData['completed_at'] = now();
                $updateData['graded_at'] = now();
            }

            $studentActivity->update($updateData);

            // Auto-complete modules if requirements met (only for auto-graded assignments)
            if (!$progress->requires_grading) {
                $enrollment = \App\Models\CourseEnrollment::where('student_id', $student->id)
                    ->where('course_id', $studentActivity->course_id)
                    ->first();
                
                if ($enrollment) {
                    $enrollment->updateProgress();
                    $enrollment->checkAndCompleteModules();
                }
            }

            // Create notification for instructor
            // Get course through modules
            $module = $assignment->activity->modules()->first();
            $course = $module ? $module->course : null;
            
            // Get instructor's user_id (not instructor record id)
            $instructorUserId = null;
            if ($course && $course->instructor_id) {
                $instructor = \App\Models\Instructor::find($course->instructor_id);
                $instructorUserId = $instructor ? $instructor->user_id : null;
            }
            
            if ($instructorUserId) {
                InstructorNotification::create([
                    'instructor_id' => $instructorUserId,
                    'type' => 'assignment_submitted',
                    'title' => 'New Assignment Submission',
                    'message' => auth()->user()->name . ' has submitted the assignment "' . $assignment->title . '"',
                    'data' => [
                        'student_id' => $student->id,
                        'student_name' => auth()->user()->name,
                        'assignment_id' => $assignment->id,
                        'assignment_title' => $assignment->title,
                        'activity_id' => $assignment->activity_id,
                        'course_id' => $course ? $course->id : null,
                        'requires_grading' => $progress->requires_grading,
                    ],
                    'related_type' => 'App\Models\Assignment',
                    'related_id' => $assignment->id,
                ]);
            }

            DB::commit();

            // Redirect to results page using unified activity results route
            return redirect()->route('student.activities.results', $studentActivity->id)
                ->with('success', 'Assignment submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to submit assignment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'student_id' => $student->id ?? null,
                'assignment_id' => $assignment->id,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return back()->with('error', 'Failed to submit assignment: ' . $e->getMessage());
        }
    }

    /**
     * Get letter grade from percentage (kept for backward compatibility if needed)
     */
    private function getLetterGrade(?float $percentage): ?string
    {
        if ($percentage === null) return null;
        
        return match(true) {
            $percentage >= 97 => 'A+',
            $percentage >= 93 => 'A',
            $percentage >= 90 => 'A-',
            $percentage >= 87 => 'B+',
            $percentage >= 83 => 'B',
            $percentage >= 80 => 'B-',
            $percentage >= 77 => 'C+',
            $percentage >= 73 => 'C',
            $percentage >= 70 => 'C-',
            $percentage >= 67 => 'D+',
            $percentage >= 65 => 'D',
            default => 'F',
        };
    }
}
