<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\StudentActivity;
use App\Models\StudentQuizProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentActivityController extends Controller
{
    /**
     * Mark an activity as complete for the current student
     */
    public function markComplete(Request $request, $activityId)
    {
        $activity = Activity::findOrFail($activityId);
        $user = Auth::user();
        $student = $user->student;
        
        // Get the module ID from the request
        $moduleId = $request->input('module_id');
        if (!$moduleId) {
            return back()->withErrors(['error' => 'Module ID is required']);
        }

        if (!$student) {
            return back()->withErrors(['error' => 'Student record not found']);
        }

        // Check if activity can be marked as complete
        // Only allow for non-quiz/assignment activities OR quiz/assignment with 0 questions
        $activityTypeName = $activity->activityType ? $activity->activityType->name : 'Unknown';
        
        $canMarkComplete = false;
        
        if (!in_array($activityTypeName, ['Quiz', 'Assignment'])) {
            // Non-quiz/assignment activities can always be marked complete
            $canMarkComplete = true;
        } elseif ($activityTypeName === 'Quiz') {
            // Quiz activities can be marked complete only if they have 0 questions
            $canMarkComplete = $activity->quiz && $activity->quiz->questions()->count() === 0;
        } elseif ($activityTypeName === 'Assignment') {
            // Assignment activities can always be marked complete manually
            $canMarkComplete = true;
        }

        if (!$canMarkComplete) {
            return back()->withErrors(['error' => 'This activity cannot be marked as complete directly']);
        }

        // For Quiz activities only, create a completed quiz progress record if needed
        if ($activityTypeName === 'Quiz' && $activity->quiz && $activity->quiz->id) {
            // Use updateOrCreate to prevent duplicates
            StudentQuizProgress::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'quiz_id' => $activity->quiz->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'score' => 0,
                    'percentage_score' => 100, // 100% since there are no questions
                    'total_questions' => 0,
                    'is_completed' => true,
                    'is_submitted' => true,
                    'started_at' => now(),
                    'time_spent' => 0,
                ]
            );
        }

        // Mark activity as complete in student_activities table
        // Since StudentActivity records are auto-created in StudentCourseController,
        // we can assume the record exists and just update it
        $studentActivity = StudentActivity::where('student_id', $student->id)
            ->where('activity_id', $activity->id)
            ->first();

        if (!$studentActivity) {
            // Fallback: create record if somehow it doesn't exist
            $module = \App\Models\Module::findOrFail($moduleId);
            $studentActivity = StudentActivity::create([
                'student_id' => $student->id,
                'module_id' => $moduleId,
                'course_id' => $module->course_id,
                'activity_id' => $activity->id,
                'status' => 'not_started',
                'score' => null,
                'max_score' => 0,
                'percentage_score' => null,
                'activity_type' => strtolower($activityTypeName),
                'started_at' => null,
                'completed_at' => null,
                'submitted_at' => null,
            ]);
        }

        // Update the record to mark as complete, preserve existing scores if they exist
        $updateData = [
            'status' => 'completed',
            'completed_at' => now(),
            'submitted_at' => now(),
            'started_at' => $studentActivity->started_at ?: now(),
            // Only update scores if they're currently null (for activities with no scoring)
            'score' => $studentActivity->score !== null ? $studentActivity->score : 0,
            'max_score' => $studentActivity->max_score ?: 0,
            'percentage_score' => $studentActivity->percentage_score !== null 
                ? $studentActivity->percentage_score 
                : ($studentActivity->max_score > 0 ? 0 : 100), // 100% for activities with no scoring
        ];
        
        $studentActivity->update($updateData);

        // Return back to preserve scroll position and show success in frontend
        return back()->with('success', 'Activity marked as complete successfully!');
    }
}
