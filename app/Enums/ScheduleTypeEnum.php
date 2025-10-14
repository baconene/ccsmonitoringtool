<?php

namespace App\Enums;

enum ScheduleTypeEnum: string
{
    case ACTIVITY = 'activity';
    case COURSE = 'course';
    case ADHOC = 'adhoc';
    case EXAM = 'exam';
    case OFFICE_HOURS = 'office_hours';
    case COURSE_DUE_DATE = 'course_due_date'; // Auto-generated course schedules

    /**
     * Get the display name for the schedule type
     */
    public function label(): string
    {
        return match($this) {
            self::ACTIVITY => 'Activity',
            self::COURSE => 'Course',
            self::ADHOC => 'Personal/Adhoc',
            self::EXAM => 'Exam',
            self::OFFICE_HOURS => 'Office Hours',
            self::COURSE_DUE_DATE => 'Course Due Date',
        };
    }

    /**
     * Get the description for the schedule type
     */
    public function description(): string
    {
        return match($this) {
            self::ACTIVITY => 'Scheduled activities like quizzes, assignments, and assessments',
            self::COURSE => 'Course lectures, seminars, and sessions',
            self::ADHOC => 'Personal events, meetings, and administrative tasks',
            self::EXAM => 'Formal examinations and tests',
            self::OFFICE_HOURS => 'Instructor office hours for student consultations',
            self::COURSE_DUE_DATE => 'Automatically created schedule for course due dates',
        };
    }

    /**
     * Get the color for the schedule type (hex format)
     */
    public function color(): string
    {
        return match($this) {
            self::ACTIVITY => '#3B82F6',      // Blue
            self::COURSE => '#10B981',        // Green
            self::ADHOC => '#F59E0B',         // Amber
            self::EXAM => '#EF4444',          // Red
            self::OFFICE_HOURS => '#8B5CF6',  // Purple
            self::COURSE_DUE_DATE => '#06B6D4', // Cyan
        };
    }

    /**
     * Get the icon name for the schedule type
     */
    public function icon(): string
    {
        return match($this) {
            self::ACTIVITY => 'clipboard-list',
            self::COURSE => 'book-open',
            self::ADHOC => 'calendar',
            self::EXAM => 'file-text',
            self::OFFICE_HOURS => 'users',
            self::COURSE_DUE_DATE => 'calendar-check',
        };
    }

    /**
     * Get the Lucide Vue icon component name
     */
    public function lucideIcon(): string
    {
        return match($this) {
            self::ACTIVITY => 'ClipboardList',
            self::COURSE => 'BookOpen',
            self::ADHOC => 'Calendar',
            self::EXAM => 'FileText',
            self::OFFICE_HOURS => 'Users',
            self::COURSE_DUE_DATE => 'CalendarCheck',
        };
    }

    /**
     * Get all schedule types as an array
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
            'color' => $case->color(),
            'icon' => $case->icon(),
        ], self::cases());
    }

    /**
     * Get schedule type from string value
     */
    public static function fromString(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Check if a value is a valid schedule type
     */
    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }

    /**
     * Get all values as array of strings
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get database seeder data format
     */
    public function toSeederArray(): array
    {
        return [
            'name' => $this->value,
            'description' => $this->description(),
            'color' => $this->color(),
            'icon' => $this->icon(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Get all seeder data for database seeding
     */
    public static function getAllSeederData(): array
    {
        return array_map(fn($case) => $case->toSeederArray(), self::cases());
    }
}
