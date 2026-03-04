<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class GradeSetting extends Model
{
    protected $fillable = [
        'setting_type',
        'setting_key',
        'activity_type_id',
        'display_name',
        'weight_percentage',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    /**
     * Scopes
     */
    public function scopeModuleComponents($query)
    {
        return $query->where('setting_type', 'module_component')->where('is_active', true);
    }

    public function scopeActivityTypes($query)
    {
        return $query->where('setting_type', 'activity_type')->where('is_active', true);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('setting_key', $key);
    }

    /**
     * Static Methods for Getting Settings
     */
    public static function getModuleComponentWeights(): array
    {
        return Cache::remember('grade_settings.module_components', 3600, function () {
            return self::moduleComponents()
                ->pluck('weight_percentage', 'setting_key')
                ->toArray();
        });
    }

    public static function getActivityTypeWeights(): array
    {
        return Cache::remember('grade_settings.activity_types', 3600, function () {
            return self::activityTypes()
                ->pluck('weight_percentage', 'setting_key')
                ->toArray();
        });
    }

    public static function getActivityTypeWeight(string $typeName): float
    {
        $weights = self::getActivityTypeWeights();
        return $weights[$typeName] ?? 0;
    }

    public static function clearCache(): void
    {
        Cache::forget('grade_settings.module_components');
        Cache::forget('grade_settings.activity_types');
    }

    /**
     * Boot method to clear cache on updates
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Validation: Check if module component weights total 100%
     */
    public static function validateModuleComponentWeights(array $weights): bool
    {
        $total = array_sum($weights);
        return abs($total - 100) < 0.01; // Allow for floating point precision
    }

    /**
     * Validation: Check if activity type weights total 100%
     */
    public static function validateActivityTypeWeights(array $weights): bool
    {
        $total = array_sum($weights);
        return abs($total - 100) < 0.01;
    }

    /**
     * Sync activity type settings with activity_types table
     * Creates settings for any new activity types that don't have settings yet
     */
    public static function syncActivityTypeSettings(?int $createdBy = null): int
    {
        $activityTypes = ActivityType::all();
        $existingSettings = self::where('setting_type', 'activity_type')
            ->pluck('activity_type_id')
            ->filter()
            ->toArray();

        $created = 0;
        
        foreach ($activityTypes as $activityType) {
            if (!in_array($activityType->id, $existingSettings)) {
                // Get total existing weight to calculate default for new type
                $totalExistingWeight = self::where('setting_type', 'activity_type')
                    ->where('is_active', true)
                    ->sum('weight_percentage');
                
                // Default new types to 0, admin should configure
                $defaultWeight = 0.00;
                
                self::create([
                    'setting_type' => 'activity_type',
                    'setting_key' => $activityType->name,
                    'activity_type_id' => $activityType->id,
                    'display_name' => $activityType->name . ' Weight',
                    'weight_percentage' => $defaultWeight,
                    'description' => "Weight of {$activityType->name} in activity score calculation",
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]);
                
                $created++;
            }
        }

        if ($created > 0) {
            self::clearCache();
        }

        return $created;
    }

    /**
     * Get all activity type settings dynamically from activity_types table
     */
    public static function getActivityTypeWeightsDynamic(): array
    {
        return Cache::remember('grade_settings.activity_types.dynamic', 3600, function () {
            // Sync first to ensure all activity types have settings
            self::syncActivityTypeSettings();
            
            // Get weights indexed by activity type name
            return self::activityTypes()
                ->with('activityType')
                ->get()
                ->pluck('weight_percentage', 'setting_key')
                ->toArray();
        });
    }
}
