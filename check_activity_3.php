<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activity = \App\Models\Activity::find(3);
if ($activity) {
    echo "Activity ID: " . $activity->id . "\n";
    echo "Activity Title: " . $activity->title . "\n";
    echo "Activity Type: " . $activity->activityType->name . "\n";
    echo "Modules Count: " . $activity->modules->count() . "\n";
    echo "Module IDs: " . json_encode($activity->modules->pluck('id')->toArray()) . "\n";
    
    // Check if it has enrollments in related courses
    $courseIds = $activity->modules->pluck('course_id')->unique();
    echo "Course IDs: " . json_encode($courseIds->toArray()) . "\n";
    
    if ($courseIds->count() > 0) {
        $enrollments = \App\Models\CourseEnrollment::whereIn('course_id', $courseIds)->count();
        echo "Enrollments in courses: " . $enrollments . "\n";
    }
} else {
    echo "Activity 3 not found\n";
}
