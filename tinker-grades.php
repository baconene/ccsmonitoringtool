<?php

/**
 * Quick script to get grade levels
 * Run with: php tinker-grades.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n═══════════════════════════════════════════\n";
echo "📚 Grade Levels in Database\n";
echo "═══════════════════════════════════════════\n\n";

// Get unique grade levels from students
$gradeLevels = \App\Models\User::whereNotNull('grade_level')
    ->distinct()
    ->orderBy('grade_level')
    ->pluck('grade_level');

echo "Available Grade Levels:\n";
foreach ($gradeLevels as $grade) {
    $count = \App\Models\User::where('grade_level', $grade)->count();
    echo "  • {$grade} ({$count} students)\n";
}

echo "\n";

// Get grade distribution by section
echo "Grade Distribution by Section:\n";
$distribution = \App\Models\User::whereNotNull('grade_level')
    ->whereNotNull('section')
    ->select('grade_level', 'section', \DB::raw('count(*) as total'))
    ->groupBy('grade_level', 'section')
    ->orderBy('grade_level')
    ->orderBy('section')
    ->get();

foreach ($distribution as $item) {
    echo "  • {$item->grade_level} - {$item->section}: {$item->total} students\n";
}

echo "\n";

// Get courses per grade level
echo "Courses per Grade Level:\n";
$courses = \App\Models\Course::whereNotNull('grade_level')
    ->select('grade_level', \DB::raw('count(*) as total'))
    ->groupBy('grade_level')
    ->orderBy('grade_level')
    ->get();

foreach ($courses as $course) {
    echo "  • {$course->grade_level}: {$course->total} courses\n";
}

echo "\n═══════════════════════════════════════════\n";
echo "✅ Done!\n";
echo "═══════════════════════════════════════════\n\n";
