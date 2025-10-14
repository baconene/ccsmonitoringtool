<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Base Document Model
 * 
 * Serves as the central document storage interface for all document types
 * across the system. All specific document types extend this model.
 */
class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'extension',
        'document_type',
        'uploaded_by',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the user who uploaded the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get all course documents.
     */
    public function courseDocuments()
    {
        return $this->hasMany(CourseDocument::class);
    }

    /**
     * Get all lesson documents.
     */
    public function lessonDocuments()
    {
        return $this->hasMany(LessonDocument::class);
    }

    /**
     * Get all activity documents.
     */
    public function activityDocuments()
    {
        return $this->hasMany(ActivityDocument::class);
    }

    /**
     * Get all module documents.
     */
    public function moduleDocuments()
    {
        return $this->hasMany(ModuleDocument::class);
    }

    /**
     * Get all report documents.
     */
    public function reportDocuments()
    {
        return $this->hasMany(ReportDocument::class);
    }

    /**
     * Get all project documents.
     */
    public function projectDocuments()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    /**
     * Get all assessment documents.
     */
    public function assessmentDocuments()
    {
        return $this->hasMany(AssessmentDocument::class);
    }

    /**
     * Get all student documents.
     */
    public function studentDocuments()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * Get all instructor documents.
     */
    public function instructorDocuments()
    {
        return $this->hasMany(InstructorDocument::class);
    }

    /**
     * Scope to filter by document type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to filter by uploader.
     */
    public function scopeUploadedBy($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get full file URL.
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if document is an image.
     */
    public function isImage()
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ]);
    }

    /**
     * Check if document is a PDF.
     */
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if document is a document file (Word, Excel, etc.).
     */
    public function isOfficeDocument()
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);
    }
}
