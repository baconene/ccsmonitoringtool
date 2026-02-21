<?php

namespace Tests\Unit\Services;

use App\Models\Activity;
use App\Models\Course;
use App\Models\Module;
use App\Models\Skill;
use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentQuizProgress;
use App\Models\User;
use App\Services\StudentAssessmentService;
use Tests\TestCase;

class StudentAssessmentServiceTest extends TestCase
{
    protected StudentAssessmentService $assessmentService;
    protected Student $student;
    protected Course $course;
    protected Module $module;
    protected Skill $skill;
    protected Activity $activity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assessmentService = app(StudentAssessmentService::class);

        // Create test data
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create user and student
        $user = User::factory()->create();
        $this->student = Student::factory()->create(['user_id' => $user->id]);

        // Create course
        $this->course = Course::factory()->create();

        // Enroll student in course
        $this->student->courses()->attach($this->course->id);

        // Create module
        $this->module = Module::factory()->create(['course_id' => $this->course->id]);

        // Create skill
        $this->skill = Skill::factory()->create([
            'module_id' => $this->module->id,
            'competency_threshold' => 70,
            'weight' => 1.0,
        ]);

        // Create activity
        $this->activity = Activity::factory()->create();

        // Link activity to module
        $this->module->activities()->attach($this->activity->id);

        // Link skill to activity
        $this->skill->activities()->attach($this->activity->id, ['weight' => 1.0]);
    }

    /**
     * Test normalized score calculation
     */
    public function test_normalized_score_calculation(): void
    {
        // Create student activity
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
            'attempt_count' => 1,
        ]);

        // Create quiz progress with 85% score
        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 85,
        ]);

        // Calculate assessment
        $result = $this->assessmentService->calculateOrUpdateSkillAssessment(
            $this->student,
            $this->skill
        );

        // Normalized score should be close to 85
        $this->assertGreaterThanOrEqual(84, $result['normalized_score']);
        $this->assertLessThanOrEqual(86, $result['normalized_score']);
    }

    /**
     * Test mastery level determination
     */
    public function test_mastery_level_not_met(): void
    {
        // Create student activity with low score
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 50, // Below 70% threshold
        ]);

        $result = $this->assessmentService->calculateOrUpdateSkillAssessment(
            $this->student,
            $this->skill
        );

        $this->assertEquals('not_met', $result['mastery_level']);
    }

    /**
     * Test mastery level met
     */
    public function test_mastery_level_met(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 75, // At threshold
        ]);

        $result = $this->assessmentService->calculateOrUpdateSkillAssessment(
            $this->student,
            $this->skill
        );

        $this->assertEquals('met', $result['mastery_level']);
    }

    /**
     * Test mastery level exceeds
     */
    public function test_mastery_level_exceeds(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        // 70 + 15 = 85% required to exceed
        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 95,
        ]);

        $result = $this->assessmentService->calculateOrUpdateSkillAssessment(
            $this->student,
            $this->skill
        );

        $this->assertEquals('exceeds', $result['mastery_level']);
    }

    /**
     * Test improvement factor calculation
     */
    public function test_improvement_factor_single_attempt(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
            'attempt_count' => 1,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 80,
        ]);

        $result = $this->assessmentService->calculateOrUpdateSkillAssessment(
            $this->student,
            $this->skill
        );

        // Single attempt should have improvement factor of 1.0
        $this->assertEquals(1.0, $result['consistency_score']);
    }

    /**
     * Test improvement factor with multiple attempts
     */
    public function test_improvement_factor_multiple_attempts(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
            'attempt_count' => 3, // Multiple attempts
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 85,
        ]);

        $result = $this->assessmentService->calculateOrUpdateSkillAssessment(
            $this->student,
            $this->skill
        );

        // Should have improvement factor > 1.0 from multiple attempts
        $this->assertGreaterThan(1.0, $result['consistency_score']);
    }

    /**
     * Test overall student assessment calculation
     */
    public function test_overall_student_assessment(): void
    {
        // Create student activity
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 85,
        ]);

        // Perform assessment
        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        // Verify structure
        $this->assertIsArray($assessment);
        $this->assertArrayHasKey('student_id', $assessment);
        $this->assertArrayHasKey('overall_score', $assessment);
        $this->assertArrayHasKey('readiness_level', $assessment);
        $this->assertArrayHasKey('courses', $assessment);
        $this->assertArrayHasKey('strengths', $assessment);
        $this->assertArrayHasKey('weaknesses', $assessment);
        $this->assertArrayHasKey('radar_chart', $assessment);

        // Verify values
        $this->assertEquals($this->student->id, $assessment['student_id']);
        $this->assertIsNumeric($assessment['overall_score']);
        $this->assertGreaterThanOrEqual(0, $assessment['overall_score']);
        $this->assertLessThanOrEqual(100, $assessment['overall_score']);
    }

    /**
     * Test readiness level determination
     */
    public function test_readiness_level_not_ready(): void
    {
        // Create activity with low score
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 40,
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        $this->assertEquals('Not Ready', $assessment['readiness_level']);
    }

    /**
     * Test readiness level developing
     */
    public function test_readiness_level_developing(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 60,
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        $this->assertEquals('Developing', $assessment['readiness_level']);
    }

    /**
     * Test readiness level proficient
     */
    public function test_readiness_level_proficient(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 80,
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        $this->assertEquals('Proficient', $assessment['readiness_level']);
    }

    /**
     * Test readiness level advanced
     */
    public function test_readiness_level_advanced(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 90,
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        $this->assertEquals('Advanced', $assessment['readiness_level']);
    }

    /**
     * Test strength identification
     */
    public function test_strength_identification(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 95, // High score
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        // Should be identified as strength
        $strengthNames = array_column($assessment['strengths'], 'skill_name');
        $this->assertContains($this->skill->name, $strengthNames);
    }

    /**
     * Test weakness identification
     */
    public function test_weakness_identification(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 40, // Low score
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        // Should be identified as weakness
        $weaknessNames = array_column($assessment['weaknesses'], 'skill_name');
        $this->assertContains($this->skill->name, $weaknessNames);
    }

    /**
     * Test radar chart data generation
     */
    public function test_radar_chart_data_generation(): void
    {
        $studentActivity = StudentActivity::factory()->create([
            'student_id' => $this->student->id,
            'activity_id' => $this->activity->id,
            'module_id' => $this->module->id,
        ]);

        StudentQuizProgress::factory()->create([
            'student_activity_id' => $studentActivity->id,
            'score' => 85,
        ]);

        $assessment = $this->assessmentService->calculateStudentAssessment($this->student);

        $radarChart = $assessment['radar_chart'];
        $this->assertArrayHasKey('labels', $radarChart);
        $this->assertArrayHasKey('datasets', $radarChart);
        $this->assertIsArray($radarChart['labels']);
        $this->assertIsArray($radarChart['datasets']);
    }

    /**
     * Test empty assessment for student with no courses
     */
    public function test_empty_assessment_no_courses(): void
    {
        // Create new student without course enrollment
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $assessment = $this->assessmentService->calculateStudentAssessment($student);

        $this->assertEquals(['student_id' => $student->id], [
            'student_id' => $assessment['student_id'],
        ]);
        $this->assertEquals(0, $assessment['overall_score']);
        $this->assertEquals('Not Ready', $assessment['readiness_level']);
    }
}
