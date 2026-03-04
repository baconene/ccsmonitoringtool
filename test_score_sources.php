<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\StudentActivity;
use App\Models\StudentActivityProgress;

echo "Checking score sources for student activities:\n\n";

// Get student activities
$studentActivities = StudentActivity::where('student_id', 16)->get();
foreach ($studentActivities as $sa) {
    echo "Student Activity ID {$sa->id} (Activity {$sa->activity_id}):\n";
    echo "  Score on StudentActivity: {$sa->score}\n";
    echo "  Percentage Score: " . ($sa->percentage_score ?? 'null') . "\n";
    
    // Check StudentActivityProgress
    $progress = StudentActivityProgress::where('student_id', 16)
        ->where('activity_id', $sa->activity_id)
        ->first();
    
    if ($progress) {
        echo "  StudentActivityProgress found:\n";
        echo "    - percentage_score: " . ($progress->percentage_score ?? 'null') . "\n";
        echo "    - score: " . ($progress->score ?? 'null') . "\n";
        echo "    - max_score: " . ($progress->max_score ?? 'null') . "\n";
    } else {
        echo "  StudentActivityProgress: NOT FOUND\n";
    }
    
    echo "\n";
}

// List all StudentActivityProgress records for student 16
echo "All StudentActivityProgress records for student 16:\n";
$allProgress = StudentActivityProgress::where('student_id', 16)->get();
echo "Count: " . count($allProgress) . "\n";
foreach ($allProgress as $progress) {
    echo "  - Activity {$progress->activity_id}: score={$progress->score}, max_score={$progress->max_score}, percentage={$progress->percentage_score}\n";
}
?>
