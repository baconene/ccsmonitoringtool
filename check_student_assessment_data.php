<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;
use App\Services\StudentAssessmentService;

echo "=== Student Assessment Data Check ===\n\n";

// Get a student user
$studentUsers = User::whereHas('role', function($q) {
    $q->where('name', 'student');
})->get();

echo "Found {$studentUsers->count()} student users\n\n";

if ($studentUsers->isEmpty()) {
    echo "No students found in the system!\n";
    exit;
}

foreach ($studentUsers->take(3) as $user) {
    echo "--- User: {$user->name} (ID: {$user->id}) ---\n";
    
    $student = Student::where('user_id', $user->id)->first();
    
    if (!$student) {
        echo "  ⚠ No Student record found for this user!\n\n";
        continue;
    }
    
    echo "  Student ID: {$student->id}\n";
    
    // Check enrolled courses
    $enrolledCourses = $student->courses()->get();
    echo "  Enrolled Courses: {$enrolledCourses->count()}\n";
    
    if ($enrolledCourses->isEmpty()) {
        echo "  ⚠ Student has no enrolled courses!\n\n";
        continue;
    }
    
    foreach ($enrolledCourses as $course) {
        echo "    - Course: {$course->title} (ID: {$course->id})\n";
        
        $modules = $course->modules()->get();
        echo "      Modules: {$modules->count()}\n";
        
        foreach ($modules as $module) {
            echo "        - Module: {$module->title} (ID: {$module->id})\n";
            
            $skills = $module->skills()->get();
            echo "          Skills: {$skills->count()}\n";
            
            if ($skills->isEmpty()) {
                echo "          ⚠ No skills attached to this module!\n";
            } else {
                foreach ($skills->take(3) as $skill) {
                    echo "            - Skill: {$skill->name} (ID: {$skill->id})\n";
                    
                    $activities = $skill->activities()->get();
                    echo "              Activities: {$activities->count()}\n";
                    
                    if ($activities->isEmpty()) {
                        echo "              ⚠ No activities for this skill!\n";
                    }
                }
            }
        }
    }
    
    // Check student activities
    $studentActivities = \App\Models\StudentActivity::where('student_id', $student->id)->count();
    echo "  Total StudentActivity records: {$studentActivities}\n";
    
    // Check skill assessments
    $skillAssessments = $student->skillAssessments()->count();
    echo "  Skill Assessments: {$skillAssessments}\n";
    
    // Try calculating assessment
    echo "\n  Attempting to calculate assessment...\n";
    try {
        $assessmentService = app(StudentAssessmentService::class);
        $assessment = $assessmentService->calculateStudentAssessment($student);
        
        echo "  ✓ Assessment calculated successfully!\n";
        echo "    Overall Score: {$assessment['overall_score']}%\n";
        echo "    Readiness Level: {$assessment['readiness_level']}\n";
        echo "    Courses in assessment: " . count($assessment['courses']) . "\n";
        echo "    Strengths: " . count($assessment['strengths']) . "\n";
        echo "    Weaknesses: " . count($assessment['weaknesses']) . "\n";
        
    } catch (Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
        echo "  Stack trace:\n";
        echo "    " . str_replace("\n", "\n    ", $e->getTraceAsString()) . "\n";
    }
    
    echo "\n";
}
