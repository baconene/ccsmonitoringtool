<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ModuleCompletion;
use App\Models\StudentActivity;
use App\Models\LessonCompletion;

class CourseEnrollment extends Model
{

    protected $fillable = [
        'user_id', // Keep for backward compatibility during transition
        'student_id', // New: References students table
        'course_id',
        'instructor_id', // New: Track who enrolled the student
        'enrolled_at',
        'progress',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'decimal:2',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user for this enrollment (backward compatibility).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student for this enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor who enrolled the student.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the course for this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Update progress based on completed activities
     */
    public function updateProgress(): void
    {
        $course = $this->course;
        $modules = $course->modules()->with('activities')->get();
        
        if ($modules->isEmpty()) {
            $this->progress = 0.0;
            $this->save();
            return;
        }

        // Calculate progress based on completed activities
        $totalActivities = 0;
        $completedActivities = 0;
        
        foreach ($modules as $module) {
            $moduleActivities = $module->activities;
            $totalActivities += $moduleActivities->count();
            
            // Count completed activities for this student
            $completedActivities += StudentActivity::where('course_id', $course->id)
                ->where('student_id', $this->student_id)
                ->where('status', 'completed')
                ->whereIn('activity_id', $moduleActivities->pluck('id'))
                ->count();
        }
        
        // Calculate progress percentage
        $progress = $totalActivities > 0 
            ? round(($completedActivities / $totalActivities) * 100, 2) 
            : 0.0;
        
        $this->progress = (float) $progress;
        
        // Check if course should be marked as complete or incomplete
        // Course is complete when all modules are marked as complete
        $totalModules = $modules->count();
        $completedModules = ModuleCompletion::where('student_id', $this->student_id)
            ->where('course_id', $course->id)
            ->count();
        
        if ($totalModules > 0 && $completedModules >= $totalModules) {
            // All modules complete - mark course as complete
            if (!$this->is_completed) {
                $this->is_completed = true;
                $this->completed_at = now();
            }
        } else {
            // Not all modules complete - ensure course is not marked as complete
            if ($this->is_completed) {
                $this->is_completed = false;
                $this->completed_at = null;
            }
        }
        
        $this->save();
    }

    /**
     * Check and auto-complete modules when all their requirements are met
     * This ensures modules are marked complete automatically without manual button clicks
     */
    public function checkAndCompleteModules(): void
    {
        $course = $this->course;
        $modules = $course->modules()->with(['activities', 'lessons'])->get();
        
        foreach ($modules as $module) {
            // Skip if module is already completed (use student_id to match unique constraint)
            $existingCompletion = ModuleCompletion::where('student_id', $this->student_id)
                ->where('module_id', $module->id)
                ->where('course_id', $course->id)
                ->first();
            
            if ($existingCompletion) {
                continue; // Module already marked complete
            }
            
            // Check if all activities in this module are completed
            $moduleActivityIds = $module->activities->pluck('id');
            
            // Skip if no activities (prevents division by zero)
            if ($moduleActivityIds->isEmpty()) {
                $allActivitiesCompleted = true; // No activities = considered complete
            } else {
                $completedActivitiesCount = StudentActivity::where('course_id', $course->id)
                    ->where('student_id', $this->student_id)
                    ->where('status', 'completed')
                    ->whereIn('activity_id', $moduleActivityIds)
                    ->count();
                
                $allActivitiesCompleted = $completedActivitiesCount === $moduleActivityIds->count();
            }
            
            // Check if all lessons in this module are completed
            $moduleLessonIds = $module->lessons->pluck('id');
            
            // Skip if no lessons
            if ($moduleLessonIds->isEmpty()) {
                $allLessonsCompleted = true; // No lessons = considered complete
            } else {
                $completedLessonsCount = LessonCompletion::where('user_id', $this->student_id)
                    ->where('course_id', $course->id)
                    ->whereIn('lesson_id', $moduleLessonIds)
                    ->count();
                
                $allLessonsCompleted = $completedLessonsCount === $moduleLessonIds->count();
            }
            
            // If all requirements are met, auto-complete the module
            // Use student_id in WHERE clause to match unique constraint (student_id, module_id, course_id)
            if ($allActivitiesCompleted && $allLessonsCompleted) {
                ModuleCompletion::updateOrCreate(
                    [
                        'student_id' => $this->student_id,
                        'module_id' => $module->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'user_id' => $this->student_id, // Keep for backward compatibility
                        'completed_at' => now(),
                        'completion_data' => json_encode([
                            'method' => 'automatic',
                            'timestamp' => now()->toISOString(),
                        ]),
                    ]
                );
            }
        }
    }
}
