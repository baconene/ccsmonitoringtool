<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeLevel;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gradeLevels = [
    // Year Levels
    ['name' => 'Year 1', 'display_name' => 'Year 1', 'level' => 1, 'is_active' => true],
    ['name' => 'Year 2', 'display_name' => 'Year 2', 'level' => 2, 'is_active' => true],
    ['name' => 'Year 3', 'display_name' => 'Year 3', 'level' => 3, 'is_active' => true],
    ['name' => 'Year 4', 'display_name' => 'Year 4', 'level' => 4, 'is_active' => true],
    ['name' => 'Year 5', 'display_name' => 'Year 5', 'level' => 5, 'is_active' => true],

    // Grades
    ['name' => 'Grade 1', 'display_name' => 'Grade 1', 'level' => 1, 'is_active' => true],
    ['name' => 'Grade 2', 'display_name' => 'Grade 2', 'level' => 2, 'is_active' => true],
    ['name' => 'Grade 3', 'display_name' => 'Grade 3', 'level' => 3, 'is_active' => true],
    ['name' => 'Grade 4', 'display_name' => 'Grade 4', 'level' => 4, 'is_active' => true],
    ['name' => 'Grade 5', 'display_name' => 'Grade 5', 'level' => 5, 'is_active' => true],
    ['name' => 'Grade 6', 'display_name' => 'Grade 6', 'level' => 6, 'is_active' => true],
    ['name' => 'Grade 7', 'display_name' => 'Grade 7', 'level' => 7, 'is_active' => true],
    ['name' => 'Grade 8', 'display_name' => 'Grade 8', 'level' => 8, 'is_active' => true],
    ['name' => 'Grade 9', 'display_name' => 'Grade 9', 'level' => 9, 'is_active' => true],
    ['name' => 'Grade 10', 'display_name' => 'Grade 10', 'level' => 10, 'is_active' => true],
    ['name' => 'Grade 11', 'display_name' => 'Grade 11', 'level' => 11, 'is_active' => true],
    ['name' => 'Grade 12', 'display_name' => 'Grade 12', 'level' => 12, 'is_active' => true],
];


        foreach ($gradeLevels as $gradeLevel) {
            GradeLevel::create($gradeLevel);
            echo "✓ Created Grade Level: {$gradeLevel['name']}\n";
        }
    }
}
