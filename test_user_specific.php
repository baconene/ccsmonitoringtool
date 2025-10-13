<?php

require_once 'vendor/autoload.php';

$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Exact test case from user
$result = \App\Models\Student::where('user_id', 23)->first()->enrolledCourses;

echo "Result of Student::where('user_id',23)->first()->enrolledCourses:\n";
echo str_repeat('-', 60) . "\n";

if ($result === null) {
    echo "Result: null\n";
} else {
    echo "Result: Collection with " . $result->count() . " courses\n";
    echo "Courses:\n";
    foreach ($result as $course) {
        echo "  - ID: " . $course->id . ", Title: " . $course->title . "\n";
    }
}