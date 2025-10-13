<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive database seeding...');
        
        // First, seed the basic foundation data
        $this->call([
            RoleSeeder::class,              // 1. Roles (admin, instructor, student)
            GradeLevelSeeder::class,        // 2. Grade Levels (Year 1-5, Grade 1-12)
            ActivityTypeSeeder::class,      // 3. Activity Types (Quiz, Assignment, etc.)
            QuestionTypeSeeder::class,      // 4. Question Types (Multiple Choice, True/False, etc.)
        ]);
        
        $this->command->info('✅ Foundation data seeded successfully!');
        
        // Then seed the comprehensive data
        $this->call([
            ComprehensiveSeeder::class,     // 5. Users, Students, Courses, etc.
        ]);
        
        $this->command->info('🎉 Database seeding completed successfully!');
    }
}
