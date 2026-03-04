<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     * Auto-link new activities to skills based on their modules.
     */
    public function created(Activity $activity): void
    {
        $this->linkActivityToSkills($activity);
    }

    /**
     * Handle the Activity "updated" event.
     * Sync skills if modules relationship changes.
     */
    public function updated(Activity $activity): void
    {
        if ($activity->wasChanged('id') || $activity->wasChanged(['created_at', 'updated_at'])) {
            // Only re-sync if meaningful changes occurred
            return;
        }

        $this->syncActivitySkills($activity);
    }

    /**
     * Link activity to all skills in its assigned modules
     */
    private function linkActivityToSkills(Activity $activity): void
    {
        // Get modules through the module_activities pivot if it exists
        $modules = $activity->modules()->get();

        if ($modules->isEmpty()) {
            return;
        }

        // Collect all skills from all modules
        $skillIds = [];
        foreach ($modules as $module) {
            $moduleSkills = $module->skills()->pluck('skills.id');
            $skillIds = array_merge($skillIds, $moduleSkills->toArray());
        }

        // Remove duplicates
        $skillIds = array_unique($skillIds);

        if (!empty($skillIds)) {
            // Create pivot records with default weight
            $pivotData = [];
            foreach ($skillIds as $skillId) {
                $pivotData[$skillId] = ['weight' => 1.0]; // Default weight
            }

            $activity->skills()->sync($pivotData);
        }
    }

    /**
     * Sync skills when activity-module relationships change
     */
    private function syncActivitySkills(Activity $activity): void
    {
        // Re-link skills based on current modules
        $this->linkActivityToSkills($activity);
    }
}
