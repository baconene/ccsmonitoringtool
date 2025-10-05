<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentQuizProgress extends Model
{
    protected $table = 'student_quiz_progress';

    protected $fillable = [
        'student_id',
        'quiz_id',
        'activity_id',
        'started_at',
        'last_accessed_at',
        'is_completed',
        'is_submitted',
        'completed_questions',
        'total_questions',
        'score',
        'percentage_score',
        'time_spent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_completed' => 'boolean',
        'is_submitted' => 'boolean',
        'score' => 'float',
        'percentage_score' => 'float',
    ];

    protected $with = ['quiz', 'answers'];

    /**
     * Get the student associated with this progress
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the quiz associated with this progress
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the activity associated with this progress
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the answers for this progress
     */
    public function answers(): HasMany
    {
        return $this->hasMany(StudentQuizAnswer::class, 'quiz_progress_id');
    }

    /**
     * Calculate and update the score
     */
    public function calculateScore(): void
    {
        $this->score = $this->answers->sum('points_earned');
        $totalPoints = $this->quiz->questions->sum('points');
        
        if ($totalPoints > 0) {
            $this->percentage_score = ($this->score / $totalPoints) * 100;
        }
        
        $this->save();
    }
}
