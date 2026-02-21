<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n=== TESTING LARAVEL API RESPONSE ===\n";

try {
    // Test the same logic as the controller
    $users = User::with(['role', 'student.gradeLevel'])->get();
    
    $response = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'role' => [
                'id' => $user->role?->id,
                'name' => $user->role?->name,
            ],
            'grade_level' => $user->student?->gradeLevel?->display_name,
            'grade_level_id' => $user->student?->grade_level_id,
            'section' => $user->student?->section,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    })->all();
    
    // Test JSON encoding
    $json = json_encode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON Error: " . json_last_error_msg() . "\n";
    } else {
        echo "✓ JSON encoding successful\n";
        echo "Response size: " . strlen($json) . " bytes\n";
        echo "Users returned: " . count($response) . "\n";
        echo "\nFirst user:\n";
        echo json_encode($response[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}
