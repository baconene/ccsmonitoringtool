<?php

use App\Http\Controllers\Api\ActivityTypeController;
use App\Http\Controllers\Api\CourseApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\ScheduleApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes (no authentication required)
Route::get('/activity-types', [ActivityTypeController::class, 'index']);
Route::get('/grade-levels', [App\Http\Controllers\GradeLevelController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    
    // Debug authentication status
    Route::get('/debug/auth', function (Request $request) {
        return [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'user' => auth()->user()?->only(['id', 'name', 'email']),
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
        ];
    });

    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardApiController::class, 'getStats']);
    });

    // Instructor API routes
    Route::prefix('instructor')->group(function () {
        Route::get('/profile', [DashboardApiController::class, 'getInstructorProfile']);
    });

    // Course API routes
    Route::get('/courses', [App\Http\Controllers\CourseController::class, 'getCourses']);
    Route::apiResource('courses', CourseApiController::class)->except(['index'])->names([
        'show' => 'api.courses.show',
        'store' => 'api.courses.store',
        'update' => 'api.courses.update',
        'destroy' => 'api.courses.destroy'
    ]);
    Route::get('/courses/{course}/students', [CourseApiController::class, 'getStudents']);

    // Student API routes
    Route::apiResource('students', StudentApiController::class)->only(['index', 'show']);

    // Schedule API routes
    Route::prefix('schedule')->group(function () {
        Route::get('/', [ScheduleApiController::class, 'index']);
        Route::get('/upcoming', [ScheduleApiController::class, 'upcoming']);
    });

    // Student module status API routes
    Route::prefix('student/module')->group(function () {
        Route::get('/status/{module_id}', [App\Http\Controllers\Api\Student\ModuleStatusController::class, 'getModuleStatus']);
        Route::post('/complete/{module_id}', [App\Http\Controllers\Api\Student\ModuleStatusController::class, 'markModuleComplete']);
    });
});

// Public routes (if any)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'Learning Management System API'
    ]);
});

// Test authentication route
Route::middleware(['auth:sanctum'])->get('/test-auth', function (Request $request) {
    return response()->json([
        'authenticated' => true,
        'user' => $request->user(),
        'message' => 'Authentication successful'
    ]);
});