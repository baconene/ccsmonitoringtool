<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'enrollment_number',
        'academic_year',
        'program',
        'department',
        'enrollment_date',
        'status',
        'metadata'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the student record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courses that this student is enrolled in.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
            ->withPivot(['enrolled_at', 'status', 'grade', 'notes'])
            ->withTimestamps();
    }

    /**
     * Accessor to get student's name from user relationship.
     */
    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Accessor to get student's email from user relationship.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * Generate a unique student ID.
     */
    public static function generateStudentId(): string
    {
        $year = date('Y');
        $prefix = 'STU' . $year;
        
        // Get the last student ID for this year
        $lastStudent = self::where('student_id', 'LIKE', $prefix . '%')
            ->orderBy('student_id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the full name with student ID.
     */
    public function getFullDisplayNameAttribute(): string
    {
        return "{$this->student_id} - {$this->name}";
    }

    /**
     * Check if student is currently enrolled (active status).
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get student's academic standing based on metadata.
     */
    public function getAcademicStandingAttribute(): string
    {
        return $this->metadata['academic_status'] ?? 'regular';
    }

    /**
     * Get student's scholarship information.
     */
    public function getScholarshipAttribute(): ?string
    {
        return $this->metadata['scholarship_type'] ?? null;
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for students by academic year.
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Get all student activities for this student.
     */
    public function studentActivities()
    {
        return $this->hasMany(StudentActivity::class);
    }

    /**
     * Get student activities for a specific module.
     */
    public function getModuleActivities(int $moduleId)
    {
        return $this->studentActivities()
            ->where('module_id', $moduleId)
            ->with(['activity', 'quizProgress', 'assignmentProgress', 'projectProgress', 'assessmentProgress'])
            ->get();
    }

    /**
     * Get student quiz progress records through user relationship.
     */
    public function quizProgress()
    {
        return $this->hasMany(StudentQuizProgress::class, 'student_id', 'user_id');
    }
}
