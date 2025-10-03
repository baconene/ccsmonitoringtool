<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Insert a course
        $courseId = DB::table('courses')->insertGetId([
            'name' => 'Test Course',
            'description' => 'This is a test course for seeding example.',
            'instructor_id' => 1, // Assign to first user
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Insert a module for this course
        $moduleId = DB::table('modules')->insertGetId([
            'course_id' => $courseId,
            'description' => 'Introduction Module',
            'sequence' => 1,
            'completion_percentage' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Insert a lesson for this module
        $lessonId = DB::table('lessons')->insertGetId([ 
            'title' => 'Getting Started',
            'description' => '<p>Welcome to the first lesson of the test course.</p>',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Optionally insert a related document
        DB::table('documents')->insert([ 
            'name' => 'Sample PDF',
            'file_path' => 'documents/sample.pdf',
            'doc_type' => 'pdf',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
 
        // Link lesson to module
        DB::table('lesson_module')->insert([
            'lesson_id' => $lessonId,
            'module_id' => $moduleId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('lesson_document')->insert([
            'lesson_id' => $lessonId,
            'document_id' => 1, // Assuming the document ID is 1
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Add additional courses with instructor_id
        $additionalCourses = [
            [
                'name' => 'Advanced JavaScript',
                'title' => 'Advanced JavaScript Programming',
                'description' => 'Deep dive into advanced JavaScript concepts, ES6+, and modern frameworks.',
                'instructor_id' => 1,
            ],
            [
                'name' => 'React Development', 
                'title' => 'Modern React Development',
                'description' => 'Build dynamic user interfaces with React.js and modern development tools.',
                'instructor_id' => 1,
            ],
            [
                'name' => 'Vue.js Fundamentals',
                'title' => 'Vue.js Framework Fundamentals', 
                'description' => 'Learn Vue.js framework for building interactive web applications.',
                'instructor_id' => 1,
            ],
            [
                'name' => 'Laravel Backend',
                'title' => 'Laravel PHP Backend Development',
                'description' => 'Master Laravel framework for building robust web applications.',
                'instructor_id' => 1,
            ],
            [
                'name' => 'Database Design',
                'title' => 'Advanced Database Design',
                'description' => 'Learn database design principles and optimization techniques.',
                'instructor_id' => 1,
            ],
            [
                'name' => 'API Development',
                'title' => 'RESTful API Development',
                'description' => 'Build and design RESTful APIs with proper authentication and security.',
                'instructor_id' => 1,
            ]
        ];

        foreach ($additionalCourses as $course) {
            DB::table('courses')->insert([
                'name' => $course['name'],
                'title' => $course['title'],
                'description' => $course['description'],
                'instructor_id' => $course['instructor_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
