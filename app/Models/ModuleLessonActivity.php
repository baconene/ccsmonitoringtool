<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleLessonActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_lesson_id',
        'activity_id',
        'order'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'module_lesson_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
