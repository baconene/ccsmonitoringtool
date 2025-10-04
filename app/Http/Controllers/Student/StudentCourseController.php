<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentCourseController extends Controller
{
    /**
     * Display a listing of the student's enrolled courses.
     */
    public function index(): Response
    {
        $user = auth()->user();
        
        $enrollments = CourseEnrollment::with(['course.lessons', 'course.modules'])
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($enrollment) use ($user) {
                $course = $enrollment->course;
                $totalLessons = $course->lessons->count();
                $completedLessons = LessonCompletion::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->count();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'instructor_name' => $course->instructor_name,
                    'progress' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0,
                    'is_completed' => $enrollment->is_completed,
                    'enrolled_at' => $enrollment->created_at->format('Y-m-d'),
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'duration' => $course->lessons->sum('duration') ?? 0,
                ];
            });

        return Inertia::render('Student/Courses', [
            'courses' => $enrollments,
            'stats' => [
                'total_courses' => $enrollments->count(),
                'completed_courses' => $enrollments->where('is_completed', true)->count(),
                'in_progress' => $enrollments->where('is_completed', false)->count(),
                'total_hours' => round($enrollments->sum('duration') / 60, 1),
            ]
        ]);
    }

    /**
     * Display the specified course details for the student.
     */
    public function show(Course $course): Response
    {
        $user = auth()->user();
        
        // Check if student is enrolled in this course
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$enrollment) {
            abort(404, 'You are not enrolled in this course.');
        }

        // Get lessons with completion status
        $lessons = $course->lessons()
            ->with('module')
            ->orderBy('order')
            ->get()
            ->map(function ($lesson) use ($user, $course) {
                $completion = LessonCompletion::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->where('course_id', $course->id)
                    ->first();
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'duration' => $lesson->duration ?? 45,
                    'order' => $lesson->order,
                    'is_completed' => $completion ? true : false,
                    'completed_at' => $completion ? $completion->completed_at : null,
                    'content_type' => $lesson->content_type ?? 'text',
                    'module_name' => $lesson->module ? $lesson->module->name : 'General',
                ];
            });

        $totalLessons = $lessons->count();
        $completedLessons = $lessons->where('is_completed', true)->count();
        $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        return Inertia::render('Student/CourseDetail', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'instructor_name' => $course->instructor_name,
                'progress' => $progress,
                'is_completed' => $enrollment->is_completed,
                'enrolled_at' => $enrollment->created_at->format('Y-m-d'),
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'lessons' => $lessons,
            ]
        ]);
    }

    /**
     * Mark a lesson as completed for the student.
     */
    public function completeLesson(Request $request, Course $course, $lessonId)
    {
        $user = auth()->user();
        
        // Verify enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Verify lesson belongs to course
        $lesson = $course->lessons()->find($lessonId);
        if (!$lesson) {
            return response()->json(['error' => 'Lesson not found'], 404);
        }

        // Check if already completed
        $existingCompletion = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->where('course_id', $course->id)
            ->first();
            
        if ($existingCompletion) {
            return response()->json(['message' => 'Lesson already completed']);
        }

        // Create lesson completion
        LessonCompletion::create([
            'user_id' => $user->id,
            'lesson_id' => $lessonId,
            'course_id' => $course->id,
            'completed_at' => now(),
            'completion_data' => json_encode([
                'method' => 'manual',
                'timestamp' => now()->toISOString(),
            ])
        ]);

        // Update course enrollment progress
        $enrollment->updateProgress();

        return response()->json([
            'message' => 'Lesson marked as completed',
            'progress' => $enrollment->progress
        ]);
    }

    /**
     * Get lessons for a specific course (API endpoint).
     */
    public function getLessons(Course $course)
    {
        $user = auth()->user();
        
        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        $lessons = $course->lessons()
            ->with('module')
            ->orderBy('order')
            ->get()
            ->map(function ($lesson) use ($user, $course) {
                $completion = LessonCompletion::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->where('course_id', $course->id)
                    ->first();
                
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'description' => $lesson->description,
                    'duration' => $lesson->duration ?? 45,
                    'order' => $lesson->order,
                    'is_completed' => $completion ? true : false,
                    'completed_at' => $completion ? $completion->completed_at : null,
                    'content_type' => $lesson->content_type ?? 'text',
                    'module_name' => $lesson->module ? $lesson->module->name : 'General',
                ];
            });

        return response()->json([
            'lessons' => $lessons,
            'progress' => $enrollment->progress
        ]);
    }
}