<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\StudentActivity;
use App\Models\Activity;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class StudentSubmissionController extends Controller
{
    /**
     * Display a specific student submission
     */
    public function show(StudentActivity $submission)
    {
        Log::info('Showing submission', ['submission_id' => $submission->id]);

        // Load relationships
        $submission->load([
            'student.user',
            'activity.modules.course',
            'activity.assignment.questions.options',
            'activity.quiz.questions.options',
            'activity.activityType'
        ]);

        // Get activity type and details
        $activity = $submission->activity;
        // Use the activity_type string from StudentActivity or derive from activityType relationship
        $activityType = $submission->activity_type ?? strtolower($activity->activityType->name ?? 'assignment');

        // Get answers/questions based on activity type
        $answers = $this->getSubmissionAnswers($submission, $activity, $activityType);

        // Format submission data
        $formattedSubmission = [
            'id' => $submission->id,
            'student_id' => $submission->student_id,
            'student' => [
                'id' => $submission->student->id,
                'name' => $submission->student->user->name,
                'email' => $submission->student->user->email,
            ],
            'status' => $this->getSubmissionStatus($submission),
            'progress' => $submission->progress ?? 0,
            'score' => $submission->score,
            'total_score' => $submission->total_score ?? $activity->total_score ?? 100,
            'submitted_at' => $submission->submitted_at,
            'graded_at' => $submission->graded_at,
            'answers' => $answers,
        ];

        // Format activity data
        $formattedActivity = [
            'id' => $activity->id,
            'title' => $activity->title,
            'description' => $activity->description,
            'activity_type' => $activityType,
            'activity_type_name' => $activity->activityType?->name ?? 'Unknown',
        ];

        return Inertia::render('ActivityManagement/StudentSubmissionShow', [
            'submission' => $formattedSubmission,
            'activity' => $formattedActivity,
            'activityType' => $activityType,
        ]);
    }

    /**
     * Grade a student submission
     */
    public function grade(Request $request, StudentActivity $submission)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.question_id' => 'required|integer',
            'grades.*.earned_points' => 'required|numeric|min:0',
            'grades.*.feedback' => 'nullable|string',
            'total_score' => 'required|numeric|min:0',
        ]);

        Log::info('Grading submission', [
            'submission_id' => $submission->id,
            'grades_count' => count($request->grades),
            'total_score' => $request->total_score,
        ]);

        try {
            // Update individual question grades
            // Note: You may need to implement answer grading based on your activity type
            foreach ($request->grades as $grade) {
                // This would need to be implemented based on your specific answer models
                // StudentQuizAnswer or StudentAssignmentAnswer
                Log::info('Grading question', [
                    'question_id' => $grade['question_id'],
                    'points' => $grade['earned_points']
                ]);
            }

            // Update overall submission
            $submission->update([
                'score' => $request->total_score,
                'graded_at' => now(),
                'progress' => 100, // Mark as complete
            ]);

            // Create notification for student
            $this->notifyStudent($submission);

            return redirect()->back()->with('success', 'Submission graded successfully!');
        } catch (\Exception $e) {
            Log::error('Error grading submission', [
                'error' => $e->getMessage(),
                'submission_id' => $submission->id,
            ]);

            return redirect()->back()->with('error', 'Failed to grade submission.');
        }
    }

    /**
     * Determine activity type from activity
     * @deprecated Use $submission->activity_type instead
     */
    private function determineActivityType(Activity $activity): string
    {
        // Use activityType relationship to get the correct type name
        $type = strtolower($activity->activityType->name ?? 'assignment');
        
        // Map activity types to frontend types
        $typeMap = [
            'quiz' => 'quiz',
            'assignment' => 'assignment',
            'project' => 'project',
            'lesson' => 'lesson',
        ];

        return $typeMap[$type] ?? 'assignment';
    }

    /**
     * Get submission status
     */
    private function getSubmissionStatus(StudentActivity $submission): string
    {
        if ($submission->graded_at) {
            return 'graded';
        }

        if ($submission->submitted_at) {
            return 'submitted';
        }

        return 'in_progress';
    }

    /**
     * Get submission answers based on activity type
     */
    private function getSubmissionAnswers(StudentActivity $submission, Activity $activity, string $activityType): array
    {
        $answers = [];

        // Get questions based on activity type
        if ($activityType === 'quiz') {
            $questions = $activity->quiz?->questions ?? collect();
        } elseif ($activityType === 'assignment') {
            $questions = $activity->assignment?->questions ?? collect();
        } else {
            $questions = $activity->questions ?? collect();
        }

        foreach ($questions as $question) {
            // Get student's answer - this should be implemented based on your answer models
            $answer = null; // TODO: Implement based on activity type (StudentQuizAnswer or StudentAssignmentAnswer)

            $questionData = [
                'id' => $question->id,
                'question_text' => $question->question_text ?? $question->text ?? '',
                'question_type' => $question->question_type ?? $question->type ?? 'multiple_choice',
                'points' => $question->points ?? 1,
                'student_answer' => $answer ? $answer->answer : null,
                'student_answers' => $answer ? ($answer->answers ?? []) : [],
                'correct_answer' => $question->correct_answer ?? null,
                'correct_answers' => $question->correct_answers ?? [],
                'is_correct' => $answer ? $answer->is_correct : null,
                'earned_points' => $answer ? ($answer->earned_points ?? 0) : 0,
                'feedback' => $answer ? ($answer->feedback ?? null) : null,
            ];

            // Add options if this is a multiple choice question
            if (in_array($questionData['question_type'], ['multiple_choice', 'single_choice'])) {
                $questionData['options'] = ($question->options ?? collect())->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'text' => $option->option_text ?? '',
                        'is_correct' => $option->is_correct ?? false,
                    ];
                })->toArray();
            }

            $answers[] = $questionData;
        }

        return $answers;
    }

    /**
     * Notify student about grading
     */
    private function notifyStudent(StudentActivity $submission): void
    {
        try {
            $activity = $submission->activity;
            // Notification logic can be implemented here when notification system is ready
            Log::info('Grading notification would be sent', [
                'student_id' => $submission->student_id,
                'activity_id' => $activity->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating student notification', [
                'error' => $e->getMessage(),
                'submission_id' => $submission->id,
            ]);
        }
    }
}
