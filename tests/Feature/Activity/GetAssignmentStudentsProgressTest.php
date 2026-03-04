<?php

namespace Tests\Feature\Activity;

use App\Http\Controllers\ActivityController;
use App\Models\Activity;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use Tests\TestCase;

class GetAssignmentStudentsProgressTest extends TestCase
{
    public function test_get_assignment_students_progress_for_activity_2_assignment_1(): void
    {
        if (!Schema::hasTable('activities')) {
            Artisan::call('migrate:fresh', [
                '--seed' => true,
                '--seeder' => 'FreshTestDataSeeder',
            ]);
        }

        $activity = Activity::with('assignment')->find(2);

        if (!$activity || !$activity->assignment || (int) $activity->assignment->id !== 1) {
            $this->markTestSkipped('Fixture pair activity_id=2 and assignment_id=1 is not available in the test database.');
        }

        $courseId = $activity->modules()->value('course_id');
        $this->assertNotNull($courseId, 'No course_id found from activity modules for activity id=2.');

        $controller = app(ActivityController::class);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getAssignmentStudentsProgress');
        $method->setAccessible(true);

        $rows = $method->invoke($controller, $activity, $activity->assignment);

        $this->assertIsArray($rows);

        $enrollmentCount = CourseEnrollment::where('course_id', $courseId)->count();
        $this->assertCount($enrollmentCount, $rows, 'Method row count must match course_enrollments count.');

        if (!empty($rows)) {
            $firstRow = $rows[0];
            $this->assertArrayHasKey('student_id', $firstRow);
            $this->assertArrayHasKey('student_name', $firstRow);
            $this->assertArrayHasKey('status', $firstRow);
            $this->assertArrayHasKey('is_taking_activity', $firstRow);
            $this->assertArrayHasKey('student_activity_id', $firstRow);
        }
    }
}
