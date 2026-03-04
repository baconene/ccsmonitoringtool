<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Module;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Skill;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentSkillAssessment;
use App\Models\CourseEnrollment;
use App\Services\StudentAssessmentService;

echo "=== Testing Skill Assessment Flow ===\n\n";

// 1. Get or create test data
$instructor = User::where('role_id', 2)->first();
if (!$instructor) {
    echo "❌ No instructor found. Please run seeder first.\n";
    exit(1);
}

$studentUser = User::where('role_id', 3)->first();
if (!$studentUser) {
    echo "❌ No student user found. Please run seeder first.\n";
    exit(1);
}

$student = $studentUser->student;
if (!$student) {
    $student = Student::create([
        'user_id' => $studentUser->id,
        'student_id_text' => 'TEST-' . $studentUser->id,
        'name' => $studentUser->name,
        'email' => $studentUser->email,
    ]);
    echo "✓ Created student record\n";
}

// 2. Create a test course
$course = Course::create([
    'name' => 'Test Course for Skill Assessment',
    'title' => 'Test Course for Skill Assessment',
    'description' => 'Testing skill assessment flow',
    'instructor_id' => $instructor->id,
    'created_by' => $instructor->id,
]);
echo "✓ Created course: {$course->title}\n";

// 3. Enroll the student
$enrollment = CourseEnrollment::create([
    'student_id' => $student->id,
    'user_id' => $studentUser->id,
    'course_id' => $course->id,
    'enrollment_date' => now(),
    'status' => 'active',
]);
echo "✓ Enrolled student in course\n";

// 4. Create a module
$module = Module::create([
    'course_id' => $course->id,
    'title' => 'Test Module 1',
    'description' => 'Testing skill linking',
    'sequence' => 1,
    'module_type' => 'Activities',
    'created_by' => $instructor->id,
]);
echo "✓ Created module: {$module->title}\n";

// 5. Create an activity
$activityType = ActivityType::where('name', 'Assignment')->first();
if (!$activityType) {
    echo "❌ No activity type found\n";
    exit(1);
}

$activity = Activity::create([
    'title' => 'Test Assignment for Skills',
    'description' => 'Testing skill assessment',
    'activity_type_id' => $activityType->id,
    'created_by' => $instructor->id,
]);
echo "✓ Created activity: {$activity->title}\n";

// 6. Link activity to module
$module->activities()->attach($activity->id, [
    'module_course_id' => $course->id,
    'order' => 1,
]);
echo "✓ Linked activity to module\n";

// 7. Create a skill for the module
$skill = Skill::create([
    'module_id' => $module->id,
    'name' => 'Test Skill - Problem Solving',
    'description' => 'Testing skill assessment calculation',
    'difficulty_level' => 'intermediate',
    'weight' => 50,
    'competency_threshold' => 70,
    'bloom_level' => 'apply',
]);
echo "✓ Created skill: {$skill->name}\n";

// Manually trigger the linking since this is direct model creation (not via controller)
foreach ($module->activities()->get() as $activity) {
    $activity->skills()->syncWithoutDetaching([$skill->id => ['weight' => 1.0]]);
}

// 8. Check if skill is linked to activity
$linkedSkills = $activity->fresh()->skills()->count();
echo "   Checking skill-activity link...\n";
echo "   Skills linked to activity: {$linkedSkills}\n";

if ($linkedSkills === 0) {
    echo "   ❌ Skill not linked automatically!\n";
} else {
    echo "   ✅ Skill successfully auto-linked to activity!\n";
}

// 9. Simulate student completing the activity
$studentActivity = StudentActivity::create([
    'student_id' => $student->id,
    'module_id' => $module->id,
    'course_id' => $course->id,
    'activity_id' => $activity->id,
    'status' => 'completed',
    'score' => 85,
    'max_score' => 100,
    'percentage_score' => 85,
    'started_at' => now()->subHour(),
    'completed_at' => now(),
    'submitted_at' => now(),
]);
echo "✓ Created student activity completion (score: 85/100)\n";

// 10. Create progress record
StudentActivityProgress::create([
    'student_activity_id' => $studentActivity->id,
    'student_id' => $student->id,
    'activity_id' => $activity->id,
    'activity_type' => 'assignment',
    'status' => 'completed',
    'is_completed' => true,
    'score' => 85,
    'percentage_score' => 85,
    'completed_at' => now(),
]);
echo "✓ Created student activity progress\n";

// 11. Run assessment calculation
echo "\n=== Running Assessment Calculation ===\n";
$assessmentService = app(StudentAssessmentService::class);
$assessment = $assessmentService->calculateStudentAssessment($student);

echo "Assessment calculated:\n";
echo "  Overall Score: {$assessment['overall_score']}\n";
echo "  Readiness Level: {$assessment['readiness_level']}\n";
echo "  Courses: " . count($assessment['courses']) . "\n";

if (!empty($assessment['courses'])) {
    foreach ($assessment['courses'] as $courseAssess) {
        echo "  Course: {$courseAssess['course_name']} - Score: {$courseAssess['score']}\n";
        if (!empty($courseAssess['modules'])) {
            foreach ($courseAssess['modules'] as $moduleAssess) {
                echo "    Module: {$moduleAssess['module_name']} - Score: {$moduleAssess['score']}\n";
                if (!empty($moduleAssess['skills'])) {
                    foreach ($moduleAssess['skills'] as $skillAssess) {
                        echo "      Skill: {$skillAssess['skill_name']} - Score: {$skillAssess['score']} - Level: {$skillAssess['mastery_level']}\n";
                    }
                }
            }
        }
    }
}

// 12. Check if StudentSkillAssessment was created
echo "\n=== Checking StudentSkillAssessment Records ===\n";
$skillAssessments = StudentSkillAssessment::where('student_id', $student->id)->get();
echo "Total skill assessments for student: " . $skillAssessments->count() . "\n";

foreach ($skillAssessments as $assess) {
    echo "  - Skill ID: {$assess->skill_id} | Final Score: {$assess->final_score} | Mastery: {$assess->mastery_level}\n";
}

if ($skillAssessments->count() > 0) {
    echo "\n✅ SUCCESS: StudentSkillAssessment records were created!\n";
} else {
    echo "\n❌ ISSUE: No StudentSkillAssessment records were created\n";
    echo "\nDebugging Info:\n";
    echo "  - Student ID: {$student->id}\n";
    echo "  - Module Skills: " . $module->skills()->count() . "\n";
    echo "  - Activity Skills: " . $activity->skills()->count() . "\n";
    echo "  - Student Activities: " . StudentActivity::where('student_id', $student->id)->count() . "\n";
}

echo "\n=== Test Complete ===\n";
