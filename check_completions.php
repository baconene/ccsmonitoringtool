<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\LessonCompletion;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Course;

// Check all completions
$completions = LessonCompletion::all();
echo "Total Lesson Completions: " . count($completions) . "\n";

if (count($completions) > 0) {
    echo "Sample completions:\n";
    foreach ($completions->take(10) as $c) {
        echo "  - User: {$c->user_id}, Lesson: {$c->lesson_id}, Course: {$c->course_id}, Completed: {$c->completed_at}\n";
    }
} else {
    echo "No lesson completions found!\n\n";
    
    // Check how many students and lessons exist
    $studentCount = User::where('role_id', function($q) {
        $q->select('id')->from('roles')->where('name', 'student');
    })->count();
    
    echo "Total Students: {$studentCount}\n";
    
    $lessonCount = Lesson::count();
    echo "Total Lessons: {$lessonCount}\n";
    
    $courseCount = Course::count();
    echo "Total Courses: {$courseCount}\n";
}
