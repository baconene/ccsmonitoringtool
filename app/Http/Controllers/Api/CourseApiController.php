<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CourseApiController extends Controller
{
    /**
     * Display a listing of courses for the authenticated instructor.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get courses for the authenticated instructor
            $courses = Course::with(['students'])
                ->where('instructor_id', $user->id)
                ->get()
                ->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'description' => $course->description,
                        'instructor_id' => $course->instructor_id,
                        'created_at' => $course->created_at->toISOString(),
                        'updated_at' => $course->updated_at->toISOString(),
                        'students' => $course->students->map(function ($student) use ($course) {
                            return [
                                'id' => $student->id,
                                'name' => $student->name,
                                'email' => $student->email,
                                'courseId' => $course->id,
                                'courseTitle' => $course->title,
                            ];
                        })
                    ];
                });

            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $course = Course::create([
                'title' => $request->title,
                'description' => $request->description,
                'instructor_id' => Auth::id(),
            ]);

            return response()->json($course->load('students'), 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): JsonResponse
    {
        try {
            // Check if user has access to this course
            if ($course->instructor_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized access to course'
                ], 403);
            }

            return response()->json($course->load('students'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course): JsonResponse
    {
        try {
            // Check if user has access to this course
            if ($course->instructor_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized access to course'
                ], 403);
            }

            $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
            ]);

            $course->update($request->only(['title', 'description']));

            return response()->json($course->load('students'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): JsonResponse
    {
        try {
            // Check if user has access to this course
            if ($course->instructor_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized access to course'
                ], 403);
            }

            $course->delete();

            return response()->json([
                'message' => 'Course deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students enrolled in a specific course.
     */
    public function getStudents(Course $course): JsonResponse
    {
        try {
            // Check if user has access to this course
            if ($course->instructor_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Unauthorized access to course'
                ], 403);
            }

            $students = $course->students->map(function ($student) use ($course) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'courseId' => $course->id,
                    'courseTitle' => $course->title,
                ];
            });

            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch course students',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}