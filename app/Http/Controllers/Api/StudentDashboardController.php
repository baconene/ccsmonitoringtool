<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\StudentActivityProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    /**
     * Get dashboard data for the authenticated student.
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get enrolled courses with progress
            $enrollments = CourseEnrollment::with(['course.lessons', 'course.modules'])
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($enrollment) use ($user) {
                    $course = $enrollment->course;
                    $totalModules = $course->modules->count();
                    
                    // Count completed modules
                    $completedModules = \App\Models\ModuleCompletion::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->count();
                    
                    // Get next class date (using created_at + 7 days as example)
                    $nextClass = $course->created_at->addDays(7);
                    
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'instructor' => $course->instructor_name ?? 'Unknown',
                        'progress' => (float) $enrollment->progress,
                        'nextClass' => $nextClass->format('Y-m-d H:i A'),
                    ];
                });

            $student = $user->student;
            if (!$student) {
                return response()->json([
                    'enrolledCourses' => $enrollments->values()->toArray(),
                    'assignments' => [],
                    'overdueActivities' => [],
                    'grades' => [],
                    'schedule' => []
                ]);
            }

            // Get all courses the student is enrolled in
            $enrolledCoursesWithActivities = CourseEnrollment::with([
                'course.modules.activities.activityType',
                'course.modules.lessons.activities.activityType'
            ])
            ->where('user_id', $user->id)
            ->get();

            $studentActivities = collect();
            foreach ($enrolledCoursesWithActivities as $enrollment) {
                $course = $enrollment->course;
                
                // Get activities from modules
                foreach ($course->modules as $module) {
                    foreach ($module->activities as $activity) {
                        $studentActivities->push([
                            'activity' => $activity,
                            'course' => $course,
                            'module' => $module,
                        ]);
                    }
                    
                    // Get activities from lessons within modules
                    foreach ($module->lessons as $lesson) {
                        foreach ($lesson->activities as $activity) {
                            $studentActivities->push([
                                'activity' => $activity,
                                'course' => $course,
                                'module' => $module,
                            ]);
                        }
                    }
                }
            }

            $assignments = [];
            $overdueActivities = [];
            
            foreach ($studentActivities as $item) {
                $activity = $item['activity'];
                $course = $item['course'];
                
                // Get activity progress/status
                $progress = StudentActivityProgress::where('student_id', $student->id)
                    ->where('activity_id', $activity->id)
                    ->first();
                
                $isCompleted = false;
                if ($progress && $progress->is_completed && $progress->is_submitted) {
                    $isCompleted = true;
                }
                
                $dueDate = $activity->due_date ?? $activity->created_at->addDays(7);
                $isOverdue = $dueDate->isPast() && !$isCompleted;
                
                $activityData = [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'course' => $course->title,
                    'dueDate' => $dueDate->format('Y-m-d'),
                    'status' => $isCompleted ? 'completed' : 'pending',
                    'activityType' => $activity->activityType ? $activity->activityType->name : 'Assignment'
                ];
                
                if ($isOverdue) {
                    $overdueActivities[] = $activityData;
                } else {
                    $assignments[] = $activityData;
                }
            }

            // Get grades from completed activities
            $grades = [];
            foreach ($studentActivities as $item) {
                $activity = $item['activity'];
                $course = $item['course'];
                
                $progress = StudentActivityProgress::where('student_id', $student->id)
                    ->where('activity_id', $activity->id)
                    ->first();
                
                if ($progress && $progress->is_completed && $progress->is_submitted) {
                    $score = $progress->percentage_score ?? 85; // Default score if not set
                    
                    $grades[] = [
                        'course' => $course->title,
                        'assignment' => $activity->title,
                        'score' => (int) $score
                    ];
                }
            }

            // Mock schedule data (you can enhance this with a real Schedule model)
            $schedule = $enrollments->map(function ($course) {
                return [
                    'id' => $course['id'],
                    'course' => $course['title'],
                    'type' => 'Lecture',
                    'date' => Carbon::now()->addDays(rand(1, 7))->format('Y-m-d'),
                    'time' => '10:00 AM',
                    'room' => 'Room ' . rand(101, 299)
                ];
            });

            return response()->json([
                'enrolledCourses' => $enrollments->values()->toArray(),
                'assignments' => $assignments,
                'overdueActivities' => $overdueActivities,
                'grades' => $grades,
                'schedule' => $schedule->values()->toArray(),
                'stats' => [
                    'totalCourses' => $enrollments->count(),
                    'pendingAssignments' => collect($assignments)->where('status', 'pending')->count(),
                    'overdueCount' => count($overdueActivities),
                    'averageGrade' => $grades ? round(collect($grades)->avg('score'), 0) : 0,
                    'upcomingClasses' => $schedule->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}