<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Role;
use Carbon\Carbon;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n👨‍🏫 Creating Instructor Records...\n";

        // Get all users with instructor or admin role
        $instructorRole = Role::where('name', 'instructor')->first();
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$instructorRole && !$adminRole) {
            echo "⚠️  Warning: Instructor/Admin roles not found. Please run RoleSeeder first.\n";
            return;
        }

        $instructorUsers = User::whereIn('role_id', array_filter([$instructorRole?->id, $adminRole?->id]))->get();
        
        if ($instructorUsers->isEmpty()) {
            echo "⚠️  Warning: No instructor/admin users found. Please run DatabaseSeeder first.\n";
            return;
        }

        // Available departments
        $departments = [
            'Computer Science',
            'Information Technology',
            'Mathematics',
            'Physics',
            'English Literature',
            'Education',
            'Engineering',
            'Business Administration',
        ];

        // Available specializations by department
        $specializations = [
            'Computer Science' => ['Software Engineering', 'Data Science', 'Artificial Intelligence', 'Cybersecurity'],
            'Information Technology' => ['Network Administration', 'Database Management', 'Web Development', 'IT Support'],
            'Mathematics' => ['Applied Mathematics', 'Statistics', 'Calculus', 'Discrete Mathematics'],
            'Physics' => ['Quantum Physics', 'Thermodynamics', 'Mechanics', 'Electromagnetism'],
            'English Literature' => ['Creative Writing', 'Literary Analysis', 'Grammar', 'Public Speaking'],
            'Education' => ['Curriculum Development', 'Educational Psychology', 'Special Education', 'Early Childhood'],
            'Engineering' => ['Mechanical Engineering', 'Electrical Engineering', 'Civil Engineering', 'Chemical Engineering'],
            'Business Administration' => ['Management', 'Marketing', 'Finance', 'Human Resources'],
        ];

        // Available titles
        $titles = ['Dr.', 'Prof.', 'Mr.', 'Ms.', 'Mrs.'];

        // Education levels
        $educationLevels = ['PhD', 'Masters', 'Bachelors', 'Post-Doctorate'];

        // Employment types
        $employmentTypes = ['full-time', 'part-time', 'adjunct'];

        foreach ($instructorUsers as $user) {
            try {
                // Check if instructor record already exists
                $existingInstructor = Instructor::where('user_id', $user->id)->first();
                
                if ($existingInstructor) {
                    echo "✓ Instructor record already exists for: {$user->email}\n";
                    continue;
                }

                // Select random department and specialization
                $department = $departments[array_rand($departments)];
                $specialization = $specializations[$department][array_rand($specializations[$department])];

                // Generate hire date (random date within the last 5 years)
                $hireDate = Carbon::now()->subYears(rand(1, 5))->subMonths(rand(0, 11))->format('Y-m-d');

                // Create instructor record
                $instructor = Instructor::create([
                    'instructor_id' => Instructor::generateInstructorId(),
                    'user_id' => $user->id,
                    'employee_id' => $this->generateEmployeeId(),
                    'title' => $titles[array_rand($titles)],
                    'department' => $department,
                    'specialization' => $specialization,
                    'bio' => $this->generateBio($user->name, $specialization, $department),
                    'office_location' => $this->generateOfficeLocation($department),
                    'phone' => $this->generatePhoneNumber(),
                    'office_hours' => $this->generateOfficeHours(),
                    'hire_date' => $hireDate,
                    'employment_type' => $employmentTypes[array_rand($employmentTypes)],
                    'status' => 'active',
                    'education_level' => $educationLevels[array_rand($educationLevels)],
                    'certifications' => $this->generateCertifications($specialization),
                    'years_experience' => rand(2, 25),
                    'metadata' => [
                        'created_by_seeder' => true,
                        'preferred_contact' => 'email',
                        'languages' => ['English'],
                        'research_interests' => [$specialization],
                    ]
                ]);

                echo "✓ Created Instructor: {$instructor->instructor_id} for {$user->name} ({$user->email})\n";
                echo "  - Title: {$instructor->full_display_name}\n";
                echo "  - Department: {$instructor->department}\n";
                echo "  - Specialization: {$instructor->specialization}\n";
                echo "  - Office: {$instructor->office_location}\n";
                echo "  - Experience: {$instructor->years_experience} years\n";

            } catch (\Exception $e) {
                echo "⚠️  Failed to create instructor record for {$user->email}: {$e->getMessage()}\n";
                continue;
            }
        }

        echo "\n✅ Instructor seeding completed!\n";
        echo "📊 Total instructors created: " . Instructor::count() . "\n";
    }

    /**
     * Generate a unique employee ID.
     */
    private function generateEmployeeId(): string
    {
        $year = date('Y');
        $prefix = 'EMP' . $year . '-';
        
        do {
            $number = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
            $employeeId = $prefix . $number;
        } while (Instructor::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }

    /**
     * Generate a bio for the instructor.
     */
    private function generateBio(string $name, string $specialization, string $department): string
    {
        $templates = [
            "{$name} is a dedicated educator in the {$department} department with expertise in {$specialization}. They are committed to providing quality education and mentoring students.",
            "{$name} brings extensive knowledge in {$specialization} to the {$department} department. They focus on innovative teaching methods and student engagement.",
            "With a passion for {$specialization}, {$name} serves as an instructor in {$department}, helping students develop critical thinking and practical skills.",
            "{$name} is an experienced professional in {$specialization} who enjoys sharing knowledge and fostering student success in the {$department} field.",
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Generate office location based on department.
     */
    private function generateOfficeLocation(string $department): string
    {
        $buildingMap = [
            'Computer Science' => 'CS Building',
            'Information Technology' => 'IT Complex',
            'Mathematics' => 'Science Building',
            'Physics' => 'Science Building',
            'English Literature' => 'Humanities Building',
            'Education' => 'Education Building',
            'Engineering' => 'Engineering Complex',
            'Business Administration' => 'Business Building',
        ];

        $building = $buildingMap[$department] ?? 'Main Building';
        $room = rand(101, 399);

        return "{$building}, Room {$room}";
    }

    /**
     * Generate a phone number.
     */
    private function generatePhoneNumber(): string
    {
        return '+1-' . rand(200, 999) . '-' . rand(100, 999) . '-' . rand(1000, 9999);
    }

    /**
     * Generate office hours.
     */
    private function generateOfficeHours(): string
    {
        $days = [
            'Monday 2:00-4:00 PM, Wednesday 10:00 AM-12:00 PM',
            'Tuesday 1:00-3:00 PM, Thursday 9:00-11:00 AM',
            'Monday & Friday 11:00 AM-1:00 PM',
            'Wednesday 3:00-5:00 PM, Friday 10:00 AM-12:00 PM',
            'By appointment only',
        ];

        return $days[array_rand($days)];
    }

    /**
     * Generate certifications based on specialization.
     */
    private function generateCertifications(string $specialization): string
    {
        $certificationMap = [
            'Software Engineering' => 'Certified Software Development Professional (CSDP), Agile Certified Practitioner',
            'Data Science' => 'Certified Analytics Professional (CAP), Python Data Science Certification',
            'Artificial Intelligence' => 'Machine Learning Engineer Certification, AI Ethics Certificate',
            'Cybersecurity' => 'Certified Information Security Manager (CISM), Ethical Hacker Certification',
            'Network Administration' => 'Cisco Certified Network Associate (CCNA), CompTIA Network+',
            'Database Management' => 'Oracle Database Administrator Certification, Microsoft SQL Server Certification',
            'Web Development' => 'Full Stack Developer Certification, React.js Professional Certificate',
            'Applied Mathematics' => 'Statistical Analysis Certification, Mathematical Modeling Certificate',
            'Statistics' => 'Statistical Analysis System (SAS) Certification, R Programming Certificate',
            'Creative Writing' => 'MFA in Creative Writing, Published Author Certification',
            'Literary Analysis' => 'PhD in Literature, Critical Theory Certificate',
        ];

        return $certificationMap[$specialization] ?? 'Professional Teaching Certificate';
    }
}
