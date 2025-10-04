<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Module;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get or create instructor user
        $instructorRole = Role::where('name', 'instructor')->first();
        if (!$instructorRole) {
            $instructorRole = Role::create([
                'name' => 'instructor',
                'display_name' => 'Instructor',
                'description' => 'Can create and manage courses'
            ]);
        }
        
        $instructorUser = User::where('email', 'instructor@example.com')->first();
        if (!$instructorUser) {
            $instructorUser = User::create([
                'name' => 'Dr. Sarah Johnson',
                'email' => 'instructor@example.com',
                'password' => bcrypt('123456789'),
                'role_id' => $instructorRole->id,
                'email_verified_at' => now(),
            ]);
        }

        // Create sample courses
        $courses = [
            [
                'title' => 'Introduction to Mathematics',
                'name' => 'Mathematics 101',
                'description' => 'This comprehensive course covers fundamental mathematical concepts including algebra, geometry, calculus, and statistics.',
                'instructor_name' => 'Dr. Sarah Johnson',
                'instructor_id' => $instructorUser->id,
                'duration' => 1200,
            ],
            [
                'title' => 'Web Development Fundamentals',
                'name' => 'Web Dev 101',
                'description' => 'Learn the basics of web development including HTML, CSS, JavaScript, and responsive design.',
                'instructor_name' => 'Prof. Michael Chen',
                'instructor_id' => $instructorUser->id,
                'duration' => 1500,
            ],
            [
                'title' => 'Data Science with Python',
                'name' => 'Data Science 201',
                'description' => 'Explore data analysis, visualization, and machine learning using Python.',
                'instructor_name' => 'Dr. Emily Rodriguez',
                'instructor_id' => $instructorUser->id,
                'duration' => 1800,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create($courseData);
            
            // Create modules for each course
            $modules = [
                ['name' => 'Fundamentals', 'description' => 'Basic concepts and foundations', 'sequence' => 1],
                ['name' => 'Intermediate', 'description' => 'Intermediate level topics', 'sequence' => 2],
                ['name' => 'Advanced', 'description' => 'Advanced concepts and applications', 'sequence' => 3],
            ];
            
            $createdModules = [];
            foreach ($modules as $moduleData) {
                $moduleData['course_id'] = $course->id;
                $moduleData['completion_percentage'] = 0;
                $module = Module::create($moduleData);
                $createdModules[] = $module;
            }
            
            // Create lessons for each module
            $lessonTemplates = [
                [
                    'title' => 'Introduction and Overview',
                    'description' => 'Overview of the topic and learning objectives.',
                    'duration' => 45,
                    'content_type' => 'video',
                    'order' => 1,
                ],
                [
                    'title' => 'Core Concepts',
                    'description' => 'Understanding the fundamental concepts.',
                    'duration' => 60,
                    'content_type' => 'text',
                    'order' => 2,
                ],
                [
                    'title' => 'Practical Exercises',
                    'description' => 'Hands-on practice and exercises.',
                    'duration' => 50,
                    'content_type' => 'assignment',
                    'order' => 3,
                ],
                [
                    'title' => 'Assessment Quiz',
                    'description' => 'Test your understanding with this quiz.',
                    'duration' => 30,
                    'content_type' => 'quiz',
                    'order' => 4,
                ],
            ];
            
            foreach ($createdModules as $index => $module) {
                foreach ($lessonTemplates as $lessonData) {
                    $lesson = $lessonData;
                    $lesson['course_id'] = $course->id;
                    $lesson['module_id'] = $module->id;
                    $lesson['title'] = $module->name . ': ' . $lesson['title'];
                    $lesson['order'] = ($index * 4) + $lesson['order'];
                    
                    Lesson::create($lesson);
                }
            }
        }

        // Get students for enrollment
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $students = User::where('role_id', $studentRole->id)->limit(8)->get();
            
            if (!$students->isEmpty()) {
                $allCourses = Course::all();
                
                foreach ($students as $student) {
                    $coursesToEnroll = $allCourses->random(rand(2, 3));
                    
                    foreach ($coursesToEnroll as $course) {
                        $existingEnrollment = CourseEnrollment::where('user_id', $student->id)
                            ->where('course_id', $course->id)
                            ->first();
                            
                        if (!$existingEnrollment) {
                            $enrollment = CourseEnrollment::create([
                                'user_id' => $student->id,
                                'course_id' => $course->id,
                                'enrolled_at' => $faker->dateTimeBetween('-3 months', 'now'),
                                'progress' => 0,
                                'is_completed' => false,
                            ]);
                            
                            // Create some lesson completions
                            $courseLessons = Lesson::where('course_id', $course->id)->get();
                            if ($courseLessons->count() > 0) {
                                $completedLessons = $courseLessons->random(rand(1, min(5, $courseLessons->count())));
                                
                                foreach ($completedLessons as $lesson) {
                                    LessonCompletion::create([
                                        'user_id' => $student->id,
                                        'lesson_id' => $lesson->id,
                                        'course_id' => $course->id,
                                        'completed_at' => $faker->dateTimeBetween($enrollment->enrolled_at, 'now'),
                                        'completion_data' => json_encode([
                                            'method' => 'seeded',
                                            'score' => $faker->numberBetween(70, 100),
                                        ])
                                    ]);
                                }
                                
                                $enrollment->updateProgress();
                            }
                        }
                    }
                }
            }
        }

        $this->command->info('Course seeder completed successfully!');
    }
}
