<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleAdhoc extends Model
{
    use HasFactory;

    protected $table = 'schedule_adhoc';

    protected $fillable = [
        'schedule_id',
        'event_type',
        'privacy_level',
        'reminder_minutes',
    ];

    protected $casts = [
        'reminder_minutes' => 'integer',
    ];

    /**
     * Get the schedule
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
