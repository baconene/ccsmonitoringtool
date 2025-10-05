<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 
        'title',
        'description', 
        'sequence', 
        'completion_percentage',
        'module_type',
        'module_percentage',
        'created_by'
    ];

    protected $casts = [
        'module_percentage' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_module');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Module activities relationship (many-to-many with Activity)
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'module_activities')
            ->withPivot('module_course_id', 'order')
            ->orderBy('module_activities.order');
    }

    // Module completions relationship
    public function completions()
    {
        return $this->hasMany(ModuleCompletion::class);
    }

    // Direct access to module activities pivot records
    public function moduleActivities()
    {
        return $this->hasMany(ModuleActivity::class);
    }
}
