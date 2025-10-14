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
        $this->command->info('Seeding schedule types...');
        
        $createdCount = 0;
        $updatedCount = 0;
        
        // Loop through each schedule type and updateOrCreate
        foreach (ScheduleTypeEnum::cases() as $type) {
            $data = $type->toSeederArray();
            
            // Check if it exists
            $exists = DB::table('schedule_types')
                ->where('name', $type->value)
                ->exists();
            
            if ($exists) {
                // Update existing
                DB::table('schedule_types')
                    ->where('name', $type->value)
                    ->update([
                        'description' => $data['description'],
                        'color' => $data['color'],
                        'icon' => $data['icon'],
                        'is_active' => $data['is_active'],
                        'updated_at' => now(),
                    ]);
                $updatedCount++;
                $this->command->line("   ↻ Updated: {$type->label()} ({$type->value}) - {$type->color()}");
            } else {
                // Create new
                DB::table('schedule_types')->insert($data);
                $createdCount++;
                $this->command->line("   ✓ Created: {$type->label()} ({$type->value}) - {$type->color()}");
            }
        }
        
        $this->command->info("✅ Schedule types seeded: {$createdCount} created, {$updatedCount} updated");
    }
}
