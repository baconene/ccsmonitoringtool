<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GradeLevel extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($gradeLevel) {
            // Detach all courses when grade level is deleted
            $gradeLevel->courses()->detach();
        });
    }

    protected $fillable = [
        'name',
        'display_name',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Get the courses that have this grade level.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_grade_level');
    }

    /**
     * Scope to get only active grade levels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by level.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level');
    }
}
