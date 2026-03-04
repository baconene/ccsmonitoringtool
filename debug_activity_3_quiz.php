<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Activity;
use App\Models\Quiz;

$activity = Activity::find(3);
$activity->load(['quiz']);

echo "Activity 3:\n";
echo "  - ID: " . $activity->id . "\n";
echo "  - Title: " . $activity->title . "\n";
echo "  - Activity Type: " . $activity->activity_type_id . "\n";

if ($activity->quiz) {
    echo "  - Has Quiz: YES\n";
    echo "  - Quiz ID: " . $activity->quiz->id . "\n";
    echo "  - Quiz Title: " . $activity->quiz->title . "\n";
    echo "  - Quiz Questions: " . $activity->quiz->questions()->count() . "\n";
} else {
    echo "  - Has Quiz: NO\n";
}

// Check all quizzes
$allQuizzes = Quiz::all();
echo "\nAll Quizzes in DB: " . $allQuizzes->count() . "\n";
foreach ($allQuizzes as $quiz) {
    echo "  - Quiz " . $quiz->id . ": " . $quiz->title . "\n";
}
