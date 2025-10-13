<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQuizAnswer extends Model
{
    protected $fillable = [
        'student_id',
        'quiz_progress_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'points_earned',
        'answered_at',
    ];

    protected $casts = [
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
     * Get the quiz progress associated with this answer
     */
    public function quizProgress(): BelongsTo
    {
        return $this->belongsTo(StudentQuizProgress::class, 'quiz_progress_id');
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
        // Handle enumeration and short-answer questions
        elseif (($question->question_type === 'enumeration' || $question->question_type === 'short_answer') && !empty($this->answer_text)) {
            // For enumeration and short answer, manual grading is needed
            // However, we acknowledge the answer was provided
            // Set is_correct to false by default (pending manual grading) and points to 0
            // Instructors can manually grade these later and update the score
            $this->is_correct = false; // false means not graded yet / pending review
            $this->points_earned = 0; // Default to 0 until manually graded
        }
        // No valid answer provided
        else {
            $this->is_correct = false;
            $this->points_earned = 0;
        }
        
        $this->save();
    }
}
