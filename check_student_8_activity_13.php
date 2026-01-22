<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;

echo "=== Checking Student 8 (User 9) Activity 13 Data ===\n\n";

// Student User 8 in the screenshot
$userId = 9; // User ID
$activityId = 13;

// Get student
$student = \App\Models\User::find($userId)?->student;

if (!$student) {
    echo "Student not found for user ID {$userId}\n";
    exit(1);
}

echo "Student ID: {$student->id}\n";
echo "User ID: {$userId}\n\n";

// Get student_activity
$studentActivity = StudentActivity::where('student_id', $student->id)
    ->where('activity_id', $activityId)
    ->first();

echo "=== student_activity ===\n";
if ($studentActivity) {
    echo "ID: {$studentActivity->id}\n";
    echo "Status: {$studentActivity->status}\n";
    echo "Score: " . ($studentActivity->score ?? 'NULL') . "\n";
    echo "Percentage: " . ($studentActivity->percentage_score ?? 'NULL') . "%\n";
    echo "Completed At: " . ($studentActivity->completed_at ?? 'NULL') . "\n";
} else {
    echo "No student_activity record found\n";
}

echo "\n=== student_activity_progress ===\n";
$activityProgress = StudentActivityProgress::where('student_id', $student->id)
    ->where('activity_id', $activityId)
    ->first();

if ($activityProgress) {
    echo "ID: {$activityProgress->id}\n";
    echo "Status: {$activityProgress->status}\n";
    echo "is_completed: " . ($activityProgress->is_completed ? 'true' : 'false') . "\n";
    echo "Progress: {$activityProgress->progress_percentage}%\n";
    echo "Score: " . ($activityProgress->score ?? 'NULL') . "\n";
    echo "Percentage: " . ($activityProgress->percentage_score ?? 'NULL') . "%\n";
    echo "Completed At: " . ($activityProgress->completed_at ?? 'NULL') . "\n";
} else {
    echo "No student_activity_progress record found\n";
}

echo "\n=== Grade Calculator Logic ===\n";
if ($activityProgress) {
    $status = $activityProgress->is_completed ? 'completed' : 'in_progress';
    echo "Calculated status: {$status}\n";
    echo "is_completed: " . ($activityProgress->is_completed ? 'true' : 'false') . "\n";
    echo "This is WHY it shows 'IN PROGRESS' in the report!\n";
} elseif ($studentActivity) {
    echo "Would use studentActivity->status: {$studentActivity->status}\n";
}
