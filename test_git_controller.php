<?php
// Load Laravel bootstrap
require_once __DIR__ . '/bootstrap/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Make the application instance available in the global scope
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Get the controller
$controller = new \App\Http\Controllers\GitHistoryController();

// Mock a request
$request = new \Illuminate\Http\Request();
$request->query->add(['limit' => 20, 'format' => 'detailed']);

// Call the method
$response = $controller->getHistory($request);

echo "Response Content:\n";
echo $response->getContent();
