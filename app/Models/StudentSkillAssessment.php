<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSkillAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'skill_id',
        'normalized_score',
        'feedback_score',
        'peer_review_score',
        'attempt_count',
        'improvement_factor',
        'days_late',
        'final_score',
        'mastery_level',
        'consistency_score',
        'assessment_metadata',
    ];

    protected $casts = [
        'normalized_score' => 'decimal:2',
        'feedback_score' => 'decimal:2',
        'peer_review_score' => 'decimal:2',
        'improvement_factor' => 'decimal:2',
        'final_score' => 'decimal:2',
        'consistency_score' => 'decimal:2',
        'assessment_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the skill
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Check if skill is mastered
     */
    public function isMastered(): bool
    {
        return $this->mastery_level === 'met' || $this->mastery_level === 'exceeds';
    }

    /**
     * Check if skill exceeds expectations
     */
    public function isExceeding(): bool
    {
        return $this->mastery_level === 'exceeds';
    }

    /**
     * Get assessment status for display
     */
    public function getStatusAttribute(): string
    {
        return match ($this->mastery_level) {
            'not_met' => 'Not Met',
            'met' => 'Met',
            'exceeds' => 'Exceeds Expectations',
            default => 'Unknown',
        };
    }

    /**
     * Get skill progress percentage (0-100)
     */
    public function getProgressPercentageAttribute(): float
    {
        return min(100, max(0, $this->final_score));
    }
}
