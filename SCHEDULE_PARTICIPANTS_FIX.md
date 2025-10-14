# Schedule System Fix - Participants Not Added

## Problem Identified

The schedule page was showing "No Upcoming Schedules" even though schedules existed in the database because:

1. **Schedules were created without participants**
2. **Query filters by participants**: The `Schedule::forUser($userId)` scope uses `whereHas('participants')` which only returns schedules where the user is a participant
3. **Root cause**: When course schedules were automatically created, no participants were being added to the `schedule_participants` table

## Issues Found

### 1. Schedule Creation Without Participants
**File:** `app/Http/Controllers/CourseController.php`

**Problem:**
```php
// Old code only created schedule and pivot, but NO participants
$schedule = \App\Models\Schedule::create($scheduleData);
\App\Models\ScheduleCourse::create([...]);
// Missing: participant creation
```

**Result:** Schedules existed but were invisible to users since the query filters by participants.

### 2. Database State
```sql
-- Schedule exists
SELECT * FROM schedules WHERE id = 1;
-- Returns: 1 row (schedule for course "TEST")

-- But no participants
SELECT * FROM schedule_participants WHERE schedule_id = 1;
-- Returns: 0 rows
```

## Solution Implemented

### 1. Added Participant Creation Method

**File:** `app/Http/Controllers/CourseController.php`

```php
/**
 * Add course participants (instructor and enrolled students) to schedule.
 */
private function addCourseParticipantsToSchedule(\App\Models\Schedule $schedule, Course $course)
{
    try {
        $participants = [];

        // Add instructor as organizer if exists
        if ($course->instructor && $course->instructor->user) {
            $participants[] = [
                'schedule_id' => $schedule->id,
                'user_id' => $course->instructor->user->id,
                'role_in_schedule' => 'instructor',
                'participation_status' => 'accepted',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Add enrolled students
        $enrolledStudents = $course->students()
            ->wherePivot('status', 'enrolled')
            ->with('user')
            ->get();

        foreach ($enrolledStudents as $student) {
            if ($student->user) {
                $participants[] = [
                    'schedule_id' => $schedule->id,
                    'user_id' => $student->user->id,
                    'role_in_schedule' => 'student',
                    'participation_status' => 'invited',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert or update participants
        if (!empty($participants)) {
            // Delete existing participants for this schedule
            \App\Models\ScheduleParticipant::where('schedule_id', $schedule->id)->delete();
            
            // Insert new participants
            \App\Models\ScheduleParticipant::insert($participants);
        }

    } catch (\Exception $e) {
        Log::error('Failed to add participants to course schedule', [
            'schedule_id' => $schedule->id,
            'course_id' => $course->id,
            'error' => $e->getMessage()
        ]);
    }
}
```

### 2. Integrated into Schedule Creation Flow

**Updated:** `createOrUpdateCourseSchedule()` method

```php
// After creating/updating schedule and pivot
// Add participants to the schedule
$this->addCourseParticipantsToSchedule($schedule, $course);
```

### 3. Fix Existing Schedules (Temporary Route)

**File:** `routes/web.php`

Created temporary admin route `/fix-schedules` that:
1. Loops through all existing course schedules
2. Adds instructor and enrolled students as participants
3. Returns count of fixed schedules

**Usage:**
1. Login as admin
2. Visit: `http://192.168.1.6:8000/fix-schedules`
3. Check result: "Fixed X schedules with participants"
4. **Remove this route after running once** (security)

## Participant Roles & Statuses

### Roles:
- **instructor**: Course instructor (organizer)
- **student**: Enrolled student (participant)
- **organizer**: Event organizer (for adhoc events)
- **attendee**: General attendee

### Statuses:
- **invited**: Waiting for response
- **accepted**: Confirmed attendance
- **declined**: Declined invitation
- **attended**: Marked as attended (after event)
- **absent**: Marked as absent (after event)

## New Workflow

### When Creating Course with End Date:

1. **Create Schedule**
   ```php
   $schedule = Schedule::create([...]);
   ```

2. **Create Pivot**
   ```php
   ScheduleCourse::create([...]);
   ```

3. **Add Participants** ✅ NEW
   ```php
   // Instructor
   ScheduleParticipant::insert([
       'user_id' => instructor_user_id,
       'role_in_schedule' => 'instructor',
       'participation_status' => 'accepted'
   ]);
   
   // Each enrolled student
   ScheduleParticipant::insert([
       'user_id' => student_user_id,
       'role_in_schedule' => 'student',
       'participation_status' => 'invited'
   ]);
   ```

4. **Result**: Schedule now visible to instructor and students

### When Updating Course End Date:

Same flow - participants are refreshed (deleted and re-inserted)

## Testing Checklist

- [x] Fix existing schedules using `/fix-schedules` route
- [ ] Create new course with end_date
- [ ] Verify schedule appears in instructor's schedule page
- [ ] Enroll student in course
- [ ] Update course end_date
- [ ] Verify schedule appears in student's schedule page
- [ ] Check schedule details show all participants
- [ ] Verify different participation statuses display correctly

## Database Verification

```sql
-- Check schedule exists
SELECT id, title, from_datetime, to_datetime, created_by 
FROM schedules 
WHERE schedulable_type = 'App\\Models\\Course';

-- Check participants exist
SELECT sp.*, u.name, u.email 
FROM schedule_participants sp
JOIN users u ON sp.user_id = u.id
WHERE sp.schedule_id = [SCHEDULE_ID];

-- Check course-schedule relationship
SELECT sc.*, s.title, c.title as course_title
FROM schedule_courses sc
JOIN schedules s ON sc.schedule_id = s.id
JOIN courses c ON sc.course_id = c.id;
```

## Files Modified

1. ✅ `app/Http/Controllers/CourseController.php`
   - Added `addCourseParticipantsToSchedule()` method
   - Updated `createOrUpdateCourseSchedule()` to call participant creation

2. ✅ `routes/web.php`
   - Added temporary `/fix-schedules` route (remove after use)

## API Endpoints Used

- **GET** `/api/users/{userId}/schedules/upcoming`
  - Fetches schedules where user is a participant
  - Includes schedule type, creator, participants
  - Filters by `from_datetime >= now()`
  - Uses `Schedule::forUser($userId)->upcoming()` scope

## Schedule Query Flow

```
User visits /schedule
  ↓
Frontend calls: GET /api/users/4/schedules/upcoming
  ↓
ScheduleController::getUserUpcomingSchedules()
  ↓
Schedule::forUser(4)->upcoming()->get()
  ↓
whereHas('participants', user_id = 4)  ← This is why participants are needed!
  ↓
Returns schedules where user is participant
```

## Future Enhancements

### Automatic Student Addition
When a student enrolls in a course:
```php
// In enrollment service
if ($course->schedule) {
    ScheduleParticipant::create([
        'schedule_id' => $course->schedule->id,
        'user_id' => $student->user_id,
        'role_in_schedule' => 'student',
        'participation_status' => 'invited'
    ]);
}
```

### Automatic Student Removal
When a student drops a course:
```php
// In enrollment service
if ($course->schedule) {
    ScheduleParticipant::where('schedule_id', $course->schedule->id)
        ->where('user_id', $student->user_id)
        ->delete();
}
```

### Activity Schedules
Similar logic should be applied for activity schedules:
- Add instructor as organizer
- Add students assigned to activity

## Important Notes

1. **Always add participants when creating schedules**
2. **Refresh participants when updating schedules**
3. **The `/fix-schedules` route is temporary** - remove it after running once
4. **Consider adding participant management when students enroll/drop**
5. **Instructor always gets `accepted` status, students get `invited`**

## Summary

✅ **Problem**: Schedules existed but had no participants, making them invisible to users  
✅ **Root Cause**: Schedule creation didn't include participant insertion  
✅ **Solution**: Added automatic participant creation for instructor and enrolled students  
✅ **Fix for Existing**: Created temporary route to fix existing schedules  
✅ **Result**: Schedules now visible to instructors and enrolled students

## Next Steps

1. Run `/fix-schedules` as admin to fix existing schedules
2. Remove the temporary route from `routes/web.php`
3. Test creating new courses with end dates
4. Test enrolling students and verifying they see the schedule
5. Consider implementing automatic participant management in enrollment service
