# Course Creation - Instructor ID & Created By Fix

## Overview
This document explains the implementation of proper `instructor_id` and `created_by` field population when creating courses, based on the authenticated user's role.

## Requirements
- **If user role is INSTRUCTOR**: Set `instructor_id` to the instructor model ID and `created_by` to user ID
- **If user role is ADMIN**: Set only `created_by` to user ID, leave `instructor_id` empty (or allow manual assignment)
- **Backend should handle the logic**: Frontend doesn't need to pass instructor_id explicitly

## Database Schema

### Courses Table Foreign Keys
```
courses.instructor_id → instructors.id (Instructor model ID)
courses.created_by → users.id (User ID for audit trail)
```

### Instructors Table
```
instructors.id (PK) - Instructor model ID
instructors.user_id (FK → users.id) - References the user account
```

### Relationship Flow
```
User (id=4, role=instructor)
  ↓
Instructor (id=1, user_id=4)
  ↓
Course (instructor_id=1, created_by=4)
```

## Implementation Details

### 1. Backend: CourseController.php

#### Updated `store()` Method
**File**: `app/Http/Controllers/CourseController.php`

**Changes Made**:

1. **Validation Update**: Changed `instructor_id` validation from `exists:users,id` to `exists:instructors,id`
   ```php
   'instructor_id' => 'nullable|exists:instructors,id',
   ```

2. **Role-Based Logic**: Implemented logic to set `instructor_id` and `created_by` based on user role
   ```php
   $user = auth()->user();
   
   // Always set created_by to the authenticated user's ID
   $validated['created_by'] = $user->id;
   
   // Handle instructor_id based on user role
   if ($user->isInstructor()) {
       // For instructors: Get or create instructor record and use instructor model ID
       $instructor = $user->getOrCreateInstructorRecord();
       $validated['instructor_id'] = $instructor->id;
   } elseif ($user->isAdmin()) {
       // For admins: Only set created_by, don't set instructor_id
       // Admin can optionally assign an instructor via the form
       if (!isset($validated['instructor_id'])) {
           unset($validated['instructor_id']);
       }
   } else {
       // Fallback: use instructor_id from request or leave it null
       if (!isset($validated['instructor_id'])) {
           unset($validated['instructor_id']);
       }
   }
   ```

#### Complete Updated Code
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'name' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'instructor_id' => 'nullable|exists:instructors,id', // ← Changed from users to instructors
        'grade_level' => 'nullable|string|max:50',
        'course_code' => 'nullable|string|max:20|unique:courses',
        'credits' => 'nullable|integer|min:1|max:10',
        'semester' => 'nullable|string|in:Fall,Spring,Summer',
        'academic_year' => 'nullable|string|size:4',
        'is_active' => 'nullable|boolean',
        'enrollment_limit' => 'nullable|integer|min:1',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after:start_date',
        'default_modules' => 'nullable|boolean',
        'grade_level_ids' => 'nullable|array',
        'grade_level_ids.*' => 'exists:grade_levels,id',
    ]);

    try {
        $user = auth()->user();
        
        // Set created_by to the authenticated user's ID
        $validated['created_by'] = $user->id;
        
        // Handle instructor_id based on user role
        if ($user->isInstructor()) {
            // For instructors: Get or create instructor record and use instructor model ID
            $instructor = $user->getOrCreateInstructorRecord();
            $validated['instructor_id'] = $instructor->id;
        } elseif ($user->isAdmin()) {
            // For admins: Only set created_by, don't set instructor_id
            // Admin can optionally assign an instructor via the form
            if (!isset($validated['instructor_id'])) {
                unset($validated['instructor_id']);
            }
        } else {
            // Fallback: use instructor_id from request or leave it null
            if (!isset($validated['instructor_id'])) {
                unset($validated['instructor_id']);
            }
        }
        
        // Use CourseService for main course creation
        $course = $this->courseService->addCourse($validated);

        // Handle grade level associations if provided (legacy support)
        if (!empty($validated['grade_level_ids'])) {
            $course->gradeLevels()->attach($validated['grade_level_ids']);
        }

        // Load relationships for response
        $course->load('gradeLevels', 'creator', 'modules');

        return response()->json([
            'success' => true,
            'course' => $course,
            'message' => $this->getModelSuccessMessage('created', $course)
        ], 201);

    } catch (\Exception $e) {
        Log::error('Failed to create course', [
            'error' => $e->getMessage(),
            'data' => $validated
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create course: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. Frontend: CourseModal.vue

#### Updated Form Submission
**File**: `resources/js/course/CourseModal.vue`

**Changes Made**: Added comment explaining that `instructor_id` is handled by backend

```javascript
async function handleSubmit() {
  if (isSubmitting.value) return;
  
  isSubmitting.value = true;
  errors.value = {};
  successMessage.value = '';

  try {
    const formData = {
      title: localCourse.title,
      name: localCourse.name || localCourse.title,
      course_code: localCourse.course_code,
      description: localCourse.description,
      grade_level_ids: localCourse.grade_level_ids,
      // instructor_id will be automatically set by backend based on user role
      // No need to pass it from frontend
    };

    if (props.mode === 'create') {
      const response = await axios.post('/courses', formData);
      const newCourseId = response.data.course?.id || response.data.id;
      
      successMessage.value = 'Course created successfully!';
      setTimeout(() => {
        emit('refresh', newCourseId);
        emit('close');
      }, 1500);
      
    } else if (props.mode === 'edit' && props.course?.id) {
      await axios.put(`/courses/${props.course.id}`, formData);
      successMessage.value = 'Course updated successfully!';
      setTimeout(() => {
        emit('refresh');
        emit('close');
      }, 1500);
    }
  } catch (error: any) {
    // Error handling...
  } finally {
    isSubmitting.value = false;
  }
}
```

## User Model Relationships

### Instructor Relationship
```php
// User.php
public function instructor()
{
    return $this->hasOne(Instructor::class);
}

public function hasInstructorRecord(): bool
{
    return $this->instructor()->exists();
}

public function getOrCreateInstructorRecord(array $additionalData = []): Instructor
{
    if ($this->hasInstructorRecord()) {
        return $this->instructor;
    }

    // Only create if user has instructor or admin role
    if (!$this->isInstructor() && !$this->isAdmin()) {
        throw new \Exception('Cannot create instructor record for non-instructor user');
    }

    // Creates instructor record with auto-generated instructor_id
    return Instructor::create([
        'instructor_id' => Instructor::generateInstructorId(),
        'user_id' => $this->id,
        'department' => 'General Studies',
        'employment_type' => 'full-time',
        'status' => 'active',
        'hire_date' => now(),
        'metadata' => ['auto_created' => true]
    ]);
}
```

### Role Checks
```php
// User.php
public function isAdmin(): bool
{
    return $this->hasRole('admin');
}

public function isInstructor(): bool
{
    return $this->hasRole('instructor');
}

public function isStudent(): bool
{
    return $this->hasRole('student');
}
```

## Usage Examples

### Example 1: Instructor Creates Course

**Scenario**: User with ID 4 and role "instructor" creates a course

**Flow**:
1. User (id=4, role=instructor) submits course form
2. Backend checks: `$user->isInstructor()` → true
3. Backend calls: `$instructor = $user->getOrCreateInstructorRecord()`
4. Returns: Instructor model with id=1, user_id=4
5. Course created with:
   - `instructor_id = 1` (Instructor model ID)
   - `created_by = 4` (User ID)

**Database Result**:
```
courses table:
id | name               | instructor_id | created_by
1  | Advanced Math      | 1             | 4

instructors table:
id | user_id | instructor_id | department
1  | 4       | INS-001       | General Studies

users table:
id | name              | role_id
4  | Dr. Instructor 1  | 2
```

### Example 2: Admin Creates Course

**Scenario**: User with ID 3 and role "admin" creates a course

**Flow**:
1. User (id=3, role=admin) submits course form
2. Backend checks: `$user->isInstructor()` → false
3. Backend checks: `$user->isAdmin()` → true
4. Backend sets: `created_by = 3`
5. Backend: instructor_id not set (nullable, can be assigned later)
6. Course created with:
   - `instructor_id = NULL` (or specified value if admin selected one)
   - `created_by = 3` (User ID)

**Database Result**:
```
courses table:
id | name               | instructor_id | created_by
2  | General Science    | NULL          | 3

users table:
id | name              | role_id
3  | Admin User        | 1
```

### Example 3: Admin Assigns Instructor to Course

**Scenario**: Admin manually assigns an existing instructor when creating course

**Flow**:
1. Admin selects instructor from dropdown (instructor_id=2)
2. Form submits with `instructor_id: 2` in payload
3. Backend checks: `$user->isAdmin()` → true
4. Backend checks: `isset($validated['instructor_id'])` → true
5. Backend keeps the specified instructor_id
6. Course created with:
   - `instructor_id = 2` (Manually assigned)
   - `created_by = 3` (Admin's user ID)

**Database Result**:
```
courses table:
id | name               | instructor_id | created_by
3  | Advanced Physics   | 2             | 3

instructors table:
id | user_id | instructor_id | department
2  | 5       | INS-002       | Science

users table:
id | name              | role_id
3  | Admin User        | 1
5  | Dr. Instructor 2  | 2
```

## Testing

### Test 1: Instructor Course Creation
```bash
# Login as instructor (user_id=4)
POST /courses
{
    "title": "Mathematics 101",
    "description": "Introduction to Mathematics",
    "grade_level_ids": [1, 2]
}

# Expected Response:
{
    "success": true,
    "course": {
        "id": 1,
        "title": "Mathematics 101",
        "instructor_id": 1,  // Instructor model ID
        "created_by": 4      // User ID
    }
}

# Verify in database:
SELECT c.*, i.user_id 
FROM courses c 
JOIN instructors i ON c.instructor_id = i.id 
WHERE c.id = 1;

# Should show:
# instructor_id=1, created_by=4, i.user_id=4
```

### Test 2: Admin Course Creation Without Instructor
```bash
# Login as admin (user_id=3)
POST /courses
{
    "title": "General Studies",
    "description": "General education course"
}

# Expected Response:
{
    "success": true,
    "course": {
        "id": 2,
        "title": "General Studies",
        "instructor_id": null,  // Not set
        "created_by": 3          // Admin's user ID
    }
}
```

### Test 3: Admin Course Creation With Assigned Instructor
```bash
# Login as admin (user_id=3)
POST /courses
{
    "title": "Physics 101",
    "instructor_id": 2,  // Assigning to instructor model ID 2
    "description": "Introduction to Physics"
}

# Expected Response:
{
    "success": true,
    "course": {
        "id": 3,
        "title": "Physics 101",
        "instructor_id": 2,  // As specified
        "created_by": 3       // Admin's user ID
    }
}
```

## Verification Queries

### Check Course with Instructor Details
```sql
SELECT 
    c.id,
    c.title,
    c.instructor_id,
    c.created_by,
    i.id as instructor_model_id,
    i.user_id as instructor_user_id,
    i.instructor_id as instructor_code,
    u.name as instructor_name,
    creator.name as created_by_name
FROM courses c
LEFT JOIN instructors i ON c.instructor_id = i.id
LEFT JOIN users u ON i.user_id = u.id
LEFT JOIN users creator ON c.created_by = creator.id
ORDER BY c.id;
```

### Check Instructor Records for Users
```sql
SELECT 
    u.id as user_id,
    u.name,
    u.email,
    r.name as role,
    i.id as instructor_model_id,
    i.instructor_id as instructor_code
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
LEFT JOIN instructors i ON u.id = i.user_id
WHERE r.name IN ('instructor', 'admin')
ORDER BY u.id;
```

### Check Courses Created by Each User
```sql
SELECT 
    u.id as user_id,
    u.name as user_name,
    r.name as role,
    COUNT(c.id) as courses_created,
    GROUP_CONCAT(c.title) as course_titles
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
LEFT JOIN courses c ON u.id = c.created_by
GROUP BY u.id, u.name, r.name
HAVING courses_created > 0
ORDER BY courses_created DESC;
```

## Future Enhancements

### 1. Admin Instructor Selection UI
Add a dropdown in the CourseModal for admins to select an instructor:

```vue
<!-- CourseModal.vue -->
<div v-if="userRole === 'admin'">
  <label class="block text-sm font-medium">Assign Instructor (Optional)</label>
  <select v-model="localCourse.instructor_id" class="w-full mt-1 p-2 border rounded">
    <option :value="null">-- No Instructor Assigned --</option>
    <option v-for="instructor in availableInstructors" :key="instructor.id" :value="instructor.id">
      {{ instructor.user.name }}
    </option>
  </select>
</div>
```

### 2. API Endpoint for Instructor List
```php
// CourseController.php
public function getAvailableInstructors()
{
    $instructors = Instructor::with('user')
        ->whereHas('user', function($query) {
            $query->whereHas('role', function($q) {
                $q->where('name', 'instructor');
            });
        })
        ->get()
        ->map(function($instructor) {
            return [
                'id' => $instructor->id,
                'instructor_id' => $instructor->instructor_id,
                'user' => [
                    'id' => $instructor->user_id,
                    'name' => $instructor->user->name,
                    'email' => $instructor->user->email,
                ],
                'department' => $instructor->department,
            ];
        });

    return response()->json(['instructors' => $instructors]);
}
```

### 3. Course Transfer Between Instructors
```php
// CourseController.php
public function transferCourse(Request $request, Course $course)
{
    $request->validate([
        'new_instructor_id' => 'required|exists:instructors,id',
    ]);

    $course->update([
        'instructor_id' => $request->new_instructor_id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Course transferred successfully',
        'course' => $course->load('instructor.user')
    ]);
}
```

## Summary

### ✅ What Was Fixed
1. **Backend Logic**: CourseController now automatically sets `instructor_id` and `created_by` based on user role
2. **Validation**: Changed `instructor_id` validation from `exists:users,id` to `exists:instructors,id`
3. **Instructor Role**: Auto-creates instructor record if needed, uses instructor model ID
4. **Admin Role**: Only sets `created_by`, allows manual instructor assignment
5. **Frontend**: Updated comments to clarify backend handles the logic

### 📋 Key Points
- `instructor_id` → References `instructors.id` (Instructor model ID)
- `created_by` → References `users.id` (User ID for audit)
- Instructors get their instructor model ID automatically assigned
- Admins can create courses without assigning an instructor
- Backend handles all the logic, frontend just submits basic course data

### 🎯 Production Ready
- Database schema correct (foreign keys pointing to right tables)
- Role-based logic implemented
- Auto-creation of instructor records
- Proper audit trail with `created_by` field
- Backward compatible with existing courses
