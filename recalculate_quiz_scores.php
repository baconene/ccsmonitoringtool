<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;
use App\Models\StudentQuizAnswer;

echo "Recalculating quiz scores for completed quizzes...\n\n";

// Find all completed quiz activities that have progress records
$quizActivities = StudentActivity::whereHas('activity', function($query) {
    $query->whereHas('activityType', function($q) {
        $q->where('name', 'Quiz');
    });
})
->where('status', 'completed')
->with(['activity'])
->get();

echo "Found {$quizActivities->count()} completed quiz activities.\n\n";

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($quizActivities as $studentActivity) {
    echo "Processing StudentActivity ID: {$studentActivity->id}\n";
    echo "  Student ID: {$studentActivity->student_id}, Activity ID: {$studentActivity->activity_id}\n";
    
    // Find the progress record
    $progress = StudentActivityProgress::where('student_id', $studentActivity->student_id)
        ->where('activity_id', $studentActivity->activity_id)
        ->where('activity_type', 'quiz')
        ->first();
    
    if (!$progress) {
        echo "  ⚠️  No progress record found. Skipping.\n\n";
        $skipped++;
        continue;
    }
    
    // Get all quiz answers for this progress
    $answers = StudentQuizAnswer::where('activity_progress_id', $progress->id)->get();
    
    if ($answers->isEmpty()) {
        echo "  ⚠️  No answers found. Skipping.\n\n";
        $skipped++;
        continue;
    }
    
    // Calculate scores from answers
    $totalPointsEarned = $answers->sum('points_earned');
    $totalPointsPossible = $answers->sum(function($answer) {
        return $answer->question->points ?? 0;
    });
    $percentageScore = $totalPointsPossible > 0 ? round(($totalPointsEarned / $totalPointsPossible) * 100, 2) : 0;
    
    echo "  Current scores - SA: {$studentActivity->score}/{$studentActivity->max_score} ({$studentActivity->percentage_score}%)\n";
    echo "  Current scores - Progress: {$progress->score}/{$progress->max_score} ({$progress->percentage_score}%)\n";
    echo "  Calculated scores: {$totalPointsEarned}/{$totalPointsPossible} ({$percentageScore}%)\n";
    
    // Check if scores need updating
    if ($studentActivity->score == $totalPointsEarned && 
        $studentActivity->max_score == $totalPointsPossible && 
        $studentActivity->percentage_score == $percentageScore) {
        echo "  ✅ Scores are already correct. Skipping.\n\n";
        $skipped++;
        continue;
    }
    
    try {
        // Update StudentActivityProgress
        $progress->update([
            'score' => $totalPointsEarned,
            'max_score' => $totalPointsPossible,
            'points_earned' => $totalPointsEarned,
            'points_possible' => $totalPointsPossible,
            'percentage_score' => $percentageScore,
        ]);
        
        // Update StudentActivity
        $studentActivity->update([
            'score' => $totalPointsEarned,
            'max_score' => $totalPointsPossible,
            'percentage_score' => $percentageScore,
        ]);
        
        echo "  ✅ Updated scores to: {$totalPointsEarned}/{$totalPointsPossible} ({$percentageScore}%)\n\n";
        $updated++;
    } catch (\Exception $e) {
        echo "  ❌ Error updating: {$e->getMessage()}\n\n";
        $errors++;
    }
}

echo "\n========================================\n";
echo "Summary:\n";
echo "  Total processed: {$quizActivities->count()}\n";
echo "  Updated: {$updated}\n";
echo "  Skipped: {$skipped}\n";
echo "  Errors: {$errors}\n";
echo "========================================\n";
