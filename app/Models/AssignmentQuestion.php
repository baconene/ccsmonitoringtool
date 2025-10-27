<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentQuestion extends Model
{
    protected $fillable = [
        'assignment_id',
        'question_text',
        'question_type',
        'points',
        'correct_answer',
        'acceptable_answers',
        'case_sensitive',
        'order',
        'explanation',
    ];

    protected $casts = [
        'acceptable_answers' => 'array',
        'case_sensitive' => 'boolean',
        'points' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the assignment that owns the question
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the options for the question (for multiple choice)
     */
    public function options(): HasMany
    {
        return $this->hasMany(AssignmentQuestionOption::class)->orderBy('order');
    }

    /**
     * Get the student answers for this question
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssignmentAnswer::class);
    }

    /**
     * Check if answer is correct (for auto-gradable questions)
     */
    public function checkAnswer($answer): bool
    {
        if ($this->question_type === 'true_false') {
            return strtolower(trim($answer)) === strtolower(trim($this->correct_answer));
        }

        if ($this->question_type === 'multiple_choice') {
            // $answer should be an array of option IDs
            $correctOptionIds = $this->options()->where('is_correct', true)->pluck('id')->toArray();
            sort($correctOptionIds);
            sort($answer);
            return $correctOptionIds === $answer;
        }

        if ($this->question_type === 'enumeration' || $this->question_type === 'short_answer') {
            $answerText = trim($answer);
            if (!$this->case_sensitive) {
                $answerText = strtolower($answerText);
            }

            // Check against acceptable answers if provided
            if ($this->acceptable_answers && is_array($this->acceptable_answers)) {
                foreach ($this->acceptable_answers as $acceptable) {
                    $acceptableText = trim($acceptable);
                    if (!$this->case_sensitive) {
                        $acceptableText = strtolower($acceptableText);
                    }
                    if ($answerText === $acceptableText) {
                        return true;
                    }
                }
                return false;
            }

            // Check against correct_answer
            $correctText = trim($this->correct_answer);
            if (!$this->case_sensitive) {
                $correctText = strtolower($correctText);
            }
            return $answerText === $correctText;
        }

        return false;
    }

    /**
     * Get the question type display name
     */
    public function getQuestionTypeDisplayAttribute(): string
    {
        return match($this->question_type) {
            'true_false' => 'True/False',
            'multiple_choice' => 'Multiple Choice',
            'enumeration' => 'Enumeration',
            'short_answer' => 'Short Answer',
            default => $this->question_type,
        };
    }
}
