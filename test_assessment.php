<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Student;

$user = User::find(24);
echo "User 24:\n";
echo "  ID: " . $user->id . "\n";
echo "  Name: " . $user->name . "\n";
echo "  Email: " . $user->email . "\n";
echo "  Role: " . $user->role . "\n";

$student = Student::where('user_id', 24)->first();
if ($student) {
    echo "\nStudent Record:\n";
    echo "  Student ID: " . $student->id . "\n";
    echo "  Status: " . $student->status . "\n";
    
    // Check enrolled courses
    $courses = $student->courses;
    echo "  Enrolled Courses: " . count($courses) . "\n";
    foreach ($courses as $course) {
        echo "    - " . $course->title . " (ID: " . $course->id . ")\n";
    }
    
    // Check skill assessments
    $skills = $student->skillAssessments;
    echo "  Skill Assessments: " . count($skills) . "\n";
} else {
    echo "\nNo student record found for user 24!\n";
}
