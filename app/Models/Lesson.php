<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory ;

    protected $fillable = ['title', 'description'];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'lesson_module');
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'lesson_document');
    }
}
