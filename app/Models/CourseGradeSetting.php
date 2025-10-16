<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CourseGradeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'setting_type',
        'setting_key',
        'display_name',
        'weight_percentage',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are saved or deleted
        static::saved(function ($setting) {
            $setting->clearCache();
        });

        static::deleted(function ($setting) {
            $setting->clearCache();
        });
    }

    /**
     * Relationships
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('setting_key', $key);
    }

    /**
     * Get module component weights for a specific course
     * Returns array like ['lessons' => 20, 'activities' => 80]
     * Falls back to global settings if no course-specific settings exist
     */
    public static function getModuleComponentWeights(int $courseId): array
    {
        $cacheKey = "course_grade_settings.{$courseId}.module_components";

        return Cache::remember($cacheKey, 3600, function () use ($courseId) {
            $settings = self::forCourse($courseId)
                ->moduleComponents()
                ->get();

            // If no course-specific settings, fall back to global settings
            if ($settings->isEmpty()) {
                return GradeSetting::getModuleComponentWeights();
            }

            return $settings->pluck('weight_percentage', 'setting_key')->toArray();
        });
    }

    /**
     * Get activity type weights for a specific course
     * Returns array like ['Quiz' => 30, 'Assignment' => 15, ...]
     * Falls back to global settings if no course-specific settings exist
     */
    public static function getActivityTypeWeights(int $courseId): array
    {
        $cacheKey = "course_grade_settings.{$courseId}.activity_types";

        return Cache::remember($cacheKey, 3600, function () use ($courseId) {
            $settings = self::forCourse($courseId)
                ->activityTypes()
                ->get();

            // If no course-specific settings, fall back to global settings
            if ($settings->isEmpty()) {
                return GradeSetting::getActivityTypeWeights();
            }

            return $settings->pluck('weight_percentage', 'setting_key')->toArray();
        });
    }

    /**
     * Copy global settings to a course
     */
    public static function copyGlobalSettingsToCourse(int $courseId, int $userId): void
    {
        // Get global module component settings
        $moduleSettings = GradeSetting::moduleComponents()->get();
        foreach ($moduleSettings as $setting) {
            self::updateOrCreate(
                [
                    'course_id' => $courseId,
                    'setting_type' => 'module_component',
                    'setting_key' => $setting->setting_key,
                ],
                [
                    'display_name' => $setting->display_name,
                    'weight_percentage' => $setting->weight_percentage,
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        // Get global activity type settings
        $activitySettings = GradeSetting::activityTypes()->get();
        foreach ($activitySettings as $setting) {
            self::updateOrCreate(
                [
                    'course_id' => $courseId,
                    'setting_type' => 'activity_type',
                    'setting_key' => $setting->setting_key,
                ],
                [
                    'display_name' => $setting->display_name,
                    'weight_percentage' => $setting->weight_percentage,
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }
    }

    /**
     * Check if course has custom settings
     */
    public static function courseHasCustomSettings(int $courseId): bool
    {
        return self::forCourse($courseId)->exists();
    }

    /**
     * Clear cache for this course's settings
     */
    public function clearCache()
    {
        Cache::forget("course_grade_settings.{$this->course_id}.module_components");
        Cache::forget("course_grade_settings.{$this->course_id}.activity_types");
    }

    /**
     * Validate module component weights total 100%
     */
    public static function validateModuleComponentWeights(array $weights): bool
    {
        $total = array_sum($weights);
        return abs($total - 100) < 0.01; // Allow small floating point errors
    }

    /**
     * Validate activity type weights total 100%
     */
    public static function validateActivityTypeWeights(array $weights): bool
    {
        $total = array_sum($weights);
        return abs($total - 100) < 0.01;
    }
}