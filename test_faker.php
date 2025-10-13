<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $faker = \Illuminate\Container\Container::getInstance()->make(\Faker\Generator::class);
    echo "Faker instance created successfully!\n";
    echo "Random name: " . $faker->name . "\n";
    echo "Random email: " . $faker->email . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
