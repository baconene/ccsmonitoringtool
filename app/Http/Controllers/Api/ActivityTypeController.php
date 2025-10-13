<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use Illuminate\Http\JsonResponse;

class ActivityTypeController extends Controller
{
    /**
     * Get all activity types
     */
    public function index(): JsonResponse
    {
        try {
            $activityTypes = ActivityType::orderBy('name')->get();
            
            return response()->json($activityTypes);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch activity types',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}