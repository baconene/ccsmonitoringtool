<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking student_activity_progress table structure ===\n\n";

$columns = Schema::getColumnListing('student_activity_progress');

echo "Columns in student_activity_progress:\n";
foreach ($columns as $column) {
    echo "  - {$column}\n";
}

echo "\n=== Sample data from student_activity_progress (ID 97 - ASSIGNMENT 1) ===\n\n";

$sample = DB::table('student_activity_progress')->where('id', 97)->first();

if ($sample) {
    foreach ($sample as $key => $value) {
        $displayValue = $value === null ? 'NULL' : (is_string($value) ? $value : json_encode($value));
        echo "{$key}: {$displayValue}\n";
    }
}
