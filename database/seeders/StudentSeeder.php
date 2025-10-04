<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the student role (create if it doesn't exist)
        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            [
                'display_name' => 'Student',
                'description' => 'Student who can access courses and complete assignments',
                'is_active' => true,
            ]
        );
        
        $this->command->info("Using student role: {$studentRole->display_name} (ID: {$studentRole->id})");

        // Sample student data
        $students = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob.smith@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie.brown@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Diana Prince',
                'email' => 'diana.prince@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Edward Wilson',
                'email' => 'edward.wilson@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Fiona Davis',
                'email' => 'fiona.davis@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'George Martinez',
                'email' => 'george.martinez@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Hannah Taylor',
                'email' => 'hannah.taylor@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Ivan Rodriguez',
                'email' => 'ivan.rodriguez@student.edu',
                'password' => '123456789',
            ],
            [
                'name' => 'Julia Anderson',
                'email' => 'julia.anderson@student.edu',
                'password' => '123456789',
            ],
        ];

        // Create each student
        foreach ($students as $studentData) {
            $existingStudent = User::where('email', $studentData['email'])->first();
            
            if (!$existingStudent) {
                $student = User::create([
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'password' => Hash::make($studentData['password']),
                    'role_id' => $studentRole->id,
                    'email_verified_at' => now(),
                ]);

                $this->command->info("Created student: {$student->name} ({$student->email})");
            } else {
                // Update existing student's password and role
                $existingStudent->update([
                    'password' => Hash::make($studentData['password']),
                    'role_id' => $studentRole->id,
                ]);
                
                $this->command->info("Updated existing student: {$existingStudent->name} ({$existingStudent->email})");
            }
        }

        $this->command->info('Student seeder completed successfully!');
        $this->command->info('All students have the password: 123456789');
    }
}
