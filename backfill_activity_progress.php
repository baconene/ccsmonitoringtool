<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\Activity;

echo "=== Backfilling Missing student_activity_progress Records ===\n\n";

// Find all student_activity records that don't have corresponding progress records
$studentActivities = StudentActivity::whereNotExists(function ($query) {
    $query->select(\DB::raw(1))
        ->from('student_activity_progress')
        ->whereColumn('student_activity_progress.student_activity_id', 'student_activities.id');
})
->with('activity.activityType')
->get();

echo "Found {$studentActivities->count()} student_activity records without progress records\n\n";

if ($studentActivities->count() === 0) {
    echo "No records to backfill. All student_activity records have corresponding progress records.\n";
    exit(0);
}

$created = 0;
$errors = [];

foreach ($studentActivities as $studentActivity) {
    try {
        $activity = $studentActivity->activity;
        if (!$activity) {
            $errors[] = "StudentActivity ID {$studentActivity->id}: Activity not found";
            continue;
        }

        $activityType = $activity->activityType;
        $activityTypeName = $activityType ? strtolower($activityType->name) : 'assessment';
        
        // Determine progress percentage based on status
        $progressPercentage = 0;
        if ($studentActivity->status === 'completed' || $studentActivity->status === 'graded') {
            $progressPercentage = 100;
        } elseif ($studentActivity->status === 'in_progress' || $studentActivity->status === 'submitted') {
            $progressPercentage = 50;
        }

        StudentActivityProgress::create([
            'student_activity_id' => $studentActivity->id,
            'student_id' => $studentActivity->student_id,
            'activity_id' => $activity->id,
            'activity_type' => $activityTypeName,
            'status' => $studentActivity->status,
            'progress_percentage' => $progressPercentage,
            'score' => $studentActivity->score,
            'percentage_score' => $studentActivity->percentage_score,
            'points_earned' => $studentActivity->score,
            'completed_items' => $progressPercentage === 100 ? 1 : 0,
            'total_items' => 1,
            'requires_grading' => false,
            'started_at' => $studentActivity->started_at,
            'completed_at' => $studentActivity->completed_at,
            'last_activity_at' => $studentActivity->updated_at,
        ]);

        $created++;
        
        echo "✓ Created progress for StudentActivity ID {$studentActivity->id} ";
        echo "(Student: {$studentActivity->student_id}, Activity: {$activity->id} - {$activity->title}, ";
        echo "Status: {$studentActivity->status}, Progress: {$progressPercentage}%)\n";

    } catch (\Exception $e) {
        $errors[] = "StudentActivity ID {$studentActivity->id}: " . $e->getMessage();
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "Summary:\n";
echo "- Records found: {$studentActivities->count()}\n";
echo "- Records created: {$created}\n";
echo "- Errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  ✗ {$error}\n";
    }
}

echo "\nBackfill complete!\n";
