<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivityProgress;
use Illuminate\Support\Facades\DB;

echo "=== Fixing is_submitted Flag for Completed Activities ===\n\n";

DB::beginTransaction();

try {
    // Find all progress records where:
    // - is_completed = true
    // - status = 'completed'
    // - BUT is_submitted = false
    
    $recordsToFix = StudentActivityProgress::where('is_completed', true)
        ->where('status', 'completed')
        ->where('is_submitted', false)
        ->get();
    
    echo "Found {$recordsToFix->count()} records to fix\n\n";
    
    foreach ($recordsToFix as $progress) {
        $activity = $progress->activity;
        
        echo "Fixing Progress ID {$progress->id}\n";
        echo "  Student ID: {$progress->student_id}\n";
        echo "  Activity: " . ($activity ? $activity->title : "Unknown") . "\n";
        echo "  Type: {$progress->activity_type}\n";
        
        // Update the record
        $progress->update([
            'is_submitted' => true,
            'submitted_at' => $progress->completed_at ?? now(),
        ]);
        
        echo "  ✅ Updated is_submitted to true\n\n";
    }
    
    DB::commit();
    
    echo "=== Summary ===\n";
    echo "Fixed {$recordsToFix->count()} records\n";
    echo "\n✅ All completed activities now have is_submitted = true!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
