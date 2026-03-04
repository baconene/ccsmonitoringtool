<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Module;
use App\Models\Skill;
use App\Models\StudentSkillAssessment;
use App\Services\StudentCourseEnrollmentService;

echo "=== Testing Skill Assessment Auto-Initialization on Enrollment ===\n\n";

// 1. Get test data
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

echo "Student: {$student->name} (ID: {$student->id})\n\n";

// 2. Create a test course
$course = Course::create([
    'name' => 'Enrollment Skill Test Course',
    'title' => 'Enrollment Skill Test Course',
    'description' => 'Testing skill assessment initialization on enrollment',
    'instructor_id' => $instructor->id,
    'created_by' => $instructor->id,
]);
echo "✓ Created course: {$course->title}\n";

// 3. Create modules with skills
$module1 = Module::create([
    'course_id' => $course->id,
    'title' => 'Module 1 - Basics',
    'description' => 'Testing skill initialization',
    'sequence' => 1,
    'module_type' => 'Activities',
    'created_by' => $instructor->id,
]);
echo "✓ Created module 1\n";

$module2 = Module::create([
    'course_id' => $course->id,
    'title' => 'Module 2 - Advanced',
    'description' => 'More skill initialization testing',
    'sequence' => 2,
    'module_type' => 'Activities',
    'created_by' => $instructor->id,
]);
echo "✓ Created module 2\n";

// 4. Create skills for each module
$skill1 = Skill::create([
    'module_id' => $module1->id,
    'name' => 'Basic Problem Solving',
    'description' => 'Fundamental problem-solving skills',
    'difficulty_level' => 'basic',
    'weight' => 30,
    'competency_threshold' => 70,
    'bloom_level' => 'understand',
]);
echo "✓ Created skill 1: {$skill1->name}\n";

$skill2 = Skill::create([
    'module_id' => $module1->id,
    'name' => 'Critical Thinking',
    'description' => 'Analytical and critical thinking',
    'difficulty_level' => 'intermediate',
    'weight' => 40,
    'competency_threshold' => 75,
    'bloom_level' => 'analyze',
]);
echo "✓ Created skill 2: {$skill2->name}\n";

$skill3 = Skill::create([
    'module_id' => $module2->id,
    'name' => 'Advanced Analysis',
    'description' => 'Complex analytical skills',
    'difficulty_level' => 'advanced',
    'weight' => 50,
    'competency_threshold' => 80,
    'bloom_level' => 'evaluate',
]);
echo "✓ Created skill 3: {$skill3->name}\n";

echo "\nTotal skills created: 3\n";
echo "Module 1 skills: 2\n";
echo "Module 2 skills: 1\n\n";

// 5. Check skill assessments BEFORE enrollment
$beforeCount = StudentSkillAssessment::where('student_id', $student->id)->count();
echo "=== Before Enrollment ===\n";
echo "StudentSkillAssessment records for student: {$beforeCount}\n\n";

// 6. Enroll student using the service
echo "=== Enrolling Student ===\n";
$enrollmentService = app(StudentCourseEnrollmentService::class);

try {
    $result = $enrollmentService->enrollStudentToACourse(
        $course->id,
        $student->id,
        ['enrolled_at' => now()]
    );
    
    if ($result['success']) {
        echo "✓ Student enrolled successfully\n\n";
    } else {
        echo "❌ Enrollment failed\n";
        exit(1);
    }
} catch (\Exception $e) {
    echo "❌ Enrollment error: {$e->getMessage()}\n";
    exit(1);
}

// 7. Check skill assessments AFTER enrollment
echo "=== After Enrollment ===\n";
$afterCount = StudentSkillAssessment::where('student_id', $student->id)->count();
echo "StudentSkillAssessment records for student: {$afterCount}\n";

if ($afterCount === 0) {
    echo "❌ FAILED: No skill assessments were created!\n";
    exit(1);
}

// 8. Verify each skill assessment
$assessments = StudentSkillAssessment::where('student_id', $student->id)
    ->with('skill')
    ->get();

echo "\nDetailed Skill Assessments:\n";
foreach ($assessments as $assessment) {
    echo "  - Skill: {$assessment->skill->name}\n";
    echo "    Final Score: {$assessment->final_score}\n";
    echo "    Mastery Level: {$assessment->mastery_level}\n";
    echo "    Attempt Count: {$assessment->attempt_count}\n";
    echo "    Metadata: " . json_encode($assessment->assessment_metadata) . "\n";
    echo "\n";
}

// 9. Verify all expected skills have assessments
$expectedSkillIds = [$skill1->id, $skill2->id, $skill3->id];
$actualSkillIds = $assessments->pluck('skill_id')->toArray();

$missing = array_diff($expectedSkillIds, $actualSkillIds);
$extra = array_diff($actualSkillIds, $expectedSkillIds);

if (!empty($missing)) {
    echo "⚠️  Missing skill assessments: " . implode(', ', $missing) . "\n";
}

if (!empty($extra)) {
    echo "⚠️  Extra skill assessments: " . implode(', ', $extra) . "\n";
}

if (empty($missing) && empty($extra) && $afterCount === 3) {
    echo "✅ SUCCESS: All 3 skill assessments were created correctly!\n";
    echo "   - All scores initialized to 0\n";
    echo "   - All mastery levels set to 'not_met'\n";
    echo "   - All assessments properly linked to student and skills\n";
} else {
    echo "❌ PARTIAL SUCCESS: Expected 3 assessments, got {$afterCount}\n";
}

// 10. Verify default values are correct
$allCorrect = true;
foreach ($assessments as $assessment) {
    if ($assessment->final_score != 0.00) {
        echo "❌ Skill {$assessment->skill_id} has non-zero score: {$assessment->final_score}\n";
        $allCorrect = false;
    }
    if ($assessment->mastery_level !== 'not_met') {
        echo "❌ Skill {$assessment->skill_id} has incorrect mastery level: {$assessment->mastery_level}\n";
        $allCorrect = false;
    }
    if ($assessment->attempt_count != 0) {
        echo "❌ Skill {$assessment->skill_id} has non-zero attempts: {$assessment->attempt_count}\n";
        $allCorrect = false;
    }
}

if ($allCorrect) {
    echo "\n✅ All default values are correct!\n";
}

echo "\n=== Test Complete ===\n";
