<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Course;
use App\Models\Module;
use App\Models\Activity;
use App\Models\Student;

// Get course 4
$course = Course::find(4);
echo "Course: " . $course->name . " (ID: {$course->id})\n";
echo "  Status: " . $course->status . "\n";

// Get modules in course
$modules = Module::where('course_id', 4)->get();
echo "\nModules in course: " . count($modules) . "\n";
foreach ($modules as $module) {
    echo "  - {$module->title} (ID: {$module->id})\n";
    
    // Get activities in module
    $activities = Activity::where('module_id', $module->id)->get();
    echo "    Activities: " . count($activities) . "\n";
    foreach ($activities as $activity) {
        echo "      - {$activity->title} (ID: {$activity->id}, Type: {$activity->activity_type_id})\n";
    }
}

// Check student data for user 24 (student 16)
$student = Student::find(16);
echo "\nStudent 16 (User 24):\n";
echo "  User ID: " . $student->user_id . "\n";
echo "  Status: " . $student->status . "\n";

// Check studentActivity records
$studentActivities = $student->studentActivities()->get();
echo "\n  Student Activities Count: " . count($studentActivities) . "\n";
foreach ($studentActivities as $sa) {
    echo "    - Activity {$sa->activity_id}: Status={$sa->status}, Score={$sa->score}\n";
}

// Check course enrollments
$courseEnrollments = $student->courseEnrollments()->get();
echo "\n  Course Enrollments: " . count($courseEnrollments) . "\n";
foreach ($courseEnrollments as $ce) {
    echo "    - Course {$ce->course_id}: Status={$ce->status}, Enrollment Date=" . ($ce->enrollment_date ?? 'null') . "\n";
}
?>
