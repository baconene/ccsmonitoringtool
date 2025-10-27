<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudentAssignmentAnswer extends Model
{
    protected $fillable = [
        'student_id',
        'assignment_id',
        'assignment_question_id',
        'answer_text',
        'selected_options',
        'file_path',
        'original_filename',
        'is_correct',
        'points_earned',
        'instructor_feedback',
        'answered_at',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the student that owns the answer
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the assignment that owns the answer
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the question that owns the answer
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(AssignmentQuestion::class, 'assignment_question_id');
    }

    /**
     * Get the file URL if exists
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Delete associated file when answer is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($answer) {
            if ($answer->file_path && Storage::exists($answer->file_path)) {
                Storage::delete($answer->file_path);
            }
        });
    }
}
