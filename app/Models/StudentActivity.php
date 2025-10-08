<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class StudentActivity extends Model
{
    protected $fillable = [
        'user_id',
        'module_id',
        'course_id',
        'activity_id',
        'activity_type',
        'status',
        'score',
        'max_score',
        'percentage_score',
        'started_at',
        'completed_at',
        'submitted_at',
        'graded_at',
        'progress_data',
        'feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'progress_data' => 'array',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    // Progress tracking relationships
    public function quizProgress(): BelongsTo
    {
        return $this->belongsTo(StudentQuizProgress::class, 'activity_id', 'activity_id')
            ->where('student_id', $this->student_id);
    }

    public function assignmentProgress(): HasOne
    {
        return $this->hasOne(StudentAssignmentProgress::class);
    }

    public function projectProgress(): HasOne
    {
        return $this->hasOne(StudentProjectProgress::class);
    }

    public function assessmentProgress(): HasOne
    {
        return $this->hasOne(StudentAssessmentProgress::class);
    }

    // Helper methods
    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'submitted', 'graded']);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isNotStarted(): bool
    {
        return $this->status === 'not_started';
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => Carbon::now(),
        ]);
    }

    public function markAsCompleted(float $score = null, float $maxScore = null): void
    {
        $data = [
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ];

        if ($score !== null) {
            $data['score'] = $score;
            if ($maxScore !== null) {
                $data['max_score'] = $maxScore;
                $data['percentage_score'] = ($maxScore > 0) ? ($score / $maxScore) * 100 : 0;
            }
        }

        $this->update($data);
    }

    public function getProgressPercentage(): float
    {
        return match($this->activity_type) {
            'quiz' => $this->quizProgress?->percentage_score ?? 0,
            'assignment' => $this->assignmentProgress ? 
                ($this->assignmentProgress->submission_status === 'submitted' ? 100 : 50) : 0,
            'project' => $this->projectProgress?->overall_progress_percentage ?? 0,
            'assessment' => $this->assessmentProgress?->proficiency_level ?? 0,
            default => 0,
        };
    }

    // Scopes
    public function scopeByModule($query, $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'submitted', 'graded']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByActivityType($query, $type)
    {
        return $query->where('activity_type', $type);
    }
}
