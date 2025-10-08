<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'activity_type_id',
        'created_by',
        'passing_percentage',
        'due_date',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    protected $with = ['activityType', 'creator'];

    /**
     * Get the activity type
     */
    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    /**
     * Get the creator (user)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the quiz associated with this activity
     */
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    /**
     * Get the assignment associated with this activity
     */
    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    /**
     * Get the modules this activity belongs to
     */
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_activities', 'activity_id', 'module_id');
    }

    /**
     * Get student activities for this activity
     */
    public function studentActivities()
    {
        return $this->hasMany(StudentActivity::class);
    }

    /**
     * Get student activity for a specific student
     */
    public function getStudentActivity(int $studentId, int $moduleId = null)
    {
        $query = $this->studentActivities()->where('student_id', $studentId);
        
        if ($moduleId) {
            $query->where('module_id', $moduleId);
        }
        
        return $query->with(['quizProgress', 'assignmentProgress', 'projectProgress', 'assessmentProgress'])->first();
    }
}
