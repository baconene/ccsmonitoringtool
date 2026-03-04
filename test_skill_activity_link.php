<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Module;
use App\Models\Skill;
use App\Models\Activity;

echo "Checking skill-activity relationships:\n\n";

// Get modules for course 4
$modules = Module::where('course_id', 4)->get();
foreach ($modules as $module) {
    echo "Module {$module->id} ({$module->title}):\n";
    
    // Get skills in module
    $skills = $module->skills()->get();
    echo "  Skills: " . count($skills) . "\n";
    foreach ($skills as $skill) {
        echo "    - Skill {$skill->id}: {$skill->name}\n";
        
        // Get activities linked to this skill
        $activities = $skill->activities()->get();
        echo "      Activities for this skill: " . count($activities) . "\n";
        foreach ($activities as $activity) {
            echo "        - Activity {$activity->id}: {$activity->title}\n";
        }
    }
    
    // Get activities in module
    $moduleActivities = $module->activities()->get();
    echo "  Activities in module: " . count($moduleActivities) . "\n";
    foreach ($moduleActivities as $activity) {
        echo "    - Activity {$activity->id}: {$activity->title}\n";
        
        // Check what skills this activity is linked to
        $activitySkills = $activity->skills()->get();
        echo "      Skills for this activity: " . count($activitySkills) . "\n";
        foreach ($activitySkills as $skill) {
            echo "        - Skill {$skill->id}: {$skill->name}\n";
        }
    }
    
    echo "\n";
}

// Check the skill_activities pivot table
echo "Checking skill_activities pivot table:\n";
\Illuminate\Support\Facades\DB::table('skill_activities')->get()->each(function ($row) {
    echo "  Skill {$row->skill_id} ↔ Activity {$row->activity_id} (weight: {$row->weight})\n";
});
?>
