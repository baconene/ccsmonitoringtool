# Course and Enrollment Services Usage Guide

This document demonstrates how to use the newly created services for course management and student enrollment.

## Services Overview

### 1. CourseService
Handles all Course CRUD operations:
- `addCourse(array $data): Course`
- `editCourse(int $courseId, array $data): Course`
- `removeCourse(int $courseId, bool $forceDelete = false): bool`
- `getCourses(array $filters = [], int $perPage = 15)`
- `getCourse(int $courseId): Course`

### 2. StudentCourseEnrollmentService
Handles all student enrollment operations:
- `enrollStudentToACourse(int $courseId, int $studentId, array $enrollmentData = [])`
- `updateEnrollmentStatus(int $courseId, int $studentId, string $status, array $additionalData = [])`
- `removeStudentEnrollment(int $courseId, int $studentId, string $reason = 'withdrawn')`
- `getCourseEnrollments(int $courseId, array $filters = [])`
- `getStudentEnrollments(int $studentId, array $filters = [])`
- `getEnrollmentStatistics(int $courseId)`

## Controller Implementation

### CourseController
The `CourseController` now uses dependency injection to access the `CourseService`:

```php
public function __construct(CourseService $courseService)
{
    $this->courseService = $courseService;
}

// Create course
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $course = $this->courseService->addCourse($validated);
    return response()->json(['course' => $course]);
}

// Update course
public function update(Request $request, Course $course)
{
    $validated = $request->validate([...]);
    $course = $this->courseService->editCourse($course->id, $validated);
    return response()->json(['course' => $course]);
}

// Delete course
public function destroy(Request $request, Course $course)
{
    $forceDelete = $request->input('force_delete', false);
    $this->courseService->removeCourse($course->id, $forceDelete);
    return response()->json(['message' => 'Course deleted successfully']);
}
```

### CourseStudentController  
The `CourseStudentController` now uses the `StudentCourseEnrollmentService`:

```php
public function __construct(StudentCourseEnrollmentService $enrollmentService)
{
    $this->enrollmentService = $enrollmentService;
}

// Enroll students
public function enrollStudents(Request $request, Course $course)
{
    $userIds = $request->student_ids;
    
    foreach ($userIds as $userId) {
        $user = User::findOrFail($userId);
        $student = $user->getOrCreateStudentRecord();
        
        $result = $this->enrollmentService->enrollStudentToACourse(
            $course->id,
            $student->id,
            [
                'enrolled_at' => now(),
                'status' => 'enrolled',
                'notes' => 'Enrolled via course management'
            ]
        );
    }
}

// Remove students  
public function removeStudents(Request $request, Course $course)
{
    $userIds = $request->student_ids;
    
    foreach ($userIds as $userId) {
        $user = User::findOrFail($userId);
        $student = $user->student;
        
        $this->enrollmentService->removeStudentEnrollment(
            $course->id,
            $student->id,
            'withdrawn'
        );
    }
}
```

## Available Routes

### Course Management Routes
```
GET    /course-management              - List courses (CourseController@index)
POST   /courses                       - Create course (CourseController@store)
PUT    /courses/{course}              - Update course (CourseController@update)  
DELETE /courses/{course}              - Delete course (CourseController@destroy)
```

### Student Enrollment Routes
```
GET    /courses/{course}/manage-students           - Course student management page
POST   /courses/{course}/enroll-students          - Enroll students to course
POST   /courses/{course}/remove-students          - Remove students from course
GET    /courses/{course}/eligible-students        - Get eligible students
GET    /courses/{course}/enrollment-statistics    - Get enrollment statistics
GET    /courses/{course}/enrollments              - Get course enrollments
PUT    /courses/{course}/enrollments/{user}/status - Update enrollment status
```

## Usage Examples

### Creating a New Course
```php
// In a controller or service
$courseData = [
    'title' => 'Introduction to Laravel',
    'description' => 'Learn Laravel framework basics',
    'instructor_id' => auth()->id(),
    'grade_level' => 'Intermediate',
    'credits' => 3,
    'semester' => 'Fall',
    'academic_year' => '2025',
    'is_active' => true,
    'enrollment_limit' => 30,
    'default_modules' => true
];

$course = app(CourseService::class)->addCourse($courseData);
```

### Enrolling a Student
```php
// In a controller or service
$enrollmentService = app(StudentCourseEnrollmentService::class);

$result = $enrollmentService->enrollStudentToACourse(
    $courseId = 1,
    $studentId = 5,
    [
        'enrolled_at' => now(),
        'status' => 'enrolled',
        'notes' => 'Regular enrollment'
    ]
);

if ($result['success']) {
    // Enrollment successful
    echo $result['message']; // "Student enrolled successfully"
}
```

### Getting Enrollment Statistics
```php
$enrollmentService = app(StudentCourseEnrollmentService::class);
$stats = $enrollmentService->getEnrollmentStatistics($courseId);

/*
Returns:
[
    'total_enrollments' => 25,
    'active_enrollments' => 20,
    'completed_enrollments' => 3,
    'dropped_enrollments' => 1,
    'withdrawn_enrollments' => 1,
    'average_grade' => 85.5,
    'completion_rate' => 12.0
]
*/
```

## Benefits

1. **Separation of Concerns**: Controllers handle only request/response logic
2. **Reusability**: Services can be used across multiple controllers
3. **Testability**: Business logic is isolated and easily testable
4. **Maintainability**: Changes to business logic only require service updates
5. **Consistency**: Standardized patterns across the application
6. **Error Handling**: Centralized error handling and logging
7. **Transaction Management**: Database transactions handled in services
8. **Validation**: Input validation remains in controllers, business validation in services

## Error Handling

Both services implement comprehensive error handling:

- Database transaction rollback on failures
- Detailed logging for debugging
- Custom exceptions with meaningful messages
- Graceful fallback handling

## Database Compatibility

The services work with the existing database structure:
- `courses` table for course data
- `course_student` pivot table for enrollments
- `students` table linked to `users` table
- Proper foreign key constraints and indexes