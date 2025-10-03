<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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