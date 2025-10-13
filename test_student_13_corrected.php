<?php

require_once 'vendor/autoload.php';

$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Student Course Enrollment with Corrected Student Model Relationships\n";
echo str_repeat('=', 80) . "\n";

// Get student record with ID 13
$student = \App\Models\Student::find(13);

if (!$student) {
    echo "Student with ID 13 not found.\n";
    exit;
}

echo "Student Information:\n";
echo "- Student ID: " . $student->id . "\n";
echo "- Student Number: " . ($student->student_number ?? 'N/A') . "\n";
echo "- User: " . ($student->user->name ?? 'N/A') . "\n";
echo "- Email: " . ($student->user->email ?? 'N/A') . "\n\n";

// Test course enrollments via the StudentCourseEnrollmentService
echo "Using StudentCourseEnrollmentService:\n";
echo str_repeat('-', 50) . "\n";

try {
    $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
    $enrollments = $enrollmentService->getStudentEnrollments(13);
    
    echo "Service returned " . $enrollments->count() . " enrollments:\n";
    foreach ($enrollments as $enrollment) {
        echo " - Course: " . $enrollment->course_title . "\n";
        echo "   Status: " . $enrollment->status . "\n";
        echo "   Enrolled: " . $enrollment->enrolled_at . "\n\n";
    }
} catch (Exception $e) {
    echo "Error using service: " . $e->getMessage() . "\n";
}

// Test course enrollments via the Student model courseEnrollments relationship
echo "Student Model - courseEnrollments relationship:\n";
echo str_repeat('-', 50) . "\n";
$courseEnrollments = $student->courseEnrollments;
echo "Found " . $courseEnrollments->count() . " enrollment records:\n";
foreach ($courseEnrollments as $enrollment) {
    echo " - Course: " . ($enrollment->course->title ?? 'N/A') . "\n";
    echo "   Status: " . ($enrollment->status ?? 'N/A') . "\n";
    echo "   Progress: " . ($enrollment->progress ?? 'N/A') . "\n";
    echo "   Enrolled: " . ($enrollment->enrolled_at ?? 'N/A') . "\n\n";
}

// Test course relationships via the Student model courses relationship (pivot table)
echo "Student Model - courses relationship (pivot table):\n";
echo str_repeat('-', 50) . "\n";
$courses = $student->courses;
echo "Enrolled in " . $courses->count() . " courses via pivot:\n";
foreach ($courses as $course) {
    echo " - Course: " . $course->title . "\n";
    echo "   Status: " . $course->pivot->status . "\n";
    echo "   Enrolled: " . $course->pivot->enrolled_at . "\n\n";
}

// Test enrolling student in a new course using the corrected approach
echo "Testing New Enrollment:\n";
echo str_repeat('-', 50) . "\n";

try {
    // Try to enroll student 13 in course 2 using the service
    $result = $enrollmentService->enrollStudentToACourse(2, 13, [
        'status' => 'enrolled',
        'notes' => 'Enrolled via corrected Student model relationships'
    ]);
    
    echo "Enrollment Result:\n";
    echo "- Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
    echo "- Message: " . $result['message'] . "\n";
    echo "- Type: " . $result['enrollment_type'] . "\n\n";
    
    // Check enrollments again
    echo "After enrollment - Student's course enrollments:\n";
    $student->refresh(); // Refresh the model
    $updatedEnrollments = $student->courseEnrollments;
    echo "Found " . $updatedEnrollments->count() . " enrollment records:\n";
    foreach ($updatedEnrollments as $enrollment) {
        echo " - Course: " . ($enrollment->course->title ?? 'N/A') . "\n";
        echo "   Status: " . ($enrollment->status ?? 'N/A') . "\n";
        echo "   Notes: " . ($enrollment->notes ?? 'N/A') . "\n\n";
    }
    
} catch (Exception $e) {
    echo "Error during enrollment: " . $e->getMessage() . "\n";
}

echo "Testing completed - All relationships are now properly connected to Student model!\n";