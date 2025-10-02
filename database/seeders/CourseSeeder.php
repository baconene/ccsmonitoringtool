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
    }
}
