<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ModuleDocument Model
 * 
 * Links documents to modules.
 */
class ModuleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'module_id',
        'visibility',
        'is_required',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the document.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the module.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
