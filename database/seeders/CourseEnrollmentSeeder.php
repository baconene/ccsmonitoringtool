<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get the first user as instructor
        $instructor = User::first();
        
        if (!$instructor) {
            $instructor = User::create([
                'name' => 'Dr. Jane Smith',
                'email' => 'instructor@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]);
        }

        // Create sample students
        $students = [];
        $studentData = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Mike Johnson', 'email' => 'mike@example.com'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@example.com'],
            ['name' => 'David Brown', 'email' => 'david@example.com'],
            ['name' => 'Emily Davis', 'email' => 'emily@example.com'],
            ['name' => 'Chris Lee', 'email' => 'chris@example.com'],
            ['name' => 'Alex Thompson', 'email' => 'alex@example.com'],
            ['name' => 'Lisa Garcia', 'email' => 'lisa@example.com'],
        ];

        foreach ($studentData as $data) {
            $student = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'email_verified_at' => now(),
                    'password' => bcrypt('password'),
                ]
            );
            $students[] = $student;
        }

        // Create sample courses
        $coursesData = [
            [
                'title' => 'Introduction to Web Development',
                'name' => 'Introduction to Web Development', // For backward compatibility
                'description' => 'Learn the basics of HTML, CSS, and JavaScript to build modern web applications.',
                'instructor_id' => $instructor->id,
            ],
            [
                'title' => 'Advanced JavaScript',
                'name' => 'Advanced JavaScript',
                'description' => 'Deep dive into advanced JavaScript concepts, ES6+, and modern frameworks.',
                'instructor_id' => $instructor->id,
            ],
            [
                'title' => 'React Development',
                'name' => 'React Development',
                'description' => 'Build dynamic user interfaces with React.js and modern development tools.',
                'instructor_id' => $instructor->id,
            ]
        ];

        foreach ($coursesData as $courseData) {
            $course = Course::firstOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );

            // Enroll random students in each course
            if ($course->wasRecentlyCreated || $course->students->isEmpty()) {
                $randomStudents = collect($students)->random(rand(2, 5));
                foreach ($randomStudents as $student) {
                    DB::table('course_user')->insertOrIgnore([
                        'course_id' => $course->id,
                        'user_id' => $student->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
