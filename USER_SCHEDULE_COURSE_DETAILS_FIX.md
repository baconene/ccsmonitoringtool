# User Schedule: Course Details Fix

## Issue
The User Schedule page was not capturing `schedule_course` data from the `schedule_courses` pivot table.

## Root Cause
The schedule query in `routes/web.php` was loading the `schedulable` relationship (the Course model itself) but **not loading the pivot table relationships** that contain schedule-specific metadata:
- `courseDetails` → `schedule_courses` table (session_number, topics_covered, required_materials)
- `activityDetails` → `schedule_activities` table  
- `adhocDetails` → `schedule_adhocs` table

## Solution

### 1. Updated Schedule Query (routes/web.php)

**Before:**
```php
$schedules = \App\Models\Schedule::with([
    'scheduleType',
    'creator:id,name,email',
    'participants.user:id,name,email',
    'schedulable', // Only loads the Course/Activity model
])
```

**After:**
```php
$schedules = \App\Models\Schedule::with([
    'scheduleType',
    'creator:id,name,email',
    'participants.user:id,name,email',
    'schedulable',
    'courseDetails',    // ✅ Load schedule_courses pivot data
    'activityDetails',  // ✅ Load schedule_activities pivot data
    'adhocDetails',     // ✅ Load schedule_adhocs pivot data
])
```

### 2. Added Details to Response Map

```php
->map(function ($schedule) use ($user) {
    return [
        // ... existing fields ...
        
        // ✅ NEW: Include course-specific data
        'course_details' => $schedule->courseDetails ? [
            'session_number' => $schedule->courseDetails->session_number,
            'topics_covered' => $schedule->courseDetails->topics_covered,
            'required_materials' => $schedule->courseDetails->required_materials,
            'homework_assigned' => $schedule->courseDetails->homework_assigned,
        ] : null,
        
        // ✅ NEW: Include activity-specific data
        'activity_details' => $schedule->activityDetails ? [
            'submission_deadline' => $schedule->activityDetails->submission_deadline,
            'points' => $schedule->activityDetails->points,
            'instructions' => $schedule->activityDetails->instructions,
        ] : null,
    ];
})
```

### 3. Updated TypeScript Interface (UserSchedule.vue)

```typescript
interface Schedule {
  // ... existing fields ...
  
  // ✅ NEW: Course-specific schedule data
  course_details?: {
    session_number: number | null;
    topics_covered: string | null;
    required_materials: string | null;
    homework_assigned: string | null;
  } | null;
  
  // ✅ NEW: Activity-specific schedule data
  activity_details?: {
    submission_deadline: string | null;
    points: number | null;
    instructions: string | null;
  } | null;
}
```

## Database Structure Recap

### schedule_courses Table
| Column | Type | Description |
|--------|------|-------------|
| schedule_id | FK | Reference to schedules table |
| course_id | FK | Reference to courses table |
| session_number | int | Which session (e.g., "Session 3") |
| topics_covered | text | Topics for this session |
| required_materials | text | Materials students need |

### schedule_activities Table
| Column | Type | Description |
|--------|------|-------------|
| schedule_id | FK | Reference to schedules table |
| activity_id | FK | Reference to activities table |
| submission_deadline | datetime | When submissions are due |
| points | int | Points possible |
| instructions | text | Special instructions |

## Model Relationships

### Schedule Model (app/Models/Schedule.php)

```php
// Get course-specific details if this is a course schedule
public function courseDetails()
{
    return $this->hasOne(ScheduleCourse::class);
}

// Get activity-specific details if this is an activity schedule
public function activityDetails()
{
    return $this->hasOne(ScheduleActivity::class);
}
```

## What's Now Available in Frontend

The UserSchedule.vue component can now access:

```vue
<template>
  <div v-for="schedule in schedules" :key="schedule.id">
    <!-- Basic schedule info -->
    <h3>{{ schedule.title }}</h3>
    
    <!-- Course-specific info (if this is a course schedule) -->
    <div v-if="schedule.course_details">
      <p>Session #{{ schedule.course_details.session_number }}</p>
      <p>Topics: {{ schedule.course_details.topics_covered }}</p>
      <p>Materials: {{ schedule.course_details.required_materials }}</p>
    </div>
    
    <!-- Activity-specific info (if this is an activity schedule) -->
    <div v-if="schedule.activity_details">
      <p>Due: {{ schedule.activity_details.submission_deadline }}</p>
      <p>Points: {{ schedule.activity_details.points }}</p>
      <p>Instructions: {{ schedule.activity_details.instructions }}</p>
    </div>
  </div>
</template>
```

## Testing

1. **Create a course schedule** with session number and topics
2. **Navigate to User Schedule page** (`/schedule`)
3. **Check browser console** for schedule data:
   ```javascript
   console.log(schedules[0].course_details)
   // Should show: { session_number: 1, topics_covered: "...", ... }
   ```
4. **Verify in Vue DevTools** that `initialSchedules` prop contains `course_details`

## Files Modified
- ✅ `routes/web.php` - Added eager loading of detail relationships
- ✅ `resources/js/Pages/SchedulingManagement/UserSchedule.vue` - Updated TypeScript interface

## Related Documentation
- `SCHEDULE_IMPLEMENTATION_GUIDE.md` - Complete scheduling system overview
- `COURSE_SCHEDULE_MODAL_FIXES.md` - Modal and display fixes
- `SCHEDULING_SYSTEM_DOCUMENTATION.md` - Database schema and relationships
