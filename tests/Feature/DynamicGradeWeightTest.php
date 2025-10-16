<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\LessonCompletion;
use App\Models\StudentActivity;
use App\Services\GradeCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DynamicGradeWeightTest extends TestCase
{
    use RefreshDatabase;

    private GradeCalculatorService $gradeService;
    private User $student;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gradeService = new GradeCalculatorService();
        
        // Create test user
        $this->student = User::factory()->create();
        
        // Create test course
        $this->course = Course::factory()->create([
            'name' => 'Test Course',
            'title' => 'Dynamic Weight Test'
        ]);
    }

    /** @test */
    public function it_handles_module_with_only_lessons()
    {
        // Create module with only lessons
        $module = Module::factory()->create([
            'course_id' => $this->course->id,
            'description' => 'Lessons Only Module'
        ]);

        // Create 2 lessons
        $lesson1 = Lesson::factory()->create(['module_id' => $module->id]);
        $lesson2 = Lesson::factory()->create(['module_id' => $module->id]);

        // Complete both lessons
        LessonCompletion::create([
            'user_id' => $this->student->id,
            'lesson_id' => $lesson1->id,
            'is_completed' => true,
            'completed_at' => now()
        ]);
        LessonCompletion::create([
            'user_id' => $this->student->id,
            'lesson_id' => $lesson2->id,
            'is_completed' => true,
            'completed_at' => now()
        ]);

        // Calculate grade
        $result = $this->gradeService->calculateModuleGrade($this->student->id, $module);

        // Assertions
        $this->assertEquals(100, $result['lesson_score']); // 2/2 lessons = 100%
        $this->assertEquals(100, $result['lesson_weight_used']); // Lessons should be 100% of weight
        $this->assertEquals(0, $result['activity_weight_used']); // Activities should be 0%
        $this->assertEquals(100, $result['module_score']); // 100% lesson score × 100% weight = 100%
        $this->assertTrue($result['has_lessons']);
        $this->assertFalse($result['has_activities']);
    }

    /** @test */
    public function it_handles_module_with_only_activities()
    {
        // Create module with only activities
        $module = Module::factory()->create([
            'course_id' => $this->course->id,
            'description' => 'Activities Only Module'
        ]);

        // Create activity type
        $quizType = ActivityType::firstOrCreate(['name' => 'Quiz']);

        // Create 2 quizzes
        $quiz1 = Activity::factory()->create([
            'module_id' => $module->id,
            'activity_type_id' => $quizType->id,
            'max_score' => 100
        ]);
        $quiz2 = Activity::factory()->create([
            'module_id' => $module->id,
            'activity_type_id' => $quizType->id,
            'max_score' => 100
        ]);

        // Complete both with 80% and 90%
        StudentActivity::create([
            'student_id' => $this->student->id,
            'activity_id' => $quiz1->id,
            'module_id' => $module->id,
            'course_id' => $this->course->id,
            'activity_type' => 'Quiz',
            'score' => 80,
            'max_score' => 100,
            'percentage_score' => 80,
            'status' => 'completed',
            'completed_at' => now()
        ]);
        StudentActivity::create([
            'student_id' => $this->student->id,
            'activity_id' => $quiz2->id,
            'module_id' => $module->id,
            'course_id' => $this->course->id,
            'activity_type' => 'Quiz',
            'score' => 90,
            'max_score' => 100,
            'percentage_score' => 90,
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Calculate grade
        $result = $this->gradeService->calculateModuleGrade($this->student->id, $module);

        // Assertions
        $this->assertEquals(85, $result['activity_score']); // (80 + 90) / 2 = 85%
        $this->assertEquals(0, $result['lesson_weight_used']); // Lessons should be 0%
        $this->assertEquals(100, $result['activity_weight_used']); // Activities should be 100%
        $this->assertEquals(85, $result['module_score']); // 85% activity score × 100% weight = 85%
        $this->assertFalse($result['has_lessons']);
        $this->assertTrue($result['has_activities']);
    }

    /** @test */
    public function it_handles_module_with_only_one_activity_type()
    {
        // Create module with lessons and only quizzes
        $module = Module::factory()->create([
            'course_id' => $this->course->id,
            'description' => 'Lessons + Quizzes Module'
        ]);

        // Create 1 lesson
        $lesson = Lesson::factory()->create(['module_id' => $module->id]);
        LessonCompletion::create([
            'user_id' => $this->student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
            'completed_at' => now()
        ]);

        // Create activity type
        $quizType = ActivityType::firstOrCreate(['name' => 'Quiz']);

        // Create 2 quizzes
        $quiz1 = Activity::factory()->create([
            'module_id' => $module->id,
            'activity_type_id' => $quizType->id,
            'max_score' => 100
        ]);
        $quiz2 = Activity::factory()->create([
            'module_id' => $module->id,
            'activity_type_id' => $quizType->id,
            'max_score' => 100
        ]);

        // Complete with 80% and 90%
        StudentActivity::create([
            'student_id' => $this->student->id,
            'activity_id' => $quiz1->id,
            'module_id' => $module->id,
            'course_id' => $this->course->id,
            'activity_type' => 'Quiz',
            'score' => 80,
            'max_score' => 100,
            'percentage_score' => 80,
            'status' => 'completed',
            'completed_at' => now()
        ]);
        StudentActivity::create([
            'student_id' => $this->student->id,
            'activity_id' => $quiz2->id,
            'module_id' => $module->id,
            'course_id' => $this->course->id,
            'activity_type' => 'Quiz',
            'score' => 90,
            'max_score' => 100,
            'percentage_score' => 90,
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Calculate grade
        $result = $this->gradeService->calculateModuleGrade($this->student->id, $module);

        // Assertions
        $this->assertEquals(100, $result['lesson_score']); // 1/1 lessons = 100%
        $this->assertEquals(85, $result['activity_score']); // (80 + 90) / 2 = 85%
        
        // With default weights (20/80), should use those since both exist
        $this->assertEquals(20, $result['lesson_weight_used']);
        $this->assertEquals(80, $result['activity_weight_used']);
        
        // Module score = (100 × 20%) + (85 × 80%) = 20 + 68 = 88%
        $this->assertEquals(88, $result['module_score']);
        
        // Check activity type weight normalization
        $typeScores = $result['activity_types'];
        $this->assertCount(1, $typeScores); // Only Quiz type
        $this->assertEquals(100, $typeScores[0]['weight_used']); // Quiz should be 100% of activity weight
    }

    /** @test */
    public function it_handles_module_with_multiple_activity_types()
    {
        // Create module
        $module = Module::factory()->create([
            'course_id' => $this->course->id,
            'description' => 'Mixed Activities Module'
        ]);

        // Create activity types
        $quizType = ActivityType::firstOrCreate(['name' => 'Quiz']);
        $assignmentType = ActivityType::firstOrCreate(['name' => 'Assignment']);

        // Create activities
        $quiz = Activity::factory()->create([
            'module_id' => $module->id,
            'activity_type_id' => $quizType->id,
            'max_score' => 100
        ]);
        $assignment = Activity::factory()->create([
            'module_id' => $module->id,
            'activity_type_id' => $assignmentType->id,
            'max_score' => 100
        ]);

        // Complete with scores
        StudentActivity::create([
            'student_id' => $this->student->id,
            'activity_id' => $quiz->id,
            'module_id' => $module->id,
            'course_id' => $this->course->id,
            'activity_type' => 'Quiz',
            'score' => 90,
            'max_score' => 100,
            'percentage_score' => 90,
            'status' => 'completed',
            'completed_at' => now()
        ]);
        StudentActivity::create([
            'student_id' => $this->student->id,
            'activity_id' => $assignment->id,
            'module_id' => $module->id,
            'course_id' => $this->course->id,
            'activity_type' => 'Assignment',
            'score' => 80,
            'max_score' => 100,
            'percentage_score' => 80,
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Calculate grade
        $result = $this->gradeService->calculateModuleGrade($this->student->id, $module);

        // With default weights: Quiz=30%, Assignment=15%
        // Total configured = 45%, normalized: Quiz=66.67%, Assignment=33.33%
        // Activity score = (90 × 66.67%) + (80 × 33.33%) = 60 + 26.67 = 86.67%
        
        $typeScores = $result['activity_types'];
        $this->assertCount(2, $typeScores);
        
        // Check that weights are normalized
        $totalNormalizedWeight = array_sum(array_column($typeScores, 'weight_used'));
        $this->assertEquals(100, round($totalNormalizedWeight));
    }
}
