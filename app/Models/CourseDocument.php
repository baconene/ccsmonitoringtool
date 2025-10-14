<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CourseDocument Model
 * 
 * Links documents to courses.
 */
class CourseDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'course_id',
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
     * Get the course.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
