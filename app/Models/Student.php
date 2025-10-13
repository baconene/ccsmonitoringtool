<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Student extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($student) {
            // Delete all related records when student is deleted
            $student->courseEnrollments()->delete();
            $student->studentActivities()->delete();
            $student->quizProgress()->delete();
            $student->lessonCompletions()->delete();
            $student->moduleCompletions()->delete();
        });
    }

    protected $fillable = [
        'student_id_text',
        'user_id',
        'grade_level_id',
        'section',
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
     * Get the grade level that the student belongs to.
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
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
     * Accessor for backward compatibility - maps student_id to student_id_text.
     */
    public function getStudentIdAttribute(): ?string
    {
        return $this->student_id_text;
    }

    /**
     * Mutator for backward compatibility - maps student_id to student_id_text.
     */
    public function setStudentIdAttribute($value): void
    {
        $this->attributes['student_id_text'] = $value;
    }

    /**
     * Generate a unique student ID text.
     */
    public static function generateStudentIdText(): string
    {
        $year = date('Y');
        $prefix = 'STU' . $year;
        
        // Get the last student ID for this year
        $lastStudent = self::where('student_id_text', 'LIKE', $prefix . '%')
            ->orderBy('student_id_text', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id_text, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the full name with student ID text.
     */
    public function getFullDisplayNameAttribute(): string
    {
        return "{$this->student_id_text} - {$this->name}";
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
     * Get course enrollments for this student.
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'student_id', 'id');
    }

    /**
     * Get enrolled courses for this student via many-to-many relationship.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot(['progress', 'is_completed', 'enrolled_at', 'completed_at', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get enrolled courses for this student via the enrollment table (deprecated).
     * @deprecated Use courses relationship instead
     */
    public function enrolledCourses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Course::class,
            CourseEnrollment::class,
            'student_id', // Foreign key on course_enrollments table
            'id', // Foreign key on courses table
            'id', // Local key on students table
            'course_id' // Local key on course_enrollments table
        );
    }

    /**
     * Get lesson completions for this student.
     */
    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class, 'student_id', 'id');
    }

    /**
     * Check if student has completed a specific lesson.
     */
    public function hasCompletedLesson(int $lessonId): bool
    {
        return $this->lessonCompletions()
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    /**
     * Mark a lesson as completed.
     */
    public function completeLesson(Lesson $lesson, array $completionData = []): LessonCompletion
    {
        // Create completion record directly for this student
        $completion = $this->lessonCompletions()->updateOrCreate(
            [
                'lesson_id' => $lesson->id,
            ],
            [
                'course_id' => $lesson->course_id,
                'completed_at' => now(),
                'completion_data' => $completionData,
                'user_id' => $this->user_id, // Keep user_id for backward compatibility
            ]
        );

        // Update course progress through student's enrollment
        $enrollment = $this->courseEnrollments()
            ->where('course_id', $lesson->course_id)
            ->first();
        
        if ($enrollment) {
            $enrollment->updateProgress();
        }

        return $completion;
    }

    /**
     * Get module completions for this student.
     */
    public function moduleCompletions(): HasMany
    {
        return $this->hasMany(ModuleCompletion::class, 'student_id', 'id');
    }

    /**
     * Get all student activities for this student.
     * Now properly connected via student_id.
     */
    public function studentActivities(): HasMany
    {
        return $this->hasMany(StudentActivity::class, 'student_id', 'id');
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
     * Get student quiz progress records.
     */
    public function quizProgress(): HasMany
    {
        return $this->hasMany(StudentQuizProgress::class, 'student_id', 'id');
    }

    /**
     * Get completed activities count.
     */
    public function getCompletedActivitiesCountAttribute(): int
    {
        return $this->studentActivities()->completed()->count();
    }

    /**
     * Get total activities count.
     */
    public function getTotalActivitiesCountAttribute(): int
    {
        return $this->studentActivities()->count();
    }

    /**
     * Get completion percentage across all activities.
     */
    public function getOverallCompletionPercentageAttribute(): float
    {
        $total = $this->total_activities_count;
        if ($total === 0) {
            return 0.0;
        }
        
        return ($this->completed_activities_count / $total) * 100;
    }
}
