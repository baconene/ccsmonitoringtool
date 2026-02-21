<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'name',
        'description',
        'difficulty_level',
        'weight',
        'competency_threshold',
        'bloom_level',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'weight' => 'decimal:2',
        'competency_threshold' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the module this skill belongs to
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get all activities associated with this skill
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'skill_activities')
            ->withPivot('weight')
            ->withTimestamps();
    }

    /**
     * Get all student assessments for this skill
     */
    public function studentAssessments(): HasMany
    {
        return $this->hasMany(StudentSkillAssessment::class);
    }

    /**
     * Get the course through module
     */
    public function course()
    {
        return $this->hasManyThrough(
            Course::class,
            Module::class,
            'id',
            'id',
            'module_id',
            'course_id'
        );
    }
}
