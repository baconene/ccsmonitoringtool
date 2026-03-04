<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use App\Models\CourseEnrollment;
use App\Models\StudentActivityProgress;
use App\Models\StudentActivity;

$activity = Activity::find(3);
$activity->load([
    'activityType',
    'modules.course'
]);

echo "=== Activity 3 Debug ===\n";
echo "Activity: " . $activity->title . " (" . $activity->activityType->name . ")\n";
echo "Modules: " . json_encode($activity->modules->pluck('id')->toArray()) . "\n";

$courseId = $activity->modules()->value('course_id');
echo "Course ID: " . $courseId . "\n";

if (!$courseId) {
    echo "ERROR: No course ID found\n";
    exit;
}

$enrollments = CourseEnrollment::with(['student.user', 'user'])
    ->where('course_id', $courseId)
    ->get();

echo "\nEnrollments: " . $enrollments->count() . "\n";

foreach ($enrollments as $enrollment) {
    $student = $enrollment->student;
    if (!$student && $enrollment->user_id) {
        $student = \App\Models\Student::with('user')
            ->where('user_id', $enrollment->user_id)
            ->first();
    }
    
    $studentId = $student?->id;
    echo "\n--- Student: " . ($student?->user?->name ?? 'Unknown') . " (ID: $studentId) ---\n";
    
    // Check StudentActivityProgress
    $progress = StudentActivityProgress::where('student_id', $studentId)
        ->where('activity_id', $activity->id)
        ->latest('updated_at')
        ->first();
    
    if ($progress) {
        echo "StudentActivityProgress found:\n";
        echo "  - Status: " . $progress->status . "\n";
        echo "  - Score: " . $progress->score . "\n";
        echo "  - Progress %: " . $progress->progress_percentage . "\n";
    } else {
        echo "StudentActivityProgress: NOT FOUND\n";
    }
    
    // Check StudentActivity
    $studentActivity = StudentActivity::where('student_id', $studentId)
        ->where('activity_id', $activity->id)
        ->where('course_id', $courseId)
        ->first();
    
    if ($studentActivity) {
        echo "StudentActivity found:\n";
        echo "  - Status: " . $studentActivity->status . "\n";
        echo "  - Score: " . $studentActivity->score . "\n";
    } else {
        echo "StudentActivity: NOT FOUND\n";
    }
}
