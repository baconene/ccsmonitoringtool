<?php

namespace App\Observers;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

class ModuleObserver
{
    /**
     * Handle the Module "created" event.
     * When a module is created, link its skills to all activities in that module
     */
    public function created(Module $module): void
    {
        $this->linkModuleSkillsToActivities($module);
    }

    /**
     * Handle the Module "updated" event.
     * When module skills are updated, sync them with all module activities
     */
    public function updated(Module $module): void
    {
        if ($module->wasChanged('title') || $module->wasChanged('description')) {
            // Only these changes shouldn't trigger skill sync
            return;
        }

        $this->syncModuleSkillsToActivities($module);
    }

    /**
     * Link all skills in this module to all activities in this module
     */
    private function linkModuleSkillsToActivities(Module $module): void
    {
        // Get all skills in this module
        $moduleSkills = $module->skills()->pluck('skills.id');

        if ($moduleSkills->isEmpty()) {
            return;
        }

        // Get all activities in this module
        $activities = $module->activities()->get();

        foreach ($activities as $activity) {
            // Create or update skill-activity links
            $pivotData = [];
            foreach ($moduleSkills as $skillId) {
                $pivotData[$skillId] = ['weight' => 1.0];
            }

            // Sync without detaching (preserve existing relationships with other skills)
            $activity->skills()->syncWithoutDetaching($pivotData);
        }
    }

    /**
     * Sync skills when module structure changes
     */
    private function syncModuleSkillsToActivities(Module $module): void
    {
        $this->linkModuleSkillsToActivities($module);
    }
}
