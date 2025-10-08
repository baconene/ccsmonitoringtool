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
                    
                    $dueDate = $activity->due_date ?? $activity->created_at->addDays(7);
                    $isPastDue = now()->isAfter($dueDate);
                    
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'activity_type' => $activity->activityType,
                        'question_count' => $activity->quiz ? $activity->quiz->questions->count() : 0,
                        'total_points' => $activity->quiz ? $activity->quiz->questions->sum('points') : 0,
                        'due_date' => $dueDate->toDateTimeString(),
                        'is_past_due' => $isPastDue,
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
            ],
            'enrollment' => [
                'is_completed' => $enrollment->is_completed,
                'created_at' => $enrollment->created_at->format('Y-m-d'),
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
            return response()->json(['error' => "You are not enrolled in the '{$course->title}' course"], 403);
        }

        // Verify lesson belongs to course
        $lesson = $course->lessons()->find($lessonId);
        if (!$lesson) {
            return response()->json(['error' => "Lesson not found in the '{$course->title}' course"], 404);
        }

        // Check if already completed
        $existingCompletion = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->where('course_id', $course->id)
            ->first();
            
        if ($existingCompletion) {
            return response()->json(['message' => "You have already completed the '{$lesson->title}' lesson"]);
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
            return response()->json(['error' => "You are not enrolled in the '{$course->title}' course"], 403);
        }

        // Get the module to check its weight
        $module = $course->modules()->find($moduleId);
        if (!$module) {
            return response()->json(['error' => "Module not found in the '{$course->title}' course"], 404);
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
            'success' => "'{$module->title}' module marked as completed successfully",
            'progress' => (float) $enrollment->progress,
        ]);
    }

    /**
     * Display student's activity summary with status and due dates
     */
    public function activities(Request $request)
    {
        $user = auth()->user();
        
        // Use the new method from StudentQuizProgress to get all activities
        $studentActivities = \App\Models\StudentQuizProgress::getStudentActivities($user->id);

        $activities = [];
        $courses = collect();
        
        foreach ($studentActivities as $item) {
            $activity = $item['activity'];
            $course = $item['course'];
            $module = $item['module'];
            $lesson = $item['lesson'];
            
            // Get activity status and progress
            $statusData = \App\Models\StudentQuizProgress::getActivityStatus($user->id, $activity->id);
            
            // Get due date from activity model (fallback to created_at + 7 days if not set)
            $dueDate = $activity->due_date ?? $activity->created_at->addDays(7);
            $isPastDue = $dueDate->isPast() && $statusData['status'] !== 'completed';
            
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
                'progress_id' => ($statusData['progress'] && is_object($statusData['progress'])) ? $statusData['progress']->id : null,
                'progress' => ($statusData['progress'] && is_object($statusData['progress'])) ? [
                    'score' => $statusData['progress']->score ?? 0,
                    'percentage_score' => $statusData['progress']->percentage_score ?? 0,
                    'completed_questions' => $statusData['progress']->completed_questions ?? 0,
                    'total_questions' => $statusData['progress']->total_questions ?? 0,
                ] : null,
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
     * Display detailed view of a specific module for the student
     */
    public function showModule(Course $course, $moduleId): Response
    {
        $user = auth()->user();
        
        // Check if student is enrolled in this course
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Get the specific module with all relationships
        $module = $course->modules()
            ->with([
                'lessons.documents',
                'activities.activityType',
                'activities.quiz.questions',
                'documents'
            ])
            ->where('id', $moduleId)
            ->first();
            
        if (!$module) {
            abort(404, 'Module not found in this course.');
        }

        // Check module completion status
        $moduleCompletion = \App\Models\ModuleCompletion::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->where('course_id', $course->id)
            ->first();

        // Get lessons with completion status
        $lessons = $module->lessons->map(function ($lesson) use ($user, $course) {
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
                'content_type' => $lesson->content_type ?? 'text',
                'is_completed' => $completion ? true : false,
                'completed_at' => $completion ? $completion->completed_at : null,
                'documents' => $lesson->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'name' => $doc->name,
                        'file_path' => $doc->file_path,
                        'doc_type' => $doc->doc_type,
                    ];
                }),
            ];
        });

        // Get activities with quiz progress
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
                'activity_type' => $activity->activityType ? $activity->activityType->name : 'Unknown',
                'question_count' => $activity->quiz ? $activity->quiz->questions->count() : 0,
                'total_points' => $activity->quiz ? $activity->quiz->questions->sum('points') : 0,
                'is_completed' => $quizProgress ? ($quizProgress->is_completed && $quizProgress->is_submitted) : false,
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

        // Calculate completion status
        $totalLessons = $lessons->count();
        $completedLessons = $lessons->where('is_completed', true)->count();
        $totalActivities = $activities->count();
        $completedActivities = $activities->where('is_completed', true)->count();
        
        // Module can only be marked complete if all lessons and activities are completed
        $canMarkComplete = ($totalLessons === $completedLessons) && ($totalActivities === $completedActivities);

        // Update course enrollment progress
        $enrollment->updateProgress();
        $enrollment->refresh();

        return Inertia::render('Student/CourseModuleDetail', [
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'instructor_name' => $course->instructor_name,
                'progress' => (float) $enrollment->progress,
                'is_completed' => $enrollment->is_completed,
                'enrolled_at' => $enrollment->created_at->format('Y-m-d'),
            ],
            'module' => [
                'id' => $module->id,
                'title' => $module->title,
                'description' => $module->description,
                'module_type' => $module->module_type,
                'module_percentage' => $module->module_percentage,
                'is_completed' => $moduleCompletion ? true : false,
                'completed_at' => $moduleCompletion ? $moduleCompletion->completed_at : null,
                'can_mark_complete' => $canMarkComplete,
                'lessons' => $lessons,
                'activities' => $activities,
                'documents' => $module->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'name' => $doc->name,
                        'file_path' => $doc->file_path,
                        'doc_type' => $doc->doc_type,
                    ];
                }),
            ],
            'stats' => [
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'total_activities' => $totalActivities,
                'completed_activities' => $completedActivities,
                'completion_percentage' => $totalLessons + $totalActivities > 0 
                    ? round((($completedLessons + $completedActivities) / ($totalLessons + $totalActivities)) * 100, 1)
                    : 0,
            ]
        ]);
    }
}