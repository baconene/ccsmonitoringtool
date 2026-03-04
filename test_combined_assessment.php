<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Models\Skill;
use App\Models\Module;
use App\Models\Activity;
use App\Models\StudentActivity;
use App\Models\StudentSkillAssessment;
use App\Services\StudentAssessmentService;

echo "=== Testing Combined Activity + Lesson Skill Assessment ===\n\n";

// Get a student
$student = Student::with('user')->find(1);
echo "Student: {$student->user->name} (ID: {$student->id})\n\n";

// Find a module with both skills and activities
$module = Module::with(['skills', 'activities', 'lessons'])
    ->whereHas('skills')
    ->whereHas('activities')
    ->whereHas('lessons')
    ->first();

if (!$module) {
    echo "No module found with skills, activities, AND lessons!\n";
    
    // Try to find module with skills and activities only
    $module = Module::with(['skills', 'activities', 'lessons'])
        ->whereHas('skills')
        ->whereHas('activities')
        ->first();
    
    if (!$module) {
        echo "No module found with skills and activities at all!\n";
        exit(1);
    }
    echo "Using module with skills and activities (no lessons).\n";
}

echo "Module: {$module->title} (ID: {$module->id})\n";
echo "Skills: " . $module->skills->count() . "\n";
echo "Activities: " . $module->activities->count() . "\n";
echo "Lessons: " . $module->lessons->count() . "\n\n";

// Get the first skill
$skill = $module->skills->first();
echo "Testing Skill: {$skill->name}\n";
echo "Threshold: {$skill->competency_threshold}%\n\n";

// Check activities for this skill
$activitiesForSkill = $skill->activities;
echo "--- Activities Linked to Skill ---\n";
echo "Count: {$activitiesForSkill->count()}\n";

$activityProgress = [];
foreach ($activitiesForSkill as $activity) {
    $studentActivity = StudentActivity::where('student_id', $student->id)
        ->where('activity_id', $activity->id)
        ->first();
    
    if ($studentActivity) {
        $activityProgress[] = $activity->title;
        echo "  ✓ {$activity->title} (has progress)\n";
    } else {
        echo "  ✗ {$activity->title} (no progress)\n";
    }
}
echo "\n";

// Check lesson completions
$lessonCompletions = \App\Models\LessonCompletion::where('user_id', $student->user_id)
    ->whereIn('lesson_id', $module->lessons->pluck('id'))
    ->get();

echo "--- Lesson Completions in Module ---\n";
echo "Completed: {$lessonCompletions->count()} / {$module->lessons->count()}\n";
foreach ($lessonCompletions as $completion) {
    $lesson = $module->lessons->where('id', $completion->lesson_id)->first();
    if ($lesson) {
        echo "  ✓ {$lesson->title}\n";
    }
}
echo "\n";

// Calculate skill assessment
echo "--- Calculating Skill Assessment ---\n";
$assessmentService = new StudentAssessmentService();
$result = $assessmentService->calculateOrUpdateSkillAssessment($student, $skill);

// Display result
$assessment = StudentSkillAssessment::where('student_id', $student->id)
    ->where('skill_id', $skill->id)
    ->first();

if ($assessment) {
    echo "\n✓ Skill Assessment Created/Updated\n\n";
    echo "Final Score: {$assessment->final_score}%\n";
    echo "Normalized Score: {$assessment->normalized_score}%\n";
    echo "Mastery Level: {$assessment->mastery_level}\n";
    echo "Consistency Score: {$assessment->consistency_score}\n";
    echo "Attempt Count: {$assessment->attempt_count}\n";
    
    $metadata = $assessment->assessment_metadata ?? [];
    echo "\nBreakdown:\n";
    echo "  Activities: " . ($metadata['activity_count'] ?? 0) . "\n";
    echo "  Lessons: " . ($metadata['lesson_count'] ?? 0) . "\n";
    echo "  Total Components: " . ($metadata['total_components'] ?? 0) . "\n";
    
    echo "\nWeighting Info:\n";
    echo "  - Activities have weight: 1.0 (default)\n";
    echo "  - Lessons have weight: 0.5 (half of activities)\n";
    echo "  - Completed lessons count as 100% score\n";
    
} else {
    echo "ERROR: No assessment found!\n";
}

echo "\n=== Test Complete ===\n";
