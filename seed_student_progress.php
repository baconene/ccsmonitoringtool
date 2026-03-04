<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\LessonCompletion;
use App\Models\Lesson;

// Get student 9
$student = User::find(9);

if (!$student) {
    echo "Student 9 not found!\n";
    exit;
}

echo "Marking lessons as complete for: {$student->name} (ID: {$student->id})\n\n";

// Get all enrolled courses for this student
$enrolledCourses = $student->enrolledCourses;

echo "Processing " . count($enrolledCourses) . " enrolled courses...\n\n";

$totalCreated = 0;

foreach ($enrolledCourses as $course) {
    $lessons = $course->lessons;
    $lessonsToComplete = ceil($lessons->count() * 0.66); // Complete 66% of lessons
    
    echo "Course: {$course->title}\n";
    echo "  - Total Lessons: {$lessons->count()}\n";
    echo "  - Will complete: {$lessonsToComplete} lessons\n";
    
    // Get random lessons to complete
    $lessonsShuffled = $lessons->shuffle();
    $lessonsToCompleteCollection = $lessonsShuffled->take($lessonsToComplete);
    
    foreach ($lessonsToCompleteCollection as $lesson) {
        // Check if already completed
        $existing = LessonCompletion::where('user_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->where('course_id', $course->id)
            ->first();
        
        if (!$existing) {
            LessonCompletion::create([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
                'course_id' => $course->id,
                'completed_at' => now()->subDays(rand(0, 30)),
            ]);
            
            echo "  ✓ Completed: {$lesson->title}\n";
            $totalCreated++;
        } else {
            echo "  - Already completed: {$lesson->title}\n";
        }
    }
    
    echo "\n";
}

echo "==========================================\n";
echo "Total new completions created: {$totalCreated}\n";
echo "\nRun the following to verify:\n";
echo "Visit: http://127.0.0.1:8000/student/9/details\n";
