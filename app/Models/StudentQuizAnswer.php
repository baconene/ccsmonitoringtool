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
        'is_correct' => 'boolean',
        'points_earned' => 'float',
        'answered_at' => 'datetime',
    ];

    protected $with = ['question'];

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
     * Check if the answer is correct and update
     */
    public function checkAnswer(): void
    {
        $question = $this->question;
        
        // Handle multiple-choice and true-false questions
        if (($question->question_type === 'multiple-choice' || $question->question_type === 'true-false') && $this->selected_option_id) {
            $selectedOption = $this->selectedOption;
            $this->is_correct = $selectedOption && $selectedOption->is_correct;
            $this->points_earned = $this->is_correct ? $question->points : 0;
        } 
        // Handle enumeration and short-answer questions
        elseif (($question->question_type === 'enumeration' || $question->question_type === 'short-answer') && !empty($this->answer_text)) {
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
