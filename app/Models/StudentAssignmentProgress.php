<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class StudentAssignmentProgress extends Model
{
    protected $fillable = [
        'student_activity_id',
        'submission_content',
        'attachment_files',
        'submission_status',
        'revision_count',
        'due_date',
        'submission_date',
        'grading_date',
        'instructor_comments',
        'points_earned',
        'points_possible',
        'rubric_scores',
    ];

    protected $casts = [
        'attachment_files' => 'array',
        'due_date' => 'datetime',
        'submission_date' => 'datetime',
        'grading_date' => 'datetime',
        'points_earned' => 'decimal:2',
        'points_possible' => 'decimal:2',
        'rubric_scores' => 'array',
    ];

    // Relationships
    public function studentActivity(): BelongsTo
    {
        return $this->belongsTo(StudentActivity::class);
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date < Carbon::now() && 
               !$this->isSubmitted();
    }

    public function isSubmitted(): bool
    {
        return in_array($this->submission_status, ['submitted', 'approved']);
    }

    public function isDraft(): bool
    {
        return $this->submission_status === 'draft';
    }

    public function submitAssignment(): void
    {
        $this->update([
            'submission_status' => 'submitted',
            'submission_date' => Carbon::now(),
        ]);

        // Update parent activity
        $this->studentActivity->update([
            'status' => 'submitted',
            'submitted_at' => Carbon::now(),
        ]);
    }

    public function gradeAssignment(float $points, string $comments = null): void
    {
        $percentage = $this->points_possible > 0 ? 
                     ($points / $this->points_possible) * 100 : 0;

        $this->update([
            'points_earned' => $points,
            'instructor_comments' => $comments,
            'grading_date' => Carbon::now(),
        ]);

        // Update parent activity
        $this->studentActivity->update([
            'status' => 'graded',
            'score' => $points,
            'max_score' => $this->points_possible,
            'percentage_score' => $percentage,
            'graded_at' => Carbon::now(),
            'feedback' => $comments,
        ]);
    }

    public function getGradePercentage(): float
    {
        if (!$this->points_possible || $this->points_possible <= 0) {
            return 0;
        }

        return ($this->points_earned / $this->points_possible) * 100;
    }

    public function getDaysUntilDue(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        return Carbon::now()->diffInDays($this->due_date, false);
    }
}
