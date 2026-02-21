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
        $users = User::with(['role', 'student.gradeLevel'])->get();
        
        $formattedUsers = $users->map(function ($user) {
            // Helper function to safely handle UTF-8 encoding
            $safeString = function($value) {
                if ($value === null) return null;
                $str = (string) $value;
                // Use iconv to handle malformed UTF-8 sequences
                // //IGNORE removes malformed characters, //TRANSLIT replaces them
                $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
                return $converted !== false ? $converted : $str;
            };
            
            return [
                'id' => (int) $user->id,
                'name' => $safeString($user->name),
                'email' => $safeString($user->email),
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
                'role_name' => $safeString($user->role?->name),
                'role_display_name' => $safeString($user->role?->display_name),
                'grade_level' => $safeString($user->student?->gradeLevel?->display_name),
                'grade_level_id' => $user->student?->grade_level_id ? (int) $user->student->grade_level_id : null,
                'section' => $safeString($user->student?->section),
                'created_at' => $safeString($user->created_at),
                'updated_at' => $safeString($user->updated_at),
            ];
        })->toArray();
        
        return response()->json($formattedUsers);
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
        } elseif ($request->role === 'instructor') {
            // Create Instructor profile for instructors
            \App\Models\Instructor::create([
                'instructor_id' => $this->generateInstructorId(),
                'user_id' => $user->id,
                'employee_id' => $this->generateEmployeeId(),
                'department' => 'General',
                'status' => 'active',
            ]);
        }

        // Load relationships and format response same as index()
        $user->load(['role', 'student.gradeLevel', 'instructor']);
        
        // Helper function to safely handle UTF-8 encoding
        $safeString = function($value) {
            if ($value === null) return null;
            $str = (string) $value;
            $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            return $converted !== false ? $converted : $str;
        };
        
        // Format response with flat role fields for consistency with index()
        $response = [
            'id' => (int) $user->id,
            'name' => $safeString($user->name),
            'email' => $safeString($user->email),
            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
            'role_name' => $safeString($user->role?->name),
            'role_display_name' => $safeString($user->role?->display_name),
            'grade_level' => $safeString($user->student?->gradeLevel?->display_name),
            'grade_level_id' => $user->student?->grade_level_id ? (int) $user->student->grade_level_id : null,
            'section' => $safeString($user->student?->section),
            'created_at' => $safeString($user->created_at),
            'updated_at' => $safeString($user->updated_at),
        ];
        
        return response()->json($response);
    }

    public function update(Request $request, User $user)
    {
        \Log::info('UPDATE REQUEST RECEIVED:', [
            'all_data' => $request->all(),
            'email_verified_present' => $request->has('email_verified'),
            'email_verified_value' => $request->input('email_verified'),
        ]);

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
                'role' => ['required', 'string', 'exists:roles,name'],
                'grade_level' => ['nullable', 'string', 'max:50'],
                'grade_level_id' => ['nullable', 'exists:grade_levels,id'],
                'section' => ['nullable', 'string', 'max:50'],
                'email_verified' => ['nullable', 'boolean'],
            ]);
        } catch (\Exception $e) {
            \Log::error('VALIDATION ERROR:', ['error' => $e->getMessage()]);
            throw $e;
        }

        $role = Role::where('name', $request->role)->first();

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $role->id,
        ];

        // Handle email verification status with boolean flag
        if ($request->has('email_verified')) {
            \Log::info('Setting email verification:', [
                'user_id' => $user->id,
                'email_verified_value' => $request->input('email_verified'),
                'email_verified_boolean' => $request->boolean('email_verified'),
            ]);
            
            if ($request->boolean('email_verified')) {
                $updateData['email_verified_at'] = now();
            } else {
                $updateData['email_verified_at'] = null;
            }
        }

        \Log::info('UPDATE DATA:', $updateData);
        $user->update($updateData);
        $user->refresh();
        \Log::info('USER AFTER UPDATE:', [
            'id' => $user->id,
            'email_verified_at' => $user->email_verified_at,
            'email_verified_at_raw' => $user->getAttributes()['email_verified_at'] ?? null,
        ]);

        // Update the associated Student record if this user is a student
        if ($request->role === 'student') {
            $student = $user->student;
            if ($student) {
                $student->update([
                    'grade_level_id' => $request->grade_level_id ?? $student->grade_level_id,
                    'section' => $request->section ?? $student->section,
                ]);
            } else {
                // Create student record if it doesn't exist (role change from non-student to student)
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
        } elseif ($request->role === 'instructor') {
            // Create Instructor profile if it doesn't exist (role change to instructor)
            if (!$user->instructor) {
                \App\Models\Instructor::create([
                    'instructor_id' => $this->generateInstructorId(),
                    'user_id' => $user->id,
                    'employee_id' => $this->generateEmployeeId(),
                    'department' => 'General',
                    'status' => 'active',
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

        // Load relationships
        $user->load(['role', 'student.gradeLevel', 'instructor']);
        
        // Helper function to safely handle UTF-8 encoding
        $safeString = function($value) {
            if ($value === null) return null;
            $str = (string) $value;
            $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            return $converted !== false ? $converted : $str;
        };
        
        // Format response with flat role fields for consistency with index() and store()
        $response = [
            'id' => (int) $user->id,
            'name' => $safeString($user->name),
            'email' => $safeString($user->email),
            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
            'role_name' => $safeString($user->role?->name),
            'role_display_name' => $safeString($user->role?->display_name),
            'grade_level' => $safeString($user->student?->gradeLevel?->display_name),
            'grade_level_id' => $user->student?->grade_level_id ? (int) $user->student->grade_level_id : null,
            'section' => $safeString($user->student?->section),
            'created_at' => $safeString($user->created_at),
            'updated_at' => $safeString($user->updated_at),
        ];

        return response()->json($response);
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
            'bio' => ['sometimes', 'nullable', 'string'],
            'office_location' => ['sometimes', 'nullable', 'string', 'max:100'],
            'office_hours' => ['sometimes', 'nullable', 'string'],
            'hire_date' => ['sometimes', 'nullable', 'date'],
            'employment_type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'education_level' => ['sometimes', 'nullable', 'string', 'max:100'],
            'years_experience' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'salary' => ['sometimes', 'nullable', 'numeric'],
            'certifications' => ['sometimes', 'nullable', 'array'],
            'employee_id' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        // Separate user fields from instructor fields
        $userFields = array_intersect_key($validated, array_flip(['name', 'email']));
        $instructorFields = array_intersect_key($validated, array_flip([
            'phone',
            'title',
            'department',
            'specialization',
            'bio',
            'office_location',
            'office_hours',
            'hire_date',
            'employment_type',
            'education_level',
            'years_experience',
            'status',
            'salary',
            'certifications',
            'employee_id'
        ]));

        // Update the user if there are user fields
        if (!empty($userFields)) {
            $user->update($userFields);
        }

        // Update the instructor record if it exists and there are instructor fields
        if ($user->instructor && !empty($instructorFields)) {
            $user->instructor->update($instructorFields);
        }

        // Reload relationships and return formatted response
        $user->load(['role', 'student.gradeLevel', 'instructor']);
        
        // Helper function to safely handle UTF-8 encoding
        $safeString = function($value) {
            if ($value === null) return null;
            $str = (string) $value;
            $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            return $converted !== false ? $converted : $str;
        };
        
        // Format response with flat role fields for consistency
        $response = [
            'id' => (int) $user->id,
            'name' => $safeString($user->name),
            'email' => $safeString($user->email),
            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toIso8601String() : null,
            'role_name' => $safeString($user->role?->name),
            'role_display_name' => $safeString($user->role?->display_name),
            'message' => 'Instructor details updated successfully'
        ];

        return response()->json($response);
    }

    /**
     * Generate a unique enrollment number for student records.
     */
    private function generateEnrollmentNumber(): string
    {
        $year = date('Y');
        $lastStudent = \App\Models\Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastStudent ? ((int) substr($lastStudent->enrollment_number, -4)) + 1 : 1;
        
        return $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique instructor ID.
     */
    private function generateInstructorId(): string
    {
        $prefix = 'INS-';
        do {
            $id = $prefix . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (\App\Models\Instructor::where('instructor_id', $id)->exists());
        return $id;
    }

    /**
     * Generate a unique employee ID for instructor records.
     */
    private function generateEmployeeId(): string
    {
        $year = date('Y');
        $lastInstructor = \App\Models\Instructor::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInstructor ? ((int) substr($lastInstructor->employee_id, -4)) + 1 : 1;
        
        return 'EMP-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Download empty CSV template
     */
    public function downloadCsvTemplate()
    {
        $filePath = public_path('templates/users_bulk_upload_template.csv');
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Template file not found'], 404);
        }

        return response()->download($filePath, 'users_bulk_upload_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download admin user CSV example
     */
    public function downloadAdminExample()
    {
        $filePath = public_path('templates/users_admin_example.csv');
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Example file not found'], 404);
        }

        return response()->download($filePath, 'users_admin_example.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download instructor user CSV example
     */
    public function downloadInstructorExample()
    {
        $filePath = public_path('templates/users_instructor_example.csv');
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Example file not found'], 404);
        }

        return response()->download($filePath, 'users_instructor_example.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download student user CSV example
     */
    public function downloadStudentExample()
    {
        $filePath = public_path('templates/users_student_example.csv');
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Example file not found'], 404);
        }

        return response()->download($filePath, 'users_student_example.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Get CSV format information
     */
    public function getCsvFormatInfo()
    {
        return response()->json([
            'format' => [
                'headers' => 'role_id,name,email,password,grade_level_id,section,program,department,title,specialization,bio,office_location,phone,office_hours,hire_date,employment_type,status,salary,education_level,certifications,years_experience',
                'role_ids' => [
                    1 => 'Admin',
                    2 => 'Instructor',
                    3 => 'Student'
                ],
                'required_fields' => [
                    'all' => ['role_id', 'name', 'email', 'password'],
                    'student' => ['grade_level_id']
                ],
                'optional_fields' => [
                    'grade_level_id', 'section', 'program', 'department', 'title',
                    'specialization', 'bio', 'office_location', 'phone', 'office_hours',
                    'hire_date', 'employment_type', 'status', 'salary', 'education_level',
                    'certifications', 'years_experience'
                ]
            ],
            'template_urls' => [
                'empty' => '/api/users/download-csv-template',
                'admin_example' => '/api/users/download-admin-example',
                'instructor_example' => '/api/users/download-instructor-example',
                'student_example' => '/api/users/download-student-example'
            ]
        ]);
    }
}
