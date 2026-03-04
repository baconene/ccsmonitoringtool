<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQuizAnswer extends Model
{
    protected $fillable = [
        'student_id',
        'activity_progress_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'points_earned',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'float',
        'answered_at' => 'datetime',
    ];

    protected $with = ['question'];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically populate answer_text from selected option
        static::saving(function ($answer) {
            // If selected_option_id is set and answer_text is empty, populate it
            if ($answer->selected_option_id && empty($answer->answer_text)) {
                $option = QuestionOption::find($answer->selected_option_id);
                if ($option) {
                    $answer->answer_text = $option->option_text;
                }
            }
        });
    }

    /**
     * Get the student associated with this answer
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the activity progress associated with this answer
     */
    public function activityProgress(): BelongsTo
    {
        return $this->belongsTo(StudentActivityProgress::class, 'activity_progress_id');
    }
    
    /**
     * Backwards compatibility alias for quizProgress
     * @deprecated Use activityProgress() instead
     */
    public function quizProgress(): BelongsTo
    {
        return $this->activityProgress();
    }

    /**
     * Get the question associated with this answer
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the selected option for multiple choice questions
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    /**
     * Get the display text for the student's answer based on question type
     */
    public function getDisplayAnswerAttribute(): string
    {
        $question = $this->question;
        
        // For multiple choice and true-false questions, use selected option text
        if (($question->question_type === 'multiple_choice' || $question->question_type === 'true_false')) {
            if ($this->selected_option_id && $this->selectedOption) {
                return $this->selectedOption->option_text;
            }
        }
        
        // For enumeration and short-answer questions, use answer_text
        if (($question->question_type === 'enumeration' || $question->question_type === 'short_answer')) {
            if (!empty($this->answer_text)) {
                return $this->answer_text;
            }
        }
        
        // For any other case with answer_text
        if (!empty($this->answer_text)) {
            return $this->answer_text;
        }
        
        return 'Not answered';
    }

    /**
     * Check if the answer is correct and update
     */
    public function checkAnswer(): void
    {
        $question = $this->question;
        
        // Handle multiple-choice and true-false questions
        if (($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') && $this->selected_option_id) {
            $selectedOption = $this->selectedOption;
            $this->is_correct = $selectedOption && $selectedOption->is_correct;
            $this->points_earned = $this->is_correct ? $question->points : 0;
        } 
        // Handle enumeration questions - auto-grade by comparing with correct answer
        elseif ($question->question_type === 'enumeration' && !empty($this->answer_text)) {
            $studentItems = $this->parseEnumerationAnswers($this->answer_text);
            $correctItems = $this->parseEnumerationAnswers($question->correct_answer);
            
            // Case-insensitive comparison
            $studentItems = array_map('strtolower', $studentItems);
            $correctItems = array_map('strtolower', $correctItems);
            
            sort($studentItems);
            sort($correctItems);
            
            $this->is_correct = $studentItems === $correctItems;
            $this->points_earned = $this->is_correct ? $question->points : 0;
        }
        // Handle short-answer questions - auto-grade by comparing with correct answer
        elseif ($question->question_type === 'short_answer' && !empty($this->answer_text)) {
            $studentAnswer = $this->normalizeText($this->answer_text);
            $correctAnswer = $this->normalizeText($question->correct_answer);
            
            $this->is_correct = $studentAnswer === $correctAnswer;
            $this->points_earned = $this->is_correct ? $question->points : 0;
        }
        // No valid answer provided
        else {
            $this->is_correct = false;
            $this->points_earned = 0;
        }
        
        $this->save();
    }

    /**
     * Normalize text for comparison (case-insensitive, whitespace normalized)
     */
    private function normalizeText($value): string
    {
        $text = trim((string) $value);
        $text = strtolower($text);
        return preg_replace('/\s+/u', ' ', $text) ?? $text;
    }

    /**
     * Parse enumeration answers into array of items
     */
    private function parseEnumerationAnswers($value): array
    {
        if (is_array($value)) {
            $rawItems = $value;
        } else {
            // Split by newlines, commas, or semicolons
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
}
