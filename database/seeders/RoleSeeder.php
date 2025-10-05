<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding Roles...');
        
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
            try {
                $role = Role::updateOrCreate(
                    ['name' => $roleData['name']],
                    $roleData
                );
                $this->command->info("✓ Role: {$role->name}");
            } catch (\Exception $e) {
                $this->command->warn("⚠️  Skipped role {$roleData['name']}: " . $e->getMessage());
                continue;
            }
        }
        
        $this->command->info('✅ Roles seeded successfully!');
    }
}
