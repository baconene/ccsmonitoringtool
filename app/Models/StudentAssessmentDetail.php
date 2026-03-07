<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class StudentAssessmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'assessed_by_user_id',
        'description',
        'course_mostyle',
    ];

    /**
     * Get the student record for this assessment detail.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for this assessment detail.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor/admin who created this assessment detail.
     */
    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by_user_id');
    }
}
