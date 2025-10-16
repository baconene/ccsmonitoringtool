# Course Schedule: Single Schedule Edit Mode Implementation

## Overview
Updated the course schedule system to:
1. ✅ Allow editing existing schedules instead of creating duplicates
2. ✅ Prevent multiple schedules per course (one schedule only)
3. ✅ Change icon from CalendarPlus to Edit3 when schedule exists
4. ✅ Pass existing schedule data to modal for editing

## Changes Made

### 1. CourseBanner.vue Updates

#### A. Dynamic Button Icon
**Before:**
```vue
<button @click="showScheduleModal = true" title="Create Schedule">
  <CalendarPlus class="..." />
</button>
```

**After:**
```vue
<button 
  @click="handleScheduleButtonClick" 
  :title="hasSchedule ? 'Edit Schedule' : 'Create Schedule'"
>
  <Edit3 v-if="hasSchedule" class="..." /> <!-- Blue on hover -->
  <CalendarPlus v-else class="..." />      <!-- Green on hover -->
</button>
```

#### B. New State and Computed Properties
```typescript
const existingSchedule = ref<Schedule | null>(null);
const hasSchedule = computed(() => schedules.value.length > 0);
```

#### C. Handle Schedule Button Click
```typescript
const handleScheduleButtonClick = () => {
  if (hasSchedule.value) {
    // Edit mode - pass first schedule
    existingSchedule.value = schedules.value[0];
  } else {
    // Create mode
    existingSchedule.value = null;
  }
  showScheduleModal.value = true;
};
```

#### D. Pass Existing Schedule to Modal
```vue
<CourseScheduleModal
  :is-open="showScheduleModal"
  :course-id="reactiveCourse.id"
  :course-name="reactiveCourse.name"
  :existing-schedule="existingSchedule"  <!-- NEW -->
  @close="handleScheduleModalClose"
/>
```

### 2. CourseScheduleModal.vue Updates

#### A. New Props Interface
```typescript
interface Schedule {
  id: number;
  title: string;
  description?: string;
  from_datetime: string;
  to_datetime: string;
  location?: string;
  is_recurring: boolean;
  recurrence_rule?: string;
  session_number?: number;
  topics_covered?: string;
  required_materials?: string;
}

interface Props {
  isOpen: boolean;
  courseId: number;
  courseName: string;
  existingSchedule?: Schedule | null;  // NEW
}
```

#### B. Edit Mode Detection
```typescript
const isEditMode = computed(() => !!props.existingSchedule);
```

#### C. Form Population on Edit
```typescript
watch(() => props.existingSchedule, (schedule) => {
  if (schedule) {
    // Edit mode - populate form with existing data
    form.value = {
      title: schedule.title || '',
      description: schedule.description || '',
      from_datetime: formatDatetimeLocal(schedule.from_datetime),
      to_datetime: formatDatetimeLocal(schedule.to_datetime),
      location: schedule.location || '',
      is_recurring: schedule.is_recurring || false,
      recurrence_rule: schedule.recurrence_rule || '',
      session_number: schedule.session_number || null,
      topics_covered: schedule.topics_covered || '',
      required_materials: schedule.required_materials || '',
    };
  } else {
    // Create mode - reset form
    resetForm();
  }
}, { immediate: true });
```

#### D. DateTime Formatting Helper
```typescript
const formatDatetimeLocal = (datetime: string): string => {
  const date = new Date(datetime);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};
```

**Why:** `input[type="datetime-local"]` requires format: `YYYY-MM-DDTHH:MM`

#### E. Dynamic Submit Handler
```typescript
const handleSubmit = () => {
  // Determine URL and method
  const url = isEditMode.value
    ? `/courses/${props.courseId}/schedules/${props.existingSchedule!.id}`
    : `/courses/${props.courseId}/schedules`;
  
  const method = isEditMode.value ? 'put' : 'post';

  router[method](url, data, {
    onSuccess: () => {
      console.log(`✅ Schedule ${isEditMode.value ? 'updated' : 'created'}`);
      close();
      router.reload({ only: ['courses'] });
    },
    // ... error handling
  });
};
```

#### F. Dynamic UI Text
**Modal Header:**
```vue
<h2>{{ isEditMode ? 'Edit Course Schedule' : 'Create Course Schedule' }}</h2>
```

**Submit Button:**
```vue
{{ isSubmitting 
  ? (isEditMode ? 'Updating...' : 'Creating...') 
  : (isEditMode ? 'Update Schedule' : 'Create Schedule') 
}}
```

## User Flow

### Create Schedule (No Schedule Exists)
1. User sees **CalendarPlus icon** (green on hover)
2. Clicks button
3. Modal opens: "Create Course Schedule"
4. Fill in form
5. Click "Create Schedule"
6. POST to `/courses/{courseId}/schedules`
7. Schedule created, modal closes
8. Icon changes to **Edit3** (blue on hover)

### Edit Schedule (Schedule Exists)
1. User sees **Edit3 icon** (blue on hover)
2. Clicks button
3. Modal opens: "Edit Course Schedule"
4. Form pre-filled with existing data
5. Modify fields
6. Click "Update Schedule"
7. PUT to `/courses/{courseId}/schedules/{scheduleId}`
8. Schedule updated, modal closes
9. Schedule display refreshes

## Backend Routes

### Create Schedule (POST)
```php
Route::post('/{course}/schedules', [CourseScheduleController::class, 'store']);
```

### Update Schedule (PUT)
```php
Route::put('/{course}/schedules/{schedule}', [CourseScheduleController::class, 'update']);
```

**Note:** Update method already exists and includes `syncCourseParticipants()` to add new enrolled students.

## Single Schedule Enforcement

### Frontend Approach
- Button icon changes to Edit3 when `schedules.length > 0`
- Only first schedule is editable: `schedules.value[0]`
- No "Create New" option when schedule exists

### Backend Approach (Optional Enhancement)
Add validation in `store()` method:

```php
public function store(Request $request, Course $course)
{
    // Check if schedule already exists
    if ($course->schedules()->count() > 0) {
        return redirect()->back()
            ->with('error', 'A schedule already exists for this course. Please edit the existing schedule instead.');
    }
    
    // ... rest of creation logic
}
```

## Testing

### Test 1: Create First Schedule
1. **Go to** Course Management
2. **Find course** with NO schedule
3. **Verify** CalendarPlus icon visible
4. **Click** calendar icon
5. **Fill** schedule form
6. **Submit**
7. **Verify** schedule created and displayed

### Test 2: Edit Existing Schedule
1. **Go to** Course Management
2. **Find course** with existing schedule
3. **Verify** Edit3 icon visible (not CalendarPlus)
4. **Click** edit icon
5. **Verify** form pre-filled with existing data
6. **Modify** some fields (e.g., change time)
7. **Submit**
8. **Verify** schedule updated in display

### Test 3: Schedule Display
1. **Check** "Upcoming Schedules" section shows updated data
2. **Verify** time, location, session number reflect changes

### Test 4: Participants Sync
1. **Enroll new student** in course
2. **Edit schedule** (just change title)
3. **Check database**: New student should be added as participant
```sql
SELECT u.name, sp.role_in_schedule 
FROM schedule_participants sp
JOIN users u ON sp.user_id = u.id
WHERE sp.schedule_id = [schedule_id];
```

## UI Visual Changes

### Before This Update
```
[📅+] Create Schedule  (always green icon)
```

### After This Update - No Schedule
```
[📅+] Create Schedule  (green icon, hover: green)
```

### After This Update - Has Schedule
```
[✏️] Edit Schedule  (gray icon, hover: blue)
```

## Benefits

1. ✅ **No Duplicate Schedules**: Only one schedule per course
2. ✅ **Clear Intent**: Icon indicates whether creating or editing
3. ✅ **Better UX**: Edit existing rather than create new
4. ✅ **Data Preservation**: Existing schedule data, participants preserved
5. ✅ **Consistent Behavior**: Same modal for both create and edit

## Future Enhancements

### 1. Multiple Schedules Support
If you want to support multiple schedules in the future:

```typescript
// Add dropdown to select which schedule to edit
<select v-model="selectedScheduleId">
  <option v-for="schedule in schedules" :value="schedule.id">
    {{ formatScheduleTime(schedule) }}
  </option>
</select>
```

### 2. Delete Schedule Option
Add delete button in edit mode:

```vue
<button 
  v-if="isEditMode"
  @click="handleDelete"
  class="bg-red-600"
>
  Delete Schedule
</button>
```

### 3. Schedule History
Track all changes to schedules for audit purposes.

### 4. Duplicate Schedule Option
Allow creating a new schedule based on existing one:

```typescript
const duplicateSchedule = () => {
  const newSchedule = { ...existingSchedule.value };
  newSchedule.id = null; // Create as new
  newSchedule.title += ' (Copy)';
  // Submit as new schedule
};
```

## Files Modified

- ✅ `resources/js/course/CourseBanner.vue`
  - Added dynamic icon logic (CalendarPlus vs Edit3)
  - Added `existingSchedule` state
  - Added `hasSchedule` computed
  - Added `handleScheduleButtonClick()` function
  - Updated modal props to pass `existingSchedule`

- ✅ `resources/js/components/CourseScheduleModal.vue`
  - Added `existingSchedule` prop
  - Added `isEditMode` computed
  - Added form population watcher
  - Added `formatDatetimeLocal()` helper
  - Added `resetForm()` helper
  - Updated `handleSubmit()` for PUT/POST
  - Updated modal header text dynamically
  - Updated button text dynamically

## Related Documentation

- `COURSE_SCHEDULE_PARTICIPANTS_SYNC.md` - Participant auto-sync
- `COURSE_SCHEDULE_MODAL_FIXES.md` - Modal flow fixes
- `SCHEDULE_IMPLEMENTATION_GUIDE.md` - Complete system overview
