<?php

require_once 'vendor/autoload.php';

$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Student Relationships Fix\n";
echo str_repeat('=', 50) . "\n";

// Test the specific query the user mentioned
$student = \App\Models\Student::where('user_id', 23)->first();

if ($student) {
    echo "Student found:\n";
    echo "- ID: " . $student->id . "\n";
    echo "- Name: " . $student->name . "\n";
    echo "- User ID: " . $student->user_id . "\n\n";
    
    echo "Testing relationships:\n";
    
    try {
        echo "Course Enrollments count: " . $student->courseEnrollments->count() . "\n";
        
        foreach ($student->courseEnrollments as $enrollment) {
            echo "  - Course: " . ($enrollment->course->title ?? 'N/A') . "\n";
            echo "    Progress: " . ($enrollment->progress ?? 'N/A') . "\n";
            echo "    Status: " . ($enrollment->status ?? 'N/A') . "\n\n";
        }
    } catch (Exception $e) {
        echo "Error with courseEnrollments: " . $e->getMessage() . "\n\n";
    }
    
    try {
        echo "Enrolled Courses count: " . $student->enrolledCourses->count() . "\n";
        
        foreach ($student->enrolledCourses as $course) {
            echo "  - " . $course->title . "\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Error with enrolledCourses: " . $e->getMessage() . "\n\n";
    }
    
    try {
        echo "Courses (pivot) count: " . $student->courses->count() . "\n";
        
        foreach ($student->courses as $course) {
            echo "  - " . $course->title . " (Status: " . $course->pivot->status . ")\n";
        }
        echo "\n";
    } catch (Exception $e) {
        echo "Error with courses: " . $e->getMessage() . "\n\n";
    }
    
} else {
    echo "No student found with user_id 23\n";
    echo "Let's check what students exist:\n";
    
    $students = \App\Models\Student::take(5)->get();
    foreach ($students as $s) {
        echo "- Student ID: {$s->id}, User ID: {$s->user_id}, Name: {$s->name}\n";
    }
}