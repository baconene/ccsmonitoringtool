# Course Deletion with Enrolled Students - Fix

## Issue
Cannot delete a course when there are students enrolled in it. The system was throwing a 500 Internal Server Error instead of providing proper feedback.

## Root Cause
The `CourseService::removeCourse()` method was correctly checking for enrolled students and throwing an exception, but:
1. The error wasn't being handled properly in the controller
2. The frontend wasn't informed about the validation issue (422 status code)
3. No clear message was provided about what action to take

## Solution Implemented

### 1. **Updated CourseController::destroy()**
Added early validation check before attempting deletion:

```php
// Check if course has enrolled students first
$enrolledStudentsCount = $course->courseEnrollments()->count();

if ($enrolledStudentsCount > 0 && !$forceDelete) {
    return response()->json([
        'success' => false,
        'message' => "Cannot delete course '{$course->title}' because it has {$enrolledStudentsCount} enrolled student(s). Please unenroll all students first or use force delete.",
        'enrolled_students' => $enrolledStudentsCount,
        'requires_force_delete' => true
    ], 422); // Validation error status
}
```

**Benefits:**
- ✅ Returns 422 (Unprocessable Entity) instead of 500 (Server Error)
- ✅ Provides clear, user-friendly error message
- ✅ Shows exact number of enrolled students
- ✅ Suggests actionable solutions (unenroll students or force delete)
- ✅ Includes flag for frontend to show force delete option

### 2. **Cleaned Up Course Model Boot Method**
Removed duplicate cascade logic that was conflicting with CourseService:

```php
protected static function boot()
{
    parent::boot();

    static::deleting(function ($course) {
        // Note: Cleanup is handled by CourseService::cleanupCourseModules()
        // This boot method only handles many-to-many relationships
        
        // Detach many-to-many relationships
        $course->gradeLevels()->detach();
        $course->enrolledUsers()->detach();
    });
}
```

**Benefits:**
- ✅ Prevents duplicate deletion attempts
- ✅ Maintains separation of concerns (Service handles complex logic)
- ✅ Model only handles many-to-many detachment
- ✅ Clearer code organization

## How It Works Now

### Normal Delete (No Force Delete)
```
1. User attempts to delete course
2. Controller checks for enrolled students
3. If students exist:
   ❌ Returns 422 error with message
   ❌ Shows: "Cannot delete course 'Math 101' because it has 5 enrolled student(s)"
   ✓ Suggests: "Please unenroll all students first or use force delete"
4. If no students:
   ✓ Proceeds with deletion
   ✓ CourseService::cleanupCourseModules() removes all related data
   ✓ Returns success message
```

### Force Delete (force_delete = true)
```
1. User confirms force delete
2. Request includes force_delete parameter
3. Controller allows deletion even with enrolled students
4. CourseService::cleanupCourseModules() removes:
   - All modules and their children
   - All activities, quizzes, questions
   - All student progress records
   - All enrollments
5. Course is deleted
6. Returns success message
```

## API Response Examples

### Error Response (Students Enrolled)
```json
{
  "success": false,
  "message": "Cannot delete course 'Math 101' because it has 5 enrolled student(s). Please unenroll all students first or use force delete.",
  "enrolled_students": 5,
  "requires_force_delete": true
}
```
**Status Code:** 422 (Unprocessable Entity)

### Success Response
```json
{
  "success": true,
  "message": "Course deleted successfully"
}
```
**Status Code:** 200 (OK)

### Server Error Response (Unexpected Error)
```json
{
  "success": false,
  "message": "Failed to delete course: [error details]"
}
```
**Status Code:** 500 (Internal Server Error)

## Frontend Integration

The frontend can now handle the response properly:

```javascript
// Example frontend handling
try {
  const response = await axios.delete(`/courses/${courseId}`);
  showSuccess(response.data.message);
} catch (error) {
  if (error.response.status === 422) {
    // Validation error - students enrolled
    const data = error.response.data;
    
    if (data.requires_force_delete) {
      // Show confirmation dialog with force delete option
      showForceDeleteDialog({
        message: data.message,
        enrolledStudents: data.enrolled_students,
        courseId: courseId
      });
    }
  } else {
    // Other errors
    showError(error.response.data.message);
  }
}
```

## Testing Scenarios

### ✅ Test Case 1: Delete Course with No Students
- Action: Delete a course with no enrollments
- Expected: Course deleted successfully
- Status: 200

### ✅ Test Case 2: Delete Course with Enrolled Students (No Force)
- Action: Delete a course with active enrollments
- Expected: Deletion blocked with helpful message
- Status: 422

### ✅ Test Case 3: Force Delete Course with Enrolled Students
- Action: Delete course with `force_delete: true`
- Expected: Course and all related data deleted
- Status: 200

### ✅ Test Case 4: Delete Course with Completed Students
- Action: Delete course where students have completed it
- Expected: Same as Test Case 2 (still enrolled)
- Status: 422

## Database Integrity

The deletion flow ensures:
1. ✅ No orphaned records remain
2. ✅ Foreign key constraints are respected
3. ✅ All related data is properly cleaned up
4. ✅ Transaction rollback on any error
5. ✅ Comprehensive logging for debugging

## Files Modified

1. `app/Http/Controllers/CourseController.php`
   - Added early validation check
   - Improved error handling
   - Better response structure

2. `app/Models/Course.php`
   - Simplified boot method
   - Removed duplicate cascade logic
   - Added clarifying comments

3. `app/Services/CourseService.php`
   - Already had proper logic (no changes needed)
   - Handles complex cleanup operations

## Implementation Date
October 13, 2025

## Notes
- The `force_delete` parameter is optional and defaults to `false`
- Only authorized users should be able to force delete courses
- Consider adding a confirmation dialog in the frontend for force delete
- Audit logging should track force delete operations
