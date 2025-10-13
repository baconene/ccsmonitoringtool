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

// Student-specific dashboard route (for direct access)
Route::get('student-dashboard', function () {
    return Inertia::render('Dashboard', ['dashboardComponent' => 'StudentDashboard']);
})->middleware(['auth', 'verified', 'role:student'])->name('student.dashboard');

// Role management page (admin only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Role Management UI
    Route::get('/role-management', function () {
        return Inertia::render('RoleManagement');
    })->name('role.management');

    // Student Details Page
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
    
    // Quiz routes
    Route::get('/quiz/start/{activity}', [App\Http\Controllers\Student\StudentQuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{progress}/answer', [App\Http\Controllers\Student\StudentQuizController::class, 'submitAnswer'])->name('quiz.answer');
    Route::post('/quiz/{progress}/submit', [App\Http\Controllers\Student\StudentQuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{progress}/results', [App\Http\Controllers\Student\StudentQuizController::class, 'results'])->name('quiz.results');
    Route::get('/quiz/{activity}/progress', [App\Http\Controllers\Student\StudentQuizController::class, 'getProgress'])->name('quiz.progress');
});

// API ROUTES FOR WEB (Session-based authentication)
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\DashboardApiController::class, 'getStats']);
        Route::get('/student-data', [App\Http\Controllers\Api\StudentDashboardController::class, 'getDashboardData'])->middleware('role:student');
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
            'user' => auth()->user()?->only(['id', 'name', 'email']),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ]);
    });
});

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
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
