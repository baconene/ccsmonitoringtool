<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\AssignmentType;
use App\Models\QuestionType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Activity Types...');

        // Activity Types
        $activityTypes = [
            ['name' => 'Quiz', 'description' => 'Interactive quiz with multiple questions'],
            ['name' => 'Assignment', 'description' => 'Assignment with document submission'],
            ['name' => 'Exercise', 'description' => 'Practice exercise for students'],
        ];

        foreach ($activityTypes as $type) {
            try {
                ActivityType::updateOrCreate(
                    ['name' => $type['name']],
                    $type
                );
                $this->command->info("✓ Activity Type: {$type['name']}");
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Skipped activity type {$type['name']}: " . $e->getMessage());
                continue;
            }
        }

        // Question Types
        $questionTypes = [
            ['type' => 'multiple-choice', 'description' => 'Question with multiple answer options'],
            ['type' => 'true-false', 'description' => 'True or False question'],
            ['type' => 'short-answer', 'description' => 'Short text answer question'],
            ['type' => 'enumeration', 'description' => 'List-based answer question'],
        ];

        foreach ($questionTypes as $type) {
            try {
                QuestionType::updateOrCreate(
                    ['type' => $type['type']],
                    $type
                );
                $this->command->info("✓ Question Type: {$type['type']}");
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Skipped question type {$type['type']}: " . $e->getMessage());
                continue;
            }
        }

        // Assignment Types
        $assignmentTypes = [
            ['type' => 'homework'],
            ['type' => 'project'],
            ['type' => 'essay'],
            ['type' => 'research'],
        ];

        foreach ($assignmentTypes as $type) {
            try {
                AssignmentType::updateOrCreate(
                    ['type' => $type['type']],
                    $type
                );
                $this->command->info("✓ Assignment Type: {$type['type']}");
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Skipped assignment type {$type['type']}: " . $e->getMessage());
                continue;
            }
        }

        $this->command->info('✅ Activity types seeded successfully!');
    }
}
