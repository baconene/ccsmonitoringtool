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
     * Calculate and update progress based on completed lessons.
     */
    public function updateProgress(): void
    {
        $totalLessons = $this->course->lessons()->count();
        
        if ($totalLessons > 0) {
            $completedLessons = LessonCompletion::where('user_id', $this->user_id)
                ->where('course_id', $this->course_id)
                ->count();
                
            $progress = ($completedLessons / $totalLessons) * 100;
            
            $this->update([
                'progress' => $progress,
                'is_completed' => $progress >= 100,
                'completed_at' => $progress >= 100 ? now() : null,
            ]);
        }
    }
}
