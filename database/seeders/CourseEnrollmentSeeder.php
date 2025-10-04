<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\User;
use App\Models\CourseEnrollment;
use App\Models\LessonCompletion;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create sample courses with modules and lessons
        $courses = [
            [
                'title' => 'Introduction to Mathematics',
                'description' => 'This comprehensive course covers fundamental mathematical concepts including algebra, geometry, calculus, and statistics. Students will develop problem-solving skills and learn to apply mathematical principles to real-world situations.',
                'instructor_name' => 'Dr. Sarah Smith',
                'modules' => [
                    [
                        'name' => 'Fundamentals',
                        'description' => 'Basic mathematical concepts and operations',
                        'lessons' => [
                            [
                                'title' => 'Introduction to Numbers and Basic Operations',
                                'description' => 'Learn about different types of numbers and basic mathematical operations.',
                                'duration' => 45,
                                'content_type' => 'video'
                            ],
                            [
                                'title' => 'Fractions and Decimals',
                                'description' => 'Working with fractions, decimals, and percentage calculations.',
                                'duration' => 40,
                                'content_type' => 'quiz'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Algebra',
                        'description' => 'Algebraic expressions and equation solving',
                        'lessons' => [
                            [
                                'title' => 'Algebraic Expressions and Equations',
                                'description' => 'Understanding variables, expressions, and solving linear equations.',
                                'duration' => 60,
                                'content_type' => 'text'
                            ],
                            [
                                'title' => 'Quadratic Equations',
                                'description' => 'Solving quadratic equations using various methods.',
                                'duration' => 55,
                                'content_type' => 'video'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Geometry',
                        'description' => 'Geometric shapes and spatial reasoning',
                        'lessons' => [
                            [
                                'title' => 'Geometry Basics: Shapes and Properties',
                                'description' => 'Explore basic geometric shapes, properties, and measurements.',
                                'duration' => 50,
                                'content_type' => 'video'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Statistics',
                        'description' => 'Data analysis and probability concepts',
                        'lessons' => [
                            [
                                'title' => 'Introduction to Statistics',
                                'description' => 'Basic statistical concepts, data collection, and analysis.',
                                'duration' => 55,
                                'content_type' => 'text'
                            ],
                            [
                                'title' => 'Probability Fundamentals',
                                'description' => 'Understanding probability concepts and calculations.',
                                'duration' => 45,
                                'content_type' => 'assignment'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Web Development Fundamentals',
                'description' => 'Learn the essential skills needed to become a web developer. This course covers HTML, CSS, JavaScript, and modern web development practices.',
                'instructor_name' => 'Prof. John Davis',
                'modules' => [
                    [
                        'name' => 'HTML Basics',
                        'description' => 'Structure and markup fundamentals',
                        'lessons' => [
                            [
                                'title' => 'HTML Structure and Elements',
                                'description' => 'Understanding HTML document structure and basic elements.',
                                'duration' => 50,
                                'content_type' => 'video'
                            ],
                            [
                                'title' => 'Forms and Input Elements',
                                'description' => 'Creating interactive forms and handling user input.',
                                'duration' => 45,
                                'content_type' => 'text'
                            ]
                        ]
                    ],
                    [
                        'name' => 'CSS Styling',
                        'description' => 'Styling and layout techniques',
                        'lessons' => [
                            [
                                'title' => 'CSS Selectors and Properties',
                                'description' => 'Learn how to select elements and apply styles.',
                                'duration' => 60,
                                'content_type' => 'video'
                            ],
                            [
                                'title' => 'Flexbox and Grid Layout',
                                'description' => 'Modern CSS layout techniques for responsive design.',
                                'duration' => 70,
                                'content_type' => 'quiz'
                            ]
                        ]
                    ],
                    [
                        'name' => 'JavaScript Programming',
                        'description' => 'Interactive web programming',
                        'lessons' => [
                            [
                                'title' => 'JavaScript Basics and Variables',
                                'description' => 'Introduction to JavaScript programming concepts.',
                                'duration' => 65,
                                'content_type' => 'video'
                            ],
                            [
                                'title' => 'DOM Manipulation',
                                'description' => 'Interacting with HTML elements using JavaScript.',
                                'duration' => 55,
                                'content_type' => 'assignment'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Data Science with Python',
                'description' => 'Comprehensive introduction to data science using Python. Learn data analysis, visualization, and machine learning fundamentals.',
                'instructor_name' => 'Dr. Maria Rodriguez',
                'modules' => [
                    [
                        'name' => 'Python Fundamentals',
                        'description' => 'Core Python programming concepts',
                        'lessons' => [
                            [
                                'title' => 'Python Syntax and Data Types',
                                'description' => 'Basic Python programming and data structures.',
                                'duration' => 55,
                                'content_type' => 'video'
                            ],
                            [
                                'title' => 'Control Flow and Functions',
                                'description' => 'Loops, conditions, and function definitions.',
                                'duration' => 60,
                                'content_type' => 'text'
                            ]
                        ]
                    ],
                    [
                        'name' => 'Data Analysis',
                        'description' => 'Working with data using pandas and numpy',
                        'lessons' => [
                            [
                                'title' => 'Introduction to Pandas',
                                'description' => 'Data manipulation and analysis with pandas library.',
                                'duration' => 70,
                                'content_type' => 'video'
                            ],
                            [
                                'title' => 'Data Visualization with Matplotlib',
                                'description' => 'Creating charts and graphs to visualize data.',
                                'duration' => 65,
                                'content_type' => 'quiz'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $createdCourses = [];

        // Create courses, modules, and lessons
        foreach ($courses as $courseData) {
            $course = Course::create([
                'title' => $courseData['title'],
                'description' => $courseData['description'],
                'instructor_name' => $courseData['instructor_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $createdCourses[] = $course;

            $lessonOrder = 1;
            foreach ($courseData['modules'] as $moduleData) {
                $module = Module::create([
                    'course_id' => $course->id,
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($moduleData['lessons'] as $lessonData) {
                    Lesson::create([
                        'course_id' => $course->id,
                        'module_id' => $module->id,
                        'title' => $lessonData['title'],
                        'description' => $lessonData['description'],
                        'duration' => $lessonData['duration'],
                        'content_type' => $lessonData['content_type'],
                        'order' => $lessonOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Get all students (users with role_id = 3)
        $students = User::where('role_id', 3)->get();

        if ($students->count() === 0) {
            $this->command->warn('No students found. Please run the StudentSeeder first.');
            return;
        }

        // Enroll students in courses with various progress levels
        foreach ($students as $student) {
            // Each student gets enrolled in 1-3 courses randomly
            $coursesToEnroll = $faker->randomElements($createdCourses, $faker->numberBetween(1, 3));
            
            foreach ($coursesToEnroll as $course) {
                // Create enrollment
                $enrollment = CourseEnrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'enrolled_at' => $faker->dateTimeBetween('-3 months', '-1 week'),
                    'is_completed' => false,
                    'progress_percentage' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Get all lessons for this course
                $lessons = $course->lessons()->orderBy('order')->get();
                
                if ($lessons->count() > 0) {
                    // Randomly complete some lessons (30-80% completion rate)
                    $completionRate = $faker->numberBetween(30, 80) / 100;
                    $lessonsToComplete = (int) ($lessons->count() * $completionRate);
                    
                    $completedLessons = $lessons->take($lessonsToComplete);
                    
                    foreach ($completedLessons as $lesson) {
                        LessonCompletion::create([
                            'user_id' => $student->id,
                            'lesson_id' => $lesson->id,
                            'course_id' => $course->id,
                            'completed_at' => $faker->dateTimeBetween($enrollment->enrolled_at, 'now'),
                            'completion_data' => json_encode([
                                'method' => 'auto_seeded',
                                'score' => $faker->numberBetween(70, 100),
                                'time_spent' => $faker->numberBetween(20, $lesson->duration * 2),
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Update enrollment progress
                    $enrollment->updateProgress();
                }
            }
        }

        $this->command->info('Created ' . count($createdCourses) . ' courses with modules and lessons.');
        $this->command->info('Enrolled ' . $students->count() . ' students in courses with sample progress.');
        $this->command->info('Sample courses:');
        foreach ($createdCourses as $course) {
            $this->command->line('  - ' . $course->title);
        }
    }
}
