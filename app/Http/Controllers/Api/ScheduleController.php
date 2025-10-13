<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Get upcoming schedules for a user
     * 
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserUpcomingSchedules($userId, Request $request)
    {
        try {
            $includeCancelled = $request->query('include_cancelled', false);
            
            $query = Schedule::with([
                'scheduleType',
                'creator:id,name,email',
                'participants.user:id,name,email',
                'schedulable',
            ])
            ->forUser($userId)
            ->upcoming();
            
            // Include soft deleted (cancelled) schedules if requested
            if ($includeCancelled) {
                $query->withTrashed();
            }
            
            $schedules = $query->get()
            ->map(function ($schedule) use ($userId) {
                // Find current user's role in this schedule
                $userParticipant = $schedule->participants->firstWhere('user_id', $userId);
                
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'description' => $schedule->description,
                    'location' => $schedule->location,
                    'from_datetime' => $schedule->from_datetime,
                    'to_datetime' => $schedule->to_datetime,
                    'duration_minutes' => $schedule->duration_minutes,
                    'is_all_day' => $schedule->is_all_day,
                    'status' => $schedule->status,
                    'created_by' => $schedule->created_by,
                    'schedulable_type' => $schedule->schedulable_type,
                    'schedulable_id' => $schedule->schedulable_id,
                    'deleted_at' => $schedule->deleted_at,
                    'type' => [
                        'name' => $schedule->scheduleType->name,
                        'color' => $schedule->scheduleType->color,
                        'icon' => $schedule->scheduleType->icon,
                    ],
                    'creator' => [
                        'id' => $schedule->creator->id,
                        'name' => $schedule->creator->name,
                    ],
                    'user_role' => $userParticipant ? $userParticipant->role_in_schedule : null,
                    'participation_status' => $userParticipant ? $userParticipant->participation_status : null,
                    'participants' => $schedule->participants->map(function ($participant) {
                        return [
                            'id' => $participant->user->id,
                            'name' => $participant->user->name,
                            'role' => $participant->role_in_schedule,
                            'status' => $participant->participation_status,
                        ];
                    }),
                    'participants_count' => $schedule->participants->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'count' => $schedules->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get schedules for a date range (for calendar view)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSchedulesInRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $userId = $request->user_id;
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $schedules = Schedule::with([
                'scheduleType',
                'creator:id,name',
                'participants.user:id,name',
            ])
            ->forUser($userId)
            ->inDateRange($startDate, $endDate)
            ->where('status', '!=', 'cancelled')
            ->orderBy('from_datetime')
            ->get()
            ->map(function ($schedule) use ($userId) {
                $userParticipant = $schedule->participants->firstWhere('user_id', $userId);
                
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->from_datetime->toIso8601String(),
                    'end' => $schedule->to_datetime->toIso8601String(),
                    'allDay' => $schedule->is_all_day,
                    'backgroundColor' => $schedule->scheduleType->color,
                    'borderColor' => $schedule->scheduleType->color,
                    'extendedProps' => [
                        'description' => $schedule->description,
                        'location' => $schedule->location,
                        'type' => $schedule->scheduleType->name,
                        'icon' => $schedule->scheduleType->icon,
                        'status' => $schedule->status,
                        'userRole' => $userParticipant->role_in_schedule ?? null,
                        'participationStatus' => $userParticipant->participation_status ?? null,
                        'participantsCount' => $schedule->participants->count(),
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new schedule
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_type_id' => 'required|exists:schedule_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'from_datetime' => 'required|date',
            'to_datetime' => 'required|date|after:from_datetime',
            'is_all_day' => 'boolean',
            'status' => 'in:scheduled,cancelled,completed,in_progress',
            'participants' => 'required|array|min:1',
            'participants.*.user_id' => 'required|exists:users,id',
            'participants.*.role_in_schedule' => 'required|string',
            'schedulable_type' => 'nullable|string',
            'schedulable_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create the schedule
            $schedule = Schedule::create([
                'schedule_type_id' => $request->schedule_type_id,
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'from_datetime' => $request->from_datetime,
                'to_datetime' => $request->to_datetime,
                'is_all_day' => $request->is_all_day ?? false,
                'status' => $request->status ?? 'scheduled',
                'created_by' => $request->user()->id ?? $request->created_by,
                'schedulable_type' => $request->schedulable_type,
                'schedulable_id' => $request->schedulable_id,
                'metadata' => $request->metadata,
            ]);

            // Add participants
            foreach ($request->participants as $participant) {
                ScheduleParticipant::create([
                    'schedule_id' => $schedule->id,
                    'user_id' => $participant['user_id'],
                    'role_in_schedule' => $participant['role_in_schedule'],
                    'participation_status' => $participant['participation_status'] ?? 'invited',
                    'notes' => $participant['notes'] ?? null,
                ]);
            }

            DB::commit();

            // Load relationships
            $schedule->load(['scheduleType', 'creator', 'participants.user']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule created successfully',
                'data' => $schedule,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update schedule (for drag-and-drop rescheduling)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'from_datetime' => 'sometimes|required|date',
            'to_datetime' => 'sometimes|required|date|after:from_datetime',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'status' => 'sometimes|in:scheduled,cancelled,completed,in_progress',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->update($request->only([
                'from_datetime',
                'to_datetime',
                'title',
                'description',
                'location',
                'status',
            ]));

            $schedule->load(['scheduleType', 'creator', 'participants.user']);

            return response()->json([
                'success' => true,
                'message' => 'Schedule updated successfully',
                'data' => $schedule,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a schedule
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get schedule details
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $schedule = Schedule::with([
                'scheduleType',
                'creator',
                'participants.user',
                'activityDetails.activity',
                'courseDetails.course',
                'adhocDetails',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $schedule,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Check for schedule conflicts
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConflicts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'from_datetime' => 'required|date',
            'to_datetime' => 'required|date|after:from_datetime',
            'exclude_schedule_id' => 'nullable|exists:schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $schedule = new Schedule([
                'from_datetime' => $request->from_datetime,
                'to_datetime' => $request->to_datetime,
            ]);

            $hasConflicts = $schedule->conflictsWith(
                $request->user_id,
                $request->exclude_schedule_id
            );

            if ($hasConflicts) {
                $conflicts = Schedule::forUser($request->user_id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($request) {
                        $q->where(function ($q2) use ($request) {
                            $q2->where('from_datetime', '<=', $request->from_datetime)
                                ->where('to_datetime', '>', $request->from_datetime);
                        })->orWhere(function ($q2) use ($request) {
                            $q2->where('from_datetime', '<', $request->to_datetime)
                                ->where('to_datetime', '>=', $request->to_datetime);
                        });
                    })
                    ->with('scheduleType')
                    ->get();

                return response()->json([
                    'success' => true,
                    'has_conflicts' => true,
                    'conflicts' => $conflicts,
                ]);
            }

            return response()->json([
                'success' => true,
                'has_conflicts' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check conflicts',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update participant status (accept/decline invitation, mark attendance)
     * 
     * @param Request $request
     * @param int $scheduleId
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateParticipantStatus(Request $request, $scheduleId, $userId)
    {
        $validator = Validator::make($request->all(), [
            'participation_status' => 'required|in:invited,accepted,declined,tentative,attended,absent',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $participant = ScheduleParticipant::where('schedule_id', $scheduleId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $updateData = [
                'participation_status' => $request->participation_status,
            ];

            if ($request->participation_status === 'attended') {
                $updateData['attended_at'] = now();
            }

            if (in_array($request->participation_status, ['accepted', 'declined', 'tentative'])) {
                $updateData['response_datetime'] = now();
            }

            if ($request->has('notes')) {
                $updateData['notes'] = $request->notes;
            }

            $participant->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Participation status updated successfully',
                'data' => $participant->fresh(['user', 'schedule']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update participation status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
