<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Student 13 Enrollment Test ===\n\n";

// Find student 13
$student = \App\Models\Student::find(13);
if (!$student) {
    echo "❌ Student 13 not found.\n";
    exit;
}

// Show available courses
$courses = \App\Models\Course::take(3)->get(['id', 'title', 'name']);
echo "📚 Available Courses:\n";
foreach ($courses as $course) {
    echo "- ID: {$course->id}, Title: " . ($course->title ?? $course->name) . "\n";
}
echo "\n";

// Try to enroll student 13 in course 1
if ($courses->count() > 0) {
    $course = $courses->first();
    echo "🎯 Attempting to enroll Student 13 in Course {$course->id} ({$course->title})...\n\n";
    
    try {
        $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
        
        $result = $enrollmentService->enrollStudentToACourse(
            $course->id,
            $student->id,
            [
                'enrolled_at' => now(),
                'status' => 'enrolled',
                'notes' => 'Test enrollment via script'
            ]
        );
        
        if ($result['success']) {
            echo "✅ Enrollment successful!\n";
            echo "Message: {$result['message']}\n";
            echo "Type: {$result['enrollment_type']}\n\n";
        } else {
            echo "❌ Enrollment failed!\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Enrollment error: {$e->getMessage()}\n\n";
    }
}

// Check enrollments again
echo "📋 Checking enrollments after enrollment attempt...\n";
$student->refresh();
$student->load('courses');

echo "Enrolled Courses ({$student->courses->count()}):\n";
if ($student->courses->isEmpty()) {
    echo "No enrolled courses found.\n";
} else {
    foreach ($student->courses as $course) {
        echo "• Course: {$course->title} (ID: {$course->id})\n";
        echo "  Status: {$course->pivot->status}\n";
        echo "  Enrolled: {$course->pivot->enrolled_at}\n";
        if ($course->pivot->notes) {
            echo "  Notes: {$course->pivot->notes}\n";
        }
        echo "\n";
    }
}

echo "=== End Test ===\n";