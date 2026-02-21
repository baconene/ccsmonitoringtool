<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Skill;
use App\Models\StudentSkillAssessment;
use App\Services\StudentAssessmentService;
use Illuminate\Database\Seeder;

class StudentSkillAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessmentService = app(StudentAssessmentService::class);

        // Get all students
        $students = Student::with('courses.modules.skills')
            ->whereHas('courses')
            ->take(5) // Limit to first 5 students for demo
            ->get();

        if ($students->isEmpty()) {
            $this->command->info('No students found. Please enroll students in courses first.');
            return;
        }

        $assessmentsCreated = 0;

        foreach ($students as $student) {
            foreach ($student->courses as $course) {
                foreach ($course->modules as $module) {
                    foreach ($module->skills as $skill) {
                        // Get the student's activities for this skill
                        $activities = $skill->activities()
                            ->whereIn('activities.id', function ($query) use ($module) {
                                $query->select('activity_id')
                                    ->from('module_activities')
                                    ->where('module_id', $module->id);
                            })
                            ->get();

                        if ($activities->isNotEmpty()) {
                            // Calculate assessment using the service
                            $assessmentService->calculateOrUpdateSkillAssessment($student, $skill);
                            $assessmentsCreated++;
                        }
                    }
                }
            }
        }

        $this->command->info("Created assessments for {$assessmentsCreated} student-skill combinations.");
    }
}
