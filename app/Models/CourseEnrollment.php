<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * Update progress based on completed modules
     */
    public function updateProgress(): void
    {
        $modules = $this->course->modules()->get();
        
        if ($modules->isEmpty()) {
            $this->progress = 0.0;
            $this->save();
            return;
        }

        // Get completed modules
        $completedModules = ModuleCompletion::where('user_id', $this->user_id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->where('is_completed', true)
            ->pluck('module_id')
            ->toArray();

        // Check if modules have percentages set
        $totalWeight = $modules->sum('module_percentage');
        
        if ($totalWeight > 0) {
            // Use weighted calculation if percentages are set
            $completedWeight = $modules->whereIn('id', $completedModules)->sum('module_percentage');
            $progress = ($completedWeight / $totalWeight) * 100;
        } else {
            // Use equal weight for all modules if no percentages set
            $totalModules = $modules->count();
            $completedCount = count($completedModules);
            $progress = $totalModules > 0 ? ($completedCount / $totalModules) * 100 : 0;
        }
        
        $this->progress = (float) $progress;
        $this->save();
    }
}
