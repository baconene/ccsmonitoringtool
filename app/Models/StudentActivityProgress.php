<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class StudentActivityProgress extends Model
{
    protected $table = 'student_activity_progress';

    protected $fillable = [
        // Core fields
        'student_id',
        'activity_id',
        'activity_type',
        'student_activity_id',
        
        // Status fields
        'status',
        'started_at',
        'completed_at',
        'submitted_at',
        'graded_at',
        'last_accessed_at',
        
        // Scoring fields
        'score',
        'max_score',
        'percentage_score',
        'points_earned',
        'points_possible',
        
        // Progress tracking
        'progress_percentage',
        'answered_questions',
        'total_questions',
        'completed_questions',
        'current_phase',
        
        // Content fields
        'submission_content',
        'attachment_files',
        'final_submission',
        
        // Assessment fields
        'instructor_comments',
        'feedback',
        'rubric_scores',
        
        // Type-specific JSON fields
        'quiz_data',
        'assignment_data',
        'project_data',
        'assessment_data',
        
        // Additional metadata
        'due_date',
        'submission_date',
        'grading_date',
        'time_spent',
        'revision_count',
        'is_completed',
        'is_submitted',
        'requires_grading',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'due_date' => 'datetime',
        'submission_date' => 'datetime',
        'grading_date' => 'datetime',
        
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
        'points_earned' => 'decimal:2',
        'points_possible' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
        
        'answered_questions' => 'integer',
        'total_questions' => 'integer',
        'completed_questions' => 'integer',
        'current_phase' => 'integer',
        'revision_count' => 'integer',
        'time_spent' => 'integer',
        
        'attachment_files' => 'array',
        'rubric_scores' => 'array',
        'quiz_data' => 'array',
        'assignment_data' => 'array',
        'project_data' => 'array',
        'assessment_data' => 'array',
        
        'is_completed' => 'boolean',
        'is_submitted' => 'boolean',
        'requires_grading' => 'boolean',
    ];

    protected $appends = [
        'submission_status',
    ];

    // Accessors
    protected function submissionStatus(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                // Map status to submission_status for frontend compatibility
                if ($this->status === 'completed' || $this->graded_at !== null) {
                    return 'graded';
                } elseif ($this->status === 'submitted' || $this->is_submitted) {
                    return 'submitted';
                }
                return 'draft';
            }
        );
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function studentActivity(): BelongsTo
    {
        return $this->belongsTo(StudentActivity::class);
    }

    public function answers()
    {
        // Dynamic relationship based on activity_type
        // Returns quiz answers for quizzes, assignment answers for assignments, etc.
        switch (strtolower($this->activity_type)) {
            case 'quiz':
                return $this->hasMany(StudentQuizAnswer::class, 'activity_progress_id');
            case 'assignment':
                return $this->hasMany(StudentAssignmentAnswer::class, 'activity_progress_id');
            default:
                // Return empty collection for other types
                return $this->hasMany(StudentQuizAnswer::class, 'activity_progress_id')->whereRaw('1 = 0');
        }
    }

    // Helper methods for assignments
    public function submitAssignment(): void
    {
        $this->update([
            'status' => 'submitted',
            'is_submitted' => true,
            'submitted_at' => Carbon::now(),
            'submission_date' => Carbon::now(),
        ]);
    }

    public function gradeSubmission(float $points, string $comments = null): void
    {
        $percentage = $this->points_possible > 0 ? 
                     ($points / $this->points_possible) * 100 : 0;

        $this->update([
            'status' => 'graded',
            'points_earned' => $points,
            'score' => $points,
            'percentage_score' => $percentage,
            'instructor_comments' => $comments,
            'feedback' => $comments,
            'graded_at' => Carbon::now(),
            'grading_date' => Carbon::now(),
        ]);
    }

    // Helper methods for quizzes
    public function calculateQuizScore(): void
    {
        $quizData = $this->quiz_data ?? [];
        $totalPoints = $quizData['total_points'] ?? $this->max_score ?? 0;
        
        if ($totalPoints > 0 && $this->score !== null) {
            $this->percentage_score = ($this->score / $totalPoints) * 100;
            $this->save();
        }
    }

    // Helper methods for projects
    public function updateProjectProgress(int $phase = null, float $percentage = null): void
    {
        $data = ['last_accessed_at' => Carbon::now()];

        if ($phase !== null) {
            $data['current_phase'] = $phase;
        }

        if ($percentage !== null) {
            $data['progress_percentage'] = min(100, max(0, $percentage));
        }

        $this->update($data);

        if ($percentage >= 100) {
            $this->markAsCompleted();
        }
    }

    // Common helper methods
    public function markAsStarted(): void
    {
        if (!$this->started_at) {
            $this->update([
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
            ]);
        }
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'is_completed' => true,
            'completed_at' => Carbon::now(),
            'progress_percentage' => 100,
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date < Carbon::now() && 
               !$this->is_submitted;
    }

    public function getGradePercentage(): float
    {
        return $this->percentage_score ?? 0;
    }

    // Scopes
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }

    public function scopePending($query)
    {
        return $query->where('is_submitted', true)
                    ->whereNull('graded_at');
    }
}
