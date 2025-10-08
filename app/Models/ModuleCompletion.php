<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'module_id',
        'course_id',
        'completed_at',
        'completion_data',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'completion_data' => 'array',
    ];

    /**
     * Get the user who completed the module.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module that was completed.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the course the module belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get related student activities for this module completion.
     */
    public function studentActivities()
    {
        // Get the student record for this user
        $student = Student::where('user_id', $this->user_id)->first();
        
        if (!$student) {
            return StudentActivity::whereRaw('1 = 0'); // Return empty query
        }

        return $this->hasManyThrough(
            StudentActivity::class,
            Module::class,
            'id', // Foreign key on modules table
            'module_id', // Foreign key on student_activities table
            'module_id', // Local key on module_completions table
            'id' // Local key on modules table
        )->where('student_activities.student_id', $student->id);
    }
}
