<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'description', 
        'course_id',
        'module_id',
        'order',
        'duration',
        'content'
    ];

    protected $casts = [
        'order' => 'integer',
        'duration' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'lesson_module');
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'lesson_document');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    /**
     * Check if lesson is completed by a specific user.
     */
    public function isCompletedByUser(User $user): bool
    {
        return $this->completions()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get completion for a specific user.
     */
    public function getCompletionForUser(User $user): ?LessonCompletion
    {
        return $this->completions()
            ->where('user_id', $user->id)
            ->first();
    }
}
