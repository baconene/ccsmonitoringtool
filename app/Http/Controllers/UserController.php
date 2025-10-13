<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Models\LessonCompletion;
use App\Models\Instructor;
use App\Services\UserBulkUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'student.gradeLevel'])->get()->map(function ($user) {
            // Add grade_level and section to user object for frontend compatibility
            if ($user->student) {
                $user->grade_level = $user->student->gradeLevel?->display_name;
                $user->grade_level_id = $user->student->grade_level_id;
                $user->section = $user->student->section;
            }
            return $user;
        });
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
            'grade_level' => ['nullable', 'string', 'max:50'], // Keep for backward compatibility
            'grade_level_id' => ['nullable', 'exists:grade_levels,id'],
            'section' => ['nullable', 'string', 'max:50'],
        ]);

        $role = Role::where('name', $request->role)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
        ]);

        // If this is a student user, create the corresponding Student record
        if ($request->role === 'student') {
            Student::create([
                'student_id_text' => Student::generateStudentIdText(),
                'user_id' => $user->id,
                'grade_level_id' => $request->grade_level_id ?? null,
                'section' => $request->section ?? null,
                'enrollment_number' => $this->generateEnrollmentNumber(),
                'academic_year' => date('Y') . '-' . (date('Y') + 1),
                'status' => 'active',
            ]);
        }

        // Load relationships and add student data to user object
        $user->load(['role', 'student.gradeLevel']);
        if ($user->student) {
            $user->grade_level = $user->student->gradeLevel?->display_name;
            $user->grade_level_id = $user->student->grade_level_id;
            $user->section = $user->student->section;
        }
        
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'grade_level_id' => ['nullable', 'exists:grade_levels,id'],
            'section' => ['nullable', 'string', 'max:50'],
        ]);

        $role = Role::where('name', $request->role)->first();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $role->id,
        ]);

        // Update the associated Student record if this user is a student
        if ($request->role === 'student') {
            $student = $user->student;
            if ($student) {
                $student->update([
                    'grade_level_id' => $request->grade_level_id ?? $student->grade_level_id,
                    'section' => $request->section ?? $student->section,
                ]);
            }
        }

        if ($request->filled('password')) {
            $request->validate([
                'password' => Rules\Password::defaults(),
            ]);
            
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Load relationships and add student data to user object
        $user->load(['role', 'student.gradeLevel']);
        if ($user->student) {
            $user->grade_level = $user->student->gradeLevel?->display_name;
            $user->grade_level_id = $user->student->grade_level_id;
            $user->section = $user->student->section;
        }

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function studentDetails($id)
    {
        $student = User::findOrFail($id);
        
        if ($student->role->name !== 'student') {
            return response()->json(['message' => 'User is not a student'], 404);
        }

        $enrolledCourses = $student->enrolledCourses()
            ->with(['lessons', 'completedLessons' => function ($query) use ($student) {
                $query->where('user_id', $student->id);
            }])
            ->get()
            ->map(function ($course) use ($student) {
                $totalLessons = $course->lessons->count();
                $completedLessons = $course->completedLessons->count();
                $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
                
                $lastActivity = LessonCompletion::where('user_id', $student->id)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->latest()
                    ->first();

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'progress' => $progress,
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'last_activity' => $lastActivity ? $lastActivity->created_at->diffForHumans() : null,
                ];
            });

        return response()->json([
            'student' => $student,
            'enrolledCourses' => $enrolledCourses,
        ]);
    }

    /**
     * Handle CSV bulk upload for users
     */
    public function bulkUploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            $uploadService = new UserBulkUploadService();
            $results = $uploadService->processCSV($filePath);

            if ($results['failed'] > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Bulk upload completed with some errors. Success: {$results['success']}, Failed: {$results['failed']}",
                    'results' => $results
                ], 207); // 207 Multi-Status
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully uploaded {$results['success']} users",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process CSV file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get instructor details by user ID
     */
    public function instructorDetails($id)
    {
        $user = User::with('instructor')->findOrFail($id);
        
        if ($user->user_role !== 2) {
            return response()->json(['message' => 'User is not an instructor'], 404);
        }

        // Get courses taught by this instructor
        $courses = Course::where('instructor_id', $id)
            ->withCount('students')
            ->get();

        return response()->json([
            'user' => $user,
            'instructor' => $user->instructor,
            'courses' => $courses,
        ]);
    }

    /**
     * Upload CSV file for bulk user creation
     */
    public function uploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            $uploadService = new UserBulkUploadService();
            $results = $uploadService->processCSV($path);

            return response()->json([
                'message' => 'CSV processing completed',
                'results' => $results,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process CSV file',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update instructor details.
     */
    public function updateInstructor(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Validate that the user is an instructor
        if ($user->role->name !== 'instructor') {
            return response()->json(['message' => 'User is not an instructor'], 403);
        }

        // Validate the request
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $id],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'title' => ['sometimes', 'nullable', 'string', 'max:100'],
            'department' => ['sometimes', 'nullable', 'string', 'max:100'],
            'specialization' => ['sometimes', 'nullable', 'string', 'max:100'],
            'office_location' => ['sometimes', 'nullable', 'string', 'max:100'],
            'office_hours' => ['sometimes', 'nullable', 'string'],
            'hire_date' => ['sometimes', 'nullable', 'date'],
            'employment_type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'education_level' => ['sometimes', 'nullable', 'string', 'max:100'],
            'years_of_experience' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        // Update the user
        $user->update($validated);

        // Update the instructor record if it exists
        if ($user->instructor) {
            $instructorFields = array_intersect_key($validated, array_flip([
                'department',
                'specialization',
                'hire_date',
                'employment_type',
                'office_location',
                'office_hours',
                'education_level',
                'years_of_experience'
            ]));
            
            if (!empty($instructorFields)) {
                $user->instructor->update($instructorFields);
            }
        }

        // Reload relationships
        $user->load(['role', 'instructor']);

        return response()->json([
            'message' => 'Instructor details updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Generate a unique enrollment number for student records.
     */
    private function generateEnrollmentNumber(): string
    {
        $year = date('Y');
        $prefix = $year . '-';
        
        do {
            $number = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $enrollmentNumber = $prefix . $number;
        } while (\App\Models\Student::where('enrollment_number', $enrollmentNumber)->exists());

        return $enrollmentNumber;
    }
}