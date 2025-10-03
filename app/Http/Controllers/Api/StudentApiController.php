<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentApiController extends Controller
{
    /**
     * Display a listing of students for the authenticated instructor's courses.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get all students enrolled in the instructor's courses
            // This assumes a many-to-many relationship between users and courses
            $students = DB::table('users')
                ->join('course_user', 'users.id', '=', 'course_user.user_id')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->where('courses.instructor_id', $user->id)
                ->where('users.id', '!=', $user->id) // Exclude the instructor
                ->select('users.id', 'users.name', 'users.email')
                ->distinct()
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                    ];
                });

            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student.
     */
    public function show(User $student): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Check if the student is enrolled in any of the instructor's courses
            $hasAccess = DB::table('course_user')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->where('courses.instructor_id', $user->id)
                ->where('course_user.user_id', $student->id)
                ->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'message' => 'Unauthorized access to student information'
                ], 403);
            }
            
            return response()->json([
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}