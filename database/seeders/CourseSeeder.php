<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\ModuleCompletion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Create activity types if they don't exist
        $activityTypes = [
            ['name' => 'Quiz', 'weight' => 30, 'description' => 'Interactive quizzes and assessments'],
            ['name' => 'Assignment', 'weight' => 15, 'description' => 'Written assignments and projects'],
            ['name' => 'Assessment', 'weight' => 35, 'description' => 'Major assessments and exams'],
            ['name' => 'Exercise', 'weight' => 20, 'description' => 'Practice exercises and activities']
        ];

        foreach ($activityTypes as $type) {
            ActivityType::firstOrCreate(['name' => $type['name']], $type);
        }

        // Create sample courses
        $courses = [
            [
                'name' => 'Introduction to Computer Science',
                'title' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts in computer science including programming, data structures, and algorithms.',
                'instructor_id' => 1, // Assuming user ID 1 is an instructor
            ],
            [
                'name' => 'Advanced Mathematics',
                'title' => 'Advanced Mathematics',
                'description' => 'Advanced mathematical concepts including calculus, linear algebra, and statistics.',
                'instructor_id' => 1,
            ],
            [
                'name' => 'English Literature',
                'title' => 'English Literature',
                'description' => 'Comprehensive study of English literature from classical to contemporary works.',
                'instructor_id' => 1,
            ]
        ];

        foreach ($courses as $courseData) {
            $course = Course::create($courseData);
            $this->createModulesAndActivities($course);
        }

        // Enroll students and create sample progress
        $this->enrollStudentsAndCreateProgress();
    }

    private function createModulesAndActivities($course)
    {
        $moduleData = $this->getModuleData($course->title);
        
        foreach ($moduleData as $moduleInfo) {
            $module = Module::create([
                'course_id' => $course->id,
                'title' => $moduleInfo['title'],
                'description' => $moduleInfo['description'],
                'sequence' => $moduleInfo['order'],
                'module_percentage' => $moduleInfo['weight'],
                'created_by' => $course->instructor_id
            ]);

            // Create activities for each module
            foreach ($moduleInfo['activities'] as $index => $activityInfo) {
                $activityType = ActivityType::where('name', $activityInfo['type'])->first();
                
                $activity = Activity::create([
                    'activity_type_id' => $activityType->id,
                    'title' => $activityInfo['title'],
                    'description' => $activityInfo['description'],
                    'due_date' => $activityInfo['due_date'],
                    'passing_percentage' => 70, // Default passing percentage
                    'created_by' => $course->instructor_id
                ]);

                // Link activity to module through pivot table
                DB::table('module_activities')->insert([
                    'module_id' => $module->id,
                    'activity_id' => $activity->id,
                    'module_course_id' => $course->id,
                    'order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function getModuleData($courseTitle)
    {
        switch ($courseTitle) {
            case 'Introduction to Computer Science':
                return [
                    [
                        'title' => 'Programming Fundamentals',
                        'description' => 'Basic programming concepts and syntax',
                        'order' => 1,
                        'weight' => 25,
                        'activities' => [
                            [
                                'type' => 'Quiz',
                                'title' => 'Programming Basics Quiz',
                                'description' => 'Test your knowledge of basic programming concepts',
                                'due_date' => now()->addDays(7),
                                'max_score' => 100,
                                'order' => 1
                            ],
                            [
                                'type' => 'Assignment',
                                'title' => 'Hello World Program',
                                'description' => 'Write your first program',
                                'due_date' => now()->addDays(10),
                                'max_score' => 50,
                                'order' => 2
                            ],
                            [
                                'type' => 'Exercise',
                                'title' => 'Variable Practice',
                                'description' => 'Practice working with variables',
                                'due_date' => now()->addDays(5),
                                'max_score' => 25,
                                'order' => 3
                            ]
                        ]
                    ],
                    [
                        'title' => 'Data Structures',
                        'description' => 'Arrays, lists, stacks, and queues',
                        'order' => 2,
                        'weight' => 30,
                        'activities' => [
                            [
                                'type' => 'Assessment',
                                'title' => 'Data Structures Exam',
                                'description' => 'Comprehensive exam on data structures',
                                'due_date' => now()->addDays(21),
                                'max_score' => 100,
                                'order' => 1
                            ],
                            [
                                'type' => 'Quiz',
                                'title' => 'Arrays and Lists Quiz',
                                'description' => 'Quick quiz on arrays and lists',
                                'due_date' => now()->addDays(14),
                                'max_score' => 50,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Algorithms',
                        'description' => 'Sorting, searching, and algorithm analysis',
                        'order' => 3,
                        'weight' => 25,
                        'activities' => [
                            [
                                'type' => 'Assignment',
                                'title' => 'Sorting Algorithm Implementation',
                                'description' => 'Implement various sorting algorithms',
                                'due_date' => now()->addDays(28),
                                'max_score' => 100,
                                'order' => 1
                            ],
                            [
                                'type' => 'Exercise',
                                'title' => 'Big O Notation Practice',
                                'description' => 'Practice analyzing algorithm complexity',
                                'due_date' => now()->addDays(25),
                                'max_score' => 30,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Final Project',
                        'description' => 'Capstone project integrating all concepts',
                        'order' => 4,
                        'weight' => 20,
                        'activities' => [
                            [
                                'type' => 'Assessment',
                                'title' => 'Final Project Presentation',
                                'description' => 'Present your final project',
                                'due_date' => now()->addDays(35),
                                'max_score' => 150,
                                'order' => 1
                            ]
                        ]
                    ]
                ];

            case 'Advanced Mathematics':
                return [
                    [
                        'title' => 'Calculus Review',
                        'description' => 'Review of differential and integral calculus',
                        'order' => 1,
                        'weight' => 30,
                        'activities' => [
                            [
                                'type' => 'Quiz',
                                'title' => 'Derivatives Quiz',
                                'description' => 'Test your knowledge of derivatives',
                                'due_date' => now()->addDays(7),
                                'max_score' => 100,
                                'order' => 1
                            ],
                            [
                                'type' => 'Assessment',
                                'title' => 'Calculus Midterm',
                                'description' => 'Comprehensive calculus examination',
                                'due_date' => now()->addDays(21),
                                'max_score' => 150,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Linear Algebra',
                        'description' => 'Matrices, vectors, and linear transformations',
                        'order' => 2,
                        'weight' => 35,
                        'activities' => [
                            [
                                'type' => 'Assignment',
                                'title' => 'Matrix Operations',
                                'description' => 'Complete matrix operation problems',
                                'due_date' => now()->addDays(14),
                                'max_score' => 75,
                                'order' => 1
                            ],
                            [
                                'type' => 'Exercise',
                                'title' => 'Vector Space Problems',
                                'description' => 'Practice vector space concepts',
                                'due_date' => now()->addDays(10),
                                'max_score' => 50,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Statistics',
                        'description' => 'Probability and statistical analysis',
                        'order' => 3,
                        'weight' => 35,
                        'activities' => [
                            [
                                'type' => 'Assessment',
                                'title' => 'Statistics Final Exam',
                                'description' => 'Final examination on statistical concepts',
                                'due_date' => now()->addDays(35),
                                'max_score' => 200,
                                'order' => 1
                            ],
                            [
                                'type' => 'Quiz',
                                'title' => 'Probability Quiz',
                                'description' => 'Basic probability concepts',
                                'due_date' => now()->addDays(28),
                                'max_score' => 60,
                                'order' => 2
                            ]
                        ]
                    ]
                ];

            case 'English Literature':
                return [
                    [
                        'title' => 'Classical Literature',
                        'description' => 'Study of classical literary works',
                        'order' => 1,
                        'weight' => 25,
                        'activities' => [
                            [
                                'type' => 'Assignment',
                                'title' => 'Shakespeare Analysis',
                                'description' => 'Analyze a Shakespeare play',
                                'due_date' => now()->addDays(14),
                                'max_score' => 100,
                                'order' => 1
                            ],
                            [
                                'type' => 'Quiz',
                                'title' => 'Classical Authors Quiz',
                                'description' => 'Test knowledge of classical authors',
                                'due_date' => now()->addDays(7),
                                'max_score' => 50,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Modern Literature',
                        'description' => 'Contemporary literary works and themes',
                        'order' => 2,
                        'weight' => 30,
                        'activities' => [
                            [
                                'type' => 'Assessment',
                                'title' => 'Modern Literature Exam',
                                'description' => 'Comprehensive exam on modern works',
                                'due_date' => now()->addDays(21),
                                'max_score' => 150,
                                'order' => 1
                            ],
                            [
                                'type' => 'Exercise',
                                'title' => 'Literary Analysis Practice',
                                'description' => 'Practice analyzing literary techniques',
                                'due_date' => now()->addDays(10),
                                'max_score' => 40,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Creative Writing',
                        'description' => 'Develop creative writing skills',
                        'order' => 3,
                        'weight' => 25,
                        'activities' => [
                            [
                                'type' => 'Assignment',
                                'title' => 'Short Story Writing',
                                'description' => 'Write an original short story',
                                'due_date' => now()->addDays(28),
                                'max_score' => 100,
                                'order' => 1
                            ],
                            [
                                'type' => 'Exercise',
                                'title' => 'Poetry Workshop',
                                'description' => 'Write and critique poetry',
                                'due_date' => now()->addDays(21),
                                'max_score' => 60,
                                'order' => 2
                            ]
                        ]
                    ],
                    [
                        'title' => 'Literary Criticism',
                        'description' => 'Advanced literary criticism and theory',
                        'order' => 4,
                        'weight' => 20,
                        'activities' => [
                            [
                                'type' => 'Assessment',
                                'title' => 'Critical Theory Final',
                                'description' => 'Final exam on literary criticism',
                                'due_date' => now()->addDays(35),
                                'max_score' => 120,
                                'order' => 1
                            ]
                        ]
                    ]
                ];

            default:
                return [];
        }
    }

    private function enrollStudentsAndCreateProgress()
    {
        // Get all students (users with role_id = 3)
        $students = User::where('role_id', 3)->take(20)->get();
        $courses = Course::with(['modules.activities'])->get();

        foreach ($students as $user) {
            // Find or create the student record for this user
            $student = Student::firstOrCreate([
                'user_id' => $user->id
            ], [
                'student_id' => 'STU' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'enrollment_number' => 'EN' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'academic_year' => date('Y') . '-' . (date('Y') + 1),
                'program' => 'General Studies',
                'department' => 'Academic',
                'enrollment_date' => now(),
                'status' => 'active'
            ]);

            foreach ($courses as $course) {
                // Enroll student in course
                if (!$course->students()->where('course_student.student_id', $student->id)->exists()) {
                    $course->students()->attach($student->id);
                }

                // Create random progress for this student
                $this->createStudentProgress($student, $course);
            }
        }
    }

    private function createStudentProgress($student, $course)
    {
        foreach ($course->modules as $module) {
            // Randomly determine if module is completed (70% chance)
            $isModuleCompleted = rand(1, 100) <= 70;
            
            if ($isModuleCompleted && !ModuleCompletion::where('user_id', $student->user_id)->where('module_id', $module->id)->exists()) {
                ModuleCompletion::create([
                    'user_id' => $student->user_id,
                    'module_id' => $module->id,
                    'course_id' => $course->id,
                    'completed_at' => now()->subDays(rand(1, 30))
                ]);
            }

            // Create activity progress
            foreach ($module->activities as $activity) {
                // Skip if already exists
                if (StudentActivity::where('user_id', $student->id)->where('activity_id', $activity->id)->exists()) {
                    continue;
                }

                // 80% chance of attempting activity, 60% chance of completing it
                $hasAttempted = rand(1, 100) <= 80;
                
                if ($hasAttempted) {
                    $isCompleted = rand(1, 100) <= 60;
                    $score = $isCompleted ? rand(60, 100) : rand(30, 85);
                    
                    // Map activity type names to database constraint values
                    $activityTypeMap = [
                        'Quiz' => 'quiz',
                        'Assignment' => 'assignment', 
                        'Exercise' => 'project', // Map Exercise to project
                        'Assessment' => 'assessment'
                    ];
                    
                    StudentActivity::create([
                        'user_id' => $student->id,
                        'module_id' => $module->id,
                        'course_id' => $course->id,
                        'activity_id' => $activity->id,
                        'activity_type' => $activityTypeMap[$activity->activityType->name] ?? 'project',  
                        'status' => $isCompleted ? 'completed' : 'in_progress',
                        'score' => $score,
                        'max_score' => 100,
                        'percentage_score' => $score,
                        'started_at' => now()->subDays(rand(1, 25)),
                        'completed_at' => $isCompleted ? now()->subDays(rand(1, 20)) : null
                    ]);
                }
            }
        }
    }
}