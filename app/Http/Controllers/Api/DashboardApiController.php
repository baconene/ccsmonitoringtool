<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\User;
use App\Models\Activity;
use App\Models\Schedule;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics for the authenticated user (Instructor or Student).
     */
    public function getStats(Request $request): JsonResponse
    {
        \Log::info('Dashboard getStats called', [
            'url' => $request->fullUrl(),
            'user_id' => Auth::id(),
            'authenticated' => Auth::check(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);

        try {
            $user = Auth::user();
            
            if (!$user) {
                \Log::error('getStats: User not authenticated');
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $roleName = $user->role_name;
            
            \Log::info('getStats: User role determined', [
                'user_id' => $user->id,
                'role_name' => $roleName,
            ]);
            
            if ($roleName === 'instructor' || $roleName === 'admin') {
                return $this->getInstructorStats($user);
            } elseif ($roleName === 'student') {
                return $this->getStudentStats($user);
            }
            
            \Log::warning('getStats: Invalid user role', [
                'user_id' => $user->id,
                'role_name' => $roleName,
            ]);

            return response()->json([
                'message' => 'Invalid user role',
                'user_role' => $roleName,
                'debug' => [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id
                ]
            ], 403);
            
        } catch (\Exception $e) {
            \Log::error('getStats: Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get instructor-specific dashboard statistics.
     */
    private function getInstructorStats($user): JsonResponse
    {
        \Log::info('getInstructorStats called', ['user_id' => $user->id]);

        // Get courses taught by instructor - auto-create if doesn't exist
        $instructor = Instructor::where('user_id', $user->id)->first();
        if (!$instructor) {
            \Log::info('getInstructorStats: Creating instructor record', ['user_id' => $user->id]);
            $instructor = $user->getOrCreateInstructorRecord();
        }

        \Log::info('getInstructorStats: Instructor found', ['instructor_id' => $instructor->id]);

        $totalCourses = Course::where('instructor_id', $instructor->id)->count();

        // Get unique students across all instructor's courses
        $totalStudents = DB::table('course_student')
            ->join('courses', 'course_student.course_id', '=', 'courses.id')
            ->where('courses.instructor_id', $instructor->id)
            ->where('course_student.status', 'enrolled')
            ->distinct('course_student.student_id')
            ->count('course_student.student_id');
        
        // Get total activities within instructor's courses
        $totalActivities = Activity::whereHas('modules.course', function ($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->count();
        
        // Get upcoming schedules count where instructor is a participant
        $upcomingSchedules = Schedule::forUser($user->id)
            ->where('from_datetime', '>=', Carbon::now())
            ->count();
        
        // Placeholder for pending activity reviews
        $pendingReviews = 0; // Placeholder
        
        \Log::info('getInstructorStats: Stats calculated', [
            'totalCourses' => $totalCourses,
            'totalStudents' => $totalStudents,
            'totalActivities' => $totalActivities,
            'upcomingSchedules' => $upcomingSchedules,
        ]);

        return response()->json([
            'totalCourses' => $totalCourses,
            'totalStudents' => $totalStudents,
            'totalActivities' => $totalActivities,
            'upcomingSchedules' => $upcomingSchedules,
            'pendingReviews' => $pendingReviews,
        ]);
    }
    
    /**
     * Get student-specific dashboard statistics.
     */
    private function getStudentStats($user): JsonResponse
    {
        \Log::info('getStudentStats called', ['user_id' => $user->id]);

        try {
            // Get enrolled courses count 
            $student= Student::where('user_id', $user->id)->first();
            if (!$student) {
                \Log::error('getStudentStats: Student record not found', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Student record not found for user'
                ], 404);
            }

            \Log::info('getStudentStats: Student found', ['student_id' => $student->id]);

            $totalCourses = CourseEnrollment::where('student_id', $student->id)->count();

            // Simplified incomplete activities count - placeholder for now
            $incompleteActivities = 0; // TODO: Implement proper activity tracking
            
            // Get upcoming schedules count for student using forUser scope
            $scheduledCourses = Schedule::forUser($user->id)
                ->where('from_datetime', '>=', Carbon::now())
                ->count();
            
            // Placeholder for grade average
            $gradeAverage = 0; // Placeholder
            
            \Log::info('getStudentStats: Stats calculated', [
                'totalCourses' => $totalCourses,
                'incompleteActivities' => $incompleteActivities,
                'scheduledCourses' => $scheduledCourses,
            ]);

            return response()->json([
                'totalCourses' => $totalCourses,
                'incompleteActivities' => $incompleteActivities,
                'scheduledCourses' => $scheduledCourses,
                'gradeAverage' => $gradeAverage,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getStudentStats: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Failed to fetch student statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get instructor profile information.
     */
    public function getInstructorProfile(Request $request): JsonResponse
    {
        \Log::info('DashboardApiController::getInstructorProfile called', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
        
        try {
            $user = Auth::user();
            
            \Log::info('DashboardApiController::getInstructorProfile - Profile retrieved', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);
            
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            \Log::error('DashboardApiController::getInstructorProfile - Error: ' . $e->getMessage(), [
                'user_id' => Auth::id() ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Failed to fetch instructor profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get student dashboard data including courses, activities, and schedules.
     */
    public function getStudentData(Request $request): JsonResponse
    {
        \Log::info('DashboardApiController::getStudentData called', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
        
        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->id)->first();
            
            \Log::info('DashboardApiController::getStudentData - Student lookup', [
                'user_id' => $user->id,
                'student_found' => $student !== null,
                'student_id' => $student ? $student->id : null
            ]);
            
            if (!$student) {
                \Log::warning('DashboardApiController::getStudentData - No student record found', [
                    'user_id' => $user->id,
                ]);
                
                return response()->json([
                    'message' => 'Student record not found for user',
                    'enrolledCourses' => [],
                    'assignments' => [],
                    'overdueActivities' => [],
                    'grades' => [],
                    'schedule' => [],
                ]);
            }            
            
            // Get enrolled courses from course_enrollments table
            $enrolledCourses = Course::join('course_enrollments', 'courses.id', '=', 'course_enrollments.course_id')
                ->where('course_enrollments.student_id', $student->id)
                ->with(['instructor', 'instructor.user'])
                ->select('courses.*', 'course_enrollments.progress', 'course_enrollments.enrolled_at')
                ->get()
                ->map(function ($course) use ($user) {
                    try {
                        // Get progress from enrollment
                        $progress = $course->progress ?? 0;
                        
                        return [
                            'id' => $course->id,
                            'title' => $course->title,
                            'instructor' => $course->instructor?->user?->name ?? $course->instructor?->name ?? 'N/A',
                            'progress' => $progress,
                            'nextClass' => 'No upcoming classes', // TODO: Implement schedule checking
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error mapping course: ' . $e->getMessage());
                        return null;
                    }
                })
                ->filter();
                
            \Log::info('Enrolled courses found', [
                'count' => $enrolledCourses->count(),
                'courses' => $enrolledCourses->toArray()
            ]);
            
            // Get incomplete activities for the student (not_started, in_progress, pending)
            $activities = DB::table('student_activities as sa')
                ->join('activities as a', 'sa.activity_id', '=', 'a.id')
                ->join('modules as m', 'sa.module_id', '=', 'm.id')
                ->join('courses as c', 'sa.course_id', '=', 'c.id')
                ->join('activity_types as at', 'a.activity_type_id', '=', 'at.id')
                ->where('sa.student_id', $student->id)
                ->whereIn('sa.status', ['not_started', 'in_progress', 'pending'])
                ->select(
                    'sa.id',
                    'a.title',
                    'c.title as course',
                    'sa.course_id as courseId',
                    'sa.module_id as moduleId',
                    'at.name as activityType',
                    'at.id as activityTypeId',
                    'sa.started_at as dueDate',
                    DB::raw("'pending' as status")
                )
                ->orderBy('sa.started_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'course' => $activity->course,
                        'courseId' => $activity->courseId,
                        'moduleId' => $activity->moduleId,
                        'dueDate' => Carbon::parse($activity->dueDate)->format('M d, Y'),
                        'status' => 'pending',
                        'activityType' => $activity->activityType,
                        'activityTypeId' => $activity->activityTypeId,
                    ];
                });
            
            $overdueActivities = [];
            
            // Get upcoming schedules for the student
            $schedules = Schedule::whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('from_datetime', '>=', Carbon::now())
            ->orderBy('from_datetime', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'type' => $schedule->scheduleType->name ?? 'Event',
                    'date' => Carbon::parse($schedule->from_datetime)->format('Y-m-d'),
                    'time' => Carbon::parse($schedule->from_datetime)->format('h:i A') . ' - ' . Carbon::parse($schedule->to_datetime)->format('h:i A'),
                    'location' => $schedule->location ?? 'TBA',
                ];
            });
            
            \Log::info('DashboardApiController::getStudentData - Data prepared', [
                'enrolled_courses_count' => $enrolledCourses->count(),
                'assignments_count' => $activities->count(),
                'overdue_activities_count' => count($overdueActivities),
                'schedules_count' => $schedules->count(),
            ]);
            
            return response()->json([
                'enrolledCourses' => $enrolledCourses,
                'assignments' => $activities,
                'overdueActivities' => $overdueActivities,
                'grades' => [], // Placeholder
                'schedule' => $schedules,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('DashboardApiController::getStudentData - Error: ' . $e->getMessage(), [
                'user_id' => Auth::id() ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Failed to fetch student data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get instructor dashboard data including courses and schedules.
     */
    public function getInstructorData(Request $request): JsonResponse
    {
        \Log::info('DashboardApiController::getInstructorData called', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
        
        try {
            $user = Auth::user();
            $instructor = Instructor::where('user_id', $user->id)->first();
            
            // Auto-create instructor record if doesn't exist
            if (!$instructor) {
                \Log::info('DashboardApiController::getInstructorData - Creating instructor record', [
                    'user_id' => $user->id,
                ]);
                $instructor = $user->getOrCreateInstructorRecord();
            }
            
            \Log::info('DashboardApiController::getInstructorData - Instructor lookup', [
                'user_id' => $user->id,
                'instructor_found' => $instructor !== null,
                'instructor_id' => $instructor->id ?? null,
            ]);
            
            // Get instructor's courses with student count
            $courses = Course::where('instructor_id', $instructor->id)
                ->withCount('students')
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'description' => $course->description,
                        'students_count' => $course->students_count,
                        'activities_count' => 0, // TODO: Implement activity counting
                    ];
                });
            
            // Simplified schedules - placeholder for now
            $schedules = [];
            
            \Log::info('DashboardApiController::getInstructorData - Data prepared', [
                'courses_count' => $courses->count(),
                'schedules_count' => count($schedules),
            ]);
            
            return response()->json([
                'courses' => $courses,
                'schedule' => $schedules,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getInstructorData: ' . $e->getMessage(), [
                'user_id' => $user->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Failed to fetch instructor data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}