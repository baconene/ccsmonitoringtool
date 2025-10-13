<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get users from controller
$controller = new App\Http\Controllers\UserController();
$response = $controller->index();
$users = json_decode($response->getContent(), true);

// Show first 2 students
$students = array_filter($users, fn($u) => $u['role_name'] === 'student');
$students = array_slice($students, 0, 2);

echo "Testing User API Response - Showing 2 students:\n";
echo "=" . str_repeat("=", 80) . "\n\n";

foreach ($students as $student) {
    echo "Name: {$student['name']}\n";
    echo "Email: {$student['email']}\n";
    echo "Role: {$student['role_display_name']}\n";
    echo "Grade Level: " . ($student['grade_level'] ?? 'Not set') . "\n";
    echo "Grade Level ID: " . ($student['grade_level_id'] ?? 'Not set') . "\n";
    echo "Section: " . ($student['section'] ?? 'Not set') . "\n";
    echo str_repeat("-", 80) . "\n";
}

echo "\n✅ API Response Test Complete!\n";
echo "\nVerification:\n";
echo "- grade_level is populated: " . (isset($students[0]['grade_level']) ? "YES ✅" : "NO ❌") . "\n";
echo "- grade_level_id is populated: " . (isset($students[0]['grade_level_id']) ? "YES ✅" : "NO ❌") . "\n";
echo "- section is populated: " . (isset($students[0]['section']) ? "YES ✅" : "NO ❌") . "\n";
