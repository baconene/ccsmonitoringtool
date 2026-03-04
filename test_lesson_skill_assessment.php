<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\Skill;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\StudentSkillAssessment;
use App\Services\StudentAssessmentService;

echo "=== Testing Lesson Completion → Skill Assessment ===\n\n";

// Get a student (adjust ID as needed)
$student = Student::with('user')->find(1);
if (!$student) {
    echo "Student 1 not found!\n";
    exit(1);
}

echo "Student: {$student->user->name} (ID: {$student->id})\n";
echo "User ID: {$student->user_id}\n\n";

// Get a module with skills and lessons
$module = Module::with(['skills', 'lessons'])
    ->whereHas('skills')
    ->whereHas('lessons')
    ->first();

if (!$module) {
    echo "No module found with both skills and lessons!\n";
    exit(1);
}

echo "Module: {$module->title} (ID: {$module->id})\n";
echo "Skills in module: " . $module->skills->count() . "\n";
echo "Lessons in module: " . $module->lessons->count() . "\n\n";

// Get the first skill
$skill = $module->skills->first();
echo "Testing Skill: {$skill->name} (ID: {$skill->id})\n";
echo "Competency Threshold: {$skill->competency_threshold}%\n\n";

// Check current skill assessment
$currentAssessment = StudentSkillAssessment::where('student_id', $student->id)
    ->where('skill_id', $skill->id)
    ->first();

echo "--- Current Skill Assessment ---\n";
if ($currentAssessment) {
    echo "Final Score: {$currentAssessment->final_score}%\n";
    echo "Mastery Level: {$currentAssessment->mastery_level}\n";
    echo "Attempt Count: {$currentAssessment->attempt_count}\n";
    $metadata = $currentAssessment->assessment_metadata ?? [];
    echo "Activity Count: " . ($metadata['activity_count'] ?? 0) . "\n";
    echo "Lesson Count: " . ($metadata['lesson_count'] ?? 0) . "\n";
} else {
    echo "No assessment record found yet\n";
}
echo "\n";

// Check lesson completions for this student in this module
$lessonCompletions = LessonCompletion::where('user_id', $student->user_id)
    ->whereIn('lesson_id', $module->lessons->pluck('id'))
    ->get();

echo "--- Lesson Completions in Module ---\n";
echo "Completed: {$lessonCompletions->count()} / {$module->lessons->count()}\n";
foreach ($lessonCompletions as $completion) {
    $lesson = $module->lessons->where('id', $completion->lesson_id)->first();
    echo "  ✓ {$lesson->title} (completed at: {$completion->completed_at})\n";
}
echo "\n";

// If no lessons completed, let's complete one
if ($lessonCompletions->count() === 0) {
    $lesson = $module->lessons->first();
    echo "--- Completing a lesson to test ---\n";
    echo "Lesson: {$lesson->title} (ID: {$lesson->id})\n";
    
    $completion = LessonCompletion::create([
        'user_id' => $student->user_id,
        'lesson_id' => $lesson->id,
        'completed_at' => now(),
    ]);
    
    echo "✓ Lesson marked as completed\n\n";
}

// Recalculate skill assessment
echo "--- Recalculating Skill Assessment ---\n";
$assessmentService = new StudentAssessmentService();
$result = $assessmentService->calculateOrUpdateSkillAssessment($student, $skill);

echo "Calculation completed!\n\n";

// Check updated assessment
$updatedAssessment = StudentSkillAssessment::where('student_id', $student->id)
    ->where('skill_id', $skill->id)
    ->first();

echo "--- Updated Skill Assessment ---\n";
if ($updatedAssessment) {
    echo "Final Score: {$updatedAssessment->final_score}%\n";
    echo "Normalized Score: {$updatedAssessment->normalized_score}%\n";
    echo "Mastery Level: {$updatedAssessment->mastery_level}\n";
    echo "Attempt Count: {$updatedAssessment->attempt_count}\n";
    echo "Consistency Score: {$updatedAssessment->consistency_score}\n";
    $metadata = $updatedAssessment->assessment_metadata ?? [];
    echo "\nMetadata:\n";
    echo "  Activity Count: " . ($metadata['activity_count'] ?? 0) . "\n";
    echo "  Lesson Count: " . ($metadata['lesson_count'] ?? 0) . "\n";
    echo "  Total Components: " . ($metadata['total_components'] ?? 0) . "\n";
    echo "  Evaluation Date: " . ($metadata['evaluation_date'] ?? 'N/A') . "\n";
} else {
    echo "ERROR: No assessment record found after calculation!\n";
}

echo "\n=== Test Complete ===\n";
