<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * StudentDocument Model
 * 
 * Links documents to students (transcripts, IDs, certificates, etc.).
 */
class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'student_id',
        'document_category',
        'academic_year',
        'verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
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
     * Get the user who verified the document.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
