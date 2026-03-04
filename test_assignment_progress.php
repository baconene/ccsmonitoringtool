<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Assignment;
use App\Models\StudentAssignmentProgress;
use App\Models\StudentActivity;

// Get assignment 1 (which we can infer from the submissions list test)
$assignment = Assignment::find(1);

if (!$assignment) {
    echo "Assignment 1 not found\n";
    exit;
}

echo "Testing Assignment Submissions Recovery:\n";
echo "Assignment: {$assignment->title} (Activity ID: {$assignment->activity_id})\n\n";

// Get all StudentActivities for this assignment
$studentActivities = StudentActivity::where('activity_id', $assignment->activity_id)
    ->where('activity_type', 'assignment')
    ->get();

echo "Student Activities Count: " . count($studentActivities) . "\n\n";

foreach ($studentActivities as $sa) {
    echo "StudentActivity ID: {$sa->id} (Student: {$sa->student_id})\n";
    
    // Try to get the corresponding StudentAssignmentProgress
    $assignmentProgress = StudentAssignmentProgress::where('student_activity_id', $sa->id)->first();
    
    if ($assignmentProgress) {
        echo "  StudentAssignmentProgress ID: {$assignmentProgress->id}\n";
        echo "  Points Earned: " . ($assignmentProgress->points_earned ?? 'null') . "\n";
        echo "  Points Possible: " . ($assignmentProgress->points_possible ?? 'null') . "\n";
        echo "  Submission Status: " . ($assignmentProgress->submission_status ?? 'null') . "\n";
    } else {
        echo "  StudentAssignmentProgress: NOT FOUND\n";
    }
    
    echo "\n";
}
?>
