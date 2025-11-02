<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class StudentActivity extends Model
{
    protected static function boot()
    {
        parent::boot();

        // Update course progress when activity status changes
        static::saved(function ($studentActivity) {
            if ($studentActivity->status === 'completed' && $studentActivity->course_id && $studentActivity->student_id) {
                // Find the enrollment and update progress
                $enrollment = CourseEnrollment::where('course_id', $studentActivity->course_id)
                    ->where('student_id', $studentActivity->student_id)
                    ->first();
                
                if ($enrollment) {
                    // Calculate progress based on completed activities
                    $course = $enrollment->course;
                    $totalActivities = 0;
                    $completedActivities = 0;
                    
                    foreach ($course->modules as $module) {
                        $moduleActivities = $module->activities;
                        $totalActivities += $moduleActivities->count();
                        
                        // Count completed activities
                        $completedActivities += StudentActivity::where('course_id', $course->id)
                            ->where('student_id', $studentActivity->student_id)
                            ->where('status', 'completed')
                            ->whereIn('activity_id', $moduleActivities->pluck('id'))
                            ->count();
                    }
                    
                    // Update progress percentage
                    $progressPercentage = $totalActivities > 0 
                        ? round(($completedActivities / $totalActivities) * 100, 2) 
                        : 0.0;
                    
                    $enrollment->update(['progress' => $progressPercentage]);
                }
            }
        });

        static::deleting(function ($studentActivity) {
            // Delete related progress records
            if ($studentActivity->assignmentProgress) {
                $studentActivity->assignmentProgress->delete();
            }
            if ($studentActivity->projectProgress) {
                $studentActivity->projectProgress->delete();
            }
            if ($studentActivity->assessmentProgress) {
                $studentActivity->assessmentProgress->delete();
            }
        });
    }

    protected $fillable = [
        'student_id',
        'activity_id',
        'module_id',
        'course_id',
        'score',
        'max_score',
        'percentage_score',
        'status',
        'started_at',
        'completed_at',
        'submitted_at',
        'graded_at',
        'progress_data',
        'feedback',
    ];

    protected $appends = ['activity_type'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'progress_data' => 'array',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the student that owns this activity.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    /**
     * Get the user through the student relationship.
     */
    public function getUserAttribute()
    {
        return $this->student?->user;
    }

    /**
     * Get the activity that this record tracks.
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the module this activity belongs to.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the course this activity belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the activity type dynamically from the activity relationship.
     */
    public function getActivityTypeAttribute(): string
    {
        return strtolower($this->activity->activityType->name ?? 'unknown');
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
        return $query->whereHas('activity.activityType', function($q) use ($type) {
            $q->where('name', '=', ucfirst(strtolower($type)));
        });
    }
}
