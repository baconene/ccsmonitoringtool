<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Database Schema Check ===\n\n";

// Check module_completions table
echo "📋 module_completions table structure:\n";
try {
    $columns = \Illuminate\Support\Facades\DB::select('PRAGMA table_info(module_completions)');
    foreach ($columns as $column) {
        echo "- {$column->name} ({$column->type})\n";
    }
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo "\n";

// Check student_activities table
echo "📋 student_activities table structure:\n";
try {
    $columns = \Illuminate\Support\Facades\DB::select('PRAGMA table_info(student_activities)');
    foreach ($columns as $column) {
        echo "- {$column->name} ({$column->type})\n";
    }
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
}

echo "\n";

// List all tables
echo "📋 All tables in database:\n";
$tables = \Illuminate\Support\Facades\DB::select("SELECT name FROM sqlite_master WHERE type='table'");
foreach ($tables as $table) {
    echo "- {$table->name}\n";
}

echo "\n=== End Schema Check ===\n";