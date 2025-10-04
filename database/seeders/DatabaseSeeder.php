<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed the roles
        $this->call(RoleSeeder::class);
        
        // Then seed courses
        $this->call(CourseSeeder::class);
        
        // Create test users with different roles (using string roles for now)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
        
        User::factory()->create([
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'role' => 'instructor',
        ]);
        
        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'role' => 'student',
        ]);
        
        // Migrate users from string roles to role relationships
        $this->call(UserRoleMigrationSeeder::class);
        
        // Create sample students
        $this->call(StudentSeeder::class);
    }
}
