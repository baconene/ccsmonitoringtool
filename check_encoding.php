<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

$users = \App\Models\User::all();
echo "Total users: " . count($users) . "\n\n";

foreach ($users as $user) {
    $isValid = mb_check_encoding($user->name, 'UTF-8') && 
               mb_check_encoding($user->email, 'UTF-8');
    $status = $isValid ? '✓' : '✗ BAD ENCODING';
    echo "[ID: {$user->id}] {$status} Name: {$user->name} | Email: {$user->email}\n";
}
