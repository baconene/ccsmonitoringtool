<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\Activity;

echo "=== Checking Activity 13 Data ===\n\n";

// Get Activity 13 details
$activity = Activity::find(13);
if ($activity) {
    echo "Activity 13 Details:\n";
    echo "- ID: {$activity->id}\n";
    echo "- Title: {$activity->title}\n";
    echo "- Type: {$activity->activity_type}\n";
    echo "- Points: {$activity->points}\n";
    echo "- Is Graded: " . ($activity->is_graded ? 'Yes' : 'No') . "\n";
    echo "- Created: {$activity->created_at}\n\n";
} else {
    echo "Activity 13 not found!\n\n";
}

// Check student_activity table
echo "=== student_activity Records for Activity 13 ===\n";
$studentActivities = StudentActivity::where('activity_id', 13)
    ->with('student:id,name,email')
    ->orderBy('student_id')
    ->get();

if ($studentActivities->count() > 0) {
    foreach ($studentActivities as $sa) {
        echo "\nStudent: {$sa->student->name} (ID: {$sa->student_id})\n";
        echo "- Status: {$sa->status}\n";
        echo "- Score: " . ($sa->score ?? 'NULL') . "\n";
        echo "- Percentage: " . ($sa->percentage_score ?? 'NULL') . "%\n";
        echo "- Started At: " . ($sa->started_at ?? 'NULL') . "\n";
        echo "- Completed At: " . ($sa->completed_at ?? 'NULL') . "\n";
        echo "- Graded At: " . ($sa->graded_at ?? 'NULL') . "\n";
        echo "- Created: {$sa->created_at}\n";
        echo "- Updated: {$sa->updated_at}\n";
    }
} else {
    echo "No records found in student_activity for Activity 13\n";
}

echo "\n" . str_repeat("=", 50) . "\n\n";

// Check student_activity_progress table
echo "=== student_activity_progress Records for Activity 13 ===\n";
$progressRecords = StudentActivityProgress::where('activity_id', 13)
    ->with('student:id,name,email')
    ->orderBy('student_id')
    ->get();

if ($progressRecords->count() > 0) {
    foreach ($progressRecords as $progress) {
        echo "\nStudent: {$progress->student->name} (ID: {$progress->student_id})\n";
        echo "- Status: {$progress->status}\n";
        echo "- Progress: {$progress->progress_percentage}%\n";
        echo "- Points Earned: " . ($progress->points_earned ?? 'NULL') . "\n";
        echo "- Score: " . ($progress->score ?? 'NULL') . "\n";
        echo "- Percentage: " . ($progress->percentage_score ?? 'NULL') . "%\n";
        echo "- Items Completed: {$progress->completed_items}/{$progress->total_items}\n";
        echo "- Started At: " . ($progress->started_at ?? 'NULL') . "\n";
        echo "- Completed At: " . ($progress->completed_at ?? 'NULL') . "\n";
        echo "- Last Activity: " . ($progress->last_activity_at ?? 'NULL') . "\n";
        echo "- Created: {$progress->created_at}\n";
        echo "- Updated: {$progress->updated_at}\n";
    }
} else {
    echo "No records found in student_activity_progress for Activity 13\n";
}

echo "\n" . str_repeat("=", 50) . "\n\n";

// Check which students have records
$studentsWithActivity = $studentActivities->pluck('student_id')->unique()->toArray();
$studentsWithProgress = $progressRecords->pluck('student_id')->unique()->toArray();

echo "Students with student_activity records: " . implode(', ', $studentsWithActivity) . "\n";
echo "Students with student_activity_progress records: " . implode(', ', $studentsWithProgress) . "\n\n";

$missingProgress = array_diff($studentsWithActivity, $studentsWithProgress);

if (count($missingProgress) > 0) {
    echo "Students in student_activity but MISSING from student_activity_progress: " . implode(', ', $missingProgress) . "\n";
} else {
    echo "All students with student_activity have corresponding student_activity_progress records.\n";
}

echo "\n=== Summary ===\n";
echo "Activity Type: {$activity->activity_type}\n";
echo "Records in student_activity: " . $studentActivities->count() . "\n";
echo "Records in student_activity_progress: " . $progressRecords->count() . "\n";
echo "Missing student_activity_progress records: " . count($missingProgress) . "\n";
