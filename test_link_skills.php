<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Activity;
use Illuminate\Support\Facades\DB;

echo "Linking activities to module skills:\n\n";

// Activity 9 (Module 8) → Skill 22
$activity9 = Activity::find(9);
if ($activity9) {
    if (!$activity9->skills()->where('skill_id', 22)->exists()) {
        $activity9->skills()->attach(22, ['weight' => 1.0]);
        echo "✓ Activity 9 linked to Skill 22 (TESTSKILL)\n";
    } else {
        echo "- Activity 9 already linked to Skill 22\n";
    }
}

// Activity 11 (Module 9) → Skill 23
$activity11 = Activity::find(11);
if ($activity11) {
    if (!$activity11->skills()->where('skill_id', 23)->exists()) {
        $activity11->skills()->attach(23, ['weight' => 1.0]);
        echo "✓ Activity 11 linked to Skill 23 (TEST2)\n";
    } else {
        echo "- Activity 11 already linked to Skill 23\n";
    }
}

// Verify
echo "\nVerifying skill_activities linking:\n";
$pivots = DB::table('skill_activities')->whereIn('activity_id', [9, 11])->get();
foreach ($pivots as $pivot) {
    echo "  Skill {$pivot->skill_id} ↔ Activity {$pivot->activity_id}\n";
}

// Test assessment again
echo "\nTesting assessment for Student 16:\n";
$service = new \App\Services\StudentAssessmentService();
$student = \App\Models\Student::find(16);
$assessment = $service->calculateStudentAssessment($student);

echo "Overall Score: " . $assessment['overall_score'] . "\n";
echo "Readiness Level: " . $assessment['readiness_level'] . "\n";
echo "Courses: " . count($assessment['courses']) . "\n";

foreach ($assessment['courses'] as $course) {
    echo "  Course {$course['course_id']} ({$course['course_name']}): {$course['score']}\n";
    foreach ($course['modules'] as $module) {
        echo "    Module {$module['module_id']} ({$module['module_name']}): {$module['score']}\n";
        foreach ($module['skills'] as $skill) {
            echo "      Skill {$skill['skill_id']} ({$skill['skill_name']}): {$skill['score']} ({$skill['mastery_level']})\n";
        }
    }
}
?>
