<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AssessmentDocument Model
 * 
 * Links documents to assessments (answer sheets, rubrics, etc.).
 */
class AssessmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'activity_id',
        'student_id',
        'document_category',
        'score',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    /**
     * Get the document.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the activity (assessment).
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
