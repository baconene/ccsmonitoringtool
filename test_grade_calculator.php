<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\GradeCalculatorService;

echo "=== Testing Grade Calculator for Student User 9 (Student 1), Course 4 ===\n\n";

$gradeCalculator = app(GradeCalculatorService::class);

// Student User 9 viewing Course 4
$userId = 9;
$courseId = 4;

try {
    $grades = $gradeCalculator->calculateStudentCourseGrades($userId, $courseId);
    
    echo "Student: {$grades['student']['name']}\n";
    echo "Course: {$grades['course']['title']}\n";
    echo "Overall Grade: {$grades['overall_grade']}% ({$grades['overall_letter_grade']})\n\n";
    
    echo "=== Activity Breakdown ===\n";
    foreach ($grades['modules'] as $module) {
        echo "\nModule: {$module['module_title']}\n";
        echo "Module Score: {$module['module_score']}%\n";
        
        foreach ($module['activities'] as $activity) {
            echo "\n  Activity: {$activity['activity_title']}\n";
            echo "  Type: {$activity['activity_type']}\n";
            echo "  Status: {$activity['status']}\n";
            echo "  Score: {$activity['score']}/{$activity['max_score']} ({$activity['percentage_score']}%)\n";
            echo "  Grade: {$activity['letter_grade']}\n";
            echo "  Completed: " . ($activity['is_completed'] ? 'Yes' : 'No') . "\n";
            
            if ($activity['activity_title'] === 'TEST ASSESSMENT') {
                echo "\n  ✅ TEST ASSESSMENT found!\n";
                if ($activity['status'] === 'completed' && $activity['is_completed']) {
                    echo "  ✅ Status is COMPLETED\n";
                    echo "  ✅ is_completed is true\n";
                    echo "  ✅ Should show as COMPLETED in grade report!\n";
                } else {
                    echo "  ❌ Status: {$activity['status']}\n";
                    echo "  ❌ is_completed: " . ($activity['is_completed'] ? 'true' : 'false') . "\n";
                    echo "  ❌ Will still show as IN PROGRESS\n";
                }
            }
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
