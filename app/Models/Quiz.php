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
            // Delete all student quiz progress
            $progressIds = StudentQuizProgress::where('quiz_id', $quiz->id)->pluck('id');
            StudentQuizAnswer::whereIn('quiz_progress_id', $progressIds)->delete();
            StudentQuizProgress::where('quiz_id', $quiz->id)->delete();

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
