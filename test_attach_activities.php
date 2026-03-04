<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Activity;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

// Activities need to be attached to modules
// Activity 9 → Module 8
// Activity 11 → Module 9

$assignments = [
    9 => 8,
    11 => 9
];

echo "Attaching activities to modules:\n";
foreach ($assignments as $activityId => $moduleId) {
    $activity = Activity::find($activityId);
    $module = Module::find($moduleId);
    
    if ($activity && $module) {
        // Check if already attached
        if (!$activity->modules()->where('module_id', $moduleId)->exists()) {
            $activity->modules()->attach($moduleId);
            echo "  ✓ Activity {$activityId} attached to Module {$moduleId}\n";
        } else {
            echo "  - Activity {$activityId} already attached to Module {$moduleId}\n";
        }
    } else {
        echo "  ✗ Activity {$activityId} or Module {$moduleId} not found\n";
    }
}

// Verify
echo "\nVerifying module_activities pivot:\n";
$pivot = DB::table('module_activities')->whereIn('activity_id', [9, 11])->get();
foreach ($pivot as $row) {
    echo "  Activity {$row->activity_id} ↔ Module {$row->module_id}\n";
}

// Check modules again
echo "\nModules with activities:\n";
$modules = Module::whereIn('id', [8, 9, 10])->get();
foreach ($modules as $module) {
    $count = $module->activities()->count();
    echo "  Module {$module->id} ({$module->title}): {$count} activities\n";
    if ($count > 0) {
        foreach ($module->activities as $activity) {
            echo "    - Activity {$activity->id}: {$activity->title}\n";
        }
    }
}

echo "\nNow testing assessment again...\n";
$service = new \App\Services\StudentAssessmentService();
$student = \App\Models\Student::find(16);
$assessment = $service->calculateStudentAssessment($student);

echo "Assessment for Student 16:\n";
echo "  Overall Score: " . $assessment['overall_score'] . "\n";
echo "  Readiness Level: " . $assessment['readiness_level'] . "\n";
echo "  Courses: " . count($assessment['courses']) . "\n";
if (!empty($assessment['courses'])) {
    foreach ($assessment['courses'] as $course) {
        echo "    - " . $course['name'] . ": " . $course['score'] . "\n";
    }
}
?>
