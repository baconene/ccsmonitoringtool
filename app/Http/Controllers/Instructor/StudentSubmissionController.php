<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\StudentProgress;
use App\Models\Activity;
use App\Models\QuestionAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class StudentSubmissionController extends Controller
{
    /**
     * Display a specific student submission
     */
    public function show(StudentProgress $submission)
    {
        Log::info('Showing submission', ['submission_id' => $submission->id]);

        // Load relationships
        $submission->load([
            'student.user',
            'activity.course'
        ]);

        // Get activity type and details
        $activity = $submission->activity;
        $activityType = $this->determineActivityType($activity);

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
            'activity_type' => $activity->activity_type,
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
    public function grade(Request $request, StudentProgress $submission)
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
            foreach ($request->grades as $grade) {
                QuestionAnswer::where('student_progress_id', $submission->id)
                    ->where('question_id', $grade['question_id'])
                    ->update([
                        'earned_points' => $grade['earned_points'],
                        'feedback' => $grade['feedback'] ?? null,
                        'is_correct' => $grade['earned_points'] > 0,
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
     */
    private function determineActivityType(Activity $activity): string
    {
        $type = $activity->activity_type;
        
        // Map activity types to frontend types
        $typeMap = [
            'assignment' => 'assignment',
            'quiz' => 'quiz',
            'project' => 'project',
        ];

        return $typeMap[$type] ?? 'assignment';
    }

    /**
     * Get submission status
     */
    private function getSubmissionStatus(StudentProgress $submission): string
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
    private function getSubmissionAnswers(StudentProgress $submission, Activity $activity, string $activityType): array
    {
        $answers = [];

        // Get questions based on activity type
        if ($activityType === 'quiz') {
            $questions = $activity->quiz->questions ?? collect();
        } elseif ($activityType === 'assignment') {
            $questions = $activity->assignment->questions ?? collect();
        } else {
            $questions = $activity->questions ?? collect();
        }

        foreach ($questions as $question) {
            // Get student's answer
            $answer = QuestionAnswer::where('student_progress_id', $submission->id)
                ->where('question_id', $question->id)
                ->first();

            $formattedAnswer = [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'points' => $question->points ?? 1,
                'student_answer' => $answer->answer ?? null,
                'student_answers' => $answer->answers ?? [],
                'correct_answer' => $question->correct_answer ?? null,
                'correct_answers' => $question->correct_answers ?? [],
                'is_correct' => $answer->is_correct ?? null,
                'earned_points' => $answer->earned_points ?? 0,
                'feedback' => $answer->feedback ?? null,
            ];

            $answers[] = $formattedAnswer;
        }

        return $answers;
    }

    /**
     * Notify student about grading
     */
    private function notifyStudent(StudentProgress $submission): void
    {
        try {
            $activity = $submission->activity;
            $course = $activity->course;

            // Create student notification
            \App\Models\StudentNotification::create([
                'student_id' => $submission->student_id,
                'title' => 'Submission Graded',
                'message' => "Your {$activity->activity_type} '{$activity->title}' in {$course->title} has been graded.",
                'type' => 'grade',
                'related_type' => get_class($submission),
                'related_id' => $submission->id,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating student notification', [
                'error' => $e->getMessage(),
                'submission_id' => $submission->id,
            ]);
        }
    }
}
