<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Skill-Activity Relationship Check ===\n\n";

// Try to get data from skill_activities table
try {
    $count = DB::table('skill_activities')->count();
    echo "✓ skill_activities table exists\n";
    echo "Total records in skill_activities: {$count}\n\n";
    
    if ($count > 0) {
        echo "Sample records:\n";
        $samples = DB::table('skill_activities')->limit(10)->get();
        foreach ($samples as $record) {
            echo "  Skill ID: {$record->skill_id}, Activity ID: {$record->activity_id}\n";
        }
    } else {
        echo "⚠ skill_activities table is EMPTY! This is the problem.\n\n";
        
        // Check total skills and activities
        $skillCount = DB::table('skills')->count();
        $activityCount = DB::table('activities')->count();
        
        echo "Total Skills: {$skillCount}\n";
        echo "Total Activities: {$activityCount}\n\n";
        
        echo "Activities in the system:\n";
        $activities = DB::table('activities')
            ->select('id', 'title', 'activity_type', 'module_id')
            ->limit(10)
            ->get();
        
        foreach ($activities as $activity) {
            echo "  - ID {$activity->id}: {$activity->title} (Type: {$activity->activity_type}, Module: {$activity->module_id})\n";
        }
        
        echo "\nSkills in the system:\n";
        $skills = DB::table('skills')
            ->select('id', 'name', 'module_id')
            ->get();
        
        foreach ($skills as $skill) {
            echo "  - ID {$skill->id}: {$skill->name} (Module: {$skill->module_id})\n";
        }
    }
} catch (Exception $e) {
    echo "✗ skill_activities table does NOT exist or error accessing it!\n";
    echo "   Error: {$e->getMessage()}\n";
}
