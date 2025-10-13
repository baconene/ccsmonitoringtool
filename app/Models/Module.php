<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($module) {
            // Delete module activities pivot records
            $module->moduleActivities()->delete();

            // Delete activities associated with this module
            $module->activities()->each(function ($activity) {
                $activity->delete();
            });

            // Detach lessons
            $module->lessons()->detach();

            // Detach documents
            $module->documents()->detach();

            // Delete module completions
            $module->completions()->delete();

            // Delete student activities
            $module->studentActivities()->delete();
        });
    }

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
        'completion_percentage' => 'integer',
        'sequence' => 'integer',
        'course_id' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    // Module documents relationship
    public function documents()
    {
        return $this->belongsToMany(Document::class, 'module_document');
    }

    // Student activities for this module
    public function studentActivities()
    {
        return $this->hasMany(StudentActivity::class);
    }

    // Get student activities for a specific student
    public function getStudentActivities(int $studentId)
    {
        return $this->studentActivities()
            ->where('student_id', $studentId)
            ->with(['activity', 'quizProgress', 'assignmentProgress', 'projectProgress', 'assessmentProgress'])
            ->get();
    }
}
