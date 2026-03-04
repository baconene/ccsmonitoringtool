<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\StudentActivity;

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

echo "Testing Quiz Questions with Options:\n\n";

if ($activity->quiz) {
    foreach (($activity->quiz->questions ?? []) as $q) {
        echo "Question: " . $q->question_text . "\n";
        echo "  Type: " . ($q->question_type ?? $q->type ?? 'unknown') . "\n";
        echo "  Points: " . $q->points . "\n";
        echo "  Options:\n";
        foreach (($q->options ?? []) as $opt) {
            echo "    - " . $opt->option_text . " (Correct: " . ($opt->is_correct ? 'Yes' : 'No') . ")\n";
        }
        echo "\n";
    }
}
?>
