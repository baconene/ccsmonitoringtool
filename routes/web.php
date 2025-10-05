<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

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
        $student = \App\Models\User::with('role')->findOrFail($id);
        
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
                'grade_level' => $student->grade_level ?? null,
                'section' => $student->section ?? null,
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
    });
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

 
//COURSE ROUTES
Route::get('/course-management', [CourseController::class, 'index'])->name('course.index');
Route::post('/courses/{course}/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::resource('courses', CourseController::class)->except(['create', 'show', 'edit']);
//LESSON ROUTES
Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
Route::put('/lessons/{lessonId}', [LessonController::class, 'update'])->name('lessons.update');

//MODULE ROUTES
Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
Route::get('/modules/{module}/lessons', [ModuleController::class, 'index']);

// STUDENT COURSE ROUTES
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/courses', [App\Http\Controllers\Student\StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [App\Http\Controllers\Student\StudentCourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/lessons/{lessonId}/complete', [App\Http\Controllers\Student\StudentCourseController::class, 'completeLesson'])->name('courses.lessons.complete');
    Route::get('/courses/{course}/lessons', [App\Http\Controllers\Student\StudentCourseController::class, 'getLessons'])->name('courses.lessons');
});

// API ROUTES FOR WEB (Session-based authentication)
Route::middleware(['auth'])->prefix('api')->group(function () {
    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\DashboardApiController::class, 'getStats']);
    });

    // Instructor API routes
    Route::prefix('instructor')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Api\DashboardApiController::class, 'getInstructorProfile']);
    });

    // Course API routes
    Route::get('/courses', [App\Http\Controllers\CourseController::class, 'getCourses']);
    
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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
