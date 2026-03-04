<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\StudentActivity;
use App\Models\Activity;

$submission = StudentActivity::find(99);

if (!$submission) {
    echo "Submission 99 not found\n";
    exit;
}

// Load relationships
$submission->load([
    'student.user',
    'activity.modules.course',
    'activity.assignment.questions.options',
    'activity.quiz.questions.options',
    'activity.activityType'
]);

// Simulating the controller logic
$activity = $submission->activity;
$activityType = $submission->activity_type ?? strtolower($activity->activityType->name ?? 'assignment');

echo "Activity Type Detected: $activityType\n";
echo "Activity Type Model: " . $activity->activityType->name . "\n";
echo "Quiz Object: " . ($activity->quiz ? "EXISTS" : "NULL") . "\n";

if ($activity->quiz) {
    echo "Quiz Questions Count: " . count($activity->quiz->questions ?? []) . "\n";
    
    foreach (($activity->quiz->questions ?? []) as $q) {
        echo "  Question: " . $q->question_text . "\n";
        echo "    Type: " . ($q->question_type ?? $q->type ?? 'unknown') . "\n";
        echo "    Points: " . $q->points . "\n";
        echo "    Options: " . count($q->options ?? []) . "\n";
        foreach (($q->options ?? []) as $opt) {
            echo "      - " . $opt->text . " (ID: {$opt->id})\n";
        }
    }
}

// Test formatted submission
$formattedSubmission = [
    'id' => $submission->id,
    'student_id' => $submission->student_id,
    'student' => [
        'id' => $submission->student->id,
        'name' => $submission->student->user->name,
        'email' => $submission->student->user->email,
    ],
    'status' => $submission->status,
    'progress' => $submission->progress_data ? json_decode($submission->progress_data)->progress ?? 0 : 0,
    'score' => $submission->score,
    'total_score' => $submission->max_score ?? $activity->total_score ?? 100,
    'submitted_at' => $submission->submitted_at,
    'graded_at' => $submission->graded_at,
];

echo "\nFormatted Submission:\n";
echo json_encode($formattedSubmission, JSON_PRETTY_PRINT) . "\n";
?>
