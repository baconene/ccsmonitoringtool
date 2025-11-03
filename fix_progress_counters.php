<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivityProgress;
use App\Models\Assignment;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

echo "=== Fixing Progress Counters for completed_questions and total_questions ===\n\n";

DB::beginTransaction();

try {
    $progressRecords = StudentActivityProgress::with('activity')->get();
    
    $updated = 0;
    $skipped = 0;
    
    foreach ($progressRecords as $progress) {
        if (!$progress->activity) {
            echo "⚠️  Skipping progress ID {$progress->id} - no activity found\n";
            $skipped++;
            continue;
        }
        
        $activityType = $progress->activity->activityType;
        if (!$activityType) {
            echo "⚠️  Skipping progress ID {$progress->id} - no activity type\n";
            $skipped++;
            continue;
        }
        
        $totalQuestions = 0;
        $completedQuestions = 0;
        
        // Get total questions based on activity type
        if ($activityType->name === 'Assignment') {
            $assignment = Assignment::where('activity_id', $progress->activity_id)->first();
            if ($assignment) {
                $totalQuestions = $assignment->questions()->count();
                
                // Get completed questions count from student answers
                $completedQuestions = \App\Models\StudentAssignmentAnswer::where('student_id', $progress->student_id)
                    ->whereHas('question', function($q) use ($assignment) {
                        $q->where('assignment_id', $assignment->id);
                    })
                    ->whereNotNull('assignment_question_id')
                    ->count();
            }
        } elseif ($activityType->name === 'Quiz') {
            $quiz = Quiz::where('activity_id', $progress->activity_id)->first();
            if ($quiz) {
                $totalQuestions = $quiz->questions()->count();
                
                // Get completed questions count from student answers
                $completedQuestions = \App\Models\StudentQuizAnswer::where('activity_progress_id', $progress->id)
                    ->count();
            }
        } elseif ($activityType->name === 'Assessment') {
            // Assessments don't have questions, set to 0/0
            $totalQuestions = 0;
            $completedQuestions = 0;
        }
        
        // Update the progress record
        $updateData = [
            'total_questions' => $totalQuestions,
            'completed_questions' => $completedQuestions,
            'total_items' => $totalQuestions,
            'completed_items' => $completedQuestions,
        ];
        
        // For completed activities with answers, set completed = total
        if ($progress->status === 'completed' && $totalQuestions > 0) {
            $updateData['completed_questions'] = $totalQuestions;
            $updateData['completed_items'] = $totalQuestions;
        }
        
        $progress->update($updateData);
        
        echo "✅ Updated Progress ID {$progress->id} - {$activityType->name} - Activity: {$progress->activity->title}\n";
        echo "   Questions: {$completedQuestions}/{$totalQuestions}\n";
        
        $updated++;
    }
    
    DB::commit();
    
    echo "\n=== Summary ===\n";
    echo "Updated: {$updated}\n";
    echo "Skipped: {$skipped}\n";
    echo "\n✅ All progress counters have been fixed!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
