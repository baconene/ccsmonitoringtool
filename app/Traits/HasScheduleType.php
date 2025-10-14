<?php

namespace App\Traits;

use App\Enums\ScheduleTypeEnum;
use App\Models\ScheduleType;

trait HasScheduleType
{
    /**
     * Get the schedule type enum instance
     */
    public function getScheduleTypeEnum(): ?ScheduleTypeEnum
    {
        if (!$this->scheduleType) {
            return null;
        }

        return ScheduleTypeEnum::fromString($this->scheduleType->name);
    }

    /**
     * Check if schedule is of a specific type
     */
    public function isType(ScheduleTypeEnum $type): bool
    {
        return $this->scheduleType?->name === $type->value;
    }

    /**
     * Check if schedule is an activity
     */
    public function isActivity(): bool
    {
        return $this->isType(ScheduleTypeEnum::ACTIVITY);
    }

    /**
     * Check if schedule is a course
     */
    public function isCourse(): bool
    {
        return $this->isType(ScheduleTypeEnum::COURSE);
    }

    /**
     * Check if schedule is an exam
     */
    public function isExam(): bool
    {
        return $this->isType(ScheduleTypeEnum::EXAM);
    }

    /**
     * Check if schedule is office hours
     */
    public function isOfficeHours(): bool
    {
        return $this->isType(ScheduleTypeEnum::OFFICE_HOURS);
    }

    /**
     * Check if schedule is adhoc/personal
     */
    public function isAdhoc(): bool
    {
        return $this->isType(ScheduleTypeEnum::ADHOC);
    }

    /**
     * Check if schedule is a course due date
     */
    public function isCourseDueDate(): bool
    {
        return $this->isType(ScheduleTypeEnum::COURSE_DUE_DATE);
    }

    /**
     * Get the schedule type color
     */
    public function getTypeColor(): string
    {
        return $this->getScheduleTypeEnum()?->color() ?? '#6B7280';
    }

    /**
     * Get the schedule type icon
     */
    public function getTypeIcon(): string
    {
        return $this->getScheduleTypeEnum()?->icon() ?? 'calendar';
    }

    /**
     * Get the schedule type label
     */
    public function getTypeLabel(): string
    {
        return $this->getScheduleTypeEnum()?->label() ?? 'Unknown';
    }

    /**
     * Scope: Filter by schedule type
     */
    public function scopeOfType($query, ScheduleTypeEnum $type)
    {
        return $query->whereHas('scheduleType', function ($q) use ($type) {
            $q->where('name', $type->value);
        });
    }

    /**
     * Scope: Filter by multiple schedule types
     */
    public function scopeOfTypes($query, array $types)
    {
        $typeValues = array_map(fn($type) => $type->value, $types);
        
        return $query->whereHas('scheduleType', function ($q) use ($typeValues) {
            $q->whereIn('name', $typeValues);
        });
    }
}
