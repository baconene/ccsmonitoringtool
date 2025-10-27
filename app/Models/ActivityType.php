<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'model',
    ];

    /**
     * Get all activities of this type
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the model class for this activity type
     * 
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->model;
    }

    /**
     * Check if this activity type has a model defined
     * 
     * @return bool
     */
    public function hasModel(): bool
    {
        return !empty($this->model) && class_exists($this->model);
    }

    /**
     * Create a new instance of the model for this activity type
     * 
     * @return Model|null
     */
    public function newModelInstance(): ?Model
    {
        if (!$this->hasModel()) {
            return null;
        }

        $modelClass = $this->model;
        return new $modelClass();
    }
}
