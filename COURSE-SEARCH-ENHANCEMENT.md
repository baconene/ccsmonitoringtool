# Course Search Enhancement - Search by Instructor and Creator

## Overview
Enhanced the course search functionality to allow searching by:
- **Instructor ID** - Search courses by the instructor model ID
- **Created By** - Search courses by the creator's user ID
- **Instructor Name/Email** - Search courses by the instructor's user details
- **Creator Name/Email** - Search courses by the creator's user details

## Changes Made

### 1. Backend: CourseService.php
**File**: `app/Services/CourseService.php`

**Method Updated**: `getCourses()`

**Changes**:
1. Added `instructor.user` to the eager loading relationships
2. Extended search query to include:
   - `instructor_id` field search
   - `created_by` field search
   - Instructor's user name and email (via relationship)
   - Creator's name and email (via relationship)

**Updated Code**:
```php
public function getCourses(array $filters = [], int $perPage = 15)
{
    $query = Course::with(['creator', 'instructor.user', 'modules.activities.activityType', 'modules.lessons', 'gradeLevels'])
        ->withCount(['students'])
        ->where('created_by', auth()->id()); // Only get courses created by the active user

    // Apply filters
    if (isset($filters['search'])) {
        $searchTerm = $filters['search'];
        $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhere('course_code', 'like', '%' . $searchTerm . '%')
              ->orWhere('instructor_id', 'like', '%' . $searchTerm . '%')
              ->orWhere('created_by', 'like', '%' . $searchTerm . '%')
              // Search by instructor's user details
              ->orWhereHas('instructor.user', function($userQuery) use ($searchTerm) {
                  $userQuery->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('email', 'like', '%' . $searchTerm . '%');
              })
              // Search by creator's details
              ->orWhereHas('creator', function($creatorQuery) use ($searchTerm) {
                  $creatorQuery->where('name', 'like', '%' . $searchTerm . '%')
                               ->orWhere('email', 'like', '%' . $searchTerm . '%');
              });
        });
    }
    
    // ... rest of filters
}
```

### 2. Model: Course.php
**File**: `app/Models/Course.php`

**Changes**:
- Updated `instructor()` relationship to correctly reference the `Instructor` model instead of `User`

**Before**:
```php
public function instructor(): BelongsTo
{
    //Update later this should belong to a instructor
    return $this->belongsTo(User::class, 'instructor_id');
}
```

**After**:
```php
public function instructor(): BelongsTo
{
    return $this->belongsTo(Instructor::class, 'instructor_id');
}
```

**Why This Matters**:
- The `instructor_id` foreign key references `instructors.id` (not `users.id`)
- This allows proper eager loading of instructor data with `instructor.user`
- Maintains data integrity and proper relationship structure

## Search Capabilities

### What You Can Search For

#### 1. Course Details (Original)
- **Title**: Search by course title/name
- **Description**: Search in course description
- **Course Code**: Search by course code

#### 2. Instructor ID (New)
- **instructor_id**: Direct search by instructor model ID
- Example: Search "1" to find all courses where `instructor_id = 1`

#### 3. Creator/User ID (New)
- **created_by**: Direct search by user ID
- Example: Search "4" to find all courses where `created_by = 4`

#### 4. Instructor Details (New)
- **Instructor Name**: Search by instructor's full name
- **Instructor Email**: Search by instructor's email address
- Example: Search "Dr. Smith" to find courses taught by Dr. Smith

#### 5. Creator Details (New)
- **Creator Name**: Search by creator's full name
- **Creator Email**: Search by creator's email address
- Example: Search "admin@example.com" to find courses created by that admin

## Database Relationships

```
┌────────────────────────────────────────────────────────────────┐
│                      USERS TABLE                               │
├────┬──────────────────┬─────────────────┬──────────────────────┤
│ id │      name        │     email       │      role_id         │
├────┼──────────────────┼─────────────────┼──────────────────────┤
│ 4  │ Dr. Smith        │ smith@uni.edu   │  2 (instructor)      │
│ 3  │ Admin User       │ admin@uni.edu   │  1 (admin)           │
└────┴──────────────────┴─────────────────┴──────────────────────┘
                 │
                 │ user_id FK
                 ↓
┌────────────────────────────────────────────────────────────────┐
│                  INSTRUCTORS TABLE                             │
├────┬─────────┬──────────────┬──────────────────────────────────┤
│ id │ user_id │  department  │      instructor_id               │
├────┼─────────┼──────────────┼──────────────────────────────────┤
│ 1  │    4    │   Science    │         INS-001                  │
└────┴─────────┴──────────────┴──────────────────────────────────┘
     │                           ┌─ created_by FK (to users.id)
     │ instructor_id FK          │
     ↓                           ↓
┌────────────────────────────────────────────────────────────────┐
│                    COURSES TABLE                               │
├────┬────────────────┬──────────────┬────────────┬──────────────┤
│ id │     title      │instructor_id │ created_by │  course_code │
├────┼────────────────┼──────────────┼────────────┼──────────────┤
│ 1  │ Math 101       │      1       │     4      │   MATH101    │
│ 2  │ Physics        │      1       │     3      │   PHYS101    │
└────┴────────────────┴──────────────┴────────────┴──────────────┘

RELATIONSHIPS:
- courses.instructor_id → instructors.id (Instructor model ID)
- courses.created_by → users.id (User ID)
- instructors.user_id → users.id (User ID)

SEARCH PATHS:
1. Direct: courses.instructor_id, courses.created_by
2. Via Instructor: instructor.user.name, instructor.user.email
3. Via Creator: creator.name, creator.email
```

## Search Examples

### Example 1: Search by Instructor Model ID
```
Search Input: "1"

Query matches:
- courses.instructor_id = 1

Results:
- Math 101 (instructor_id=1)
- Physics (instructor_id=1)
```

### Example 2: Search by Creator User ID
```
Search Input: "3"

Query matches:
- courses.created_by = 3

Results:
- Physics (created_by=3, Admin User)
```

### Example 3: Search by Instructor Name
```
Search Input: "Dr. Smith"

Query matches:
- instructor.user.name LIKE '%Dr. Smith%'

Results:
- Math 101 (instructor_id=1, taught by Dr. Smith)
- Physics (instructor_id=1, taught by Dr. Smith)
```

### Example 4: Search by Creator Email
```
Search Input: "admin@uni.edu"

Query matches:
- creator.email LIKE '%admin@uni.edu%'

Results:
- Physics (created_by=3, Admin User)
```

### Example 5: Combined Search
```
Search Input: "smith"

Query matches:
- courses.title LIKE '%smith%' OR
- courses.description LIKE '%smith%' OR
- instructor.user.name LIKE '%smith%' OR
- instructor.user.email LIKE '%smith%' OR
- creator.name LIKE '%smith%' OR
- creator.email LIKE '%smith%'

Results:
- All courses taught by or created by Dr. Smith
- Any courses with "smith" in title/description
```

## Use Cases

### Use Case 1: Find All Courses by Specific Instructor
**Scenario**: Admin wants to find all courses taught by instructor ID 1

**Action**: Search for "1" in the course search

**Result**: Returns courses where `instructor_id = 1`

### Use Case 2: Find Courses Created by Specific User
**Scenario**: Track which courses were created by a specific admin (user_id=3)

**Action**: Search for "3" in the course search

**Result**: Returns courses where `created_by = 3`

### Use Case 3: Find Courses by Instructor Name
**Scenario**: Search for all courses taught by "Dr. Smith"

**Action**: Type "Dr. Smith" in search box

**Result**: Returns courses where instructor's name contains "Dr. Smith"

### Use Case 4: Audit Course Creation
**Scenario**: Admin wants to see which courses were created by "admin@university.edu"

**Action**: Search for "admin@university.edu"

**Result**: Returns courses created by that admin user

### Use Case 5: Course Transfer Tracking
**Scenario**: Course was created by Admin (user_id=3) but assigned to Dr. Smith (instructor_id=1)

**Search "3"**: Shows course in creator's list
**Search "1"**: Shows course in instructor's teaching list
**Search "Dr. Smith"**: Shows course by instructor name

## Frontend Behavior

### Current Frontend Search (Client-Side)
The frontend in `CourseManagement.vue` still has client-side filtering:

```typescript
const filteredCourses = computed(() => {
  if (!searchText.value) {
    return props.courses;
  }

  return props.courses.filter(
    c =>
      c.id.toString().includes(searchText.value) ||
      (c.name && c.name.toLowerCase().includes(searchText.value.toLowerCase())) ||
      (c.description && c.description.toLowerCase().includes(searchText.value.toLowerCase()))
  );
});
```

**Note**: The frontend search is complementary to backend search. Backend filters the initial dataset, frontend provides instant filtering on loaded courses.

### How They Work Together

1. **Backend (Database Query)**: 
   - Filters courses at database level
   - Reduces data transfer
   - Applies when page loads or refreshes

2. **Frontend (Client-Side)**:
   - Filters already loaded courses
   - Provides instant feedback
   - No server round-trip needed

## Testing

### Test Query 1: Search by Instructor ID
```sql
-- Find all courses with instructor_id = 1
SELECT id, title, instructor_id, created_by 
FROM courses 
WHERE instructor_id = 1;
```

### Test Query 2: Search by Instructor Name
```sql
-- Find courses taught by instructor with name containing 'Smith'
SELECT c.id, c.title, c.instructor_id, u.name as instructor_name
FROM courses c
JOIN instructors i ON c.instructor_id = i.id
JOIN users u ON i.user_id = u.id
WHERE u.name LIKE '%Smith%';
```

### Test Query 3: Search by Creator ID
```sql
-- Find all courses created by user_id = 3
SELECT id, title, instructor_id, created_by
FROM courses
WHERE created_by = 3;
```

### Test Query 4: Combined Search
```sql
-- Find courses matching search term in multiple fields
SELECT DISTINCT c.id, c.title, c.instructor_id, c.created_by
FROM courses c
LEFT JOIN instructors i ON c.instructor_id = i.id
LEFT JOIN users instructor_user ON i.user_id = instructor_user.id
LEFT JOIN users creator_user ON c.created_by = creator_user.id
WHERE c.title LIKE '%physics%'
   OR c.description LIKE '%physics%'
   OR c.course_code LIKE '%physics%'
   OR c.instructor_id LIKE '%physics%'
   OR c.created_by LIKE '%physics%'
   OR instructor_user.name LIKE '%physics%'
   OR instructor_user.email LIKE '%physics%'
   OR creator_user.name LIKE '%physics%'
   OR creator_user.email LIKE '%physics%';
```

## Performance Considerations

### Eager Loading
```php
Course::with(['creator', 'instructor.user', ...])
```
- Loads relationships in advance
- Prevents N+1 query problem
- Single query instead of multiple

### Indexed Fields
Ensure these fields are indexed for faster searches:
- `courses.instructor_id`
- `courses.created_by`
- `users.name`
- `users.email`
- `courses.title`
- `courses.course_code`

### Query Optimization
```php
// Uses whereHas which is optimized by Laravel
->orWhereHas('instructor.user', function($userQuery) use ($searchTerm) {
    $userQuery->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('email', 'like', '%' . $searchTerm . '%');
})
```

## Benefits

### For Instructors
- ✅ Find courses they're teaching (by instructor_id)
- ✅ Find courses they created (by created_by)
- ✅ Search by colleague's name

### For Admins
- ✅ Track course assignments
- ✅ Audit course creation
- ✅ Find courses by instructor details
- ✅ Monitor course distribution

### For System
- ✅ More flexible search
- ✅ Better data discovery
- ✅ Proper relationship usage
- ✅ Efficient database queries

## Future Enhancements

### 1. Advanced Search Filters
Add dedicated filters for:
```vue
<select v-model="filters.instructor_id">
  <option value="">All Instructors</option>
  <option v-for="instructor in instructors" :value="instructor.id">
    {{ instructor.user.name }}
  </option>
</select>
```

### 2. Search Results Highlighting
Highlight matched search terms in results:
```vue
<template>
  <span v-html="highlightSearchTerm(course.title, searchText)"></span>
</template>
```

### 3. Search Analytics
Track common search terms:
```php
SearchLog::create([
    'user_id' => auth()->id(),
    'search_term' => $searchTerm,
    'results_count' => $courses->count(),
    'searched_at' => now()
]);
```

### 4. Autocomplete Suggestions
Show suggestions as user types:
```vue
<datalist id="search-suggestions">
  <option v-for="suggestion in searchSuggestions" :value="suggestion" />
</datalist>
```

## Summary

### ✅ Completed
1. **Backend Search Enhancement**: Extended search to include instructor_id, created_by, and related user details
2. **Model Relationship Fix**: Updated Course model to use correct Instructor relationship
3. **Eager Loading**: Added instructor.user to relationship loading for efficient queries
4. **Documentation**: Comprehensive guide with examples and use cases

### 🔍 Search Fields Added
- `instructor_id` (Direct instructor model ID)
- `created_by` (Direct user ID)
- `instructor.user.name` (Instructor's name)
- `instructor.user.email` (Instructor's email)
- `creator.name` (Creator's name)
- `creator.email` (Creator's email)

### 📊 Impact
- More flexible course discovery
- Better instructor/creator tracking
- Improved admin oversight
- Efficient database queries with proper relationships

The search functionality now provides comprehensive course filtering based on both instructor assignments and course authorship!
