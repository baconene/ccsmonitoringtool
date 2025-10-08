<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\ActivityType;

trait DynamicMessageTrait
{
    /**
     * Get a dynamic description for an activity
     * Falls back to activity type description if activity description is empty
     * 
     * @param Activity $activity
     * @param string|null $fallbackTemplate Template with {type} placeholder
     * @return string
     */
    public function getActivityDescription(Activity $activity, ?string $fallbackTemplate = null): string
    {
        // Use activity description if available
        if (!empty($activity->description)) {
            return $activity->description;
        }
        
        // Use activity type description if available
        if ($activity->activityType && !empty($activity->activityType->description)) {
            return $activity->activityType->description;
        }
        
        // Use fallback template with activity type name
        if ($fallbackTemplate && $activity->activityType) {
            return str_replace('{type}', $activity->activityType->name, $fallbackTemplate);
        }
        
        // Final fallback
        $typeName = $activity->activityType->name ?? 'activity';
        return "Complete this {$typeName} to progress in your learning.";
    }
    
    /**
     * Get a dynamic completion message for an activity
     * 
     * @param Activity $activity
     * @return string
     */
    public function getActivityCompletionMessage(Activity $activity): string
    {
        $typeName = $activity->activityType->name ?? 'activity';
        return "Complete the '{$activity->title}' {$typeName} to continue your progress.";
    }
    
    /**
     * Get a dynamic success message for completing an activity
     * 
     * @param Activity $activity
     * @return string
     */
    public function getActivitySuccessMessage(Activity $activity): string
    {
        $typeName = $activity->activityType->name ?? 'activity';
        return "'{$activity->title}' {$typeName} completed successfully!";
    }
    
    /**
     * Get a dynamic error message with model context
     * 
     * @param string $action
     * @param object $model
     * @param string $reason
     * @return string
     */
    public function getModelErrorMessage(string $action, object $model, string $reason = ''): string
    {
        $modelName = class_basename($model);
        $title = $model->title ?? $model->name ?? 'item';
        
        $message = "Cannot {$action} '{$title}' {$modelName}";
        
        if ($reason) {
            $message .= ". {$reason}";
        }
        
        return $message;
    }
    
    /**
     * Get a dynamic success message for model operations
     * 
     * @param string $action (created, updated, deleted)
     * @param object $model
     * @return string
     */
    public function getModelSuccessMessage(string $action, object $model): string
    {
        $modelName = strtolower(class_basename($model));
        $title = $model->title ?? $model->name ?? 'item';
        
        return "'{$title}' {$modelName} {$action} successfully!";
    }
}