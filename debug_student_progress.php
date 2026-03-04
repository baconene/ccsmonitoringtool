<?php

// Debug script to check student 9's progress

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\LessonCompletion;

// Get student 9
$student = User::with(['role', 'enrolledCourses.lessons'])->find(9);

if (!$student) {
    echo "Student 9 not found\n";
    exit;
}

echo "Student: {$student->name} (ID: {$student->id})\n";
echo "Role: {$student->role?->name}\n\n";

// Get enrolled courses
$enrolledCourses = $student->enrolledCourses;
echo "Enrolled Courses: " . count($enrolledCourses) . "\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($enrolledCourses as $course) {
    $totalLessons = $course->lessons->count();
    
    // Count completed lessons
    $completedLessons = LessonCompletion::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->count();
    
    $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
    
    echo "Course: {$course->title}\n";
    echo "  - Course ID: {$course->id}\n";
    echo "  - Total Lessons: {$totalLessons}\n";
    echo "  - Completed Lessons: {$completedLessons}\n";
    echo "  - Progress: {$progress}%\n\n";
    
    // Check lesson completions directly
    $completions = LessonCompletion::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->get();
    
    echo "  - Completions in DB: " . count($completions) . "\n";
    
    if ($completions->count() > 0) {
        foreach ($completions as $completion) {
            echo "    - Lesson ID: {$completion->lesson_id}, Completed at: {$completion->completed_at}\n";
        }
    }
    
    echo "\n";
}

// Check all completions for this student
echo "\nAll Lesson Completions for Student {$student->id}:\n";
echo str_repeat("=", 80) . "\n\n";

$allCompletions = LessonCompletion::where('user_id', $student->id)->get();
echo "Total Completions: " . count($allCompletions) . "\n\n";

foreach ($allCompletions as $completion) {
    echo "Completion ID: {$completion->id}\n";
    echo "  - Lesson ID: {$completion->lesson_id}\n";
    echo "  - Course ID: {$completion->course_id}\n";
    echo "  - Completed At: {$completion->completed_at}\n";
    echo "  - Created At: {$completion->created_at}\n\n";
}
