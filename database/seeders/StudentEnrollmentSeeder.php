<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudentEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create student users
        $studentUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@student.example.com',
                'program' => 'Computer Science',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@student.example.com',
                'program' => 'Information Technology',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@student.example.com',
                'program' => 'Computer Science',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@student.example.com',
                'program' => 'Web Development',
                'department' => 'Engineering'
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@student.example.com',
                'program' => 'Software Engineering',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@student.example.com',
                'program' => 'Computer Science',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Chris Lee',
                'email' => 'chris.lee@student.example.com',
                'program' => 'Information Technology',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Alex Thompson',
                'email' => 'alex.thompson@student.example.com',
                'program' => 'Web Development',
                'department' => 'Engineering'
            ],
            [
                'name' => 'Lisa Garcia',
                'email' => 'lisa.garcia@student.example.com',
                'program' => 'Software Engineering',
                'department' => 'Engineering'
            ],
        ];

        $students = [];
        foreach ($studentUsers as $studentData) {
            // Create or get user
            $user = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Create student record
            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'student_id' => Student::generateStudentId(),
                    'user_id' => $user->id,
                    'enrollment_number' => 'ENR' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'academic_year' => '2024-2025',
                    'program' => $studentData['program'],
                    'department' => $studentData['department'],
                    'enrollment_date' => Carbon::now()->subMonths(rand(1, 6)),
                    'status' => 'active',
                ]
            );

            $students[] = $student;
        }

        // Get all courses
        $courses = Course::all();

        // Enroll students in courses
        foreach ($courses as $course) {
            // Randomly select 3-6 students for each course
            $selectedStudents = collect($students)->random(rand(3, 6));
            
            foreach ($selectedStudents as $student) {
                // Check if already enrolled to avoid duplicates
                if (!$course->students()->where('students.id', $student->id)->exists()) {
                    $course->students()->attach($student->id, [
                        'enrolled_at' => Carbon::now()->subDays(rand(1, 30)),
                        'status' => 'enrolled',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Students and course enrollments created successfully!');
        $this->command->info('Total students created: ' . count($students));
        $this->command->info('Total courses with enrollments: ' . $courses->count());
    }
}
