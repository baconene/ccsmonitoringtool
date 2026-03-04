<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Student;
use App\Services\StudentAssessmentService;

$student = Student::find(16); // Student 16 is user 24

try {
    $assessmentService = app(StudentAssessmentService::class);
    $assessment = $assessmentService->calculateStudentAssessment($student);
    
    echo "Assessment calculated successfully!\n";
    echo "Overall Score: " . $assessment['overall_score'] . "\n";
    echo "Readiness Level: " . $assessment['readiness_level'] . "\n";
    echo "Courses: " . count($assessment['courses']) . "\n";
    echo "Strengths: " . count($assessment['strengths']) . "\n";
    echo "Weaknesses: " . count($assessment['weaknesses']) . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
