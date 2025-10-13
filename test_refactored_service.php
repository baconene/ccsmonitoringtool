<?php

require_once 'vendor/autoload.php';

$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Refactored StudentCourseEnrollmentService\n";
echo str_repeat('=', 60) . "\n";

try {
    $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
    
    // Test 1: Get student enrollments for student 1
    echo "Test 1: Getting enrollments for student 1\n";
    echo str_repeat('-', 40) . "\n";
    
    $enrollments = $enrollmentService->getStudentEnrollments(1);
    echo "Found " . $enrollments->count() . " enrollments:\n";
    
    foreach ($enrollments as $enrollment) {
        echo "  - Course: " . $enrollment->course_title . "\n";
        echo "    Status: " . ($enrollment->status ?? 'N/A') . "\n";
        echo "    Enrolled: " . $enrollment->enrolled_at . "\n";
        echo "    Progress: " . ($enrollment->progress ?? 'N/A') . "\n\n";
    }
    
    // Test 2: Get course enrollments for course 1
    echo "Test 2: Getting enrollments for course 1\n";
    echo str_repeat('-', 40) . "\n";
    
    $courseEnrollments = $enrollmentService->getCourseEnrollments(1);
    echo "Found " . $courseEnrollments->count() . " course enrollments:\n";
    
    foreach ($courseEnrollments->take(3) as $enrollment) {
        echo "  - Student: " . ($enrollment->student_name ?? 'N/A') . "\n";
        echo "    Status: " . ($enrollment->status ?? 'N/A') . "\n";
        echo "    Email: " . ($enrollment->student_email ?? 'N/A') . "\n\n";
    }
    
    // Test 3: Get enrollment statistics for course 1
    echo "Test 3: Getting enrollment statistics for course 1\n";
    echo str_repeat('-', 40) . "\n";
    
    $stats = $enrollmentService->getEnrollmentStatistics(1);
    echo "Enrollment Statistics:\n";
    echo "  - Total: " . $stats['total_enrollments'] . "\n";
    echo "  - Active: " . $stats['active_enrollments'] . "\n";
    echo "  - Completed: " . $stats['completed_enrollments'] . "\n";
    echo "  - Dropped: " . $stats['dropped_enrollments'] . "\n";
    echo "  - Withdrawn: " . $stats['withdrawn_enrollments'] . "\n";
    echo "  - Average Grade: " . ($stats['average_grade'] ?? 'N/A') . "\n";
    echo "  - Completion Rate: " . $stats['completion_rate'] . "%\n\n";
    
    echo "All tests completed successfully! Service is now using Eloquent relationships.\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}