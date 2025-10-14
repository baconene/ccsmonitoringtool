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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lesson) {
            // Delete lesson completions
            $lesson->completions()->delete();

            // Delete lesson documents (HasMany relationship - delete pivot records)
            $lesson->documents()->delete();

            // Detach modules (many-to-many)
            $lesson->modules()->detach();

            // Delete lesson activities pivot records
            $lesson->lessonActivities()->delete();

            // Detach activities (many-to-many)
            $lesson->activities()->detach();
        });
    }

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

    // Lesson documents relationship - returns LessonDocument pivot records
    public function documents()
    {
        return $this->hasMany(LessonDocument::class);
    }

    // Get all document files through LessonDocument
    public function documentFiles()
    {
        return $this->hasManyThrough(Document::class, LessonDocument::class, 'lesson_id', 'id', 'id', 'document_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    // Lesson activities relationship (many-to-many with Activity)
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'module_lesson_activities', 'module_lesson_id', 'activity_id')
            ->withPivot('order')
            ->orderBy('module_lesson_activities.order');
    }

    // Direct access to lesson activities pivot records
    public function lessonActivities()
    {
        return $this->hasMany(ModuleLessonActivity::class, 'module_lesson_id');
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
