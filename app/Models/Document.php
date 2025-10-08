<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'file_path', 'doc_type'];

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_document');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_document');
    }
}
