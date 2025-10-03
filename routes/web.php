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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Test route to check API functionality
Route::get('/test-api', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working',
        'user' => auth()->user() ? auth()->user()->only(['id', 'name', 'email']) : null
    ]);
})->middleware(['auth']);



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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
