<?php

namespace App\Services;

use App\Models\User;
use App\Models\Instructor;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class UserBulkUploadService
{
    /**
     * Process CSV file and create users based on their roles
     *
     * @param string $filePath
     * @return array
     */
    public function processCSV(string $filePath): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
            'info' => []
        ];

        try {
            $file = fopen($filePath, 'r');
            
            if ($file === false) {
                throw new Exception('Unable to open CSV file');
            }

            // Get headers
            $headers = fgetcsv($file);
            
            if ($headers === false) {
                throw new Exception('Invalid CSV format - no headers found');
            }

            // Normalize headers (trim spaces)
            $headers = array_map('trim', $headers);

            $rowNumber = 1;

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Check if row has matching number of columns
                if (count($row) !== count($headers)) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'line' => $rowNumber,
                        'email' => isset($row[2]) ? $row[2] : 'N/A',
                        'error' => "Column count mismatch. Expected " . count($headers) . " columns, got " . count($row)
                    ];
                    Log::warning("CSV Upload - Row {$rowNumber} has mismatched columns", [
                        'expected_columns' => count($headers),
                        'actual_columns' => count($row)
                    ]);
                    continue;
                }

                // Create associative array from headers and row data
                $data = array_combine($headers, $row);

                try {
                    $this->processRow($data, $rowNumber);
                    $results['success']++;
                } catch (Exception $e) {
                    // Check if this is a "user already exists" case
                    if (strpos($e->getMessage(), 'already exists - skipped') !== false) {
                        $results['skipped']++;
                        $results['info'][] = [
                            'line' => $rowNumber,
                            'email' => $data['email'] ?? 'N/A',
                            'message' => 'User already exists - skipped'
                        ];
                    } else {
                        $results['failed']++;
                        $results['errors'][] = [
                            'line' => $rowNumber,
                            'email' => $data['email'] ?? 'N/A',
                            'error' => $e->getMessage()
                        ];
                        Log::error("CSV Upload Error - Row {$rowNumber}", [
                            'error' => $e->getMessage(),
                            'data' => $data
                        ]);
                    }
                }
            }

            fclose($file);

        } catch (Exception $e) {
            $results['errors'][] = "File processing error: " . $e->getMessage();
            Log::error('CSV Upload File Error', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Process a single row based on user role
     * Uses same logic as UserController::store() method
     *
     * @param array $data
     * @param int $rowNumber
     * @return void
     * @throws Exception
     */
    protected function processRow(array $data, int $rowNumber): void
    {
        // Support both role_id and user_role columns, or role name
        $roleId = null;
        $roleName = null;
        
        if (!empty($data['role_id'])) {
            $roleId = (int) trim($data['role_id']);
        } elseif (!empty($data['user_role'])) {
            $roleId = (int) trim($data['user_role']);
        } elseif (!empty($data['role'])) {
            $roleName = trim($data['role']);
        }

        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception('Missing required fields: name, email, or password');
        }

        // If we have a role name, convert it to role_id
        if ($roleName && !$roleId) {
            $role = \App\Models\Role::where('name', $roleName)->first();
            if (!$role) {
                throw new Exception("Invalid role: {$roleName}");
            }
            $roleId = $role->id;
        } elseif (!$roleId) {
            throw new Exception('Missing required fields: role_id or role');
        }

        DB::beginTransaction();

        try {
            // Check if email already exists - skip instead of throwing error
            if (User::where('email', trim($data['email']))->exists()) {
                DB::rollBack();
                throw new Exception("User already exists - skipped");
            }

            switch ($roleId) {
                case 1: // Admin
                    $this->createAdmin($data);
                    break;
                
                case 2: // Instructor
                    $this->createInstructor($data);
                    break;
                
                case 3: // Student
                    $this->createStudent($data);
                    break;
                
                default:
                    throw new Exception("Invalid role_id: {$roleId}");
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create Admin user
     * Uses same logic as UserController::store() method
     *
     * @param array $data
     * @return User
     */
    protected function createAdmin(array $data): User
    {
        return User::create([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'password' => Hash::make($data['password']),
            'role_id' => 1,
        ]);
    }

    /**
     * Create Instructor user and profile
     * Uses same logic as UserController::store() method
     * For bulk upload, optionally creates Instructor record if provided
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    protected function createInstructor(array $data): User
    {
        // Create user - same as UserController::store()
        $user = User::create([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'password' => Hash::make($data['password']),
            'role_id' => 2,
        ]);

        // Optionally create Instructor profile if data is provided in CSV
        // This is different from newUserModal which doesn't create the Instructor record
        if (!empty($data['phone']) || !empty($data['title']) || !empty($data['department']) ||
            !empty($data['specialization']) || !empty($data['bio']) || !empty($data['office_location']) ||
            !empty($data['office_hours']) || !empty($data['hire_date']) || !empty($data['employment_type']) ||
            !empty($data['education_level']) || !empty($data['years_experience']) || !empty($data['salary'])) {
            
            // Parse certifications JSON if provided
            $certifications = null;
            if (!empty($data['certifications'])) {
                try {
                    $certString = trim($data['certifications']);
                    $certifications = json_decode($certString, true);
                    
                    if ($certifications === null && $certString !== '') {
                        $certifications = $this->parseCertifications($certString);
                    }
                } catch (Exception $e) {
                    Log::warning("Failed to parse certifications", ['data' => $data['certifications']]);
                }
            }

            Instructor::create([
                'instructor_id' => $this->generateInstructorId(),
                'user_id' => $user->id,
                'employee_id' => $this->generateEmployeeId(),
                'title' => !empty($data['title']) ? trim($data['title']) : null,
                'department' => !empty($data['department']) ? trim($data['department']) : 'General',
                'specialization' => !empty($data['specialization']) ? trim($data['specialization']) : null,
                'bio' => !empty($data['bio']) ? trim($data['bio']) : null,
                'office_location' => !empty($data['office_location']) ? trim($data['office_location']) : null,
                'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
                'office_hours' => !empty($data['office_hours']) ? trim($data['office_hours']) : null,
                'hire_date' => !empty($data['hire_date']) ? $this->parseDate($data['hire_date']) : null,
                'employment_type' => !empty($data['employment_type']) ? trim($data['employment_type']) : null,
                'status' => !empty($data['status']) ? trim($data['status']) : 'active',
                'salary' => !empty($data['salary']) ? floatval($data['salary']) : 0,
                'education_level' => !empty($data['education_level']) ? trim($data['education_level']) : null,
                'certifications' => $certifications,
                'years_experience' => !empty($data['years_experience']) ? intval($data['years_experience']) : 0,
            ]);
        }

        return $user;
    }

    /**
     * Create Student user and profile
     * Uses the same logic as UserController::store() method
     *
     * @param array $data
     * @return User
     * @throws Exception
     */
    protected function createStudent(array $data): User
    {
        // Validate student-specific required fields
        if (empty($data['grade_level_id'])) {
            throw new Exception('grade_level_id is required for students');
        }

        // Get grade level ID - support both ID and name
        $gradeLevelId = null;
        $gradeLevelValue = trim($data['grade_level_id']);
        
        // Check if it's numeric (already an ID)
        if (is_numeric($gradeLevelValue)) {
            $gradeLevelId = (int) $gradeLevelValue;
        } else {
            // Try to find by name or display_name
            $gradeLevel = \App\Models\GradeLevel::where('name', $gradeLevelValue)
                ->orWhere('display_name', $gradeLevelValue)
                ->first();
            
            if (!$gradeLevel) {
                throw new Exception("Grade level not found: {$gradeLevelValue}");
            }
            
            $gradeLevelId = $gradeLevel->id;
        }

        // Create user - same as UserController::store()
        $user = User::create([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'password' => Hash::make($data['password']),
            'role_id' => 3,
        ]);

        // Create student profile - same as UserController::store()
        Student::create([
            'student_id_text' => Student::generateStudentIdText(),
            'user_id' => $user->id,
            'grade_level_id' => $gradeLevelId,
            'section' => !empty($data['section']) ? trim($data['section']) : null,
            'enrollment_number' => $this->generateEnrollmentNumber(),
            'academic_year' => date('Y') . '-' . (date('Y') + 1),
            'status' => 'active',
        ]);

        return $user;
    }

    /**
     * Parse date from various formats
     *
     * @param string $dateString
     * @return string|null
     */
    protected function parseDate(string $dateString): ?string
    {
        try {
            $date = \DateTime::createFromFormat('m/d/Y', trim($dateString));
            if ($date === false) {
                $date = \DateTime::createFromFormat('Y-m-d', trim($dateString));
            }
            if ($date === false) {
                $date = \DateTime::createFromFormat('d/m/Y', trim($dateString));
            }
            
            return $date ? $date->format('Y-m-d') : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Parse certifications from custom format
     *
     * @param string $certString
     * @return array|null
     */
    protected function parseCertifications(string $certString): ?array
    {
        // Remove curly braces and quotes
        $certString = str_replace(['{', '}', '"', "'"], '', $certString);
        
        // Split by comma or semicolon
        $certs = preg_split('/[,;]/', $certString);
        
        $result = [];
        foreach ($certs as $cert) {
            $cert = trim($cert);
            if (!empty($cert)) {
                // Try to split by colon
                if (strpos($cert, ':') !== false) {
                    list($key, $value) = explode(':', $cert, 2);
                    $result[trim($key)] = trim($value);
                } else {
                    $result[] = $cert;
                }
            }
        }
        
        return !empty($result) ? $result : null;
    }

    /**
     * Generate unique instructor ID
     *
     * @return string
     */
    protected function generateInstructorId(): string
    {
        $prefix = 'INS-';
        do {
            $id = $prefix . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Instructor::where('instructor_id', $id)->exists());
        return $id;
    }

    /**
     * Generate unique employee ID for instructors
     *
     * @return string
     */
    protected function generateEmployeeId(): string
    {
        $year = date('Y');
        $lastInstructor = Instructor::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInstructor ? ((int) substr($lastInstructor->employee_id, -4)) + 1 : 1;
        
        return 'EMP-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique enrollment number for students
     *
     * @return string
     */
    protected function generateEnrollmentNumber(): string
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastStudent ? ((int) substr($lastStudent->enrollment_number, -4)) + 1 : 1;
        
        return 'ENR-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
