<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'course_id',
        'session_number',
        'topics_covered',
        'required_materials',
    ];

    protected $casts = [
        'session_number' => 'integer',
    ];

    /**
     * Get the schedule
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
