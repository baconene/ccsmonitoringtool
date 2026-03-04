<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Models\Assignment;
use App\Models\StudentActivity;

// Find assignments with student activities
$allAssignments = Assignment::all();

echo "Finding assignments with student activities:\n\n";

foreach ($allAssignments as $assignment) {
    $studentActivities = StudentActivity::where('activity_id', $assignment->activity_id)
        ->where('activity_type', 'assignment')
        ->count();
    
    if ($studentActivities > 0) {
        echo "Assignment ID: {$assignment->id}, Title: {$assignment->title}, Activity ID: {$assignment->activity_id}\n";
        echo "  Student Activities: $studentActivities\n";
    }
}
?>
