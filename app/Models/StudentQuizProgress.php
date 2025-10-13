<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentQuizProgress extends Model
{
    protected $table = 'student_quiz_progress';

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($progress) {
            // Delete all quiz answers when progress is deleted
            $progress->answers()->delete();
        });
    }

    protected $fillable = [
        'student_id',
        'quiz_id',
        'activity_id',
        'started_at',
        'last_accessed_at',
        'is_completed',
        'is_submitted',
        'completed_questions',
        'total_questions',
        'score',
        'percentage_score',
        'time_spent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_completed' => 'boolean',
        'is_submitted' => 'boolean',
        'score' => 'float',
        'percentage_score' => 'float',
    ];

    protected $with = ['quiz', 'answers'];

    /**
     * Get the student associated with this progress
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the quiz associated with this progress
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the activity associated with this progress
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the answers for this progress
     */
    public function answers(): HasMany
    {
        return $this->hasMany(StudentQuizAnswer::class, 'quiz_progress_id');
    }

    /**
     * Get the associated student activity (if exists)
     */
    public function studentActivity(): HasOne
    {
        return $this->hasOne(StudentActivity::class, 'activity_id', 'activity_id')
            ->where('user_id', $this->student_id)
            ->where('activity_type', 'quiz');
    }

    /**
     * Calculate and update the score
     */
    public function calculateScore(): void
    {
        $this->score = $this->answers->sum('points_earned');
        $totalPoints = $this->quiz->questions->sum('points');
        
        if ($totalPoints > 0) {
            $this->percentage_score = ($this->score / $totalPoints) * 100;
        }
        
        $this->save();
    }

    /**
     * Get all activities for a student based on their course enrollments
     * This includes activities they haven't started yet
     */
    public static function getStudentActivities($studentId)
    {
        // Get all courses the student is enrolled in
        $enrolledCourses = \App\Models\CourseEnrollment::with([
            'course.modules.activities.activityType',
            'course.modules.activities.quiz.questions',
            'course.modules.lessons.activities.activityType',
            'course.modules.lessons.activities.quiz.questions'
        ])
        ->where('user_id', $studentId)
        ->get();

        $activities = collect();

        foreach ($enrolledCourses as $enrollment) {
            $course = $enrollment->course;
            
            // Get activities from modules
            foreach ($course->modules as $module) {
                foreach ($module->activities as $activity) {
                    $activities->push([
                        'activity' => $activity,
                        'course' => $course,
                        'module' => $module,
                        'lesson' => null,
                        'source' => 'module'
                    ]);
                }
                
                // Get activities from lessons within modules
                foreach ($module->lessons as $lesson) {
                    foreach ($lesson->activities as $activity) {
                        $activities->push([
                            'activity' => $activity,
                            'course' => $course,
                            'module' => $module,
                            'lesson' => $lesson,
                            'source' => 'lesson'
                        ]);
                    }
                }
            }
        }

        return $activities;
    }

    /**
     * Get activity status for a student
     */
    public static function getActivityStatus($userId, $activityId)
    {
        // Get the student record first to use the correct student_id
        $student = \App\Models\User::find($userId)?->student;
        if (!$student) {
            return [
                'status' => 'not-taken',
                'progress' => null
            ];
        }
        
        $progress = self::where('student_id', $student->id)
            ->where('activity_id', $activityId)
            ->first();

        if (!$progress) {
            return [
                'status' => 'not-taken',
                'progress' => null
            ];
        }

        $status = 'not-taken';
        if ($progress->is_completed && $progress->is_submitted) {
            $status = 'completed';
        } elseif ($progress->started_at) {
            $status = 'in-progress';
        }

        return [
            'status' => $status,
            'progress' => $progress
        ];
    }
}
