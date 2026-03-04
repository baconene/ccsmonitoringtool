<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;

echo "=== Available Students ===\n\n";

$students = Student::with('user')->get();

foreach ($students as $student) {
    echo "ID: {$student->id} - Name: {$student->user->name} (User ID: {$student->user_id})\n";
}

echo "\nTotal: " . $students->count() . " students\n";
