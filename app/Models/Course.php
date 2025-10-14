<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($course) {
            // Note: Cleanup is handled by CourseService::cleanupCourseModules()
            // to ensure proper deletion order and avoid foreign key constraint errors.
            // This boot method is kept for model-level cascading when not using the service.
            
            // Detach many-to-many relationships
            $course->gradeLevels()->detach();
            $course->enrolledUsers()->detach();
        });
    }

    protected $fillable = [
        'title',
        'name', // Keep for backward compatibility
        'description',
        'instructor_id',
        'created_by',
        'course_code',
        'credits',
        'semester',
        'academic_year',
        'is_active',
        'enrollment_limit',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'credits' => 'integer',
        'enrollment_limit' => 'integer',
    ];

    protected $appends = [
        'students_count'
    ];

    public function getStudentsCountAttribute()
    {
        return $this->students()->count();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course_enrollments', 'course_id', 'student_id')
            ->withPivot(['progress', 'is_completed', 'enrolled_at', 'completed_at', 'instructor_id'])
            ->withTimestamps();
    }

    // Keep the old relationship for backward compatibility with User model
    public function enrolledUsers(): BelongsToMany
    {
        
        return $this->belongsToMany(User::class, 'course_user');
    }

    // Accessor for title (fallback to name if title is not set)
    public function getTitleAttribute($value)
    {
        return $value ?: $this->name;
    }

    /**
     * Get course enrollments using the CourseEnrollment model.
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'course_id', 'id');
    }

    /**
     * Get enrolled students through the CourseEnrollment model.
     */
    public function enrolledStudentsViaEnrollments()
    {
        return $this->courseEnrollments()->with('student.user');
    }

    /**
     * Get course enrollments (deprecated - use courseEnrollments instead).
     * @deprecated Use courseEnrollments relationship instead
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get enrolled users through enrollments (deprecated - use students instead).
     * @deprecated Use students relationship instead
     */
    public function enrolledStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
                    ->withPivot(['progress', 'is_completed', 'enrolled_at', 'completed_at'])
                    ->withTimestamps();
    }

    /**
     * Get lessons for this course.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Get the grade levels associated with this course.
     */
    public function gradeLevels(): BelongsToMany
    {
        return $this->belongsToMany(GradeLevel::class, 'course_grade_level')
                    ->withTimestamps();
    }

    /**
     * Get lesson completions for this course.
     */
    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Get module completions for this course.
     */
    public function moduleCompletions(): HasMany
    {
        return $this->hasMany(ModuleCompletion::class);
    }

    /**
     * Get schedules for this course (polymorphic relationship).
     */
    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    /**
     * Get completion percentage for a specific user based on module weights.
     */
    public function getCompletionPercentageForUser(User $user): float
    {
        $modules = $this->modules()->get();
        
        if ($modules->isEmpty()) {
            return 0;
        }

        // Calculate total weight of all modules
        $totalWeight = $modules->sum('module_percentage') ?: 100;
        
        // Get completed modules for this user
        $completedModules = $this->moduleCompletions()
            ->where('user_id', $user->id)
            ->pluck('module_id')
            ->toArray();
        
        // Calculate sum of completed module weights
        $completedWeight = $modules->whereIn('id', $completedModules)
            ->sum('module_percentage');
        
        return $totalWeight > 0 ? round(($completedWeight / $totalWeight) * 100, 2) : 0;
    }
}
