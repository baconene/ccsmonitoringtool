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
                $totalModules = $course->modules->count();
                
                // Count completed modules
                $completedModules = \App\Models\ModuleCompletion::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->count();
                
                // Recalculate progress based on module weights
                $enrollment->updateProgress();
                $enrollment->refresh();
                
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'instructor_name' => $course->instructor_name,
                    'progress' => (float) $enrollment->progress,
                    'is_completed' => $enrollment->is_completed,
                    'enrolled_at' => $enrollment->created_at->format('Y-m-d'),
                    'total_modules' => $totalModules,
                    'completed_modules' => $completedModules,
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

        // Load modules with activities and quiz progress
        $modules = $course->modules()
            ->with([
                'lessons',
                'activities.activityType',
                'activities.quiz.questions'
            ])
            ->get()
            ->map(function ($module) use ($user, $course) {
                // Check if module is completed
                $moduleCompletion = \App\Models\ModuleCompletion::where('user_id', $user->id)
                    ->where('module_id', $module->id)
                    ->where('course_id', $course->id)
                    ->first();
                // Map activities with quiz progress
                $activities = $module->activities->map(function ($activity) use ($user) {
                    $quizProgress = null;
                    
                    if ($activity->quiz) {
                        $quizProgress = \App\Models\StudentQuizProgress::where('student_id', $user->id)
                            ->where('activity_id', $activity->id)
                            ->first();
                    }
                    
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'activity_type' => $activity->activityType,
                        'question_count' => $activity->quiz ? $activity->quiz->questions->count() : 0,
                        'total_points' => $activity->quiz ? $activity->quiz->questions->sum('points') : 0,
                        'quiz_progress' => $quizProgress ? [
                            'id' => $quizProgress->id,
                            'is_completed' => $quizProgress->is_completed,
                            'is_submitted' => $quizProgress->is_submitted,
                            'score' => $quizProgress->score,
                            'percentage_score' => $quizProgress->percentage_score,
                            'completed_questions' => $quizProgress->completed_questions,
                            'total_questions' => $quizProgress->total_questions,
                        ] : null,
                    ];
                });

                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'description' => $module->description,
                    'module_type' => $module->module_type,
                    'lessons' => $module->lessons,
                    'activities' => $activities,
                    'is_completed' => $moduleCompletion ? true : false,
                    'completed_at' => $moduleCompletion ? $moduleCompletion->completed_at : null,
                ];
            });

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

        // Recalculate progress based on completed module weights
        $enrollment->updateProgress();
        $enrollment->refresh();
        
        $totalModules = $modules->count();
        $completedModulesCount = $modules->where('is_completed', true)->count();

        return Inertia::render('Student/CourseDetail', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'instructor_name' => $course->instructor_name,
                'progress' => (float) $enrollment->progress,
                'is_completed' => $enrollment->is_completed,
                'enrolled_at' => $enrollment->created_at->format('Y-m-d'),
                'total_modules' => $totalModules,
                'completed_modules' => $completedModulesCount,
                'lessons' => $lessons,
                'modules' => $modules,
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

        return redirect()->back()->with('success', 'Lesson marked as completed');
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

    /**
     * Mark a module as complete for the student.
     */
    public function completeModule(Course $course, $moduleId)
    {
        $user = auth()->user();
        
        // Check enrollment
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Get the module to check its weight
        $module = $course->modules()->find($moduleId);
        if (!$module) {
            return response()->json(['error' => 'Module not found'], 404);
        }

        // Create or update module completion
        $completion = \App\Models\ModuleCompletion::updateOrCreate([
            'user_id' => $user->id,
            'module_id' => $moduleId,
            'course_id' => $course->id,
        ], [
            'completed_at' => now(),
            'completion_data' => json_encode([
                'method' => 'manual',
                'timestamp' => now()->toISOString(),
                'module_weight' => $module->module_percentage,
            ])
        ]);

        // Update course enrollment progress based on module weights
        $enrollment->updateProgress();
        $enrollment->refresh();

        return redirect()->back()->with([
            'success' => 'Module marked as completed',
            'progress' => (float) $enrollment->progress,
        ]);
    }
}