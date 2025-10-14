<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ActivityDocument Model
 * 
 * Links documents to activities.
 */
class ActivityDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'activity_id',
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
     * Get the activity.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
