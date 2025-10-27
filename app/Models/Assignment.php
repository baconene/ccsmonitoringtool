<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'created_by',
        'title',
        'description',
        'document_id',
        'assignment_type',
        'total_points',
        'time_limit',
        'allow_late_submission',
        'instructions',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'time_limit' => 'integer',
        'allow_late_submission' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the activity
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the creator (user)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the document
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the questions for this assignment
     */
    public function questions(): HasMany
    {
        return $this->hasMany(AssignmentQuestion::class)->orderBy('order');
    }

    /**
     * Get student answers for this assignment
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssignmentAnswer::class);
    }

    /**
     * Get student progress records for this assignment
     */
    public function studentProgress(): HasMany
    {
        return $this->hasMany(StudentAssignmentProgress::class, 'student_activity_id');
    }

    /**
     * Check if assignment has objective questions
     */
    public function hasObjectiveQuestions(): bool
    {
        return in_array($this->assignment_type, ['objective', 'mixed']);
    }

    /**
     * Check if assignment accepts file uploads
     */
    public function acceptsFileUploads(): bool
    {
        return in_array($this->assignment_type, ['file_upload', 'mixed']);
    }

    /**
     * Calculate total possible points
     */
    public function calculateTotalPoints(): int
    {
        if ($this->hasObjectiveQuestions()) {
            return $this->questions()->sum('points');
        }
        return $this->total_points;
    }

    /**
     * Get assignment type display name
     */
    public function getAssignmentTypeDisplayAttribute(): string
    {
        return match($this->assignment_type) {
            'objective' => 'Objective (Questions Only)',
            'file_upload' => 'File Upload (Research Paper)',
            'mixed' => 'Mixed (Questions + File Upload)',
            default => $this->assignment_type,
        };
    }
}
