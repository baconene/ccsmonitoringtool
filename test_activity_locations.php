<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Activity;
use App\Models\StudentActivity;

// Check activities 9 and 11
$activities = Activity::whereIn('id', [9, 11])->get();
echo "Activities 9 and 11:\n";
foreach ($activities as $activity) {
    echo "  Activity {$activity->id}:\n";
    echo "    Title: {$activity->title}\n";
    echo "    Module ID: {$activity->module_id}\n";
    echo "    Activity Type: {$activity->activity_type_id}\n";
    echo "    Course: N/A (modules have courses, not activities)\n";
    
    // Get related module
    if ($activity->module_id) {
        $module = $activity->module;
        echo "    Module: {$module->title} (ID: {$module->id})\n";
        echo "    Course ID: {$module->course_id}\n";
    }
}

// Now check the student activity records
echo "\nStudent Activities (Student 16):\n";
$studentActivities = StudentActivity::where('student_id', 16)->get();
foreach ($studentActivities as $sa) {
    echo "  Student Activity:\n";
    echo "    Activity ID: {$sa->activity_id}\n";
    echo "    Module ID: {$sa->module_id}\n";
    echo "    Status: {$sa->status}\n";
    echo "    Score: {$sa->score}\n";
}
?>
