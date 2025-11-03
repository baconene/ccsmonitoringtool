<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivityProgress;
use App\Models\StudentActivity;
use Illuminate\Support\Facades\DB;

echo "=== Checking TEST ASSESSMENT Status for All Students ===\n\n";

// Get TEST ASSESSMENT activity
$activity = \App\Models\Activity::where('title', 'TEST ASSESSMENT')->first();

if (!$activity) {
    echo "TEST ASSESSMENT not found!\n";
    exit(1);
}

echo "Activity ID: {$activity->id}\n";
echo "Activity Title: {$activity->title}\n\n";

// Get all progress records
$progressRecords = StudentActivityProgress::where('activity_id', $activity->id)
    ->with('student')
    ->get();

foreach ($progressRecords as $progress) {
    $studentActivity = StudentActivity::where('student_id', $progress->student_id)
        ->where('activity_id', $activity->id)
        ->first();
    
    echo "Student ID: {$progress->student_id}\n";
    if ($progress->student) {
        echo "Student Name: Student User {$progress->student_id}\n";
    }
    echo "\nProgress Record:\n";
    echo "  - is_completed: " . ($progress->is_completed ? 'true' : 'false') . "\n";
    echo "  - is_submitted: " . ($progress->is_submitted ? 'true' : 'false') . "\n";
    echo "  - status: {$progress->status}\n";
    echo "  - percentage_score: {$progress->percentage_score}%\n";
    
    if ($studentActivity) {
        echo "\nStudent Activity Record:\n";
        echo "  - status: {$studentActivity->status}\n";
        echo "  - completed_at: " . ($studentActivity->completed_at ?? 'NULL') . "\n";
    } else {
        echo "\nStudent Activity Record: NOT FOUND\n";
    }
    
    // Calculate status based on current logic
    $calculatedStatus = 'not-taken';
    if ($progress->is_completed && $progress->is_submitted) {
        $calculatedStatus = 'completed';
    } elseif ($progress->started_at) {
        $calculatedStatus = 'in-progress';
    }
    
    echo "\nCalculated Frontend Status: {$calculatedStatus}\n";
    echo "Expected Status: completed\n";
    
    if ($calculatedStatus !== 'completed') {
        echo "⚠️  MISMATCH! Should be completed!\n";
    }
    
    echo "\n" . str_repeat("-", 70) . "\n\n";
}
