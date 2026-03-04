<?php

// Test git path finding
$gitPath = null;

// Windows paths to check
$windowsPaths = [
    'C:\\Program Files\\Git\\cmd\\git.exe',
    'C:\\Program Files (x86)\\Git\\cmd\\git.exe',
    'C:\\Git\\cmd\\git.exe',
];

echo "Testing git path detection:\n";
foreach ($windowsPaths as $path) {
    $exists = file_exists($path);
    echo sprintf("  %s: %s\n", $path, $exists ? 'FOUND' : 'NOT FOUND');
    if ($exists && $gitPath === null) {
        $gitPath = $path;
    }
}

if ($gitPath) {
    echo "\nUsing git at: $gitPath\n";
    echo "Testing git command:\n";
    
    $cmd = "\"$gitPath\" --version 2>&1";
    echo "Command: $cmd\n";
    
    $output = shell_exec($cmd);
    echo "Output: $output\n";
    
    // Try to run a git log command
    $projectPath = __DIR__;
    $cmd2 = "\"$gitPath\" -C \"$projectPath\" log --oneline -5 2>&1";
    echo "\nTesting git log:\n";
    echo "Command: $cmd2\n";
    $output2 = shell_exec($cmd2);
    echo "Output:\n$output2\n";
} else {
    echo "\nGit not found in any expected path!\n";
}
