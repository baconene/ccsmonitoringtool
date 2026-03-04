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
            $normalizeBoolean = function ($value): ?string {
                $text = strtolower(trim((string) $value));

                if (in_array($text, ['true', 't', '1', 'yes', 'y'], true)) {
                    return 'true';
                }

                if (in_array($text, ['false', 'f', '0', 'no', 'n'], true)) {
                    return 'false';
                }

                return null;
            };

            $studentAnswer = $normalizeBoolean($answer);
            $correctAnswer = $normalizeBoolean($this->correct_answer);

            return $studentAnswer !== null && $correctAnswer !== null && $studentAnswer === $correctAnswer;
        }

        if ($this->question_type === 'multiple_choice') {
            // $answer should be an array of option IDs
            $correctOptionIds = $this->options()->where('is_correct', true)->pluck('id')->toArray();
            sort($correctOptionIds);
            sort($answer);
            return $correctOptionIds === $answer;
        }

        if ($this->question_type === 'enumeration' || $this->question_type === 'short_answer') {
            if ($this->question_type === 'enumeration') {
                $studentItems = $this->parseEnumerationAnswers($answer);

                if (empty($studentItems)) {
                    return false;
                }

                $expectedItems = [];
                if ($this->acceptable_answers && is_array($this->acceptable_answers) && count($this->acceptable_answers) > 0) {
                    $expectedItems = $this->parseEnumerationAnswers($this->acceptable_answers);
                } else {
                    $expectedItems = $this->parseEnumerationAnswers($this->correct_answer);
                }

                if (empty($expectedItems)) {
                    return false;
                }

                if (!$this->case_sensitive) {
                    $studentItems = array_map('strtolower', $studentItems);
                    $expectedItems = array_map('strtolower', $expectedItems);
                }

                sort($studentItems);
                sort($expectedItems);

                return $studentItems === $expectedItems;
            }

            $answerText = $this->normalizeText($answer);

            if ($this->acceptable_answers && is_array($this->acceptable_answers)) {
                foreach ($this->acceptable_answers as $acceptable) {
                    if ($answerText === $this->normalizeText($acceptable)) {
                        return true;
                    }
                }
                return false;
            }

            $correctText = $this->normalizeText($this->correct_answer);
            return $answerText === $correctText;
        }

        return false;
    }

    private function normalizeText($value): string
    {
        $text = trim((string) $value);

        if (!$this->case_sensitive) {
            $text = strtolower($text);
        }

        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }

    private function parseEnumerationAnswers($value): array
    {
        if (is_array($value)) {
            $rawItems = $value;
        } else {
            $rawItems = preg_split('/[\r\n,;]+/u', (string) $value) ?: [];
        }

        $items = [];
        foreach ($rawItems as $item) {
            $normalized = preg_replace('/\s+/u', ' ', trim((string) $item));
            if ($normalized !== null && $normalized !== '') {
                $items[] = $normalized;
            }
        }

        return array_values(array_unique($items));
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
