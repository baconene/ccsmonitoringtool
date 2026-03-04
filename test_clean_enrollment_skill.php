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

echo "=== Clean Test: Skill Assessment on New Enrollment ===\n\n";

// Get a student who isn't enrolled yet
$studentUser = User::where('role_id', 3)->skip(1)->first();
if (!$studentUser) {
    echo "❌ Need at least 2 student users\n";
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
}

echo "Student: {$student->name}\n";

// Count current skill assessments
$beforeCount = StudentSkillAssessment::where('student_id', $student->id)->count();
echo "Skill assessments before: {$beforeCount}\n\n";

// Get a course with skills
$course = Course::with('modules.skills')->get()->first(function ($c) {
    return $c->modules()->whereHas('skills')->exists();
});

if (!$course) {
    echo "❌ No course with skills found\n";
    exit(1);
}

$totalSkills = 0;
foreach ($course->modules as $module) {
    $skillCount = $module->skills->count();
    $totalSkills += $skillCount;
    echo "Module '{$module->title}': {$skillCount} skills\n";
}
echo "Total expected skill assessments: {$totalSkills}\n\n";

// Check if already enrolled
$alreadyEnrolled = $student->courseEnrollments()->where('course_id', $course->id)->exists();
if ($alreadyEnrolled) {
    echo "⚠️  Student already enrolled in this course, skipping enrollment\n";
} else {
    echo "Enrolling student in '{$course->title}'...\n";
    $service = app(StudentCourseEnrollmentService::class);
    
    try {
        $result = $service->enrollStudentToACourse($course->id, $student->id);
        if ($result['success']) {
            echo "✅ Enrollment successful!\n\n";
        }
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'already enrolled') !== false) {
            echo "⚠️  Already enrolled\n\n";
        } else {
            throw $e;
        }
    }
}

// Count after enrollment
$afterCount = StudentSkillAssessment::where('student_id', $student->id)->count();
$newAssessments = $afterCount - $beforeCount;

echo "Skill assessments after: {$afterCount}\n";
echo "New assessments created: {$newAssessments}\n\n";

// Show the newly created ones with default values
$recentAssessments = StudentSkillAssessment::where('student_id', $student->id)
    ->where('attempt_count', 0)
    ->where('final_score', 0)
    ->with('skill')
    ->get();

echo "Assessments with default values (score=0, attempts=0):\n";
foreach ($recentAssessments as $assessment) {
    echo "  ✓ {$assessment->skill->name} | Score: {$assessment->final_score} | Level: {$assessment->mastery_level}\n";
}

if ($newAssessments > 0) {
    echo "\n✅ SUCCESS: {$newAssessments} skill assessment(s) were auto-initialized!\n";
} else {
    echo "\nℹ️  No new assessments (student might have been already enrolled)\n";
}

echo "\n=== Test Complete ===\n";
