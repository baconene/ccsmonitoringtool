<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking specific progress records (Student 6 completed activities) ===\n\n";

$records = DB::table('student_activity_progress as sap')
    ->join('activities as a', 'sap.activity_id', '=', 'a.id')
    ->join('student_activities as sa', function($join) {
        $join->on('sa.student_id', '=', 'sap.student_id')
             ->on('sa.activity_id', '=', 'sap.activity_id');
    })
    ->where('sap.student_id', 6)
    ->where('sa.status', 'completed')
    ->select(
        'a.title as activity_title',
        'sap.activity_type',
        'sa.status as sa_status',
        'sap.status as progress_status',
        'sap.completed_questions',
        'sap.total_questions',
        'sap.answered_questions',
        'sap.percentage_score'
    )
    ->get();

foreach ($records as $record) {
    echo "Activity: {$record->activity_title}\n";
    echo "Type: {$record->activity_type}\n";
    echo "Student Activity Status: {$record->sa_status}\n";
    echo "Progress Status: {$record->progress_status}\n";
    echo "Completed Questions: " . ($record->completed_questions ?? 'NULL') . "\n";
    echo "Total Questions: " . ($record->total_questions ?? 'NULL') . "\n";
    echo "Answered Questions: " . ($record->answered_questions ?? 'NULL') . "\n";
    echo "Percentage Score: " . ($record->percentage_score ?? 'NULL') . "%\n";
    echo str_repeat("-", 70) . "\n\n";
}
