<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseGradeLevel extends Model
{
    protected $table = 'course_grade_level';

    protected $fillable = [
        'course_id',
        'grade_level_id',
    ];

    /**
     * Get the course that this relationship belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the grade level that this relationship belongs to.
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * Check if a student with given grade level can access a course.
     */
    public static function canStudentAccessCourse(int $courseId, int $gradeLevelId): bool
    {
        return self::where('course_id', $courseId)
                   ->where('grade_level_id', $gradeLevelId)
                   ->exists();
    }

    /**
     * Get all courses accessible by a specific grade level.
     */
    public static function getCoursesForGradeLevel(int $gradeLevelId)
    {
        return Course::whereHas('gradeLevels', function ($query) use ($gradeLevelId) {
            $query->where('grade_level_id', $gradeLevelId);
        })->with(['creator', 'modules']);
    }
}
