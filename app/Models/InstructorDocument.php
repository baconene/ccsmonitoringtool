<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InstructorDocument Model
 * 
 * Links documents to instructors (certifications, resumes, etc.).
 */
class InstructorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'instructor_id',
        'document_category',
        'expiry_date',
        'verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expiry_date' => 'date',
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
     * Get the instructor.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Get the user who verified the document.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if document is expired.
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
