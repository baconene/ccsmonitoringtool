<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ProjectDocument Model
 * 
 * Links documents to student projects/submissions.
 */
class ProjectDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'activity_id',
        'student_id',
        'submission_date',
        'status',
        'feedback',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
    ];

    /**
     * Get the document.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the activity (project/assignment).
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
