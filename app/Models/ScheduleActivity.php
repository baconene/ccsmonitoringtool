<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'activity_id',
        'submission_deadline',
        'passing_score',
    ];

    protected $casts = [
        'submission_deadline' => 'datetime',
        'passing_score' => 'decimal:2',
    ];

    /**
     * Get the schedule
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the activity
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
