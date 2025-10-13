<?php

// Tinker commands to check enrolled courses for student 1

echo "=== Student Course Enrollment Check ===\n\n";

// 1. Check if student exists
$student = \App\Models\Student::find(1);

if (!$student) {
    echo "❌ Student with ID 1 not found.\n";
    echo "Available students:\n";
    $students = \App\Models\Student::with('user')->take(5)->get();
    foreach ($students as $s) {
        echo "- ID: {$s->id}, User: " . ($s->user->name ?? 'N/A') . "\n";
    }
    exit;
}

echo "✅ Student Information:\n";
echo "- ID: {$student->id}\n";
echo "- Student Number: " . ($student->student_number ?? 'N/A') . "\n"; 
echo "- User: " . ($student->user->name ?? 'N/A') . "\n";
echo "- Email: " . ($student->user->email ?? 'N/A') . "\n\n";

// 2. Get enrolled courses using model relationship
echo "📚 Enrolled Courses (via Model Relationship):\n";
echo str_repeat('-', 60) . "\n";

$enrolledCourses = $student->courses;

if ($enrolledCourses->isEmpty()) {
    echo "No enrolled courses found via model relationship.\n\n";
} else {
    foreach ($enrolledCourses as $course) {
        echo "• Course ID: {$course->id}\n";
        echo "  Title: {$course->title}\n";
        echo "  Status: {$course->pivot->status}\n";
        echo "  Enrolled At: {$course->pivot->enrolled_at}\n";
        echo "  Grade: " . ($course->pivot->grade ?? 'N/A') . "\n";
        echo "  Notes: " . ($course->pivot->notes ?? 'N/A') . "\n\n";
    }
}

// 3. Check using raw database query
echo "🔍 Raw Database Check (course_student table):\n";
echo str_repeat('-', 60) . "\n";

$rawEnrollments = DB::table('course_student')
    ->join('courses', 'course_student.course_id', '=', 'courses.id')
    ->where('course_student.student_id', 1)
    ->select([
        'course_student.*',
        'courses.title as course_title',
        'courses.name as course_name'
    ])
    ->get();

if ($rawEnrollments->isEmpty()) {
    echo "No enrollments found in course_student table.\n\n";
} else {
    foreach ($rawEnrollments as $enrollment) {
        echo "• Course: {$enrollment->course_title}\n";
        echo "  Status: {$enrollment->status}\n";
        echo "  Enrolled: {$enrollment->enrolled_at}\n";
        echo "  Grade: " . ($enrollment->grade ?? 'N/A') . "\n\n";
    }
}

// 4. Using StudentCourseEnrollmentService
echo "🚀 Using StudentCourseEnrollmentService:\n";
echo str_repeat('-', 60) . "\n";

try {
    $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
    $serviceEnrollments = $enrollmentService->getStudentEnrollments(1);
    
    if ($serviceEnrollments->isEmpty()) {
        echo "No enrollments found via service.\n\n";
    } else {
        echo "Service returned {$serviceEnrollments->count()} enrollments:\n";
        foreach ($serviceEnrollments as $enrollment) {
            echo "• Course: {$enrollment->course_title}\n";
            echo "  Status: {$enrollment->status}\n";
            echo "  Enrolled: {$enrollment->enrolled_at}\n";
            echo "  Grade: " . ($enrollment->grade ?? 'N/A') . "\n\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error using service: {$e->getMessage()}\n\n";
}

// 5. Show enrollment statistics
echo "📊 Overall Statistics:\n";
echo str_repeat('-', 60) . "\n";
echo "Total students: " . \App\Models\Student::count() . "\n";
echo "Total courses: " . \App\Models\Course::count() . "\n";
echo "Total enrollments: " . DB::table('course_student')->count() . "\n";
echo "Active enrollments: " . DB::table('course_student')->where('status', 'enrolled')->count() . "\n";

echo "\n=== End Check ===\n";