<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Activity;

// Update activities with correct module_id based on student_activity records
$updates = [
    9 => 8,  // Activity 9 should be in Module 8
    11 => 9  // Activity 11 should be in Module 9
];

echo "Updating activities with correct module assignments:\n";
foreach ($updates as $activityId => $moduleId) {
    Activity::where('id', $activityId)->update(['module_id' => $moduleId]);
    echo "  Activity {$activityId} → Module {$moduleId}\n";
}

// Verify the update
echo "\nVerifying updates:\n";
$activities = Activity::whereIn('id', [9, 11])->get();
foreach ($activities as $activity) {
    echo "  Activity {$activity->id}: Module ID = {$activity->module_id}\n";
}

// Now check if modules show the activities
echo "\nModules with activities:\n";
$modules = \App\Models\Module::whereIn('id', [8, 9, 10])->get();
foreach ($modules as $module) {
    $count = $module->activities()->count();
    echo "  Module {$module->id} ({$module->title}): {$count} activities\n";
    if ($count > 0) {
        foreach ($module->activities as $activity) {
            echo "    - Activity {$activity->id}: {$activity->title}\n";
        }
    }
}
?>
