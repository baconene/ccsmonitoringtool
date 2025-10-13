<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    protected $fillable = [
        'user_id', // Keep for backward compatibility
        'student_id', // New: References students table
        'lesson_id',
        'course_id',
        'completed_at',
        'completion_data',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'completion_data' => 'json',
    ];

    /**
     * Get the user for this completion (backward compatibility).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student for this completion.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the lesson for this completion.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the course for this completion.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
