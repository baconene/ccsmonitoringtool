<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentQuizAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentQuizController extends Controller
{
    /**
     * Show quiz by StudentActivity ID - redirects to start method
     */
    public function show(StudentActivity $studentActivity)
    {
        // Verify the student owns this activity
        $user = auth()->user();
        if (!$user->student || $studentActivity->student_id !== $user->student->id) {
            abort(403, 'Unauthorized access to this activity.');
        }

        // Redirect to the quiz start route with the activity ID
        return redirect()->route('student.quiz.start', $studentActivity->activity_id);
    }

    /**
     * Start a new quiz or continue existing progress
     */
    public function start(Request $request, $activityId)
    {
        $activity = Activity::with(['quiz.questions.options'])->findOrFail($activityId);
        
        if (!$activity->quiz) {
            return redirect()->back()->with('error', 'Quiz not found for this activity.');
        }

        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }
        
        // Check if student already has progress for this quiz
        $progress = StudentActivityProgress::where('student_id', $student->id)
            ->where('activity_id', $activityId)
            ->where('activity_type', 'quiz')
            ->first();

        // Get activity status
        $statusData = $this->getActivityStatus($student->id, $activityId);
        
        // Check if quiz is already completed
        if ($statusData['status'] === 'completed' && $statusData['progress']) {
            return redirect()->route('student.activities.results', $statusData['progress']->student_activity_id)
                ->with('info', 'Quiz already completed. Redirected to results.');
        }

        // Check if quiz is past due (get due date from activity model, fallback to created_at + 7 days)
        $dueDate = $activity->due_date ?? $activity->created_at->addDays(7);
        $isPastDue = $dueDate->isPast() && $statusData['status'] !== 'completed';
        
        if ($isPastDue) {
            return redirect()->back()
                ->with('error', "Quiz deadline has passed. This quiz was due on {$dueDate->format('M j, Y')} at {$dueDate->format('g:i A')}.");
        }

        // Use existing progress from status data or create new one
        $progress = $statusData['progress'];
        
        if (!$progress) {
            // Use updateOrCreate to prevent duplicates
            $progress = StudentActivityProgress::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'activity_id' => $activityId,
                    'activity_type' => 'quiz',
                ],
                [
                    'started_at' => now(),
                    'last_accessed_at' => now(),
                    'total_questions' => $activity->quiz->questions->count(),
                    'completed_questions' => 0,
                    'status' => 'in_progress',
                    'quiz_data' => json_encode(['quiz_id' => $activity->quiz->id]),
                ]
            );
        } else {
            // Double-check that the quiz is not completed (additional safety check)
            if ($progress->is_completed) {
                return redirect()->route('student.activities.results', $progress->student_activity_id)
                    ->with('info', 'Quiz already completed. Redirected to results.');
            }
            
            // Update last accessed time
            $progress->update(['last_accessed_at' => now()]);
        }

        return Inertia::render('Student/QuizTaking', [
            'activity' => $activity->load('activityType'),
            'quiz' => $activity->quiz->load('questions.options'),
            'progress' => $progress->load('answers'),
        ]);
    }

    /**
     * Submit an answer to a question
     */
    public function submitAnswer(Request $request, $progressId)
    {
        // Force this to not be an Inertia request
        $request->headers->remove('X-Inertia');
        $request->headers->remove('X-Inertia-Version');
        
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'nullable|exists:question_options,id',
            'answer_text' => 'nullable|string',
        ]);

        // Ensure at least one answer field is provided
        $hasSelectedOption = isset($validated['selected_option_id']) && $validated['selected_option_id'] !== null;
        $hasAnswerText = isset($validated['answer_text']) && trim($validated['answer_text']) !== '';
        
        if (!$hasSelectedOption && !$hasAnswerText) {
            return response()->json(['error' => 'Please provide an answer'], 422);
        }

        $progress = StudentActivityProgress::where('activity_type', 'quiz')->findOrFail($progressId);

        // Check if this is the current user's progress
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student || $progress->student_id !== $student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get answer_text from selected option if provided
        $answerText = $validated['answer_text'] ?? null;
        
        // If selected_option_id is provided, populate answer_text with the option text
        if (isset($validated['selected_option_id']) && $validated['selected_option_id']) {
            $selectedOption = \App\Models\QuestionOption::find($validated['selected_option_id']);
            if ($selectedOption) {
                $answerText = $selectedOption->option_text;
            }
        }
        
        // Use updateOrCreate to prevent duplicates and ensure answer_text is always populated
        StudentQuizAnswer::updateOrCreate(
            [
                'activity_progress_id' => $progress->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'student_id' => $student->id,
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $answerText,
                'answered_at' => now(),
            ]
        );

        // Get fresh count from database (not from cached relationship)
        $answeredCount = StudentQuizAnswer::where('activity_progress_id', $progress->id)->count();

        // Update progress with fresh count
        $progress->update([
            'completed_questions' => $answeredCount,
            'last_accessed_at' => now(),
        ]);

        // Return plain JSON response (not Inertia)
        return response()->json(['success' => true]);
    }

    /**
     * Submit the entire quiz
     */
    public function submit(Request $request, $progressId)
    {
        $progress = StudentActivityProgress::with(['activity.quiz.questions'])
            ->where('activity_type', 'quiz')
            ->findOrFail($progressId);

        // Check if this is the current user's progress
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student || $progress->student_id !== $student->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Check if already submitted
        if ($progress->is_submitted) {
            return redirect()->back()->with('error', 'Quiz already submitted');
        }

        // Get the quiz
        $quiz = $progress->activity->quiz;
        if (!$quiz) {
            return redirect()->back()->with('error', 'Quiz not found');
        }

        // Validate that all questions have been answered
        $totalQuestions = $quiz->questions->count();
        $answeredQuestions = StudentQuizAnswer::where('activity_progress_id', $progress->id)->count();

        if ($answeredQuestions < $totalQuestions) {
            return redirect()->back()->with('error', 'Please answer all questions before submitting.');
        }

        // Calculate time spent (in seconds) from started_at to now
        $timeSpent = $progress->started_at ? $progress->started_at->diffInSeconds(now()) : 0;

        // Check all answers and calculate points
        $answers = StudentQuizAnswer::with(['question', 'selectedOption'])
            ->where('activity_progress_id', $progress->id)
            ->get();
        
        foreach ($answers as $answer) {
            $answer->checkAnswer(); // This sets points_earned and is_correct and saves
        }

        // Reload the answers to get the updated points_earned from database
        $progress->refresh();
        $progress->load(['answers', 'activity.quiz.questions']);

        // Mark as completed and submitted
        $progress->update([
            'is_completed' => true,
            'is_submitted' => true,
            'last_accessed_at' => now(),
            'time_spent' => $timeSpent,
        ]);
        
        // Calculate final score after refreshing
        $totalPointsEarned = $progress->answers->sum('points_earned');
        $totalPointsPossible = $quiz->questions->sum('points');
        $percentageScore = $totalPointsPossible > 0 ? round(($totalPointsEarned / $totalPointsPossible) * 100, 2) : 0;
        
        $progress->update([
            'points_earned' => $totalPointsEarned,
            'points_possible' => $totalPointsPossible,
            'score' => $totalPointsEarned,
            'max_score' => $totalPointsPossible,
            'percentage_score' => $percentageScore,
        ]);
        
        // Refresh to get updated values
        $progress->refresh();

        // Update StudentActivity status to completed
        $studentActivity = \App\Models\StudentActivity::where('student_id', $student->id)
            ->where('activity_id', $progress->activity_id)
            ->first();
            
        if ($studentActivity) {
            $studentActivity->update([
                'status' => 'completed',
                'completed_at' => now(),
                'submitted_at' => now(),
                'score' => $progress->score,
                'percentage_score' => $progress->percentage_score,
            ]);
            
            // Ensure progress record has the student_activity_id
            if (!$progress->student_activity_id) {
                $progress->update(['student_activity_id' => $studentActivity->id]);
            }
            
            // Update course enrollment progress
            $courseId = $progress->activity->modules->first()?->course_id;
            if ($courseId) {
                $enrollment = \App\Models\CourseEnrollment::where('user_id', $user->id)
                    ->where('course_id', $courseId)
                    ->first();
                if ($enrollment) {
                    $enrollment->updateProgress();
                }
            }
        }

        // Get passing percentage from activity
        $passingScore = $progress->activity->passing_percentage ?? 70;
        $passed = $progress->percentage_score >= $passingScore;
        $message = $passed 
            ? "Congratulations! You passed with a score of {$progress->percentage_score}%!" 
            : "You scored {$progress->percentage_score}%. The passing score is {$passingScore}%. Keep practicing!";

        // Use student_activity_id for the unified results route
        $studentActivityId = $progress->student_activity_id ?? $studentActivity->id ?? null;
        
        if (!$studentActivityId) {
            return redirect()->back()->with('error', 'Unable to find student activity record');
        }

        return redirect()->route('student.activities.results', $studentActivityId)
            ->with('success', $message);
    }

    /**
     * Get student's quiz progress for a specific quiz
     */
    public function getProgress($activityId)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return response()->json(['error' => 'Student record not found'], 404);
        }
        
        $progress = StudentActivityProgress::where('student_id', $student->id)
            ->where('activity_id', $activityId)
            ->where('activity_type', 'quiz')
            ->with(['activity'])
            ->first();

        if ($progress) {
            // Load quiz answers separately
            $quizData = json_decode($progress->quiz_data, true);
            $answers = StudentQuizAnswer::where('quiz_progress_id', $progress->id)->get();
            $progress->answers = $answers;
        }

        return response()->json($progress);
    }
    
    /**
     * Helper method to get activity status for quiz
     */
    private function getActivityStatus($studentId, $activityId)
    {
        $progress = StudentActivityProgress::where('student_id', $studentId)
            ->where('activity_id', $activityId)
            ->where('activity_type', 'quiz')
            ->first();

        if (!$progress) {
            return [
                'status' => 'not-taken',
                'progress' => null
            ];
        }

        $status = 'not-taken';
        if ($progress->is_completed && $progress->is_submitted) {
            $status = 'completed';
        } elseif ($progress->started_at) {
            $status = 'in-progress';
        }

        return [
            'status' => $status,
            'progress' => $progress
        ];
    }
}
