<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Activity;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ScheduleApiController extends Controller
{
    /**
     * Display the schedule for the authenticated instructor.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // For now, return sample schedule data
            // In a real implementation, you would have a Schedule model
            $schedule = $this->getSampleScheduleData();
            
            return response()->json($schedule);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming schedule items.
     */
    public function upcoming(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 5);
            
            // For now, return sample schedule data limited to the requested amount
            // In a real implementation, you would query your Schedule model
            $schedule = collect($this->getSampleScheduleData())
                ->filter(function ($item) {
                    // Filter for future dates
                    return Carbon::parse($item['date'])->isFuture();
                })
                ->sortBy('date')
                ->take($limit)
                ->values()
                ->all();
            
            return response()->json($schedule);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch upcoming schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check if user is the creator
            if ($schedule->created_by !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized. Only the creator can update this schedule.'
                ], 403);
            }

            // Don't allow editing cancelled schedules
            if ($schedule->deleted_at) {
                return response()->json([
                    'message' => 'Cannot edit cancelled schedules.'
                ], 422);
            }

            // Validate request
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'from_datetime' => 'sometimes|required|date',
                'to_datetime' => 'sometimes|required|date|after:from_datetime',
                'status' => ['sometimes', 'required', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
            ]);

            DB::beginTransaction();

            // Update the schedule
            $schedule->update($validated);

            // If this schedule is linked to an Activity and to_datetime changed, update the Activity's due_date
            if ($schedule->schedulable_type === 'App\\Models\\Activity' && isset($validated['to_datetime'])) {
                $activity = Activity::find($schedule->schedulable_id);
                if ($activity) {
                    $activity->due_date = $validated['to_datetime'];
                    $activity->save();
                }
            }

            // If this schedule is linked to a Course and to_datetime changed, update the Course's end_date
            if ($schedule->schedulable_type === 'App\\Models\\Course' && isset($validated['to_datetime'])) {
                $course = Course::find($schedule->schedulable_id);
                if ($course) {
                    $course->end_date = Carbon::parse($validated['to_datetime'])->toDateString();
                    $course->save();
                }
            }

            DB::commit();

            // Reload the schedule with relationships
            $schedule->load(['participants', 'schedulable']);

            return response()->json([
                'message' => 'Schedule updated successfully',
                'schedule' => $schedule
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete (cancel) the specified schedule.
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check if user is the creator
            if ($schedule->created_by !== $user->id) {
                return response()->json([
                    'message' => 'Unauthorized. Only the creator can cancel this schedule.'
                ], 403);
            }

            // Check if already cancelled
            if ($schedule->deleted_at) {
                return response()->json([
                    'message' => 'Schedule is already cancelled.'
                ], 422);
            }

            DB::beginTransaction();

            // Soft delete the schedule
            $schedule->delete();

            // Update status to cancelled
            $schedule->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'message' => 'Schedule cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to cancel schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sample schedule data.
     * In a real implementation, this would be replaced with database queries.
     */
    private function getSampleScheduleData(): array
    {
        return [
            [
                'id' => 1,
                'courseId' => 1,
                'courseTitle' => 'Introduction to Web Development',
                'date' => '2025-10-04',
                'time' => '10:00 AM',
                'type' => 'Lecture'
            ],
            [
                'id' => 2,
                'courseId' => 2,
                'courseTitle' => 'Advanced JavaScript',
                'date' => '2025-10-04',
                'time' => '2:00 PM',
                'type' => 'Lab Session'
            ],
            [
                'id' => 3,
                'courseId' => 3,
                'courseTitle' => 'React Development',
                'date' => '2025-10-05',
                'time' => '9:00 AM',
                'type' => 'Workshop'
            ],
            [
                'id' => 4,
                'courseId' => 1,
                'courseTitle' => 'Introduction to Web Development',
                'date' => '2025-10-06',
                'time' => '10:00 AM',
                'type' => 'Assignment Review'
            ],
            [
                'id' => 5,
                'courseId' => 2,
                'courseTitle' => 'Advanced JavaScript',
                'date' => '2025-10-06',
                'time' => '3:00 PM',
                'type' => 'Project Presentation'
            ],
            [
                'id' => 6,
                'courseId' => 3,
                'courseTitle' => 'React Development',
                'date' => '2025-10-07',
                'time' => '11:00 AM',
                'type' => 'Code Review'
            ]
        ];
    }
}