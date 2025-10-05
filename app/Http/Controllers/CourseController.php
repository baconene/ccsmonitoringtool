<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of courses for the course management page.
     */
    public function index()
    {
        // Fetch all courses with their modules, lessons, and grade levels
        return Inertia::render('CourseManagement', [
            'courses' => Course::with(['modules.lessons.documents', 'gradeLevels'])->get()
        ]);
    }

    /**
     * Get courses for API (dashboard use).
     */
    public function getCourses()
    {
        $user = Auth::user();
        
        // Get courses for the authenticated instructor with students
        $courses = Course::with(['students.user', 'instructor'])
            ->where('instructor_id', $user->id)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title ?: $course->name,
                    'name' => $course->name, // For backward compatibility
                    'description' => $course->description,
                    'instructor_id' => $course->instructor_id,
                    'instructor_name' => $course->instructor->name ?? '',
                    'created_at' => $course->created_at->toISOString(),
                    'updated_at' => $course->updated_at->toISOString(),
                    'students' => $course->students->map(function ($student) use ($course) {
                        return [
                            'id' => $student->id,
                            'student_id' => $student->student_id,
                            'name' => $student->name,
                            'email' => $student->email,
                            'enrollment_number' => $student->enrollment_number,
                            'program' => $student->program,
                            'department' => $student->department,
                            'status' => $student->pivot->status ?? 'enrolled',
                            'enrolled_at' => $student->pivot->enrolled_at ?? null,
                            'grade' => $student->pivot->grade ?? null,
                            'courseId' => $course->id,
                            'courseTitle' => $course->title ?: $course->name,
                        ];
                    })
                ];
            });

        return response()->json($courses);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level_ids' => 'nullable|array',
            'grade_level_ids.*' => 'exists:grade_levels,id',
        ]);

        $course = Course::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Attach grade levels if provided
        if (!empty($validated['grade_level_ids'])) {
            $course->gradeLevels()->attach($validated['grade_level_ids']);
        }

        // Load grade levels for response
        $course->load('gradeLevels');

        return response()->json([
            'success' => true,
            'course' => $course,
            'message' => 'Course created successfully'
        ]);
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load('modules.lessons.documents');
        return response()->json($course);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'grade_level_ids' => 'nullable|array',
            'grade_level_ids.*' => 'exists:grade_levels,id',
        ]);

        $course->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync grade levels (this will add new ones and remove old ones)
        if (isset($validated['grade_level_ids'])) {
            $course->gradeLevels()->sync($validated['grade_level_ids']);
        }

        // Load grade levels for response
        $course->load('gradeLevels');

        return response()->json([
            'message' => 'Course updated successfully!',
            'course' => $course
        ]);
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully!'
        ]);
    }
}
