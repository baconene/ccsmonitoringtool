<?php

namespace Database\Seeders;

use App\Enums\ScheduleTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Uses ScheduleTypeEnum to ensure consistency across the application.
     * To add new schedule types, update the enum class.
     */
    public function run(): void
    {
        // Get all schedule types from the enum
        $scheduleTypes = ScheduleTypeEnum::getAllSeederData();

        // Insert all schedule types
        DB::table('schedule_types')->insert($scheduleTypes);
        
        $count = count($scheduleTypes);
        $this->command->info("✅ {$count} schedule types seeded successfully!");
        
        // Display what was seeded
        foreach (ScheduleTypeEnum::cases() as $type) {
            $this->command->line("   - {$type->label()} ({$type->value}) - {$type->color()}");
        }
    }
}
