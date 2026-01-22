<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Student\StudentActivityController;
use App\Http\Controllers\UserController;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Load API routes for scheduling
require __DIR__.'/api_schedules.php';

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/documentation', function () {
    return Inertia::render('Documentation');
})->name('documentation');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on user role using the new role system
    if ($user->hasRole('student') || $user->getRoleNameAttribute() === 'student') {
        return Inertia::render('Dashboard', ['dashboardComponent' => 'StudentDashboard']);
    }
    
    // Default to instructor dashboard for admin and instructor roles
    return Inertia::render('Dashboard', ['dashboardComponent' => 'InstructorDashboard']);
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard and other API routes (must be before student-dashboard route to avoid conflicts)
Route::middleware(['auth', 'verified'])->prefix('api')->group(function () {
    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\DashboardApiController::class, 'getStats']);
        Route::get('/student-data', [App\Http\Controllers\Api\DashboardApiController::class, 'getStudentData']);
        Route::get('/instructor-data', [App\Http\Controllers\Api\DashboardApiController::class, 'getInstructorData']);
        // Admin dashboard routes
        Route::get('/admin-stats', [App\Http\Controllers\Api\DashboardApiController::class, 'getAdminStats']);
        Route::get('/admin-courses', [App\Http\Controllers\Api\DashboardApiController::class, 'getAdminCourses']);
        Route::get('/admin-activities', [App\Http\Controllers\Api\DashboardApiController::class, 'getAdminActivities']);
    });
    
    // Instructor API routes
    Route::prefix('instructor')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Api\DashboardApiController::class, 'getInstructorProfile']);
    });
    
    // Course API routes
    Route::get('/courses', [App\Http\Controllers\CourseController::class, 'getCourses']);
    
    // Grade Level API routes
    Route::get('/grade-levels', [App\Http\Controllers\GradeLevelController::class, 'index']);
    
    // Schedule API routes
    Route::prefix('schedule')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\ScheduleApiController::class, 'index']);
        Route::get('/upcoming', [App\Http\Controllers\Api\ScheduleApiController::class, 'upcoming']);
    });
    
    // Debug authentication status
    Route::get('/debug/auth', function (Illuminate\Http\Request $request) {
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user' => auth()->user()?->only(['id', 'name', 'email', 'role_name']),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
    });
});

// Student-specific dashboard route (for direct access)
Route::get('student-dashboard', function () {
    return Inertia::render('Dashboard', ['dashboardComponent' => 'StudentDashboard']);
})->middleware(['auth', 'verified', 'role:student'])->name('student.dashboard');

// Schedule page (authenticated users)
Route::get('schedule', function () {
    $user = auth()->user();
    
    // Fetch initial schedule data
    $schedules = \App\Models\Schedule::with([
        'scheduleType',
        'creator:id,name,email',
        'participants.user:id,name,email',
        'schedulable',
        'courseDetails', // Load course-specific schedule data from schedule_courses table
        'activityDetails', // Load activity-specific schedule data
        'adhocDetails', // Load adhoc-specific schedule data
    ])
    ->forUser($user->id)
    ->upcoming()
    ->withTrashed() // Include cancelled schedules
    ->get()
    ->map(function ($schedule) use ($user) {
        // Find current user's role in this schedule
        $userParticipant = $schedule->participants->firstWhere('user_id', $user->id);
        
        return [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'description' => $schedule->description,
            'location' => $schedule->location,
            'from_datetime' => $schedule->from_datetime,
            'to_datetime' => $schedule->to_datetime,
            'status' => $schedule->status,
            'type' => [
                'id' => $schedule->scheduleType->id,
                'name' => $schedule->scheduleType->name,
                'color' => $schedule->scheduleType->color,
                'description' => $schedule->scheduleType->description,
            ],
            'participants' => $schedule->participants->map(function ($participant) {
                return [
                    'id' => $participant->user->id,
                    'name' => $participant->user->name,
                    'role' => $participant->role_in_schedule,
                    'status' => $participant->participation_status,
                ];
            }),
            'duration_minutes' => $schedule->duration_minutes,
            'created_by' => $schedule->created_by,
            'schedulable_type' => $schedule->schedulable_type,
            'schedulable_id' => $schedule->schedulable_id,
            'deleted_at' => $schedule->deleted_at,
            'is_recurring' => $schedule->is_recurring,
            'recurrence_rule' => $schedule->recurrence_rule,
            // Include course-specific data if available
            'course_details' => $schedule->courseDetails ? [
                'session_number' => $schedule->courseDetails->session_number,
                'topics_covered' => $schedule->courseDetails->topics_covered,
                'required_materials' => $schedule->courseDetails->required_materials,
                'homework_assigned' => $schedule->courseDetails->homework_assigned,
            ] : null,
            // Include activity-specific data if available
            'activity_details' => $schedule->activityDetails ? [
                'submission_deadline' => $schedule->activityDetails->submission_deadline,
                'points' => $schedule->activityDetails->points,
                'instructions' => $schedule->activityDetails->instructions,
            ] : null,
        ];
    });
    
    return Inertia::render('SchedulingManagement/UserSchedule', [
        'initialSchedules' => $schedules,
    ]);
})->middleware(['auth', 'verified'])->name('schedule.index');

// Temporary route to fix existing schedules (remove after running once)
Route::get('/fix-schedules', function () {
    $fixed = 0;
    $schedules = \App\Models\Schedule::with('schedulable')->get();
    
    foreach ($schedules as $schedule) {
        if ($schedule->schedulable_type === 'App\\Models\\Course' && $schedule->schedulable) {
            $course = $schedule->schedulable;
            $participants = [];
            
            // Add instructor
            if ($course->instructor && $course->instructor->user) {
                $participants[] = [
                    'schedule_id' => $schedule->id,
                    'user_id' => $course->instructor->user->id,
                    'role_in_schedule' => 'instructor',
                    'participation_status' => 'accepted',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Add enrolled students using course_student pivot table
            // Query directly from course_student table instead of using the relationship
            $enrolledStudents = \DB::table('course_student')
                ->join('students', 'course_student.student_id', '=', 'students.id')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->where('course_student.course_id', $course->id)
                ->where('course_student.status', 'enrolled')
                ->select('users.id as user_id', 'users.name')
                ->get();
            
            foreach ($enrolledStudents as $student) {
                $participants[] = [
                    'schedule_id' => $schedule->id,
                    'user_id' => $student->user_id,
                    'role_in_schedule' => 'student',
                    'participation_status' => 'invited',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($participants)) {
                \App\Models\ScheduleParticipant::where('schedule_id', $schedule->id)->delete();
                \App\Models\ScheduleParticipant::insert($participants);
                $fixed++;
            }
        }
    }
    
    return "Fixed $fixed schedules with participants. Added " . count($participants ?? []) . " participants to last schedule.";
})->middleware(['auth', 'role:admin']);

// Student Details Page (accessible by instructors and admins)
Route::middleware(['auth', 'role:instructor,admin'])->group(function () {
    Route::get('/student/{id}/details', function ($id) {
        $student = \App\Models\User::with(['role', 'student.gradeLevel'])->findOrFail($id);
        
        if (!$student->role || $student->role->name !== 'student') {
            abort(404, 'User is not a student');
        }

        $enrolledCourses = $student->enrolledCourses()
            ->with('lessons')
            ->get()
            ->map(function ($course) use ($student) {
                $totalLessons = $course->lessons->count();
                
                // Count completed lessons for this student in this course
                $completedLessons = \App\Models\LessonCompletion::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->count();
                
                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
                
                $lastActivity = \App\Models\LessonCompletion::where('user_id', $student->id)
                    ->where('course_id', $course->id)
                    ->latest()
                    ->first();

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'progress' => $progress,
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'last_activity' => $lastActivity ? $lastActivity->created_at->diffForHumans() : 'Never',
                ];
            });

        return Inertia::render('Student/StudentDetails', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'role_name' => $student->role_name,
                'role_display_name' => $student->role_display_name,
                'grade_level' => $student->student?->gradeLevel?->display_name ?? null,
                'section' => $student->student?->section ?? null,
            ],
            'enrolledCourses' => $enrolledCourses,
        ]);
    })->name('student.details');
});

// Role management page (admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Role Management UI
    Route::get('/role-management', function () {
        return Inertia::render('RoleManagement');
    })->name('role.management');

    // API Routes for User Management
    Route::prefix('api')->group(function () {
        Route::get('/roles', function () {
            return response()->json(\App\Models\Role::all());
        });
        
        Route::apiResource('users', \App\Http\Controllers\UserController::class);
        Route::get('/users/{id}/student-details', [\App\Http\Controllers\UserController::class, 'studentDetails']);
        Route::post('/users/bulk-upload', [\App\Http\Controllers\UserController::class, 'uploadCSV'])->name('users.bulk-upload');
    });

    // Instructor Details Page
    Route::get('/instructor/{id}', function ($id) {
        $user = \App\Models\User::with(['instructor', 'role'])->findOrFail($id);
        
        if ($user->role_id !== 2) {
            abort(404, 'Instructor not found');
        }

        // Get instructor model ID if exists
        $instructorModelId = $user->instructor ? $user->instructor->id : null;

        // Courses where instructor_id matches the instructor model ID
        $coursesAsInstructor = \App\Models\Course::where('instructor_id', $instructorModelId)
            ->withCount('students')
            ->get();

        // Courses where created_by matches the user ID
        $coursesCreated = \App\Models\Course::where('created_by', $user->id)
            ->withCount('students')
            ->get();

        // Calculate statistics
        $totalCoursesAsInstructor = $coursesAsInstructor->count();
        $totalStudentsEnrolled = $coursesAsInstructor->sum('students_count');
        $totalCoursesCreated = $coursesCreated->count();

        return Inertia::render('Instructor/InstructorDetails', [
            'instructor' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'employee_id' => $user->instructor->employee_id ?? null,
                'title' => $user->instructor->title ?? null,
                'department' => $user->instructor->department ?? null,
                'specialization' => $user->instructor->specialization ?? null,
                'bio' => $user->instructor->bio ?? null,
                'office_location' => $user->instructor->office_location ?? null,
                'phone' => $user->instructor->phone ?? null,
                'office_hours' => $user->instructor->office_hours ?? null,
                'hire_date' => $user->instructor->hire_date ?? null,
                'employment_type' => $user->instructor->employment_type ?? null,
                'status' => $user->instructor->status ?? null,
                'salary' => $user->instructor->salary ?? null,
                'education_level' => $user->instructor->education_level ?? null,
                'certifications' => $user->instructor->certifications ?? null,
                'years_experience' => $user->instructor->years_experience ?? null,
                'instructor_model_id' => $instructorModelId,
            ],
            'courses' => $coursesAsInstructor,
            'stats' => [
                'total_courses_as_instructor' => $totalCoursesAsInstructor,
                'total_students_enrolled' => $totalStudentsEnrolled,
                'total_courses_created' => $totalCoursesCreated,
            ],
        ]);
    })->name('instructor.details');

    // Update instructor details API
    Route::put('/api/instructor/{id}', [UserController::class, 'updateInstructor'])->name('instructor.update');
});

Route::get('role-management', function () {
    return Inertia::render('RoleManagement');
})->middleware(['auth', 'verified', 'role:admin'])->name('role.management');

// List all students (for testing)
Route::get('/list-students', function () {
    $students = App\Models\User::with('role')
        ->whereHas('role', function($query) {
            $query->where('name', 'student');
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role_display_name,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });
    
    return response()->json([
        'message' => 'All students in the system',
        'total_count' => $students->count(),
        'students' => $students,
        'note' => 'All students have password: 123456789'
    ]);
});

// Test route to check API functionality
Route::get('/test-api', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'user' => auth()->user() ? auth()->user()->only(['id', 'name', 'email']) : null
    ]);
})->middleware(['auth']);

// Test route to check users in database
Route::get('/test-users', function () {
    $students = App\Models\User::with('role')->where('role_id', 3)->limit(5)->get();
    $otherUsers = App\Models\User::with('role')->whereIn('role_id', [1, 2])->limit(3)->get();
    $allUsers = $students->concat($otherUsers);
    
    $users = $allUsers->map(function ($user) {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_name' => $user->role_name,
            'role_display_name' => $user->role_display_name,
            'role_object' => $user->role ? [
                'name' => $user->role->name,
                'display_name' => $user->role->display_name
            ] : null
        ];
    });
    
    $studentCount = App\Models\User::where('role_id', 3)->count();
    
    return response()->json([
        'message' => 'Role system working correctly',
        'total_students' => $studentCount,
        'sample_users' => $users
    ]);
});





// Handle Boost browser logs - both GET and POST
Route::match(['get', 'post'], '_boost/browser-logs', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Boost browser logs endpoint',
        'method' => request()->method()
    ], 200);
});

 
// ACTIVITY MANAGEMENT ROUTES (Instructor and Admin)
Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:instructor,admin'])->group(function () {
        // Activity Management Dashboard
        Route::get('/activity-management', [ActivityController::class, 'index'])->name('activities.index');
        
        // Activity CRUD
        Route::resource('activities', ActivityController::class)->except(['index']);
        
        // Quiz Management
        Route::resource('quizzes', QuizController::class);
        Route::post('quizzes/bulk-upload', [QuizController::class, 'bulkUpload'])->name('quizzes.bulk-upload');
        Route::get('quizzes/csv-example', [QuizController::class, 'getCsvExample'])->name('quizzes.csv-example');
        Route::get('quizzes/csv-template', [QuizController::class, 'downloadCsvTemplate'])->name('quizzes.csv-template');
        
        // Question Management
        Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        
        // Assignment Management
        Route::resource('assignments', AssignmentController::class);
        
        // Course Management (Instructor and Admin)
        Route::get('/course-management', [CourseController::class, 'index'])->name('course.index');
        Route::post('/courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
        Route::resource('courses', CourseController::class)->except(['create', 'show', 'edit']);
        
        // Course Grade Level Management
        Route::post('/courses/{course}/assign-grade-levels', [CourseController::class, 'assignGradeLevels'])->name('courses.assign-grade-levels');
        Route::get('/grade-levels', [CourseController::class, 'getAvailableGradeLevels'])->name('grade-levels.index');
        Route::get('/courses/by-grade-level', [CourseController::class, 'getCoursesForGradeLevel'])->name('courses.by-grade-level');
        
        // Student Management Routes (for instructors)
        Route::prefix('student-management')->name('student-management.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Instructor\StudentManagementController::class, 'index'])->name('index');
            Route::get('/statistics', [\App\Http\Controllers\Instructor\StudentManagementController::class, 'getStatistics'])->name('statistics');
            Route::get('/course/{course}/students', [\App\Http\Controllers\Instructor\StudentManagementController::class, 'getStudentsByCourse'])->name('course.students');
            Route::get('/course/{course}/student/{student}/activities', [\App\Http\Controllers\Instructor\StudentManagementController::class, 'getStudentActivities'])->name('student.activities');
            Route::get('/course/{course}/export', [\App\Http\Controllers\Instructor\StudentManagementController::class, 'exportReport'])->name('export');
        });
    });
});

// Course Student Management Routes
Route::middleware(['auth'])->prefix('courses')->name('courses.')->group(function () {
    Route::get('/{course}/manage-students', [\App\Http\Controllers\CourseStudentController::class, 'index'])->name('manage-students');
    Route::post('/{course}/enroll-students', [\App\Http\Controllers\CourseStudentController::class, 'enrollStudents'])->name('enroll-students');
    Route::post('/{course}/remove-students', [\App\Http\Controllers\CourseStudentController::class, 'removeStudents'])->name('remove-students');
    Route::get('/{course}/eligible-students', [\App\Http\Controllers\CourseStudentController::class, 'getEligibleStudents'])->name('eligible-students');
    
    // New routes using StudentCourseEnrollmentService
    Route::get('/{course}/enrollment-statistics', [\App\Http\Controllers\CourseStudentController::class, 'getEnrollmentStatistics'])->name('enrollment-statistics');
    Route::get('/{course}/enrollments', [\App\Http\Controllers\CourseStudentController::class, 'getCourseEnrollments'])->name('enrollments');
    
    // Course Schedule Management
    Route::get('/{course}/schedules', [\App\Http\Controllers\CourseScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/{course}/schedules', [\App\Http\Controllers\CourseScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/{course}/schedules/{schedule}', [\App\Http\Controllers\CourseScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/{course}/schedules/{schedule}', [\App\Http\Controllers\CourseScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::put('/{course}/enrollments/{studentUser}/status', [\App\Http\Controllers\CourseStudentController::class, 'updateEnrollmentStatus'])->name('enrollments.update-status');
});
//LESSON ROUTES
Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
Route::put('/lessons/{lessonId}', [LessonController::class, 'update'])->name('lessons.update');

//MODULE ROUTES
Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
Route::get('/modules/{module}/lessons', [ModuleController::class, 'index']);
Route::post('/modules/{module}/activities', [ModuleController::class, 'addActivities'])->name('modules.activities.add');
Route::delete('/modules/{module}/activities/{activity}', [ModuleController::class, 'removeActivity'])->name('modules.activities.remove');
Route::post('/modules/{module}/documents', [ModuleController::class, 'uploadDocuments'])->name('modules.documents.upload');

// DOCUMENT ROUTES
// View route is public to allow Microsoft Office Online Viewer access
Route::get('/documents/{document}/view', [App\Http\Controllers\DocumentController::class, 'view'])->name('documents.view');
Route::get('/documents/{document}/download', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');

// Protected document routes
Route::middleware(['auth'])->group(function () {
    Route::post('/api/documents/upload', [App\Http\Controllers\DocumentController::class, 'upload'])->name('documents.upload');
    Route::delete('/documents/{document}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');
});

// STUDENT COURSE ROUTES
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/courses', [App\Http\Controllers\Student\StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [App\Http\Controllers\Student\StudentCourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/modules/{moduleId}', [App\Http\Controllers\Student\StudentCourseController::class, 'showModule'])->name('courses.modules.show');
    Route::post('/courses/{course}/lessons/{lessonId}/complete-test', function(Course $course, $lessonId) {
        \Log::info('Test route hit', ['courseId' => $course->id, 'lessonId' => $lessonId]);
        return redirect()->back()->with('success', 'Test route working!');
    })->name('courses.lessons.complete.test');
    Route::post('/courses/{course}/lessons/{lessonId}/complete', [App\Http\Controllers\Student\StudentCourseController::class, 'completeLesson'])->name('courses.lessons.complete');
    Route::post('/courses/{course}/modules/{moduleId}/complete', [App\Http\Controllers\Student\StudentCourseController::class, 'completeModule'])->name('courses.modules.complete');
    Route::post('/activities/{activity}/mark-complete', [StudentActivityController::class, 'markComplete'])->name('activities.mark-complete');
    Route::get('/courses/{course}/lessons', [App\Http\Controllers\Student\StudentCourseController::class, 'getLessons'])->name('courses.lessons');
    
    // Student Activities
    Route::get('/activities', [App\Http\Controllers\Student\StudentCourseController::class, 'activities'])->name('activities');
    
    // Unified Activity Results Route (works for all activity types: Quiz, Assignment, Assessment, Exercise)
    Route::get('/activities/{studentActivity}/results', [App\Http\Controllers\Student\StudentActivityResultsController::class, 'show'])->name('activities.results');
    
    // Activity Type Specific Results Routes: /student/{activityType}/{studentActivityId}/results
    Route::get('/quiz/{studentActivity}/results', [App\Http\Controllers\Student\StudentActivityResultsController::class, 'show'])->name('quiz.results');
    Route::get('/assignment/{studentActivity}/results', [App\Http\Controllers\Student\StudentActivityResultsController::class, 'show'])->name('assignment.results');
    Route::get('/project/{studentActivity}/results', [App\Http\Controllers\Student\StudentActivityResultsController::class, 'show'])->name('project.results');
    Route::get('/assessment/{studentActivity}/results', [App\Http\Controllers\Student\StudentActivityResultsController::class, 'show'])->name('assessment.results');
    
    Route::get('/assignment/{studentActivity}/results', function(\App\Models\StudentActivity $studentActivity) {
        return redirect()->route('student.activities.results', $studentActivity->id);
    })->name('assignment.results');
    
    // Unified Activity Routes - handles start/continue for all activity types
    Route::get('/quizs/{studentActivity}', [App\Http\Controllers\Student\StudentQuizController::class, 'show'])->name('quizs.show');
    Route::get('/assignments/{studentActivity}', [App\Http\Controllers\StudentAssignmentController::class, 'showByStudentActivity'])->name('assignments.show');
    Route::get('/projects/{studentActivity}', function(\App\Models\StudentActivity $studentActivity) {
        // Placeholder: Redirect to course module until ProjectController is implemented
        return redirect()->route('student.courses.modules.show', [
            'course' => $studentActivity->course_id,
            'moduleId' => $studentActivity->module_id
        ])->with('info', 'Project functionality coming soon.');
    })->name('projects.show');
    Route::get('/assessments/{studentActivity}', function(\App\Models\StudentActivity $studentActivity) {
        // Placeholder: Redirect to course module until AssessmentController is implemented
        return redirect()->route('student.courses.modules.show', [
            'course' => $studentActivity->course_id,
            'moduleId' => $studentActivity->module_id
        ])->with('info', 'Assessment functionality coming soon.');
    })->name('assessments.show');
    
    // Quiz routes (legacy - keeping for backward compatibility)
    Route::get('/quiz/start/{activity}', [App\Http\Controllers\Student\StudentQuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{progress}/answer', [App\Http\Controllers\Student\StudentQuizController::class, 'submitAnswer'])->name('quiz.answer');
    Route::post('/quiz/{progress}/submit', [App\Http\Controllers\Student\StudentQuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{activity}/progress', [App\Http\Controllers\Student\StudentQuizController::class, 'getProgress'])->name('quiz.progress');
    
    // Assignment routes (legacy - keeping for backward compatibility)
    Route::get('/assignment/start/{activity}', [App\Http\Controllers\StudentAssignmentController::class, 'start'])->name('assignment.start');
    Route::get('/assignments/{assignment}/old', [App\Http\Controllers\StudentAssignmentController::class, 'show'])->name('assignments.show.old');
    Route::post('/assignments/{assignment}/answers', [App\Http\Controllers\StudentAssignmentController::class, 'saveAnswer'])->name('assignments.save-answer');
    Route::post('/assignments/{assignment}/upload', [App\Http\Controllers\StudentAssignmentController::class, 'uploadFile'])->name('assignments.upload');
    Route::post('/assignments/{assignment}/submit', [App\Http\Controllers\StudentAssignmentController::class, 'submit'])->name('assignments.submit');
});

// Additional API routes moved to top of file with Dashboard routes

// GRADE REPORT ROUTES
Route::middleware(['auth'])->group(function () {
    // Student grade reports
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        // Main report page (shows all courses or specific course)
        Route::get('/report', [App\Http\Controllers\GradeController::class, 'studentReport'])->name('report');
        Route::get('/{student}/report/course/{course}', [App\Http\Controllers\GradeController::class, 'studentCourseReport'])->name('course.report');
        
        // Export routes - general
        Route::get('/report/pdf', [App\Http\Controllers\GradeController::class, 'studentReportPDF'])->name('report.pdf');
        Route::get('/report/pdf/complete', [App\Http\Controllers\GradeController::class, 'studentCompleteReportPDF'])->name('report.pdf.complete');
        Route::get('/report/csv', [App\Http\Controllers\GradeController::class, 'studentReportCSV'])->name('report.csv');
        Route::get('/report/csv/complete', [App\Http\Controllers\GradeController::class, 'studentCompleteReportCSV'])->name('report.csv.complete');
        
        // Export routes - specific course
        Route::get('/{student}/report/course/{course}/pdf', [App\Http\Controllers\GradeController::class, 'studentCourseReportPDF'])->name('course.report.pdf');
        Route::get('/{student}/report/course/{course}/csv', [App\Http\Controllers\GradeController::class, 'studentCourseReportCSV'])->name('course.report.csv');
    });

    // Instructor grade reports
    Route::middleware(['role:instructor,admin'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/report', [App\Http\Controllers\GradeController::class, 'instructorReport'])->name('report');
        Route::get('/report/pdf', [App\Http\Controllers\GradeController::class, 'instructorReportPDF'])->name('report.pdf');
        Route::get('/report/csv', [App\Http\Controllers\GradeController::class, 'instructorReportCSV'])->name('report.csv');
        Route::get('/student/{student}/detail', [App\Http\Controllers\GradeController::class, 'studentDetail'])->name('student.detail');
        
        // Instructor notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            // Test route - remove after debugging
            Route::get('/test', function() {
                $userId = auth()->id();
                $notifications = \App\Models\InstructorNotification::forInstructor($userId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                return response()->json([
                    'debug' => true,
                    'user_id' => $userId,
                    'count' => $notifications->count(),
                    'total' => $notifications->total(),
                    'data' => $notifications
                ]);
            })->name('test');
            
            Route::get('/unread-count', [App\Http\Controllers\Instructor\NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/', [App\Http\Controllers\Instructor\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\Instructor\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [App\Http\Controllers\Instructor\NotificationController::class, 'markAllAsRead'])->name('read-all');
            Route::delete('/{id}', [App\Http\Controllers\Instructor\NotificationController::class, 'destroy'])->name('destroy');
        });

        // Assignment submissions and grading
        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/{assignment}/submissions', [App\Http\Controllers\Instructor\AssignmentGradingController::class, 'submissions'])->name('submissions');
            Route::get('/{assignment}/submissions/{progress}', [App\Http\Controllers\Instructor\AssignmentGradingController::class, 'viewSubmission'])->name('submissions.view');
            Route::post('/{assignment}/grade/{progress}/question', [App\Http\Controllers\Instructor\AssignmentGradingController::class, 'gradeQuestion'])->name('grade.question');
            Route::post('/{assignment}/grade/{progress}/submit', [App\Http\Controllers\Instructor\AssignmentGradingController::class, 'submitGrade'])->name('grade.submit');
        });

        // Student submission routes for viewing individual submissions
        Route::get('/submissions/{submission}', [App\Http\Controllers\Instructor\StudentSubmissionController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{submission}/grade', [App\Http\Controllers\Instructor\StudentSubmissionController::class, 'grade'])->name('submissions.grade');
    });
});

// GRADE SETTINGS ROUTES
Route::middleware(['auth', 'role:instructor,admin'])->group(function () {
    Route::get('/grade-settings', [App\Http\Controllers\GradeSettingsController::class, 'index'])->name('grade-settings.index');
    Route::post('/grade-settings/module-components', [App\Http\Controllers\GradeSettingsController::class, 'updateModuleComponents'])->name('grade-settings.module-components');
    Route::post('/grade-settings/activity-types', [App\Http\Controllers\GradeSettingsController::class, 'updateActivityTypes'])->name('grade-settings.activity-types');
    Route::post('/grade-settings/reset', [App\Http\Controllers\GradeSettingsController::class, 'reset'])->name('grade-settings.reset');
    Route::delete('/grade-settings/course', [App\Http\Controllers\GradeSettingsController::class, 'deleteCourseSettings'])->name('grade-settings.delete-course');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
