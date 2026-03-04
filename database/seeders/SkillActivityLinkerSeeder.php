<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Skill;
use App\Models\Module;

class SkillActivityLinkerSeeder extends Seeder
{
    /**
     * Auto-link activities to skills based on module relationships.
     *
     * This seeder creates relationships between activities and skills by:
     * 1. Finding all activities
     * 2. For each activity, getting its modules
     * 3. Linking the activity to all skills in those modules
     */
    public function run(): void
    {
        $this->command->info('🔗 Linking activities to skills...');

        // Clear existing relationships
        DB::table('skill_activities')->truncate();

        $activities = Activity::with('modules.skills')->get();
        $totalLinks = 0;

        foreach ($activities as $activity) {
            $modules = $activity->modules;

            if ($modules->isEmpty()) {
                $this->command->warn("  ⚠ Activity '{$activity->title}' (ID: {$activity->id}) has no modules - skipping");
                continue;
            }

            // Get all skills from all modules this activity belongs to
            $skills = $modules->flatMap(function ($module) {
                return $module->skills;
            })->unique('id');

            if ($skills->isEmpty()) {
                $this->command->warn("  ⚠ Activity '{$activity->title}' (ID: {$activity->id}) - no skills found in its modules");
                continue;
            }

            // Link activity to skills
            $skillData = [];
            foreach ($skills as $skill) {
                $skillData[$skill->id] = ['weight' => 1.0]; // Default weight of 1.0
            }

            $activity->skills()->sync($skillData);
            $totalLinks += count($skillData);

            $this->command->info("  ✓ Linked activity '{$activity->title}' to {$skills->count()} skill(s)");
        }

        $this->command->info("✅ Total skill-activity links created: {$totalLinks}");

        // Display summary
        $this->displaySummary();
    }

    /**
     * Display a summary of the linking results
     */
    private function displaySummary(): void
    {
        $this->command->info("\n📊 Summary:");

        $totalActivities = Activity::count();
        $activitiesWithSkills = Activity::has('skills')->count();
        $activitiesWithoutSkills = $totalActivities - $activitiesWithSkills;

        $this->command->info("  Total activities: {$totalActivities}");
        $this->command->info("  Activities with skills: {$activitiesWithSkills}");

        if ($activitiesWithoutSkills > 0) {
            $this->command->warn("  Activities without skills: {$activitiesWithoutSkills}");
        }

        $totalSkills = Skill::count();
        $skillsWithActivities = Skill::has('activities')->count();
        $skillsWithoutActivities = $totalSkills - $skillsWithActivities;

        $this->command->info("  Total skills: {$totalSkills}");
        $this->command->info("  Skills with activities: {$skillsWithActivities}");

        if ($skillsWithoutActivities > 0) {
            $this->command->warn("  Skills without activities: {$skillsWithoutActivities}");
        }
    }
}
