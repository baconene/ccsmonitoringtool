# Course Visibility Fix - Show All Relevant Courses

## Issue
Only 1 course was showing in Course Management, even though 5 courses exist in the database.

## Root Cause
The `getCourses()` method in `CourseService.php` had a restrictive filter:

```php
->where('created_by', auth()->id()); // Only get courses created by the active user
```

This meant:
- Instructors only saw courses THEY created
- They couldn't see courses they were ASSIGNED to teach (instructor_id)
- Admins couldn't see all courses

## Database State (Before Fix)
```
id | title                          | instructor_id | created_by
---|--------------------------------|---------------|------------
1  | Advanced Mathematics           | 1             | 4
2  | Physics Fundamentals           | 2             | 5
3  | Computer Programming           | 3             | 6
4  | TEST                           | 4             | 7
5  | TEST AD                        | 1             | 1
```

**Example**: If logged in as user_id=4 (instructor_id=1):
- **Before**: Only saw Course 1 (created_by=4)
- **After**: Sees Course 1 AND Course 5 (instructor_id=1)

## Solution

### Updated Logic in CourseService.php

```php
public function getCourses(array $filters = [], int $perPage = 15)
{
    $user = auth()->user();
    
    $query = Course::with(['creator', 'instructor.user', 'modules.activities.activityType', 'modules.lessons', 'gradeLevels'])
        ->withCount(['students']);
    
    // For instructors: show courses they created OR courses they're assigned to teach
    // For admins: show all courses
    if ($user && !$user->isAdmin()) {
        $instructorId = $user->instructor ? $user->instructor->id : null;
        
        $query->where(function($q) use ($user, $instructorId) {
            $q->where('created_by', $user->id); // Courses created by user
            
            if ($instructorId) {
                $q->orWhere('instructor_id', $instructorId); // Courses assigned to instructor
            }
        });
    }
    // If admin, no filter - show all courses

    // Apply filters
    // ... rest of method
}
```

## New Behavior

### For Instructors
Shows courses where:
1. `created_by` = user's ID (courses they created)
2. **OR** `instructor_id` = instructor model ID (courses they're assigned to teach)

### For Admins
Shows ALL courses (no filter applied)

### For Other Users
Shows only courses they created

## Examples

### Example 1: Instructor Creates and Teaches
```
User: ID=4, Role=instructor, instructor_id=1
Created: Course 1
Assigned: Course 1

Courses Shown:
- Course 1 (created_by=4, instructor_id=1) ✅
```

### Example 2: Instructor Teaches Course Created by Admin
```
User: ID=4, Role=instructor, instructor_id=1
Created: Course 1
Assigned: Course 1, Course 5

Courses Shown:
- Course 1 (created_by=4) ✅ Created by them
- Course 5 (instructor_id=1) ✅ Assigned to teach
```

### Example 3: Admin User
```
User: ID=1, Role=admin

Courses Shown:
- Course 1 ✅
- Course 2 ✅
- Course 3 ✅
- Course 4 ✅
- Course 5 ✅
All courses visible
```

### Example 4: Course Transfer Scenario
```
Scenario:
- Admin (user_id=1) creates Course 5
- Initially assigns to Instructor A (instructor_id=1, user_id=4)
- Later transfers to Instructor B (instructor_id=2, user_id=5)

Instructor A's View:
- Sees Course 1 (created_by=4) ✅
- Does NOT see Course 5 anymore (instructor_id changed) ❌

Instructor B's View:
- Sees Course 2 (created_by=5) ✅
- Sees Course 5 (instructor_id=2) ✅ Now assigned

Admin's View:
- Sees all courses ✅
```

## Frontend Analysis

### CourseManagement.vue Filtering
The frontend component has NO restrictive filters:

```typescript
const filteredCourses = computed(() => {
  if (!searchText.value) {
    return props.courses; // Shows ALL courses from backend
  }

  return props.courses.filter(
    c =>
      c.id.toString().includes(searchText.value) ||
      (c.name && c.name.toLowerCase().includes(searchText.value.toLowerCase())) ||
      (c.description && c.description.toLowerCase().includes(searchText.value.toLowerCase()))
  );
});
```

**Conclusion**: Frontend only filters based on search text. No courses are hidden by frontend logic.

## Query Explanation

### Original Query (Before Fix)
```sql
SELECT * FROM courses
WHERE created_by = 4;  -- Only shows 1 course
```

### Updated Query (After Fix)
```sql
-- For Instructors
SELECT * FROM courses
WHERE created_by = 4  -- Courses created by user
   OR instructor_id = 1;  -- OR courses assigned to instructor

-- For Admins
SELECT * FROM courses;  -- No filter, all courses
```

## Testing

### Test Case 1: Instructor with Assigned Courses
```php
// Login as instructor (user_id=4, instructor_id=1)
$courses = CourseService::getCourses();

Expected Results:
- Course 1 (created_by=4) ✅
- Course 5 (instructor_id=1) ✅
Total: 2 courses
```

### Test Case 2: Instructor with No Assignments
```php
// Login as instructor (user_id=5, instructor_id=2)
$courses = CourseService::getCourses();

Expected Results:
- Course 2 (created_by=5) ✅
Total: 1 course (or more if assigned others)
```

### Test Case 3: Admin
```php
// Login as admin (user_id=1)
$courses = CourseService::getCourses();

Expected Results:
- All 5 courses ✅
```

### Test Case 4: Empty State
```php
// New instructor with no courses
$courses = CourseService::getCourses();

Expected Results:
- Empty array []
```

## Database Verification Queries

### Count Total Courses
```sql
SELECT COUNT(*) as total_courses FROM courses;
-- Expected: 5
```

### Courses for Specific User
```sql
-- User ID 4, Instructor ID 1
SELECT id, title, created_by, instructor_id 
FROM courses 
WHERE created_by = 4 OR instructor_id = 1;
-- Expected: Courses 1, 5
```

### All Instructors and Their Courses
```sql
SELECT 
    i.id as instructor_id,
    i.user_id,
    u.name,
    COUNT(DISTINCT CASE WHEN c1.created_by = i.user_id THEN c1.id END) as created_courses,
    COUNT(DISTINCT CASE WHEN c2.instructor_id = i.id THEN c2.id END) as teaching_courses
FROM instructors i
JOIN users u ON i.user_id = u.id
LEFT JOIN courses c1 ON c1.created_by = i.user_id
LEFT JOIN courses c2 ON c2.instructor_id = i.id
GROUP BY i.id, i.user_id, u.name;
```

## Benefits

### For Instructors
✅ See courses they created (authorship)  
✅ See courses they're assigned to teach (responsibility)  
✅ Complete view of their teaching workload  
✅ No confusion about "missing" courses  

### For Admins
✅ Full system visibility  
✅ Can manage all courses  
✅ No artificial restrictions  

### For System
✅ Proper separation of authorship vs assignment  
✅ Flexible course management  
✅ Supports course transfers  
✅ Scalable as system grows  

## Edge Cases Handled

### Case 1: Instructor Without Instructor Record
```php
if ($instructorId) {
    $q->orWhere('instructor_id', $instructorId);
}
```
If user has no instructor record, only shows created courses.

### Case 2: Non-Instructor User
```php
if ($user && !$user->isAdmin()) {
    // Apply instructor filter
}
```
Regular users see only courses they created.

### Case 3: Guest User
```php
$user = auth()->user();
if ($user && !$user->isAdmin()) {
    // Apply filter
}
```
No error if not authenticated (though middleware should prevent this).

## Performance Considerations

### Efficient OR Query
```php
$query->where(function($q) use ($user, $instructorId) {
    $q->where('created_by', $user->id)
      ->orWhere('instructor_id', $instructorId);
});
```
Single query with OR condition - no multiple database calls.

### Eager Loading Maintained
```php
Course::with(['creator', 'instructor.user', 'modules.activities.activityType', ...])
```
Still loads relationships efficiently to prevent N+1 queries.

### Index Recommendations
```sql
-- Existing indexes should cover:
CREATE INDEX idx_courses_created_by ON courses(created_by);
CREATE INDEX idx_courses_instructor_id ON courses(instructor_id);
```

## Rollback Plan

If needed, revert to original behavior:

```php
public function getCourses(array $filters = [], int $perPage = 15)
{
    $query = Course::with([...])
        ->withCount(['students'])
        ->where('created_by', auth()->id());
    
    // ... rest of method
}
```

## Migration Notes

### No Database Changes Required
This is purely a logic change - no migrations needed.

### Data Remains Unchanged
All courses still have correct:
- `created_by` (who created it)
- `instructor_id` (who teaches it)

### Backward Compatible
Existing courses work correctly with new logic.

## Summary

### Problem
✅ **FIXED**: Restrictive filter showing only created courses

### Solution
✅ **IMPLEMENTED**: Show courses created OR assigned to instructor

### Result
✅ Instructors see complete course list (created + teaching)  
✅ Admins see all courses  
✅ No frontend filtering issues  
✅ Database structure verified  

### Files Modified
- `app/Services/CourseService.php` - Updated getCourses() method

### Testing Required
- [ ] Login as instructor with assignments
- [ ] Verify all relevant courses visible
- [ ] Login as admin
- [ ] Verify all courses visible
- [ ] Test search functionality
- [ ] Verify course management operations work

The course visibility issue is now resolved! Instructors will see both courses they created and courses they're assigned to teach.
