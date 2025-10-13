<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Student 13 Enrollment Check ===\n\n";

// Find student 13
$student = \App\Models\Student::with('user', 'courses')->find(13);

if (!$student) {
    echo "❌ Student 13 not found.\n";
    exit;
}

echo "✅ Student 13 Details:\n";
echo "- ID: {$student->id}\n";
echo "- Student ID: {$student->student_id}\n";
echo "- User: {$student->user->name}\n";
echo "- Email: {$student->user->email}\n";
echo "- Program: {$student->program}\n";
echo "- Department: {$student->department}\n";
echo "- Status: {$student->status}\n";
echo "- Enrollment Date: {$student->enrollment_date}\n\n";

echo "📚 Enrolled Courses ({$student->courses->count()}):\n";
echo str_repeat('-', 60) . "\n";

if ($student->courses->isEmpty()) {
    echo "No enrolled courses found.\n";
} else {
    foreach ($student->courses as $course) {
        echo "• Course: {$course->title} (ID: {$course->id})\n";
        echo "  Name: " . ($course->name ?? 'N/A') . "\n";
        echo "  Status: {$course->pivot->status}\n";
        echo "  Enrolled: {$course->pivot->enrolled_at}\n";
        if ($course->pivot->grade) {
            echo "  Grade: {$course->pivot->grade}\n";
        }
        if ($course->pivot->notes) {
            echo "  Notes: {$course->pivot->notes}\n";
        }
        echo "\n";
    }
}

// Also check using the enrollment service
echo "🚀 Using StudentCourseEnrollmentService:\n";
echo str_repeat('-', 60) . "\n";

try {
    $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
    $enrollments = $enrollmentService->getStudentEnrollments(13);
    
    echo "Service returned {$enrollments->count()} enrollments:\n";
    foreach ($enrollments as $enrollment) {
        echo "• Course: {$enrollment->course_title}\n";
        echo "  Status: {$enrollment->status}\n";
        echo "  Enrolled: {$enrollment->enrolled_at}\n";
        if ($enrollment->grade) {
            echo "  Grade: {$enrollment->grade}\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "❌ Error using service: {$e->getMessage()}\n";
}

echo "=== End Check ===\n";