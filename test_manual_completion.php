<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\Activity;

echo "=== Testing Manual Activity Completion ===\n\n";

// Test with Student 1 and Activity 13
$studentId = 1;
$activityId = 13;

echo "Before marking complete:\n";
echo str_repeat("-", 50) . "\n";

$studentActivity = StudentActivity::where('student_id', $studentId)
    ->where('activity_id', $activityId)
    ->first();

$progress = StudentActivityProgress::where('student_id', $studentId)
    ->where('activity_id', $activityId)
    ->first();

if ($studentActivity) {
    echo "student_activity:\n";
    echo "  Status: {$studentActivity->status}\n";
    echo "  Score: " . ($studentActivity->score ?? 'NULL') . "\n";
    echo "  Completed At: " . ($studentActivity->completed_at ?? 'NULL') . "\n";
} else {
    echo "student_activity: NOT FOUND\n";
}

if ($progress) {
    echo "\nstudent_activity_progress:\n";
    echo "  Status: {$progress->status}\n";
    echo "  Progress: {$progress->progress_percentage}%\n";
    echo "  Completed At: " . ($progress->completed_at ?? 'NULL') . "\n";
} else {
    echo "\nstudent_activity_progress: NOT FOUND\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Simulating manual completion...\n";
echo str_repeat("=", 50) . "\n\n";

// Simulate the StudentActivityController::complete() logic
$activity = Activity::find($activityId);
if (!$activity) {
    echo "ERROR: Activity not found!\n";
    exit(1);
}

// Update student_activity
$updateData = [
    'status' => 'completed',
    'completed_at' => now(),
    'submitted_at' => now(),
    'started_at' => $studentActivity->started_at ?: now(),
    'score' => $studentActivity->score !== null ? $studentActivity->score : 0,
    'max_score' => $studentActivity->max_score ?: 0,
    'percentage_score' => $studentActivity->percentage_score !== null 
        ? $studentActivity->percentage_score 
        : ($studentActivity->max_score > 0 ? 0 : 100),
];

$studentActivity->update($updateData);

// Create or update student_activity_progress record
$activityType = $activity->activityType;
$activityTypeName = $activityType ? strtolower($activityType->name) : 'assessment';

StudentActivityProgress::updateOrCreate(
    [
        'student_activity_id' => $studentActivity->id,
        'student_id' => $studentId,
        'activity_id' => $activityId,
    ],
    [
        'activity_type' => $activityTypeName,
        'status' => 'completed',
        'progress_percentage' => 100,
        'score' => $studentActivity->score,
        'percentage_score' => $studentActivity->percentage_score,
        'points_earned' => $studentActivity->score,
        'completed_items' => 1,
        'total_items' => 1,
        'requires_grading' => false,
        'started_at' => $studentActivity->started_at,
        'completed_at' => now(),
        'last_activity_at' => now(),
    ]
);

echo "✓ Activity marked as complete!\n\n";

echo "After marking complete:\n";
echo str_repeat("-", 50) . "\n";

// Reload data
$studentActivity->refresh();
$progress = StudentActivityProgress::where('student_id', $studentId)
    ->where('activity_id', $activityId)
    ->first();

echo "student_activity:\n";
echo "  Status: {$studentActivity->status}\n";
echo "  Score: " . ($studentActivity->score ?? 'NULL') . "\n";
echo "  Percentage: " . ($studentActivity->percentage_score ?? 'NULL') . "%\n";
echo "  Completed At: {$studentActivity->completed_at}\n";

echo "\nstudent_activity_progress:\n";
if ($progress) {
    echo "  Status: {$progress->status}\n";
    echo "  Progress: {$progress->progress_percentage}%\n";
    echo "  Score: " . ($progress->score ?? 'NULL') . "\n";
    echo "  Percentage: " . ($progress->percentage_score ?? 'NULL') . "%\n";
    echo "  Completed At: {$progress->completed_at}\n";
    echo "  Items: {$progress->completed_items}/{$progress->total_items}\n";
    echo "\n✅ Progress record CREATED/UPDATED successfully!\n";
} else {
    echo "  ❌ ERROR: Progress record NOT created!\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test complete!\n";
