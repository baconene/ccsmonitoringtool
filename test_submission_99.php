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

echo "Submission 99:\n";
echo json_encode($submission, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

// Load relationships
$submission->load([
    'student.user',
    'activity.modules.course',
    'activity.assignment.questions.options',
    'activity.quiz',
    'activity.activityType'
]);

echo "Submission with relationships:\n";
echo "Student: " . $submission->student->user->name . "\n";
echo "Activity: " . $submission->activity->title . "\n";
echo "Activity Type ID: " . $submission->activity->activity_type_id . "\n";
echo "Activity Type: " . json_encode($submission->activity->activityType, JSON_PRETTY_PRINT) . "\n";
echo "Assignment: " . json_encode($submission->activity->assignment, JSON_PRETTY_PRINT) . "\n";

if ($submission->activity->assignment) {
    echo "Assignment Questions: " . count($submission->activity->assignment->questions ?? []) . "\n";
}
?>
