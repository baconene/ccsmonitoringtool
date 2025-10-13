<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scheduleTypes = [
            [
                'name' => 'activity',
                'description' => 'Scheduled activities like quizzes, assignments, and assessments',
                'color' => '#3B82F6', // Blue
                'icon' => 'clipboard-list',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'course',
                'description' => 'Course lectures, seminars, and sessions',
                'color' => '#10B981', // Green
                'icon' => 'book-open',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'adhoc',
                'description' => 'Personal events, meetings, and administrative tasks',
                'color' => '#F59E0B', // Amber
                'icon' => 'calendar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'exam',
                'description' => 'Formal examinations and tests',
                'color' => '#EF4444', // Red
                'icon' => 'file-text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'office_hours',
                'description' => 'Instructor office hours for student consultations',
                'color' => '#8B5CF6', // Purple
                'icon' => 'users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('schedule_types')->insert($scheduleTypes);
        
        $this->command->info('✅ Schedule types seeded successfully!');
    }
}
