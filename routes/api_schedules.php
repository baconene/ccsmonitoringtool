<?php

use App\Http\Controllers\Api\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Schedule API Routes
|--------------------------------------------------------------------------
|
| These routes handle all schedule-related API endpoints for the calendar system
|
*/

Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    
    // Get upcoming schedules for a user
    Route::get('/users/{userId}/schedules/upcoming', [ScheduleController::class, 'getUserUpcomingSchedules'])
        ->name('api.users.schedules.upcoming');
    
    // Get schedules in a date range (for calendar view)
    Route::get('/schedules/range', [ScheduleController::class, 'getSchedulesInRange'])
        ->name('api.schedules.range');
    
    // Check for schedule conflicts
    Route::post('/schedules/check-conflicts', [ScheduleController::class, 'checkConflicts'])
        ->name('api.schedules.check-conflicts');
    
    // RESTful schedule routes
    Route::apiResource('schedules', ScheduleController::class);
    
    // Update participant status (accept/decline, mark attendance)
    Route::patch('/schedules/{scheduleId}/participants/{userId}/status', [ScheduleController::class, 'updateParticipantStatus'])
        ->name('api.schedules.participants.status');
});
