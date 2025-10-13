<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'user_id',
        'role_in_schedule',
        'participation_status',
        'response_datetime',
        'attended_at',
        'notes',
    ];

    protected $casts = [
        'response_datetime' => 'datetime',
        'attended_at' => 'datetime',
    ];

    /**
     * Get the schedule this participant belongs to
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the user (participant)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark participant as attended
     */
    public function markAsAttended()
    {
        $this->update([
            'participation_status' => 'attended',
            'attended_at' => now(),
        ]);
    }

    /**
     * Mark participant as absent
     */
    public function markAsAbsent()
    {
        $this->update([
            'participation_status' => 'absent',
        ]);
    }

    /**
     * Accept invitation
     */
    public function accept()
    {
        $this->update([
            'participation_status' => 'accepted',
            'response_datetime' => now(),
        ]);
    }

    /**
     * Decline invitation
     */
    public function decline()
    {
        $this->update([
            'participation_status' => 'declined',
            'response_datetime' => now(),
        ]);
    }

    /**
     * Scope: Get participants by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role_in_schedule', $role);
    }

    /**
     * Scope: Get accepted participants
     */
    public function scopeAccepted($query)
    {
        return $query->where('participation_status', 'accepted');
    }

    /**
     * Scope: Get attended participants
     */
    public function scopeAttended($query)
    {
        return $query->where('participation_status', 'attended');
    }
}
