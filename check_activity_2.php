<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use App\Models\Quiz;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;

echo "=== Activity 2 Data Check ===\n\n";

$activity = Activity::with([
    'activityType',
    'creator',
    'quiz.questions.options',
    'assignment.questions.options',
    'modules',
    'modules.course'
])->find(2);

if (!$activity) {
    echo "Activity 2 not found!\n";
    exit;
}

echo "Activity Information:\n";
echo "  ID: {$activity->id}\n";
echo "  Title: {$activity->title}\n";
echo "  Type: {$activity->activityType?->name}\n";
echo "  Created by: {$activity->creator?->name}\n";
echo "  Created at: {$activity->created_at}\n\n";

// Check associated models
echo "Associated Models:\n";
echo "  Has Quiz? " . ($activity->quiz ? "YES (ID: {$activity->quiz->id})" : "NO") . "\n";
echo "  Has Assignment? " . ($activity->assignment ? "YES (ID: {$activity->assignment->id})" : "NO") . "\n\n";

// Check modules
echo "Modules:\n";
if ($activity->modules->isEmpty()) {
    echo "  ⚠ No modules linked to this activity!\n\n";
} else {
    foreach ($activity->modules as $module) {
        echo "  - {$module->title} (Course: {$module->course->title})\n";
    }
    echo "\n";
}

// Check student activities
echo "Student Submissions:\n";
$studentActivities = \App\Models\StudentActivity::where('activity_id', $activity->id)
    ->with('student.user')
    ->get();

echo "  Total Submissions: {$studentActivities->count()}\n";

if ($studentActivities->count() > 0) {
    foreach ($studentActivities as $sa) {
        $student = $sa->student;
        $status = $sa->status ?? 'unknown';
        $score = $sa->score ?? 0;
        echo "    - {$student?->user?->name} ({$student?->student_id_text}): Status={$status}, Score={$score}\n";
    }
}

// Check if quiz/assignment has questions
echo "\nQuiz/Assignment Content:\n";
if ($activity->quiz) {
    $questionCount = $activity->quiz->questions ? $activity->quiz->questions->count() : 0;
    echo "  Quiz has {$questionCount} questions\n";
    if ($questionCount > 0) {
        foreach ($activity->quiz->questions as $q) {
            echo "    - Q{$q->id}: {$q->question_text} (Type: {$q->question_type})\n";
        }
    }
} elseif ($activity->assignment) {
    $questionCount = $activity->assignment->questions ? $activity->assignment->questions->count() : 0;
    echo "  Assignment has {$questionCount} questions\n";
    if ($questionCount > 0) {
        foreach ($activity->assignment->questions as $q) {
            echo "    - Q{$q->id}: {$q->question_text}\n";
        }
    }
} else {
    echo "  No quiz or assignment content\n";
}

// Check skill associations
echo "\nSkill Associations:\n";
$skillCount = DB::table('skill_activities')
    ->where('activity_id', $activity->id)
    ->count();
echo "  Linked to {$skillCount} skills\n";

if ($skillCount > 0) {
    $skills = DB::table('skill_activities')
        ->join('skills', 'skill_activities.skill_id', '=', 'skills.id')
        ->where('activity_id', $activity->id)
        ->select('skills.name', 'skill_activities.weight')
        ->get();
    
    foreach ($skills as $skill) {
        echo "    - {$skill->name} (Weight: {$skill->weight})\n";
    }
}
