<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index()
    {
        // Fetch all courses with their modules (and lessons if needed)
     return Inertia::render('CourseManagement', [
            'courses' => Course::with('modules.lessons.documents')->get()
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',  
        ]);

        $course = Course::create($validated);

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
        ]);

        $course->update($validated);

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
