<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$laravel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$laravel->bootstrap();

use App\Services\StudentAssessmentService;
use App\Models\Student;

echo "Testing assessment for Student 16 with detailed output:\n\n";
$service = new StudentAssessmentService();
$student = Student::find(16);
$assessment = $service->calculateStudentAssessment($student);

echo "Assessment structure:\n";
echo json_encode($assessment, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
?>
