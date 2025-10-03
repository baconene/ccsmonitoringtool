<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics for the authenticated instructor.
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get courses count
            $totalCourses = Course::where('instructor_id', $user->id)->count();
            
            // Get total students across all courses using the new Student model
            $totalStudents = DB::table('course_student')
                ->join('courses', 'course_student.course_id', '=', 'courses.id')
                ->where('courses.instructor_id', $user->id)
                ->where('course_student.status', 'enrolled')
                ->distinct('course_student.student_id')
                ->count();
            
            // Placeholder for upcoming classes - you'll need to implement based on your schedule model
            $upcomingClasses = 5; // This should be calculated based on your schedule data
            
            // Placeholder for assignments - implement based on your assignments model
            $totalAssignments = 12; // This should be calculated based on your assignments data
            
            return response()->json([
                'totalCourses' => $totalCourses,
                'totalStudents' => $totalStudents,
                'upcomingClasses' => $upcomingClasses,
                'totalAssignments' => $totalAssignments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get instructor profile information.
     */
    public function getInstructorProfile(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch instructor profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}