<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StudentActivityProgress;

echo "=== Fixing is_completed Flag for Completed Activities ===\n\n";

// Find all student_activity_progress records where status is 'completed' but is_completed is false
$progressRecords = StudentActivityProgress::where('status', 'completed')
    ->where(function($query) {
        $query->where('is_completed', false)
              ->orWhereNull('is_completed');
    })
    ->get();

echo "Found {$progressRecords->count()} progress records with status='completed' but is_completed=false\n\n";

if ($progressRecords->count() === 0) {
    echo "No records to fix!\n";
    exit(0);
}

$updated = 0;
$errors = [];

foreach ($progressRecords as $progress) {
    try {
        $progress->update([
            'is_completed' => true
        ]);
        
        $updated++;
        echo "✓ Updated Progress ID {$progress->id} (Student: {$progress->student_id}, Activity: {$progress->activity_id})\n";
    } catch (\Exception $e) {
        $errors[] = "Progress ID {$progress->id}: " . $e->getMessage();
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "Summary:\n";
echo "- Records found: {$progressRecords->count()}\n";
echo "- Records updated: {$updated}\n";
echo "- Errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  ✗ {$error}\n";
    }
}

echo "\nFix complete!\n";
