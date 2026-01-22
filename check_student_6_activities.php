<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;

echo "=== Checking Student User 6 Activity Data ===\n\n";

// Student User 6
$user = User::where('name', 'Student User 6')->first();

if (!$user) {
    echo "Student User 6 not found!\n";
    exit(1);
}

echo "User ID: {$user->id}\n";
echo "Name: {$user->name}\n\n";

$student = $user->student;
if (!$student) {
    echo "Student record not found!\n";
    exit(1);
}

echo "Student ID: {$student->id}\n\n";

echo "=== Checking Completed Activities ===\n\n";

// Get all completed student activities for this student
$completedActivities = StudentActivity::where('student_id', $student->id)
    ->where('status', 'completed')
    ->with('activity')
    ->get();

foreach ($completedActivities as $sa) {
    if (!$sa->activity) continue;
    
    echo "Activity: {$sa->activity->title}\n";
    echo "Type: " . ($sa->activity->activityType ? $sa->activity->activityType->name : 'Unknown') . "\n";
    echo "Status: {$sa->status}\n";
    
    // Get progress record
    $progress = StudentActivityProgress::where('student_id', $student->id)
        ->where('activity_id', $sa->activity_id)
        ->first();
    
    if ($progress) {
        echo "Progress Record:\n";
        echo "  - completed_questions: {$progress->completed_items}\n";
        echo "  - total_questions: {$progress->total_items}\n";
        echo "  - score: " . ($progress->score ?? 'NULL') . "\n";
        echo "  - percentage_score: " . ($progress->percentage_score ?? 'NULL') . "%\n";
        echo "  - status: {$progress->status}\n";
        echo "  - is_completed: " . ($progress->is_completed ? 'true' : 'false') . "\n";
        
        // Check if this is wrong
        if ($sa->status === 'completed' && ($progress->completed_items != $progress->total_items)) {
            echo "  ⚠️  WARNING: Activity is completed but progress shows {$progress->completed_items}/{$progress->total_items}\n";
        }
    } else {
        echo "Progress Record: NOT FOUND\n";
    }
    
    // Check for assignment/quiz specific data
    if ($sa->activity->activityType && $sa->activity->activityType->name === 'Assignment') {
        $assignment = \App\Models\Assignment::where('activity_id', $sa->activity_id)->first();
        if ($assignment) {
            $totalQuestions = $assignment->questions()->count();
            echo "Assignment Questions: {$totalQuestions}\n";
            
            $studentAnswers = \App\Models\StudentAssignmentAnswer::where('student_id', $student->id)
                ->whereHas('question', function($q) use ($assignment) {
                    $q->where('assignment_id', $assignment->id);
                })
                ->count();
            echo "Student Answers: {$studentAnswers}\n";
        }
    }
    
    if ($sa->activity->activityType && $sa->activity->activityType->name === 'Quiz') {
        $quiz = \App\Models\Quiz::where('activity_id', $sa->activity_id)->first();
        if ($quiz) {
            $totalQuestions = $quiz->questions()->count();
            echo "Quiz Questions: {$totalQuestions}\n";
            
            $studentAnswers = \App\Models\StudentQuizAnswer::where('student_id', $student->id)
                ->whereHas('question', function($q) use ($quiz) {
                    $q->where('quiz_id', $quiz->id);
                })
                ->count();
            echo "Student Answers: {$studentAnswers}\n";
        }
    }
    
    echo "\n" . str_repeat("-", 70) . "\n\n";
}

echo "\n=== Summary ===\n";
echo "Total completed activities: {$completedActivities->count()}\n";
