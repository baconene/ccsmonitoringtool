<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n👨‍🎓 Creating Student Records...\n";

        // Get all users with student role
        $studentRole = Role::where('name', 'student')->first();
        
        if (!$studentRole) {
            echo "⚠️  Warning: Student role not found. Please run RoleSeeder first.\n";
            return;
        }

        $studentUsers = User::where('role_id', $studentRole->id)->get();
        
        if ($studentUsers->isEmpty()) {
            echo "⚠️  Warning: No student users found. Please run DatabaseSeeder first.\n";
            return;
        }

        // Current academic year
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        // Available programs and departments
        $programs = [
            'Bachelor of Science in Computer Science',
            'Bachelor of Science in Information Technology',
            'Bachelor of Science in Mathematics',
            'Bachelor of Science in Physics',
            'Bachelor of Arts in English',
            'Bachelor of Education',
        ];

        $departments = [
            'College of Computer Studies',
            'College of Arts and Sciences',
            'College of Education',
            'College of Engineering',
        ];

        foreach ($studentUsers as $user) {
            try {
                // Check if student record already exists
                $existingStudent = Student::where('user_id', $user->id)->first();
                
                if ($existingStudent) {
                    echo "✓ Student record already exists for: {$user->email}\n";
                    continue;
                }

                // Generate enrollment date (random date within the last 6 months)
                $enrollmentDate = Carbon::now()->subMonths(rand(1, 6))->format('Y-m-d');

                // Create student record
                $student = Student::create([
                    'student_id' => Student::generateStudentId(),
                    'user_id' => $user->id,
                    'enrollment_number' => $this->generateEnrollmentNumber(),
                    'academic_year' => $academicYear,
                    'program' => $programs[array_rand($programs)],
                    'department' => $departments[array_rand($departments)],
                    'enrollment_date' => $enrollmentDate,
                    'status' => 'active',
                    'metadata' => [
                        'created_by_seeder' => true,
                        'grade_level' => $user->grade_level,
                        'section' => $user->section,
                        'academic_status' => 'regular',
                        'scholarship_type' => $this->getRandomScholarshipType(),
                    ]
                ]);

                echo "✓ Created Student: {$student->student_id} for {$user->name} ({$user->email})\n";
                echo "  - Program: {$student->program}\n";
                echo "  - Department: {$student->department}\n";
                echo "  - Enrollment Date: {$student->enrollment_date}\n";

            } catch (\Exception $e) {
                echo "⚠️  Failed to create student record for {$user->email}: {$e->getMessage()}\n";
                continue;
            }
        }

        echo "\n✅ Student seeding completed!\n";
        echo "📊 Total students created: " . Student::count() . "\n";
    }

    /**
     * Generate a unique enrollment number.
     */
    private function generateEnrollmentNumber(): string
    {
        $year = date('Y');
        $prefix = $year . '-';
        
        do {
            $number = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $enrollmentNumber = $prefix . $number;
        } while (Student::where('enrollment_number', $enrollmentNumber)->exists());

        return $enrollmentNumber;
    }

    /**
     * Get a random scholarship type.
     */
    private function getRandomScholarshipType(): ?string
    {
        $scholarships = [
            null, // No scholarship
            'Academic Excellence',
            'Financial Assistance',
            'Sports Scholarship',
            'Cultural Arts',
            'STEM Program',
        ];

        // 60% chance of no scholarship, 40% chance of having one
        return rand(1, 10) <= 6 ? null : $scholarships[array_rand(array_filter($scholarships))];
    }
}