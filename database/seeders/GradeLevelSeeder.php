<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Grade Levels...');

        // Year Levels (1-5) - Typically for younger students
        $yearLevels = [
            ['name' => 'Year 1', 'display_name' => 'Year 1 (Primary)', 'level' => 1],
            ['name' => 'Year 2', 'display_name' => 'Year 2 (Primary)', 'level' => 2],
            ['name' => 'Year 3', 'display_name' => 'Year 3 (Primary)', 'level' => 3],
            ['name' => 'Year 4', 'display_name' => 'Year 4 (Primary)', 'level' => 4],
            ['name' => 'Year 5', 'display_name' => 'Year 5 (Primary)', 'level' => 5],
        ];

        foreach ($yearLevels as $levelData) {
            GradeLevel::updateOrCreate(
                ['name' => $levelData['name']],
                $levelData + ['is_active' => true]
            );
            $this->command->info("✓ Grade Level: {$levelData['name']}");
        }

        // Grade Levels (1-12) - Standard K-12 system
        $gradeLevels = [
            // Elementary Grades (1-6)
            ['name' => 'Grade 1', 'display_name' => 'Grade 1 (Elementary)', 'level' => 6],
            ['name' => 'Grade 2', 'display_name' => 'Grade 2 (Elementary)', 'level' => 7],
            ['name' => 'Grade 3', 'display_name' => 'Grade 3 (Elementary)', 'level' => 8],
            ['name' => 'Grade 4', 'display_name' => 'Grade 4 (Elementary)', 'level' => 9],
            ['name' => 'Grade 5', 'display_name' => 'Grade 5 (Elementary)', 'level' => 10],
            ['name' => 'Grade 6', 'display_name' => 'Grade 6 (Elementary)', 'level' => 11],
            
            // Middle School Grades (7-8)
            ['name' => 'Grade 7', 'display_name' => 'Grade 7 (Middle School)', 'level' => 12],
            ['name' => 'Grade 8', 'display_name' => 'Grade 8 (Middle School)', 'level' => 13],
            
            // High School Grades (9-12)
            ['name' => 'Grade 9', 'display_name' => 'Grade 9 (Freshman)', 'level' => 14],
            ['name' => 'Grade 10', 'display_name' => 'Grade 10 (Sophomore)', 'level' => 15],
            ['name' => 'Grade 11', 'display_name' => 'Grade 11 (Junior)', 'level' => 16],
            ['name' => 'Grade 12', 'display_name' => 'Grade 12 (Senior)', 'level' => 17],
        ];

        foreach ($gradeLevels as $levelData) {
            GradeLevel::updateOrCreate(
                ['name' => $levelData['name']],
                $levelData + ['is_active' => true]
            );
            $this->command->info("✓ Grade Level: {$levelData['name']}");
        }

        $this->command->info('✅ Grade levels seeded successfully!');
        $this->command->info('📊 Total: ' . GradeLevel::count() . ' grade levels created');
    }
}
