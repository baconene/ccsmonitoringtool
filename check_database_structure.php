<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Database Structure Check ===\n\n";

// Get activities
echo "Activities in the system:\n";
$activities = DB::table('activities')
    ->limit(5)
    ->get();

if ($activities->count() > 0) {
    $first = $activities->first();
    echo "Columns in activities table:\n";
    foreach ($first as $column => $value) {
        echo "  - {$column}\n";
    }
    
    echo "\nSample activities:\n";
    foreach ($activities as $activity) {
        $activityType = DB::table('activity_types')->where('id', $activity->activity_type_id)->value('name');
        echo "  - ID {$activity->id}: {$activity->title} (Type: {$activityType})\n";
    }
}

echo "\n";

// Get skills
echo "Skills in the system:\n";
$skills = DB::table('skills')->get();

if ($skills->count() > 0) {
    foreach ($skills as $skill) {
        $module = DB::table('modules')->where('id', $skill->module_id)->value('title');
        echo "  - ID {$skill->id}: {$skill->name} (Module: {$module})\n";
    }
} else {
    echo "  No skills found!\n";
}

echo "\n";

echo "skill_activities table status:\n";
echo "  Records: " . DB::table('skill_activities')->count() . "\n\n";

echo "⚠ PROBLEM IDENTIFIED:\n";
echo "  The skill_activities pivot table is EMPTY.\n";
echo "  Activities exist, skills exist, but they are not linked together.\n";
echo "  The assessment service needs these links to calculate skill scores.\n\n";

echo "SOLUTION:\n";
echo "  You need to populate the skill_activities table by:\n";
echo "  1. Manually linking activities to skills through the admin interface, OR\n";
echo "  2. Running a seeder to create the associations, OR\n";
echo "  3. Using a migration/script to auto-link activities to skills based on module matching\n";
