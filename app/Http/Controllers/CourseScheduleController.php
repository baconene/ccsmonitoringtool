<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Schedule;
use App\Models\ScheduleCourse;
use App\Models\ScheduleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseScheduleController extends Controller
{
    /**
     * Get all schedules for a course
     */
    public function index(Course $course)
    {
        $schedules = $course->schedules()
            ->with(['scheduleType', 'creator'])
            ->orderBy('from_datetime', 'asc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'description' => $schedule->description,
                    'location' => $schedule->location,
                    'from_datetime' => $schedule->from_datetime,
                    'to_datetime' => $schedule->to_datetime,
                    'is_all_day' => $schedule->is_all_day,
                    'is_recurring' => $schedule->is_recurring,
                    'recurrence_rule' => $schedule->recurrence_rule,
                    'status' => $schedule->status,
                    'schedule_type' => $schedule->scheduleType?->name,
                    'creator' => $schedule->creator?->name,
                    'session_number' => $schedule->courseDetails?->session_number,
                    'topics_covered' => $schedule->courseDetails?->topics_covered,
                ];
            });

        return response()->json($schedules);
    }

    /**
     * Store a new course schedule
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'from_datetime' => 'required|date',
            'to_datetime' => 'required|date|after:from_datetime',
            'is_all_day' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|string',
            'session_number' => 'nullable|integer|min:1',
            'topics_covered' => 'nullable|string',
            'required_materials' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Get the course schedule type
            $scheduleType = ScheduleType::where('name', 'course')->firstOrFail();

            // Create the schedule
            $schedule = Schedule::create([
                'schedule_type_id' => $scheduleType->id,
                'title' => $validated['title'] ?? "Course: {$course->title}",
                'description' => $validated['description'] ?? $course->description,
                'location' => $validated['location'] ?? null,
                'from_datetime' => $validated['from_datetime'],
                'to_datetime' => $validated['to_datetime'],
                'is_all_day' => $validated['is_all_day'] ?? false,
                'is_recurring' => $validated['is_recurring'] ?? false,
                'recurrence_rule' => $validated['recurrence_rule'] ?? null,
                'status' => 'scheduled',
                'created_by' => Auth::id(),
                'schedulable_type' => Course::class,
                'schedulable_id' => $course->id,
            ]);

            // Create the schedule_course pivot record
            ScheduleCourse::create([
                'schedule_id' => $schedule->id,
                'course_id' => $course->id,
                'session_number' => $validated['session_number'] ?? null,
                'topics_covered' => $validated['topics_covered'] ?? null,
                'required_materials' => $validated['required_materials'] ?? null,
            ]);

            // TODO: Add enrolled students as schedule participants
            $this->addCourseStudentsAsParticipants($course, $schedule);

            DB::commit();

            Log::info('Course schedule created', [
                'course_id' => $course->id,
                'schedule_id' => $schedule->id,
                'created_by' => Auth::id(),
            ]);

            // Return success response for Inertia
            return redirect()->back()
                ->with('success', 'Course schedule created successfully!')
                ->with('scheduleCreated', true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create course schedule', [
                'course_id' => $course->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['schedule' => 'Failed to create course schedule: ' . $e->getMessage()])
                ->with('error', 'Failed to create course schedule: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing course schedule
     */
    public function update(Request $request, Course $course, Schedule $schedule)
    {
        // Verify the schedule belongs to this course
        if ($schedule->schedulable_type !== Course::class || $schedule->schedulable_id !== $course->id) {
            return redirect()->back()->with('error', 'Schedule does not belong to this course.');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'from_datetime' => 'required|date',
            'to_datetime' => 'required|date|after:from_datetime',
            'is_all_day' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|string',
            'session_number' => 'nullable|integer|min:1',
            'topics_covered' => 'nullable|string',
            'required_materials' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Update the schedule
            $schedule->update([
                'title' => $validated['title'] ?? $schedule->title,
                'description' => $validated['description'] ?? $schedule->description,
                'location' => $validated['location'] ?? $schedule->location,
                'from_datetime' => $validated['from_datetime'],
                'to_datetime' => $validated['to_datetime'],
                'is_all_day' => $validated['is_all_day'] ?? $schedule->is_all_day,
                'is_recurring' => $validated['is_recurring'] ?? $schedule->is_recurring,
                'recurrence_rule' => $validated['recurrence_rule'] ?? $schedule->recurrence_rule,
            ]);

            // Update the schedule_course record if it exists
            $scheduleCourse = ScheduleCourse::where('schedule_id', $schedule->id)->first();
            if ($scheduleCourse) {
                $scheduleCourse->update([
                    'session_number' => $validated['session_number'] ?? $scheduleCourse->session_number,
                    'topics_covered' => $validated['topics_covered'] ?? $scheduleCourse->topics_covered,
                    'required_materials' => $validated['required_materials'] ?? $scheduleCourse->required_materials,
                ]);
            }

            // Sync participants (add any new enrolled students, update instructor)
            $this->syncCourseParticipants($course, $schedule);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Course schedule updated successfully!')
                ->with('scheduleUpdated', true);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update course schedule', [
                'course_id' => $course->id,
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to update course schedule: ' . $e->getMessage());
        }
    }

    /**
     * Delete a course schedule
     */
    public function destroy(Course $course, Schedule $schedule)
    {
        // Verify the schedule belongs to this course
        if ($schedule->schedulable_type !== Course::class || $schedule->schedulable_id !== $course->id) {
            return redirect()->back()->with('error', 'Schedule does not belong to this course.');
        }

        try {
            $schedule->delete();

            Log::info('Course schedule deleted', [
                'course_id' => $course->id,
                'schedule_id' => $schedule->id,
            ]);

            return redirect()->back()->with('success', 'Course schedule deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete course schedule', [
                'course_id' => $course->id,
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to delete course schedule: ' . $e->getMessage());
        }
    }

    /**
     * Add all enrolled students as participants to the schedule
     */
    private function addCourseStudentsAsParticipants(Course $course, Schedule $schedule): void
    {
        try {
            // Add the user who created the schedule as organizer
            if (Auth::id()) {
                $schedule->participants()->create([
                    'user_id' => Auth::id(),
                    'role_in_schedule' => 'organizer',
                    'participation_status' => 'confirmed',
                ]);
            }

            // Get all enrolled students
            $enrolledStudents = $course->students()->get();

            foreach ($enrolledStudents as $student) {
                if ($student->user_id && $student->user_id !== Auth::id()) {
                    $schedule->participants()->create([
                        'user_id' => $student->user_id,
                        'role_in_schedule' => 'student',
                        'participation_status' => 'invited',
                    ]);
                }
            }

            // Add the course instructor if exists and not already added
            if ($course->instructor_id && $course->instructor->user_id !== Auth::id()) {
                $schedule->participants()->updateOrCreate(
                    ['user_id' => $course->instructor->user_id],
                    [
                        'role_in_schedule' => 'instructor',
                        'participation_status' => 'confirmed',
                    ]
                );
            }

        } catch (\Exception $e) {
            Log::warning('Failed to add some participants to schedule', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sync participants for course schedule (for updates)
     * This adds new students and updates instructor if needed
     */
    private function syncCourseParticipants(Course $course, Schedule $schedule): void
    {
        try {
            // Get currently enrolled students
            $enrolledStudents = $course->students()->pluck('user_id')->filter()->toArray();
            
            // Get current participants
            $currentParticipants = $schedule->participants()->pluck('user_id')->toArray();

            // Add newly enrolled students who aren't already participants
            $newStudents = array_diff($enrolledStudents, $currentParticipants);
            foreach ($newStudents as $userId) {
                if ($userId !== Auth::id()) {
                    $schedule->participants()->create([
                        'user_id' => $userId,
                        'role_in_schedule' => 'student',
                        'participation_status' => 'invited',
                    ]);
                }
            }

            // Update or add instructor
            if ($course->instructor_id && $course->instructor->user_id) {
                $schedule->participants()->updateOrCreate(
                    ['user_id' => $course->instructor->user_id],
                    [
                        'role_in_schedule' => 'instructor',
                        'participation_status' => 'confirmed',
                    ]
                );
            }

            Log::info('Synced participants for schedule', [
                'schedule_id' => $schedule->id,
                'added_students' => count($newStudents),
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to sync some participants to schedule', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
