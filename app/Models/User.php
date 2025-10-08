<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'grade_level',
        'section',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'role_name',
        'role_display_name',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Courses that this user is enrolled in (as a student).
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    /**
     * Courses that this user instructs (as an instructor).
     */
    public function instructedCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get the student record for this user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the instructor record for this user.
     */
    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    /**
     * Check if this user has a student record.
     */
    public function hasStudentRecord(): bool
    {
        return $this->student()->exists();
    }

    /**
     * Get or create a student record for this user.
     */
    public function getOrCreateStudentRecord(): Student
    {
        if ($this->hasStudentRecord()) {
            return $this->student;
        }

        // Only create if user has student role
        if (!$this->isStudent()) {
            throw new \Exception('Cannot create student record for non-student user');
        }

        return Student::create([
            'student_id' => Student::generateStudentId(),
            'user_id' => $this->id,
            'enrollment_number' => $this->generateEnrollmentNumber(),
            'academic_year' => date('Y') . '-' . (date('Y') + 1),
            'status' => 'active',
            'enrollment_date' => now(),
            'metadata' => [
                'grade_level' => $this->grade_level,
                'section' => $this->section,
                'auto_created' => true,
            ]
        ]);
    }

    /**
     * Generate enrollment number for student.
     */
    private function generateEnrollmentNumber(): string
    {
        $year = date('Y');
        $prefix = $year . '-';
        
        do {
            $number = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $enrollmentNumber = $prefix . $number;
        } while (Student::where('enrollment_number', $enrollmentNumber)->exists());

        return $enrollmentNumber;
    }

    /**
     * Check if this user has an instructor record.
     */
    public function hasInstructorRecord(): bool
    {
        return $this->instructor()->exists();
    }

    /**
     * Get or create an instructor record for this user.
     */
    public function getOrCreateInstructorRecord(array $additionalData = []): Instructor
    {
        if ($this->hasInstructorRecord()) {
            return $this->instructor;
        }

        // Only create if user has instructor or admin role
        if (!$this->isInstructor() && !$this->isAdmin()) {
            throw new \Exception('Cannot create instructor record for non-instructor user');
        }

        $defaultData = [
            'instructor_id' => Instructor::generateInstructorId(),
            'user_id' => $this->id,
            'department' => 'General Studies',
            'employment_type' => 'full-time',
            'status' => 'active',
            'hire_date' => now(),
            'metadata' => [
                'auto_created' => true,
            ]
        ];

        return Instructor::create(array_merge($defaultData, $additionalData));
    }

    /**
     * Generate employee ID for instructor.
     */
    private function generateEmployeeId(): string
    {
        $year = date('Y');
        $prefix = 'EMP' . $year . '-';
        
        do {
            $number = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
            $employeeId = $prefix . $number;
        } while (Instructor::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }

    /**
     * Get the role that belongs to the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->hasName($roleName);
    }

    /**
     * Get the role name (backward compatibility).
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role ? $this->role->name : 'instructor';
    }

    /**
     * Get the role display name.
     */
    public function getRoleDisplayNameAttribute(): string
    {
        return $this->role ? $this->role->display_name : 'Instructor';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is an instructor.
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * Get course enrollments for this user.
     */
    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get enrolled courses for this user.
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot(['progress', 'is_completed', 'enrolled_at', 'completed_at'])
                    ->withTimestamps();
    }

    /**
     * Get lesson completions for this user.
     */
    public function lessonCompletions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Check if user has completed a specific lesson.
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
        $completion = $this->lessonCompletions()->updateOrCreate(
            [
                'lesson_id' => $lesson->id,
            ],
            [
                'course_id' => $lesson->course_id,
                'completed_at' => now(),
                'completion_data' => $completionData,
            ]
        );

        // Update course progress
        $enrollment = $this->courseEnrollments()
            ->where('course_id', $lesson->course_id)
            ->first();
        
        if ($enrollment) {
            $enrollment->updateProgress();
        }

        return $completion;
    }

    /**
     * Get module completions for this user.
     */
    public function moduleCompletions()
    {
        return $this->hasMany(ModuleCompletion::class);
    }
}
