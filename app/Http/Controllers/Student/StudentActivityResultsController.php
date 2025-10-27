<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\Assignment;
use App\Models\AssignmentQuestion;
use App\Models\StudentAssignmentAnswer;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\StudentQuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StudentActivityResultsController extends Controller
{
    /**
     * Display results for any activity type (unified endpoint)
     * Route: /student/activities/{studentActivity}/results
     */
    public function show(StudentActivity $studentActivity): Response
    {
        // Verify the student owns this activity
        $user = Auth::user();
        if (!$user->student || $studentActivity->student_id !== $user->student->id) {
            abort(403, 'Unauthorized access to this activity.');
        }

        // Load the activity with its type
        $studentActivity->load(['activity.activityType', 'course', 'module']);
        
        $activity = $studentActivity->activity;
        $activityType = $activity->activityType;

        // Get the model class from activity type
        $modelClass = $activityType->model;

        if (!$modelClass || !class_exists($modelClass)) {
            abort(404, 'Activity type model not found.');
        }

        // Get the activity progress
        // Try to find progress by student_activity_id first, then fall back to student_id + activity_id
        $progress = StudentActivityProgress::where('student_id', $studentActivity->student_id)
            ->where('activity_id', $activity->id)
            ->where('activity_type', strtolower($activityType->name))
            ->first();

        if (!$progress) {
            abort(404, 'No progress found for this activity.');
        }

        // Route to appropriate handler based on activity type
        switch ($activityType->name) {
            case 'Quiz':
                return $this->handleQuizResults($studentActivity, $progress, $activity);
            
            case 'Assignment':
                return $this->handleAssignmentResults($studentActivity, $progress, $activity);
            
            case 'Assessment':
                return $this->handleAssessmentResults($studentActivity, $progress, $activity);
            
            case 'Exercise':
                return $this->handleExerciseResults($studentActivity, $progress, $activity);
            
            default:
                abort(404, 'Unknown activity type.');
        }
    }

    /**
     * Handle Quiz results
     */
    private function handleQuizResults(StudentActivity $studentActivity, StudentActivityProgress $progress, $activity): Response
    {
        $activityType = $activity->activityType;
        
        // Get the model class from activity type (e.g., "App\Models\Quiz")
        $modelClass = $activityType->model;
        $modelName = class_basename($modelClass); // Get "Quiz" from "App\Models\Quiz"
        
        // Dynamically build the answer model class (e.g., "App\Models\StudentQuizAnswer")
        $answerModelClass = "App\\Models\\Student{$modelName}Answer";
        
        if (!class_exists($answerModelClass)) {
            abort(404, "Answer model {$answerModelClass} not found.");
        }
        
        // Get the quiz
        $quiz = $modelClass::where('activity_id', $activity->id)->first();
        
        if (!$quiz) {
            abort(404, "{$modelName} not found.");
        }

        // Load answers dynamically using the answer model
        // All answer tables use 'activity_progress_id' as the foreign key
        $foreignKeyColumn = 'activity_progress_id';
        
        $answers = $answerModelClass::where($foreignKeyColumn, $progress->id)
            ->with(['question.options'])
            ->get()
            ->map(function ($answer) {
                // Get selected option if multiple choice
                if ($answer->selected_option_id) {
                    $answer->selectedOption = $answer->question->options
                        ->firstWhere('id', $answer->selected_option_id);
                }
                return $answer;
            });

        $progress->answers = $answers;
        $progress->quiz = $quiz;
        $progress->activity = $activity;

        return Inertia::render('Student/ActivityResults', [
            'activityType' => $modelName, // 'Quiz'
            'progress' => $progress,
            'studentActivity' => $studentActivity,
            'courseId' => $studentActivity->course_id,
        ]);
    }

    /**
     * Handle Assignment results
     */
    private function handleAssignmentResults(StudentActivity $studentActivity, StudentActivityProgress $progress, $activity): Response
    {
        $assignment = Assignment::where('activity_id', $activity->id)->first();
        
        if (!$assignment) {
            abort(404, 'Assignment not found.');
        }

        // Get assignment questions with options
        $questions = AssignmentQuestion::where('assignment_id', $assignment->id)
            ->with('options')
            ->orderBy('order')
            ->get();

        // Get student answers - use assignment_id and student_id (not progress_id)
        $answers = StudentAssignmentAnswer::where('assignment_id', $assignment->id)
            ->where('student_id', $studentActivity->student_id)
            ->get()
            ->keyBy('assignment_question_id');

        // Build question results
        $questionResults = $questions->map(function ($question) use ($answers) {
            $answer = $answers->get($question->id);
            
            return [
                'question' => [
                    'id' => $question->id,
                    'question_text' => $question->question_text,
                    'question_type' => $question->question_type,
                    'points' => $question->points,
                    'correct_answer' => $question->correct_answer,
                    'explanation' => $question->explanation,
                    'options' => $question->options ? $question->options->map(function ($opt) {
                        return [
                            'id' => $opt->id,
                            'option_text' => $opt->option_text,
                            'is_correct' => $opt->is_correct,
                        ];
                    })->toArray() : null,
                ],
                'student_answer' => $answer ? [
                    'answer_text' => $answer->answer_text,
                    'selected_options' => is_string($answer->selected_options) 
                        ? json_decode($answer->selected_options, true) 
                        : $answer->selected_options,
                    'file_path' => $answer->file_path,
                    'original_filename' => $answer->original_filename,
                    'instructor_feedback' => $answer->instructor_feedback,
                ] : null,
                'is_correct' => $answer?->is_correct,
                'points_earned' => $answer?->points_earned ?? 0,
                'points_possible' => $question->points,
            ];
        });

        // Calculate score if not already set in progress
        $totalPointsEarned = $answers->sum('points_earned');
        $totalPointsPossible = $assignment->total_points ?? $questions->sum('points');
        
        // Use progress values if they exist, otherwise calculate from answers
        $scoreToDisplay = $progress->score ?? $progress->points_earned ?? $totalPointsEarned;
        $percentageToDisplay = $progress->percentage_score ?? 
            ($totalPointsPossible > 0 ? round(($scoreToDisplay / $totalPointsPossible) * 100, 2) : 0);

        // Get file upload if exists
        $fileUpload = null;
        if ($progress->attachment_files) {
            $files = json_decode($progress->attachment_files, true);
            if (!empty($files) && is_array($files)) {
                $firstFile = $files[0] ?? null;
                if ($firstFile) {
                    $fileUpload = [
                        'file_path' => $firstFile['path'] ?? $firstFile['file_path'] ?? null,
                        'original_filename' => $firstFile['name'] ?? $firstFile['original_filename'] ?? 'Uploaded File',
                    ];
                }
            }
        }

        // Build summary
        $summary = [
            'total_points' => $totalPointsPossible,
            'points_earned' => $scoreToDisplay,
            'percentage' => $percentageToDisplay,
            'grade_letter' => $this->calculateGradeLetter($percentageToDisplay),
            'submitted_at' => $progress->submitted_at,
            'graded_at' => $progress->graded_at,
            'requires_grading' => $progress->requires_grading,
            'instructor_feedback' => $progress->feedback,
        ];

        // Add calculated scores to progress object for frontend
        $progress->score = $scoreToDisplay;
        $progress->percentage_score = $percentageToDisplay;
        $progress->total_points = $totalPointsPossible;
        $progress->answers = $questionResults;
        $progress->assignment = $assignment;

        return Inertia::render('Student/ActivityResults', [
            'activityType' => 'Assignment',
            'progress' => $progress,
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title ?? $activity->title,
                'description' => $assignment->description ?? $activity->description,
                'total_points' => $assignment->total_points,
                'activity' => [
                    'title' => $activity->title,
                ],
            ],
            'questionResults' => $questionResults,
            'fileUpload' => $fileUpload,
            'studentActivity' => $studentActivity,
            'summary' => $summary,
            'courseId' => $studentActivity->course_id,
        ]);
    }

    /**
     * Handle Assessment results
     */
    private function handleAssessmentResults(StudentActivity $studentActivity, StudentActivityProgress $progress, $activity): Response
    {
        // For now, return a basic view - can be expanded later
        return Inertia::render('Student/ActivityResults', [
            'activityType' => 'Assessment',
            'progress' => $progress,
            'studentActivity' => $studentActivity,
            'activity' => $activity,
            'courseId' => $studentActivity->course_id,
            'message' => 'Assessment results view - Coming soon',
        ]);
    }

    /**
     * Handle Exercise results
     */
    private function handleExerciseResults(StudentActivity $studentActivity, StudentActivityProgress $progress, $activity): Response
    {
        // For now, return a basic view - can be expanded later
        return Inertia::render('Student/ActivityResults', [
            'activityType' => 'Exercise',
            'progress' => $progress,
            'studentActivity' => $studentActivity,
            'activity' => $activity,
            'courseId' => $studentActivity->course_id,
            'message' => 'Exercise results view - Coming soon',
        ]);
    }

    /**
     * Calculate grade letter from percentage
     */
    private function calculateGradeLetter(?float $percentage): ?string
    {
        if ($percentage === null) {
            return null;
        }

        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 63) return 'D';
        if ($percentage >= 60) return 'D-';
        return 'F';
    }
}
