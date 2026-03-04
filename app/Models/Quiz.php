<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($quiz) {
            // Delete all student quiz answers and progress
            // Note: student_quiz_progress was migrated to student_activity_progress
            $progressIds = \App\Models\StudentActivityProgress::where('activity_id', $quiz->activity_id)
                ->where('activity_type', 'quiz')
                ->pluck('id');
            
            if ($progressIds->isNotEmpty()) {
                StudentQuizAnswer::whereIn('activity_progress_id', $progressIds)->delete();
                \App\Models\StudentActivityProgress::whereIn('id', $progressIds)->delete();
            }

            // Delete all questions (which will cascade to their options)
            $quiz->questions()->each(function ($question) {
                $question->delete();
            });
        });
    }

    protected $fillable = [
        'activity_id',
        'created_by',
        'title',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['questions'];

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
     * Get all questions for this quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
