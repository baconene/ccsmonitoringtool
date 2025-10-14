<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LessonDocument Model
 * 
 * Links documents to lessons.
 */
class LessonDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'lesson_id',
        'visibility',
        'is_required',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the document.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the lesson.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
