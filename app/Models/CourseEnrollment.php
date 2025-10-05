<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
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
     * Get the user for this enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Calculate and update progress based on completed module weights.
     */
    public function updateProgress(): void
    {
        // Get all modules for this course with their weights
        $modules = $this->course->modules()->get();
        
        if ($modules->isEmpty()) {
            return;
        }
        
        // Calculate total weight of all modules
        $totalWeight = $modules->sum('module_percentage') ?: 100;
        
        // Get completed modules for this user
        $completedModules = ModuleCompletion::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->pluck('module_id')
            ->toArray();
        
        // Calculate sum of completed module weights
        $completedWeight = $modules->whereIn('id', $completedModules)
            ->sum('module_percentage');
        
        // Calculate progress percentage
        $progress = $totalWeight > 0 ? ($completedWeight / $totalWeight) * 100 : 0;
        
        $this->update([
            'progress' => round($progress, 2),
            'is_completed' => $progress >= 100,
            'completed_at' => $progress >= 100 ? now() : null,
        ]);
    }
}
