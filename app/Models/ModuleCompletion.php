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
}
