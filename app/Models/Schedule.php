<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'schedule_type_id',
        'title',
        'description',
        'location',
        'from_datetime',
        'to_datetime',
        'is_all_day',
        'is_recurring',
        'recurrence_rule',
        'status',
        'created_by',
        'schedulable_type',
        'schedulable_id',
        'metadata',
    ];

    protected $casts = [
        'from_datetime' => 'datetime',
        'to_datetime' => 'datetime',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
        'metadata' => 'array',
    ];

    protected $appends = [
        'duration_minutes',
    ];

    /**
     * Get the schedule type
     */
    public function scheduleType(): BelongsTo
    {
        return $this->belongsTo(ScheduleType::class);
    }

    /**
     * Get the user who created this schedule
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all participants for this schedule
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ScheduleParticipant::class);
    }

    /**
     * Get users participating in this schedule
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'schedule_participants')
            ->withPivot(['role_in_schedule', 'participation_status', 'response_datetime', 'attended_at', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get the polymorphic schedulable model (Activity, Course, etc.)
     */
    public function schedulable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get activity-specific details if this is an activity schedule
     */
    public function activityDetails()
    {
        return $this->hasOne(ScheduleActivity::class);
    }

    /**
     * Get course-specific details if this is a course schedule
     */
    public function courseDetails()
    {
        return $this->hasOne(ScheduleCourse::class);
    }

    /**
     * Get adhoc details if this is an adhoc schedule
     */
    public function adhocDetails()
    {
        return $this->hasOne(ScheduleAdhoc::class);
    }

    /**
     * Scope: Get upcoming schedules
     */
    public function scopeUpcoming($query)
    {
        return $query->where('from_datetime', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('from_datetime', 'asc');
    }

    /**
     * Scope: Get schedules in a date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('from_datetime', [$startDate, $endDate])
                ->orWhereBetween('to_datetime', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('from_datetime', '<=', $startDate)
                        ->where('to_datetime', '>=', $endDate);
                });
        });
    }

    /**
     * Scope: Get schedules for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Scope: Get schedules by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if schedule conflicts with another schedule for a user
     */
    public function conflictsWith($userId, $scheduleIdToExclude = null)
    {
        $query = self::forUser($userId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    // New schedule starts during an existing schedule
                    $q2->where('from_datetime', '<=', $this->from_datetime)
                        ->where('to_datetime', '>', $this->from_datetime);
                })->orWhere(function ($q2) {
                    // New schedule ends during an existing schedule
                    $q2->where('from_datetime', '<', $this->to_datetime)
                        ->where('to_datetime', '>=', $this->to_datetime);
                })->orWhere(function ($q2) {
                    // New schedule completely contains an existing schedule
                    $q2->where('from_datetime', '>=', $this->from_datetime)
                        ->where('to_datetime', '<=', $this->to_datetime);
                });
            });

        if ($scheduleIdToExclude) {
            $query->where('id', '!=', $scheduleIdToExclude);
        }

        return $query->exists();
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutesAttribute()
    {
        if ($this->from_datetime && $this->to_datetime) {
            return $this->from_datetime->diffInMinutes($this->to_datetime);
        }
        return 0;
    }

    /**
     * Check if schedule is in progress
     */
    public function isInProgress()
    {
        return now()->between($this->from_datetime, $this->to_datetime);
    }

    /**
     * Check if schedule has passed
     */
    public function hasPassed()
    {
        return now()->greaterThan($this->to_datetime);
    }

    /**
     * Mark schedule as completed
     */
    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Cancel schedule
     */
    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}
