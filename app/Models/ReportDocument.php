<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ReportDocument Model
 * 
 * Links documents to student/course reports.
 */
class ReportDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'student_id',
        'course_id',
        'report_type',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    /**
     * Get the document.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who generated the report.
     */
    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
