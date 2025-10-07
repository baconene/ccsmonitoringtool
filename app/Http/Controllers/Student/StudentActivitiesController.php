<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentQuizProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Student Activities Controller
 * 
 * Handles all student activity-related functionality including:
 * - Activity listing with status and due dates
 * - Activity details and progress tracking
 * - Activity filtering and sorting
 * 
 * Separated from StudentCourseController for better code organization
 * following single responsibility principle.
 */
class StudentActivitiesController extends Controller
{
    /**
     * Display student's activity summary with status and due dates
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();
        
        // Use the new method from StudentQuizProgress to get all activities
        $studentActivities = StudentQuizProgress::getStudentActivities($user->id);

        $activities = [];
        $courses = collect();
        
        foreach ($studentActivities as $item) {
            $activity = $item['activity'];
            $course = $item['course'];
            $module = $item['module'];
            $lesson = $item['lesson'];
            
            // Get activity status and progress
            $statusData = StudentQuizProgress::getActivityStatus($user->id, $activity->id);
            
            // Calculate due date (for demo, using created_at + 7 days)
            $dueDate = $activity->created_at->addDays(7);
            $isPastDue = $dueDate->isPast() && $statusData['status'] !== 'completed';
            
            // Prepare progress data safely
            $progressData = null;
            if ($statusData['progress'] && is_object($statusData['progress'])) {
                $progress = $statusData['progress'];
                $progressData = [
                    'score' => $progress->score ?? 0,
                    'percentage_score' => $progress->percentage_score ?? 0,
                    'completed_questions' => $progress->completed_questions ?? 0,
                    'total_questions' => $progress->total_questions ?? 0,
                ];
            }
            
            $activities[] = [
                'id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description,
                'activity_type' => $activity->activityType ? $activity->activityType->name : 'Unknown',
                'course_id' => $course->id,
                'course_name' => $course->title,
                'module_id' => $module->id,
                'module_name' => $module->name,
                'lesson_id' => $lesson ? $lesson->id : null,
                'lesson_name' => $lesson ? $lesson->title : null,
                'source' => $item['source'], // 'module' or 'lesson'
                'due_date' => $dueDate->format('Y-m-d H:i:s'),
                'due_date_formatted' => $dueDate->format('M j, Y'),
                'status' => $statusData['status'],
                'is_past_due' => $isPastDue,
                'progress' => $progressData,
                'question_count' => $activity->quiz ? $activity->quiz->questions->count() : 0,
                'total_points' => $activity->quiz ? $activity->quiz->questions->sum('points') : 0,
            ];
            
            // Collect unique courses for filter dropdown
            if (!$courses->contains('id', $course->id)) {
                $courses->push([
                    'id' => $course->id,
                    'title' => $course->title,
                ]);
            }
        }
        
        // Sort by due date (ascending) then by status priority
        $statusPriority = ['in-progress' => 1, 'not-taken' => 2, 'completed' => 3];
        
        usort($activities, function ($a, $b) use ($statusPriority) {
            // First sort by due date
            $dateComparison = strcmp($a['due_date'], $b['due_date']);
            if ($dateComparison !== 0) {
                return $dateComparison;
            }
            // Then by status priority
            return ($statusPriority[$a['status']] ?? 4) - ($statusPriority[$b['status']] ?? 4);
        });

        return Inertia::render('Student/MyActivity', [
            'activities' => $activities,
            'courses' => $courses->values()->toArray(),
            'filters' => [
                'course_id' => $request->get('course_id'),
                'status' => $request->get('status'),
            ]
        ]);
    }

    /**
     * Display the specified activity details for the student
     */
    public function show(Request $request, $activityId): Response
    {
        $user = auth()->user();
        
        // TODO: Implement activity detail view
        // This would show detailed information about a specific activity
        // including instructions, due date, requirements, etc.
        
        return Inertia::render('Student/ActivityDetail', [
            'activity_id' => $activityId,
            // Add activity details here
        ]);
    }
}