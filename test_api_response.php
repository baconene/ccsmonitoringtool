<?php

// Test what the index() method returns directly
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class)->bootstrap();

// Authenticate as admin
$admin = \App\Models\User::where('email', 'admin1@example.com')->first();
auth()->login($admin);

// Now test the controller method directly
$controller = new \App\Http\Controllers\UserController();

try {
    $response = $controller->index();
    
    // Check if it's a JSON response
    if ($response instanceof \Illuminate\Http\JsonResponse) {
        $content = $response->getContent();
        echo "API Response Status: " . $response->getStatusCode() . "\n";
        echo "Response Content:\n";
        echo $content . "\n";
    } else {
        echo "Response type: " . get_class($response) . "\n";
        echo "Response: ";
        var_dump($response);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}
