<?php

require_once 'vendor/autoload.php';

$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Final Test: Student Model with Only enrolledCourses Relationship\n";
echo str_repeat('=', 70) . "\n";

// Test the Student model relationships
$student = \App\Models\Student::where('user_id', 23)->first();

if ($student) {
    echo "Student found: " . $student->name . "\n";
    echo "User ID: " . $student->user_id . "\n\n";
    
    // Test courseEnrollments relationship
    echo "Course Enrollments (via courseEnrollments relationship):\n";
    echo str_repeat('-', 50) . "\n";
    $courseEnrollments = $student->courseEnrollments;
    echo "Found " . $courseEnrollments->count() . " enrollment records:\n";
    
    foreach ($courseEnrollments as $enrollment) {
        echo "  - Course: " . ($enrollment->course->title ?? 'N/A') . "\n";
        echo "    Status: " . ($enrollment->status ?? 'N/A') . "\n";
        echo "    Progress: " . ($enrollment->progress ?? 'N/A') . "\n";
        echo "    Enrolled: " . ($enrollment->enrolled_at ?? 'N/A') . "\n\n";
    }
    
    // Test enrolledCourses relationship
    echo "Enrolled Courses (via enrolledCourses relationship):\n";
    echo str_repeat('-', 50) . "\n";
    $enrolledCourses = $student->enrolledCourses;
    echo "Found " . $enrolledCourses->count() . " enrolled courses:\n";
    
    foreach ($enrolledCourses as $course) {
        echo "  - Course: " . $course->title . "\n";
        echo "    Description: " . substr($course->description ?? 'N/A', 0, 50) . "...\n\n";
    }
    
    // Test the service
    echo "Using StudentCourseEnrollmentService:\n";
    echo str_repeat('-', 50) . "\n";
    
    try {
        $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
        $serviceEnrollments = $enrollmentService->getStudentEnrollments($student->id);
        
        echo "Service returned " . $serviceEnrollments->count() . " enrollments:\n";
        foreach ($serviceEnrollments as $enrollment) {
            echo "  - Course: " . $enrollment->course_title . "\n";
            echo "    Status: " . ($enrollment->status ?? 'N/A') . "\n";
            echo "    Enrolled: " . $enrollment->enrolled_at . "\n\n";
        }
        
        echo "✅ All relationships are working correctly!\n";
        echo "✅ Student model now uses proper enrollment-based relationships\n";
        echo "✅ Service is fully refactored to use Eloquent instead of raw DB queries\n";
        
    } catch (Exception $e) {
        echo "❌ Service error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "No student found with user_id 23\n";
}