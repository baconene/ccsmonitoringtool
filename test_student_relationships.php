<?php

require_once 'vendor/autoload.php';

$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get student record with ID 1
$student = \App\Models\Student::find(1);

if (!$student) {
    echo "Student with ID 1 not found.\n";
    exit;
}

echo "Student Information:\n";
echo "- ID: " . $student->id . "\n";
echo "- Student Number: " . ($student->student_number ?? 'N/A') . "\n";
echo "- User: " . ($student->user->name ?? 'N/A') . "\n";
echo "- Email: " . ($student->user->email ?? 'N/A') . "\n\n";

// Test course enrollments via the new courseEnrollments relationship
echo "Course Enrollments (via courseEnrollments relationship):\n";
echo str_repeat('-', 60) . "\n";
$courseEnrollments = $student->courseEnrollments;
echo "Found " . $courseEnrollments->count() . " enrollment records:\n";
foreach ($courseEnrollments as $enrollment) {
    echo " - Course: " . ($enrollment->course->title ?? 'N/A') . "\n";
    echo "   Status: " . ($enrollment->status ?? 'N/A') . "\n";
    echo "   Progress: " . ($enrollment->progress ?? 'N/A') . "\n";
    echo "   Enrolled: " . ($enrollment->enrolled_at ?? 'N/A') . "\n\n";
}

// Test course relationships via the courses relationship (pivot table)
echo "Courses (via courses relationship):\n";
echo str_repeat('-', 60) . "\n";
$courses = $student->courses;
echo "Enrolled in " . $courses->count() . " courses:\n";
foreach ($courses as $course) {
    echo " - Course: " . $course->title . "\n";
    echo "   Status: " . $course->pivot->status . "\n";
    echo "   Enrolled: " . $course->pivot->enrolled_at . "\n\n";
}

// Test lesson completions via the new relationship
echo "Lesson Completions (via lessonCompletions relationship):\n";
echo str_repeat('-', 60) . "\n";
$lessonCompletions = $student->lessonCompletions;
echo "Found " . $lessonCompletions->count() . " lesson completion records:\n";
foreach ($lessonCompletions as $completion) {
    echo " - Lesson: " . ($completion->lesson->title ?? 'Lesson ID ' . $completion->lesson_id) . "\n";
    echo "   Course: " . ($completion->course->title ?? 'Course ID ' . $completion->course_id) . "\n";
    echo "   Completed: " . $completion->completed_at . "\n\n";
}

// Test module completions via the new relationship
echo "Module Completions (via moduleCompletions relationship):\n";
echo str_repeat('-', 60) . "\n";
$moduleCompletions = $student->moduleCompletions;
echo "Found " . $moduleCompletions->count() . " module completion records:\n";
foreach ($moduleCompletions as $completion) {
    echo " - Module: " . ($completion->module->title ?? 'Module ID ' . $completion->module_id) . "\n";
    echo "   Course: " . ($completion->course->title ?? 'Course ID ' . $completion->course_id) . "\n";
    echo "   Completed: " . $completion->completed_at . "\n\n";
}

echo "All relationships are now properly connected to the Student model!\n";