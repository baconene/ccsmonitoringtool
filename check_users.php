<?php

// Use SQLite database
$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    die("❌ Database file not found at: $dbPath\n");
}

try {
    $pdo = new PDO(
        "sqlite:$dbPath",
        null,
        null,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "\n=== USERS TABLE DATA ===\n";
    echo "Database: $dbPath\n";
    echo "Connection successful\n\n";
    
    // Get users with relationships
    $query = <<<SQL
    SELECT 
        u.id, 
        u.name, 
        u.email, 
        u.email_verified_at,
        u.created_at,
        r.name as role_name,
        s.grade_level_id,
        s.section,
        gl.display_name as grade_level
    FROM users u
    LEFT JOIN roles r ON u.role_id = r.id
    LEFT JOIN students s ON u.id = s.user_id
    LEFT JOIN grade_levels gl ON s.grade_level_id = gl.id
    LIMIT 10
    SQL;
    
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($users) . " users\n\n";
    
    if (count($users) === 0) {
        echo "No users found in database!\n";
    } else {
        foreach ($users as $index => $user) {
            echo "User #" . ($index + 1) . ":\n";
            echo "  ID: " . $user['id'] . "\n";
            echo "  Name: " . $user['name'] . "\n";
            echo "  Email: " . $user['email'] . "\n";
            echo "  Role: " . ($user['role_name'] ?? 'N/A') . "\n";
            echo "  Grade Level: " . ($user['grade_level'] ?? 'N/A') . "\n";
            echo "  Section: " . ($user['section'] ?? 'N/A') . "\n";
            
            // Check for encoding issues
            $nameBytes = strlen($user['name']);
            $nameChars = mb_strlen($user['name'], 'UTF-8');
            if ($nameBytes !== $nameChars) {
                echo "  ⚠️  Name encoding issue - Bytes: $nameBytes, Chars: $nameChars\n";
            }
            
            echo "\n";
        }
    }
    
    // Test JSON encoding
    echo "\n=== TESTING JSON ENCODING ===\n";
    $json = json_encode($users, JSON_UNESCAPED_UNICODE);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON Error: " . json_last_error_msg() . "\n";
    } else {
        echo "✓ JSON encoding successful\n";
        echo "JSON Length: " . strlen($json) . " characters\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

