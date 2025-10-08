<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    /**
     * Display a listing of instructors.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Instructor::with('user:id,name,email');

            // Apply filters
            if ($request->has('department')) {
                $query->where('department', $request->department);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('employment_type')) {
                $query->where('employment_type', $request->employment_type);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('instructor_id', 'LIKE', "%{$search}%")
                      ->orWhere('department', 'LIKE', "%{$search}%")
                      ->orWhere('specialization', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $instructors = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $instructors->items(),
                'pagination' => [
                    'current_page' => $instructors->currentPage(),
                    'per_page' => $instructors->perPage(),
                    'total' => $instructors->total(),
                    'last_page' => $instructors->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch instructors',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created instructor.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:instructors,user_id',
            'employee_id' => 'nullable|unique:instructors,employee_id',
            'title' => 'nullable|string|max:10',
            'department' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'office_location' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'office_hours' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'employment_type' => 'required|in:full-time,part-time,adjunct,visiting',
            'status' => 'required|in:active,inactive,on-leave,retired',
            'salary' => 'nullable|numeric|min:0',
            'education_level' => 'nullable|string|max:50',
            'certifications' => 'nullable|string',
            'years_experience' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Verify user has instructor or admin role
            $user = User::with('role')->find($request->user_id);
            if (!$user->isInstructor() && !$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must have instructor or admin role'
                ], 400);
            }

            $data = $request->validated();
            $data['instructor_id'] = Instructor::generateInstructorId();

            $instructor = Instructor::create($data);
            $instructor->load('user:id,name,email');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Instructor created successfully',
                'data' => $instructor
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create instructor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified instructor.
     */
    public function show(Instructor $instructor): JsonResponse
    {
        try {
            $instructor->load([
                'user:id,name,email',
                'courses:id,title,description,status'
            ]);

            return response()->json([
                'success' => true,
                'data' => $instructor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch instructor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified instructor.
     */
    public function update(Request $request, Instructor $instructor): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'nullable|unique:instructors,employee_id,' . $instructor->id,
            'title' => 'nullable|string|max:10',
            'department' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'office_location' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'office_hours' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'employment_type' => 'required|in:full-time,part-time,adjunct,visiting',
            'status' => 'required|in:active,inactive,on-leave,retired',
            'salary' => 'nullable|numeric|min:0',
            'education_level' => 'nullable|string|max:50',
            'certifications' => 'nullable|string',
            'years_experience' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $instructor->update($request->validated());
            $instructor->load('user:id,name,email');

            return response()->json([
                'success' => true,
                'message' => 'Instructor updated successfully',
                'data' => $instructor
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update instructor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified instructor.
     */
    public function destroy(Instructor $instructor): JsonResponse
    {
        try {
            // Check if instructor has active courses
            $activeCourses = $instructor->courses()->where('status', 'active')->count();
            
            if ($activeCourses > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete instructor with {$activeCourses} active courses"
                ], 400);
            }

            $instructor->delete();

            return response()->json([
                'success' => true,
                'message' => 'Instructor deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete instructor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get instructor statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_instructors' => Instructor::count(),
                'active_instructors' => Instructor::where('status', 'active')->count(),
                'by_department' => Instructor::select('department', DB::raw('count(*) as count'))
                    ->groupBy('department')
                    ->get(),
                'by_employment_type' => Instructor::select('employment_type', DB::raw('count(*) as count'))
                    ->groupBy('employment_type')
                    ->get(),
                'average_experience' => Instructor::whereNotNull('years_experience')->avg('years_experience'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available instructor options for forms.
     */
    public function options(): JsonResponse
    {
        try {
            $options = [
                'departments' => [
                    'Computer Science',
                    'Information Technology',
                    'Mathematics',
                    'Physics',
                    'English Literature',
                    'Education',
                    'Engineering',
                    'Business Administration',
                ],
                'titles' => ['Dr.', 'Prof.', 'Mr.', 'Ms.', 'Mrs.'],
                'employment_types' => [
                    'full-time' => 'Full Time',
                    'part-time' => 'Part Time',
                    'adjunct' => 'Adjunct',
                    'visiting' => 'Visiting'
                ],
                'statuses' => [
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'on-leave' => 'On Leave',
                    'retired' => 'Retired'
                ],
                'education_levels' => ['PhD', 'Masters', 'Bachelors', 'Post-Doctorate']
            ];

            return response()->json([
                'success' => true,
                'data' => $options
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch options',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
