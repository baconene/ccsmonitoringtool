<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class ListGradeLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grades:list {--json : Output as JSON} {--students : Show student details}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all grade levels with statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('');
        $this->info('═══════════════════════════════════════════');
        $this->info('📚 Grade Levels in Database');
        $this->info('═══════════════════════════════════════════');
        $this->info('');

        // Get unique grade levels from students
        $gradeLevels = User::whereNotNull('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        if ($gradeLevels->isEmpty()) {
            $this->warn('No grade levels found in the database.');
            return;
        }

        // Prepare data
        $data = [];
        
        $this->info('Available Grade Levels:');
        foreach ($gradeLevels as $grade) {
            $count = User::where('grade_level', $grade)->count();
            $this->line("  • {$grade} ({$count} students)");
            
            $data[] = [
                'grade_level' => $grade,
                'student_count' => $count,
            ];
        }

        $this->info('');

        // Grade distribution by section
        $this->info('Grade Distribution by Section:');
        $distribution = User::whereNotNull('grade_level')
            ->whereNotNull('section')
            ->select('grade_level', 'section', DB::raw('count(*) as total'))
            ->groupBy('grade_level', 'section')
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get();

        $tableData = [];
        foreach ($distribution as $item) {
            $this->line("  • {$item->grade_level} - {$item->section}: {$item->total} students");
            $tableData[] = [
                'Grade Level' => $item->grade_level,
                'Section' => $item->section,
                'Students' => $item->total,
            ];
        }

        $this->info('');

        // Courses per grade level
        $this->info('Courses per Grade Level:');
        $courses = Course::whereNotNull('grade_level')
            ->select('grade_level', DB::raw('count(*) as total'))
            ->groupBy('grade_level')
            ->orderBy('grade_level')
            ->get();

        foreach ($courses as $course) {
            $this->line("  • {$course->grade_level}: {$course->total} courses");
        }

        // Show student details if requested
        if ($this->option('students')) {
            $this->info('');
            $this->info('Student Details by Grade:');
            
            foreach ($gradeLevels as $grade) {
                $students = User::where('grade_level', $grade)
                    ->orderBy('section')
                    ->orderBy('name')
                    ->get(['name', 'email', 'section']);
                
                $this->info('');
                $this->info("  {$grade}:");
                
                $studentData = $students->map(function($student) {
                    return [
                        'Name' => $student->name,
                        'Email' => $student->email,
                        'Section' => $student->section ?? 'N/A',
                    ];
                })->toArray();
                
                $this->table(['Name', 'Email', 'Section'], $studentData);
            }
        }

        // JSON output
        if ($this->option('json')) {
            $this->info('');
            $this->info('JSON Output:');
            $jsonData = [
                'grade_levels' => $data,
                'distribution' => $distribution->toArray(),
                'courses' => $courses->toArray(),
            ];
            $this->line(json_encode($jsonData, JSON_PRETTY_PRINT));
        }

        $this->info('');
        $this->info('═══════════════════════════════════════════');
        $this->info('✅ Done!');
        $this->info('═══════════════════════════════════════════');
        $this->info('');
    }
}
