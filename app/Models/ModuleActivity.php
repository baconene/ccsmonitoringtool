<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'activity_id',
        'module_course_id',
        'order'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
