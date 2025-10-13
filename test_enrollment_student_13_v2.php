<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Student 13 Enrollment Test (Updated) ===\n\n";

// Check course status
$course = \App\Models\Course::find(1);
echo "📋 Course 1 Details:\n";
echo "- Title: {$course->title}\n";
echo "- Active: " . (isset($course->is_active) ? ($course->is_active ? 'Yes' : 'No') : 'Unknown (field missing)') . "\n\n";

// Let's check the course structure
echo "📊 Course Model Structure:\n";
$fillable = $course->getFillable();
foreach ($fillable as $field) {
    $value = $course->$field ?? 'NULL';
    echo "- {$field}: {$value}\n";
}
echo "\n";

// Try updating course to be active if the field exists
if (in_array('is_active', $fillable)) {
    echo "🔧 Updating course to be active...\n";
    $course->update(['is_active' => true]);
    echo "Course updated!\n\n";
} else {
    echo "⚠️ is_active field not found in Course model. Using database migration check...\n";
    // Check if the column exists in the database
    $columns = \Illuminate\Support\Facades\DB::select("PRAGMA table_info(courses)");
    $columnNames = array_column($columns, 'name');
    
    if (in_array('is_active', $columnNames)) {
        echo "is_active column exists in database. Adding to fillable...\n";
        // Let's directly update the database
        \Illuminate\Support\Facades\DB::table('courses')
            ->where('id', 1)
            ->update(['is_active' => 1]);
        echo "Course updated via database query!\n\n";
    } else {
        echo "is_active column does not exist in database.\n";
        echo "Available columns: " . implode(', ', $columnNames) . "\n\n";
    }
}

// Try enrollment again
$student = \App\Models\Student::find(13);
echo "🎯 Attempting to enroll Student 13 in Course 1 again...\n\n";

try {
    $enrollmentService = app(\App\Services\StudentCourseEnrollmentService::class);
    
    $result = $enrollmentService->enrollStudentToACourse(
        1,
        $student->id,
        [
            'enrolled_at' => now(),
            'status' => 'enrolled',
            'notes' => 'Test enrollment via script (retry)'
        ]
    );
    
    if ($result['success']) {
        echo "✅ Enrollment successful!\n";
        echo "Message: {$result['message']}\n";
        echo "Type: {$result['enrollment_type']}\n\n";
        
        // Check enrollments
        echo "📋 Student's enrolled courses:\n";
        $student->refresh();
        $student->load('courses');
        
        foreach ($student->courses as $course) {
            echo "• Course: {$course->title} (ID: {$course->id})\n";
            echo "  Status: {$course->pivot->status}\n";
            echo "  Enrolled: {$course->pivot->enrolled_at}\n";
            echo "\n";
        }
    } else {
        echo "❌ Enrollment still failed!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Enrollment error: {$e->getMessage()}\n\n";
}

echo "=== End Test ===\n";