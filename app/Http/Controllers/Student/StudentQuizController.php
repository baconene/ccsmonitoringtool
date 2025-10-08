<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Quiz;
use App\Models\StudentQuizProgress;
use App\Models\StudentQuizAnswer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentQuizController extends Controller
{
    /**
     * Start a new quiz or continue existing progress
     */
    public function start(Request $request, $activityId)
    {
        $activity = Activity::with(['quiz.questions.options'])->findOrFail($activityId);
        
        if (!$activity->quiz) {
            return redirect()->back()->with('error', 'Quiz not found for this activity.');
        }

        $student = auth()->user();
        
        // Check if student already has progress for this quiz
        $progress = StudentQuizProgress::where('student_id', $student->id)
            ->where('quiz_id', $activity->quiz->id)
            ->where('activity_id', $activityId)
            ->first();

        // Get activity status using the existing method
        $statusData = StudentQuizProgress::getActivityStatus($student->id, $activityId);
        
        // Check if quiz is already completed
        if ($statusData['status'] === 'completed') {
            return redirect()->route('student.quiz.results', $statusData['progress']->id)
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
            // Create new progress
            $progress = StudentQuizProgress::create([
                'student_id' => $student->id,
                'quiz_id' => $activity->quiz->id,
                'activity_id' => $activityId,
                'started_at' => now(),
                'last_accessed_at' => now(),
                'total_questions' => $activity->quiz->questions->count(),
                'completed_questions' => 0,
            ]);
        } else {
            // Double-check that the quiz is not completed (additional safety check)
            if ($progress->is_completed) {
                return redirect()->route('student.quiz.results', $progress->id)
                    ->with('info', 'Quiz already completed. Redirected to results.');
            }
            
            // Update last accessed time
            $progress->update(['last_accessed_at' => now()]);
        }

        return Inertia::render('Student/QuizTaking', [
            'activity' => $activity,
            'quiz' => $activity->quiz,
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

        $progress = StudentQuizProgress::findOrFail($progressId);

        // Check if this is the current user's progress
        if ($progress->student_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if already answered
        $existingAnswer = StudentQuizAnswer::where('quiz_progress_id', $progress->id)
            ->where('question_id', $validated['question_id'])
            ->first();

        if ($existingAnswer) {
            // Update existing answer (don't check correctness yet)
            $existingAnswer->update([
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
                'answered_at' => now(),
            ]);
        } else {
            // Create new answer (don't check correctness yet)
            StudentQuizAnswer::create([
                'student_id' => auth()->id(),
                'quiz_progress_id' => $progress->id,
                'question_id' => $validated['question_id'],
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
                'answered_at' => now(),
            ]);
        }

        // Get fresh count from database (not from cached relationship)
        $answeredCount = StudentQuizAnswer::where('quiz_progress_id', $progress->id)->count();

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
        $progress = StudentQuizProgress::with(['quiz.questions', 'activity'])->findOrFail($progressId);

        // Check if this is the current user's progress
        if ($progress->student_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Check if already submitted
        if ($progress->is_submitted) {
            return redirect()->back()->with('error', 'Quiz already submitted');
        }

        // Validate that all questions have been answered
        $totalQuestions = $progress->quiz->questions->count();
        $answeredQuestions = StudentQuizAnswer::where('quiz_progress_id', $progress->id)->count();

        if ($answeredQuestions < $totalQuestions) {
            return redirect()->back()->with('error', 'Please answer all questions before submitting.');
        }

        // Calculate time spent (in seconds) from started_at to now
        $timeSpent = $progress->started_at ? $progress->started_at->diffInSeconds(now()) : 0;

        // Check all answers and calculate points
        $answers = StudentQuizAnswer::with(['question', 'selectedOption'])
            ->where('quiz_progress_id', $progress->id)
            ->get();
        
        foreach ($answers as $answer) {
            $answer->checkAnswer(); // This sets points_earned and is_correct and saves
        }

        // Reload the answers to get the updated points_earned from database
        $progress->refresh();
        $progress->load(['answers', 'quiz.questions', 'activity']);

        // Mark as completed and submitted
        $progress->update([
            'is_completed' => true,
            'is_submitted' => true,
            'last_accessed_at' => now(),
            'time_spent' => $timeSpent,
        ]);
        
        // Calculate final score after refreshing
        $progress->calculateScore();

        // Get passing percentage from activity
        $passingScore = $progress->activity->passing_percentage ?? 70;
        $passed = $progress->percentage_score >= $passingScore;
        $message = $passed 
            ? "Congratulations! You passed with a score of {$progress->percentage_score}%!" 
            : "You scored {$progress->percentage_score}%. The passing score is {$passingScore}%. Keep practicing!";

        return redirect()->route('student.quiz.results', $progress->id)
            ->with('success', $message);
    }

    /**
     * Show quiz results
     */
    public function results($progressId)
    {
        $progress = StudentQuizProgress::with([
            'quiz.questions.options',
            'answers.question.options',
            'answers.selectedOption',
            'activity.modules.course'
        ])->findOrFail($progressId);

        // Check if this is the current user's progress
        if ($progress->student_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        // Calculate total points from questions
        $totalPoints = $progress->quiz->questions->sum('points');
        
        // Attach total_points to quiz object for frontend as float
        $progress->quiz->total_points = (float) $totalPoints;

        // Get course_id from the first module (activities can be in multiple modules, but usually in one)
        $courseId = $progress->activity->modules->first()?->course_id;

        // Ensure score and percentage are floats (Laravel decimal cast returns strings)
        $progress->score = (float) $progress->score;
        $progress->percentage_score = (float) $progress->percentage_score;

        return Inertia::render('Student/QuizResults', [
            'progress' => $progress,
            'activity' => $progress->activity,
            'courseId' => $courseId,
        ]);
    }

    /**
     * Get student's quiz progress for a specific quiz
     */
    public function getProgress($activityId)
    {
        $progress = StudentQuizProgress::where('student_id', auth()->id())
            ->where('activity_id', $activityId)
            ->with(['quiz', 'answers'])
            ->first();

        return response()->json($progress);
    }
}
