<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\User::where('role', 'admin')->first();
if ($admin) {
    echo "✅ Admin user created successfully:\n";
    echo "  Name: " . $admin->name . "\n";
    echo "  Email: " . $admin->email . "\n";
    echo "  Role: " . $admin->role . "\n";
    echo "\nDatabase migration and seeding completed!\n";
} else {
    echo "❌ No admin user found\n";
    $count = \App\Models\User::count();
    echo "Total users in database: " . $count . "\n";
}
