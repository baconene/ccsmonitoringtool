<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive database seeding...');
        
        // Use the single comprehensive seeder
        $this->call([
            SingleComprehensiveSeeder::class,
        ]);
        
        $this->command->info('🎉 Database seeding completed successfully!');
    }
}
