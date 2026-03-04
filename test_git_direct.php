<?php
// Simple test to verify the git history controller works
// No authentication needed for this direct test

require_once __DIR__ . '/bootstrap/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Get the controller
$controller = new \App\Http\Controllers\GitHistoryController();

// Create a mock request
$request = new \Illuminate\Http\Request();
$request->merge(['limit' => 20, 'format' => 'detailed']);

// Call the getHistory method directly
try {
    $response = $controller->getHistory($request);
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    echo "Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
    if ($data['success']) {
        echo "Commits found: " . $data['count'] . "\n";
        if (!empty($data['commits'])) {
            echo "\nFirst 3 commits:\n";
            foreach (array_slice($data['commits'], 0, 3) as $commit) {
                echo "  - " . $commit['hash'] . " | " . $commit['message'] . "\n";
            }
        }
    } else {
        echo "Error: " . $data['error'] . "\n";
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
