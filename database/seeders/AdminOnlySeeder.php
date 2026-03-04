<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityType;
use App\Models\QuestionType;
use App\Models\AssignmentType;
use App\Models\GradeLevel;
use App\Enums\ScheduleTypeEnum;
use Carbon\Carbon;

class AdminOnlySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting admin-only database seeding...');
        
        // Clear existing data (minimal)
        $this->command->info('Clearing existing data...');
        $this->clearExistingData();

        // 1. Foundation Data (Types & Config)
        $this->command->info('Seeding foundation data...');
        $this->seedRoles();
        $this->seedGradeLevels();
        $this->seedActivityTypes();
        $this->seedScheduleTypes();
        
        $this->command->info('✅ Foundation data seeded successfully!');

        // 2. Create Admin User
        $this->command->info('Creating admin user...');
        $this->seedAdminUser();

        $this->command->info('🎉 Admin-only database seeding completed successfully!');
    }

    private function clearExistingData(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');
        
        // Only clear users and roles (not courses or activities)
        DB::table('users')->delete();
        
        DB::statement('PRAGMA foreign_keys=ON');
    }

    private function seedRoles(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'System administrator with full access to all features',
                'is_active' => true,
            ],
            [
                'name' => 'instructor',
                'display_name' => 'Instructor',
                'description' => 'Course instructor who can manage courses, assignments, and students',
                'is_active' => true,
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student who can access courses and complete assignments',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
            $this->command->info("✓ Role: {$roleData['name']}");
        }
    }

    private function seedGradeLevels(): void
    {
        // Year Levels (1-5) - Primary
        $yearLevels = [
            ['name' => 'Year 1', 'display_name' => 'Year 1 (Primary)', 'level' => 1, 'is_active' => true],
            ['name' => 'Year 2', 'display_name' => 'Year 2 (Primary)', 'level' => 2, 'is_active' => true],
            ['name' => 'Year 3', 'display_name' => 'Year 3 (Primary)', 'level' => 3, 'is_active' => true],
            ['name' => 'Year 4', 'display_name' => 'Year 4 (Primary)', 'level' => 4, 'is_active' => true],
            ['name' => 'Year 5', 'display_name' => 'Year 5 (Primary)', 'level' => 5, 'is_active' => true],
        ];

        // Grade Levels (1-12) - Standard K-12
        $gradeLevels = [
            // Elementary
            ['name' => 'Grade 1', 'display_name' => 'Grade 1 (Elementary)', 'level' => 6, 'is_active' => true],
            ['name' => 'Grade 2', 'display_name' => 'Grade 2 (Elementary)', 'level' => 7, 'is_active' => true],
            ['name' => 'Grade 3', 'display_name' => 'Grade 3 (Elementary)', 'level' => 8, 'is_active' => true],
            ['name' => 'Grade 4', 'display_name' => 'Grade 4 (Elementary)', 'level' => 9, 'is_active' => true],
            ['name' => 'Grade 5', 'display_name' => 'Grade 5 (Elementary)', 'level' => 10, 'is_active' => true],
            ['name' => 'Grade 6', 'display_name' => 'Grade 6 (Elementary)', 'level' => 11, 'is_active' => true],
            // Middle School
            ['name' => 'Grade 7', 'display_name' => 'Grade 7 (Middle School)', 'level' => 12, 'is_active' => true],
            ['name' => 'Grade 8', 'display_name' => 'Grade 8 (Middle School)', 'level' => 13, 'is_active' => true],
            // High School
            ['name' => 'Grade 9', 'display_name' => 'Grade 9 (Freshman)', 'level' => 14, 'is_active' => true],
            ['name' => 'Grade 10', 'display_name' => 'Grade 10 (Sophomore)', 'level' => 15, 'is_active' => true],
            ['name' => 'Grade 11', 'display_name' => 'Grade 11 (Junior)', 'level' => 16, 'is_active' => true],
            ['name' => 'Grade 12', 'display_name' => 'Grade 12 (Senior)', 'level' => 17, 'is_active' => true],
        ];

        foreach (array_merge($yearLevels, $gradeLevels) as $levelData) {
            GradeLevel::updateOrCreate(['name' => $levelData['name']], $levelData);
            $this->command->info("✓ Grade Level: {$levelData['name']}");
        }
    }

    private function seedActivityTypes(): void
    {
        // Activity Types with model class paths
        $activityTypes = [
            [
                'name' => 'Quiz',
                'description' => 'Interactive quiz with multiple questions for knowledge assessment',
                'model' => 'App\\Models\\Quiz'
            ],
            [
                'name' => 'Assignment',
                'description' => 'Assignment with document submission and grading',
                'model' => 'App\\Models\\Assignment'
            ],
            [
                'name' => 'Assessment',
                'description' => 'Comprehensive assessment to evaluate student competency',
                'model' => 'App\\Models\\Assessment'
            ],
            [
                'name' => 'Exercise',
                'description' => 'Practice exercise for skill development and reinforcement',
                'model' => 'App\\Models\\Exercise'
            ],
        ];

        foreach ($activityTypes as $type) {
            ActivityType::updateOrCreate(['name' => $type['name']], $type);
            $this->command->info("✓ Activity Type: {$type['name']}");
        }

        // Question Types
        $questionTypes = [
            ['type' => 'multiple-choice', 'description' => 'Question with multiple answer options'],
            ['type' => 'true-false', 'description' => 'True or False question'],
            ['type' => 'short-answer', 'description' => 'Short text answer question'],
            ['type' => 'enumeration', 'description' => 'List-based answer question'],
        ];

        foreach ($questionTypes as $type) {
            QuestionType::updateOrCreate(['type' => $type['type']], $type);
            $this->command->info("✓ Question Type: {$type['type']}");
        }

        // Assignment Types
        $assignmentTypes = [
            ['type' => 'homework'],
            ['type' => 'project'],
            ['type' => 'essay'],
            ['type' => 'research'],
        ];

        foreach ($assignmentTypes as $type) {
            AssignmentType::updateOrCreate(['type' => $type['type']], $type);
            $this->command->info("✓ Assignment Type: {$type['type']}");
        }
    }

    private function seedScheduleTypes(): void
    {
        foreach (ScheduleTypeEnum::cases() as $type) {
            $data = $type->toSeederArray();
            
            $exists = DB::table('schedule_types')->where('name', $type->value)->exists();
            
            if ($exists) {
                DB::table('schedule_types')
                    ->where('name', $type->value)
                    ->update([
                        'description' => $data['description'],
                        'color' => $data['color'],
                        'icon' => $data['icon'],
                        'is_active' => $data['is_active'],
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('schedule_types')->insert($data);
            }
            
            $this->command->info("✓ Schedule Type: {$type->label()}");
        }
    }

    private function seedAdminUser(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role_id' => 1, // Admin role
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✓ Admin User: admin@example.com (password: password)');
    }
}
