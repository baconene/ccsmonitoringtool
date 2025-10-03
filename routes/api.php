<?php

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

Route::middleware(['auth:sanctum'])->group(function () {
    // User info route
    Route::get('/user', function (Request $request) {
        return $request->user();
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