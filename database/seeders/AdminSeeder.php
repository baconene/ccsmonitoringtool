<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin role
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Administrator with full system access'
            ]
        );

        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'role_id' => $adminRole->id,
        ]);

        $this->command->info('Admin user created successfully:');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: admin123');
        $this->command->warn('Please change the default password after first login!');
    }
}
