<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles for mapping
        $adminRole = Role::where('name', 'admin')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $studentRole = Role::where('name', 'student')->first();

        if (!$adminRole || !$instructorRole || !$studentRole) {
            $this->command->error('Roles must be seeded first. Run: php artisan db:seed --class=RoleSeeder');
            return;
        }

        // Migrate users with existing string roles to role relationships
        $users = User::whereNotNull('role')->whereNull('role_id')->get();
        
        foreach ($users as $user) {
            $roleId = match($user->role) {
                'admin' => $adminRole->id,
                'instructor' => $instructorRole->id,
                'student' => $studentRole->id,
                default => $instructorRole->id, // Default to instructor
            };

            $user->update(['role_id' => $roleId]);
            
            $this->command->info("Updated user {$user->name} ({$user->email}) to role ID {$roleId}");
        }

        // For users without any role, set default to instructor
        $usersWithoutRole = User::whereNull('role')->whereNull('role_id')->get();
        foreach ($usersWithoutRole as $user) {
            $user->update([
                'role' => 'instructor',
                'role_id' => $instructorRole->id,
            ]);
            
            $this->command->info("Set default role for user {$user->name} ({$user->email})");
        }
    }
}
