# Course Schedule Participants Auto-Sync Implementation

## Overview
Automatically add and sync participants (enrolled students and instructor) to course schedules when:
1. A new course schedule is created
2. An existing course schedule is updated
3. A student enrolls in a course (future enhancement)

## Implementation

### 1. CourseScheduleController Updates

#### A. `addCourseStudentsAsParticipants()` Method
Called when a **new** schedule is created.

**What it does:**
- ✅ Adds creator as "organizer" (confirmed)
- ✅ Adds all enrolled students as "student" (invited)
- ✅ Adds course instructor as "instructor" (confirmed)

```php
private function addCourseStudentsAsParticipants(Course $course, Schedule $schedule): void
{
    // 1. Add creator as organizer
    if (Auth::id()) {
        $schedule->participants()->create([
            'user_id' => Auth::id(),
            'role_in_schedule' => 'organizer',
            'participation_status' => 'confirmed',
        ]);
    }

    // 2. Add all enrolled students
    $enrolledStudents = $course->students()->get();
    foreach ($enrolledStudents as $student) {
        if ($student->user_id && $student->user_id !== Auth::id()) {
            $schedule->participants()->create([
                'user_id' => $student->user_id,
                'role_in_schedule' => 'student',
                'participation_status' => 'invited',
            ]);
        }
    }

    // 3. Add instructor
    if ($course->instructor_id && $course->instructor->user_id !== Auth::id()) {
        $schedule->participants()->updateOrCreate(
            ['user_id' => $course->instructor->user_id],
            [
                'role_in_schedule' => 'instructor',
                'participation_status' => 'confirmed',
            ]
        );
    }
}
```

#### B. `syncCourseParticipants()` Method (NEW)
Called when a schedule is **updated**.

**What it does:**
- ✅ Finds newly enrolled students who aren't participants yet
- ✅ Adds new students as participants
- ✅ Updates/adds instructor if changed
- ❌ Does NOT remove students who unenrolled (preserves history)

```php
private function syncCourseParticipants(Course $course, Schedule $schedule): void
{
    // 1. Get currently enrolled students
    $enrolledStudents = $course->students()->pluck('user_id')->filter()->toArray();
    
    // 2. Get current participants
    $currentParticipants = $schedule->participants()->pluck('user_id')->toArray();

    // 3. Find new students (enrolled but not yet participants)
    $newStudents = array_diff($enrolledStudents, $currentParticipants);
    
    // 4. Add new students
    foreach ($newStudents as $userId) {
        if ($userId !== Auth::id()) {
            $schedule->participants()->create([
                'user_id' => $userId,
                'role_in_schedule' => 'student',
                'participation_status' => 'invited',
            ]);
        }
    }

    // 5. Update or add instructor
    if ($course->instructor_id && $course->instructor->user_id) {
        $schedule->participants()->updateOrCreate(
            ['user_id' => $course->instructor->user_id],
            [
                'role_in_schedule' => 'instructor',
                'participation_status' => 'confirmed',
            ]
        );
    }
}
```

#### C. Integration Points

**On Create (`store` method):**
```php
// Create schedule...
$schedule = Schedule::create([...]);

// Create schedule_course pivot...
ScheduleCourse::create([...]);

// ✅ Add all participants
$this->addCourseStudentsAsParticipants($course, $schedule);
```

**On Update (`update` method):**
```php
// Update schedule...
$schedule->update([...]);

// Update schedule_course pivot...
$scheduleCourse->update([...]);

// ✅ Sync participants (add new, update instructor)
$this->syncCourseParticipants($course, $schedule);
```

## Participant Roles

| Role | Status | When Added | Purpose |
|------|--------|------------|---------|
| **organizer** | confirmed | Schedule creation | Creator of the schedule |
| **instructor** | confirmed | Schedule creation/update | Course instructor |
| **student** | invited | Schedule creation/update | Enrolled students |

## Database Structure

### schedule_participants Table
```
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| schedule_id | bigint | FK to schedules |
| user_id | bigint | FK to users |
| role_in_schedule | varchar | organizer, instructor, student, etc. |
| participation_status | varchar | invited, confirmed, declined, tentative |
| response_datetime | timestamp | When user responded |
| attended_at | timestamp | When user attended |
| notes | text | Additional notes |
```

## Workflow Examples

### Example 1: Create New Course Schedule

**Before:**
- Course has 5 enrolled students
- Course has 1 instructor
- No schedules exist

**Action:**
Instructor creates a new course schedule for Friday at 2 PM

**Result:**
Schedule participants table gets 7 entries:
1. Instructor (creator) - role: organizer, status: confirmed
2. Instructor - role: instructor, status: confirmed
3. Student 1 - role: student, status: invited
4. Student 2 - role: student, status: invited
5. Student 3 - role: student, status: invited
6. Student 4 - role: student, status: invited
7. Student 5 - role: student, status: invited

### Example 2: Update Existing Schedule

**Before:**
- Schedule exists with 5 student participants
- 2 new students enroll in the course

**Action:**
Instructor updates the schedule (changes time from 2 PM to 3 PM)

**Result:**
- ✅ 2 new students are added as participants
- ✅ Total participants: 7 students + 1 instructor + 1 organizer
- ❌ No participants removed (even if students unenrolled)

### Example 3: Student Enrolls in Course

**Current Behavior:**
- Student enrolls in course
- Student does NOT automatically get added to existing schedules
- ❌ Requires manual sync or schedule update

**Future Enhancement Needed:**
Add event listener to automatically sync when student enrolls.

## Future Enhancements

### 1. Auto-Sync on Student Enrollment

**File:** `app/Services/StudentCourseEnrollmentService.php`

```php
public function enrollStudent(Student $student, Course $course): void
{
    // ... existing enrollment logic ...
    
    // ✅ NEW: Add student to all future course schedules
    $this->addStudentToFutureSchedules($student, $course);
}

private function addStudentToFutureSchedules(Student $student, Course $course): void
{
    $futureSchedules = $course->schedules()
        ->where('from_datetime', '>=', now())
        ->get();
    
    foreach ($futureSchedules as $schedule) {
        $schedule->participants()->updateOrCreate(
            ['user_id' => $student->user_id],
            [
                'role_in_schedule' => 'student',
                'participation_status' => 'invited',
            ]
        );
    }
}
```

### 2. Bulk Sync Command for Existing Schedules

**File:** `app/Console/Commands/SyncScheduleParticipants.php`

```php
php artisan schedule:sync-participants

// Syncs all course schedules with their enrolled students
```

### 3. Remove Unenrolled Students Option

Add configuration to optionally remove students who unenrolled:

```php
// In config/schedule.php
'remove_unenrolled_participants' => false, // Set to true to remove
```

## Testing

### Manual Test: Create Schedule

1. **Enroll students in a course**
   - Navigate to Course Management
   - Add 2-3 students to a course

2. **Create a schedule**
   - Click "Create Schedule" button
   - Fill in date/time
   - Submit

3. **Verify participants**
   ```sql
   SELECT sp.*, u.name, sp.role_in_schedule, sp.participation_status
   FROM schedule_participants sp
   JOIN users u ON sp.user_id = u.id
   WHERE sp.schedule_id = [schedule_id]
   ORDER BY sp.role_in_schedule;
   ```

   **Expected:**
   - Organizer (creator)
   - Instructor
   - All enrolled students

### Manual Test: Update Schedule

1. **Enroll a new student** in the course
2. **Update the schedule** (change time or title)
3. **Verify new student added** to participants

### Manual Test: User Schedule View

1. **Login as a student**
2. **Navigate to** `/schedule`
3. **Verify** student sees the course schedule in their calendar

## Files Modified

- ✅ `app/Http/Controllers/CourseScheduleController.php`
  - Added `syncCourseParticipants()` method
  - Updated `update()` method to call sync
  - Enhanced `addCourseStudentsAsParticipants()` with better error handling

## Database Queries

### Check Participants for a Schedule
```sql
SELECT 
    s.id as schedule_id,
    s.title,
    u.name as participant_name,
    sp.role_in_schedule,
    sp.participation_status
FROM schedules s
JOIN schedule_participants sp ON s.id = sp.schedule_id
JOIN users u ON sp.user_id = u.id
WHERE s.id = [schedule_id];
```

### Check All Course Schedules
```sql
SELECT 
    s.id,
    s.title,
    s.from_datetime,
    COUNT(sp.id) as participant_count
FROM schedules s
LEFT JOIN schedule_participants sp ON s.id = sp.schedule_id
WHERE s.schedulable_type = 'App\\Models\\Course'
  AND s.schedulable_id = [course_id]
GROUP BY s.id, s.title, s.from_datetime
ORDER BY s.from_datetime;
```

### Find Schedules Missing Participants
```sql
SELECT 
    s.id,
    s.title,
    s.from_datetime,
    COUNT(sp.id) as participant_count
FROM schedules s
LEFT JOIN schedule_participants sp ON s.id = sp.schedule_id
WHERE s.schedulable_type = 'App\\Models\\Course'
GROUP BY s.id, s.title, s.from_datetime
HAVING COUNT(sp.id) = 0;
```

## Troubleshooting

### Issue: No Participants Added

**Check:**
1. Course has enrolled students?
   ```sql
   SELECT * FROM course_enrollments WHERE course_id = [course_id];
   ```
2. Students have user_id?
   ```sql
   SELECT * FROM students WHERE user_id IS NULL;
   ```
3. Transaction rolled back due to error?
   - Check Laravel logs

### Issue: Duplicate Participants

**Cause:** `create()` called multiple times instead of `updateOrCreate()`

**Fix:** Use `updateOrCreate()` for instructor and organizer:
```php
$schedule->participants()->updateOrCreate(
    ['user_id' => $userId],
    ['role_in_schedule' => 'instructor', ...]
);
```

### Issue: Students Not Seeing Schedule

**Check:**
1. Student is a participant?
2. User schedule query filters by user_id correctly?
3. Schedule is in the future (not filtered by `upcoming()` scope)?

## Related Documentation

- `SCHEDULE_IMPLEMENTATION_GUIDE.md` - Complete scheduling system
- `USER_SCHEDULE_COURSE_DETAILS_FIX.md` - Course details loading
- `SCHEDULING_SYSTEM_DOCUMENTATION.md` - Database schema
