<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'user_id',
        'employee_id',
        'title',
        'department',
        'specialization',
        'bio',
        'office_location',
        'phone',
        'office_hours',
        'hire_date',
        'employment_type',
        'status',
        'salary',
        'education_level',
        'certifications',
        'years_experience',
        'metadata'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'certifications' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the instructor record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the courses that this instructor teaches.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id', 'user_id');
    }

    /**
     * Accessor to get instructor's name from user relationship.
     */
    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Accessor to get instructor's email from user relationship.
     */
    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /**
     * Get full display name with title.
     */
    public function getFullDisplayNameAttribute(): string
    {
        $title = $this->title ? "{$this->title} " : '';
        return "{$title}{$this->name}";
    }

    /**
     * Get instructor's full professional title.
     */
    public function getProfessionalTitleAttribute(): string
    {
        $title = $this->title ?? '';
        $name = $this->name;
        $department = $this->department;
        
        return trim("{$title} {$name}, {$department}");
    }

    /**
     * Generate a unique instructor ID.
     */
    public static function generateInstructorId(): string
    {
        $year = date('Y');
        $prefix = 'INS' . $year;
        
        // Get the last instructor ID for this year
        $lastInstructor = self::where('instructor_id', 'LIKE', $prefix . '%')
            ->orderBy('instructor_id', 'desc')
            ->first();

        if ($lastInstructor) {
            $lastNumber = (int) substr($lastInstructor->instructor_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for active instructors.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for instructors by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope for instructors by employment type.
     */
    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Check if instructor is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if instructor is full-time.
     */
    public function isFullTime(): bool
    {
        return $this->employment_type === 'full-time';
    }

    /**
     * Get years of service.
     */
    public function getYearsOfServiceAttribute(): int
    {
        if (!$this->hire_date) {
            return 0;
        }

        return Carbon::now()->diffInYears($this->hire_date);
    }

    /**
     * Get total number of courses taught.
     */
    public function getTotalCoursesAttribute(): int
    {
        return $this->courses()->count();
    }

    /**
     * Get current active courses.
     */
    public function getActiveCoursesAttribute(): int
    {
        return $this->courses()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Get contact information.
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
            'office' => $this->office_location,
            'office_hours' => $this->office_hours,
        ];
    }

    /**
     * Check if instructor has required certifications for a subject.
     */
    public function hasCertificationFor(string $subject): bool
    {
        if (!$this->certifications) {
            return false;
        }

        // Handle both array and string formats
        if (is_array($this->certifications)) {
            $certString = json_encode($this->certifications);
            return str_contains(strtolower($certString), strtolower($subject));
        }

        return str_contains(strtolower($this->certifications), strtolower($subject));
    }

    /**
     * Get instructor's qualification summary.
     */
    public function getQualificationSummaryAttribute(): string
    {
        $parts = [];
        
        if ($this->education_level) {
            $parts[] = $this->education_level;
        }
        
        if ($this->specialization) {
            $parts[] = "specialized in {$this->specialization}";
        }
        
        if ($this->years_experience) {
            $parts[] = "{$this->years_experience} years experience";
        }

        return implode(', ', $parts);
    }
}
