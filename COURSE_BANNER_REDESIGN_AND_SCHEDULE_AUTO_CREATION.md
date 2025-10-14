# Course Banner Redesign & Schedule Auto-Creation

## Overview
Redesigned the course banner with responsive layout and added automatic schedule creation/update when courses are created or updated with end dates.

## 1. Course Banner Responsive Layout

### Changes Made to `CourseBanner.vue`

#### Desktop View (PC/Laptop):
```
┌─────────────────────────────────────────────────────────────┐
│ [Title] [Completion: X%]              [👥] [✏️] [🗑️]        │
│ [Grade Badges]                                              │
│ Description text                                             │
│ ─────────────────────────────────────────────────────────   │
│ 👤 Instructor: Name                                         │
│ ➕ Added by: Name                                           │
│ 📅 Duration: Oct 1, 2025 - Oct 17, 2025 [Active]           │
│ Modules: 0                                                  │
└─────────────────────────────────────────────────────────────┘
```

#### Mobile/Tablet View:
```
┌───────────────────────────────┐
│ [Title]                       │
│ [Completion: X%]              │
│ [Grade Badges]                │
│                               │
│ Description text              │
│ ────────────────────────────  │
│ 👤 Instructor: Name           │
│ ➕ Added by: Name             │
│ 📅 Duration: Oct 1 - Oct 17   │
│    [Active]                   │
│ Modules: 0                    │
│                               │
│ [👥] [✏️] [🗑️]                 │
└───────────────────────────────┘
```

### Key Layout Features:
1. **Title + Completion Side by Side** (Desktop)
   - Title and completion percentage appear on the same line on desktop
   - Stacks vertically on mobile for better readability

2. **Action Buttons Position**
   - **Desktop (lg+)**: Top right corner, aligned with title
   - **Mobile/Tablet**: Bottom of card for easier thumb access

3. **Information Flow**
   - Title → Completion → Grade Levels
   - Description
   - Instructor/Creator/Duration/Modules info

4. **Status Badge**
   - Dynamic color coding:
     - 🔵 Blue = Upcoming (before start date)
     - 🟢 Green = Active (current)
     - ⚫ Gray = Completed (past end date)

### Responsive Breakpoints:
- `lg` (1024px+): Desktop layout with horizontal arrangement
- `sm` (640px+): Tablet optimization
- Below 640px: Mobile-first vertical stacking

## 2. Automatic Schedule Creation

### Backend Implementation

#### Modified: `app/Http/Controllers/CourseController.php`

Added private method `createOrUpdateCourseSchedule()`:

```php
/**
 * Create or update schedule for a course.
 * Schedule from_datetime = end_date - 1 hour
 * Schedule to_datetime = end_date
 */
private function createOrUpdateCourseSchedule(Course $course, array $data)
{
    // Parse end_date and calculate from_datetime (1 hour before)
    $endDate = new \DateTime($data['end_date']);
    $fromDate = clone $endDate;
    $fromDate->modify('-1 hour');

    // Create or update Schedule record
    // Create or update ScheduleCourse pivot record
}
```

### Schedule Logic:

1. **When Course is Created** (`store` method):
   ```php
   if (!empty($validated['end_date'])) {
       $this->createOrUpdateCourseSchedule($course, $validated);
   }
   ```

2. **When Course is Updated** (`update` method):
   ```php
   if (!empty($validated['end_date'])) {
       $this->createOrUpdateCourseSchedule($course, $validated);
   }
   ```

### Schedule Calculation:
- **From DateTime**: `end_date - 1 hour`
- **To DateTime**: `end_date` (exact due date)
- **Example**:
  - Course End Date: `2025-10-17 00:00:00`
  - Schedule From: `2025-10-16 23:00:00`
  - Schedule To: `2025-10-17 00:00:00`

### Database Records Created:

#### `schedules` Table:
```php
[
    'title' => 'Course Title - Due Date',
    'description' => 'Course due date for Course Title',
    'from_datetime' => '2025-10-16 23:00:00',
    'to_datetime' => '2025-10-17 00:00:00',
    'status' => 'scheduled',
    'created_by' => auth()->id(),
    'schedulable_type' => 'App\Models\Course',
    'schedulable_id' => $course->id,
]
```

#### `schedule_courses` Table (Pivot):
```php
[
    'schedule_id' => $schedule->id,
    'course_id' => $course->id,
    'session_number' => 1,
]
```

### Update Logic:
- **If schedule exists**: Updates existing schedule record
- **If schedule doesn't exist**: Creates new schedule
- **If pivot exists**: Updates schedule_id reference
- **If pivot doesn't exist**: Creates new pivot record

## 3. Features

### Course Banner:
✅ Responsive design for all screen sizes
✅ Completion percentage beside title on desktop
✅ Action buttons top-right on desktop, bottom on mobile
✅ Clean information hierarchy
✅ Status badge with contextual colors
✅ Dark mode support maintained

### Schedule Auto-Creation:
✅ Automatically creates schedule when course has end_date
✅ Updates schedule when course dates change
✅ 1-hour window before due date for reminders
✅ Polymorphic relationship (schedulable)
✅ Maintains schedule_courses pivot table
✅ Error handling (won't break course creation if schedule fails)
✅ Logging for debugging

## 4. Testing Checklist

### Frontend (Course Banner):
- [ ] Test on desktop (>1024px) - buttons in top right
- [ ] Test on tablet (640-1023px) - responsive layout
- [ ] Test on mobile (<640px) - buttons at bottom
- [ ] Verify completion % beside title on desktop
- [ ] Verify completion % below title on mobile
- [ ] Check status badge colors (upcoming/active/completed)
- [ ] Test dark mode appearance

### Backend (Schedule Creation):
- [ ] Create course with end_date - verify schedule created
- [ ] Create course without end_date - no schedule created
- [ ] Update course end_date - verify schedule updated
- [ ] Check schedules table for correct times (end_date - 1h)
- [ ] Check schedule_courses pivot table
- [ ] Verify schedulable_type and schedulable_id set correctly
- [ ] Test error handling (schedule failure doesn't break course)

### Database Verification:
```sql
-- Check schedule created for course
SELECT s.*, sc.* 
FROM schedules s
JOIN schedule_courses sc ON s.id = sc.schedule_id
WHERE s.schedulable_type = 'App\\Models\\Course'
AND s.schedulable_id = [COURSE_ID];
```

## 5. Files Modified

1. **`resources/js/course/CourseBanner.vue`**
   - Completely restructured template for responsive layout
   - Title and completion on same line (desktop)
   - Action buttons repositioned
   - Maintained all existing functionality

2. **`app/Http/Controllers/CourseController.php`**
   - Added `createOrUpdateCourseSchedule()` method
   - Modified `store()` to call schedule creation
   - Modified `update()` to call schedule update
   - Added Schedule and ScheduleCourse model imports

## 6. Build Status

✅ Build successful (9.10s)
✅ No TypeScript errors
✅ No PHP errors
✅ Bundle size: 292.60 kB (CourseManagement)

## 7. Usage Examples

### Creating Course with Schedule:
```javascript
// Frontend form submission
{
  title: "Advanced Mathematics",
  description: "Learn calculus and algebra",
  start_date: "2025-01-15",
  end_date: "2025-05-30",  // Schedule will be created automatically
  grade_level_ids: [1, 2]
}
```

**Result**:
- Course created
- Schedule created with:
  - From: `2025-05-29 23:00:00`
  - To: `2025-05-30 00:00:00`
  - Title: "Advanced Mathematics - Due Date"

### Updating Course Dates:
```javascript
// Update existing course
{
  end_date: "2025-06-15"  // Schedule will be updated automatically
}
```

**Result**:
- Course updated
- Schedule updated with:
  - From: `2025-06-14 23:00:00`
  - To: `2025-06-15 00:00:00`

## 8. Technical Details

### Responsive CSS Classes Used:
- `flex-col lg:flex-row` - Vertical mobile, horizontal desktop
- `sm:flex-row` - Tablet horizontal layout
- `lg:order-last` - Reorder elements on desktop
- `flex-wrap` - Allow wrapping on smaller screens
- `gap-2`, `gap-3`, `gap-4` - Consistent spacing

### Schedule Calculation:
```php
$endDate = new \DateTime($data['end_date']);
$fromDate = clone $endDate;
$fromDate->modify('-1 hour');
```

### Polymorphic Relationship:
```php
'schedulable_type' => Course::class,  // App\Models\Course
'schedulable_id' => $course->id       // Actual course ID
```

## 9. Benefits

### UX Improvements:
- Better mobile/tablet experience
- Easier thumb reach for action buttons on mobile
- More information visible at once on desktop
- Cleaner visual hierarchy

### Automation Benefits:
- No manual schedule creation needed
- Consistent schedule format across all courses
- Automatic updates when course dates change
- Reduces human error
- Enables future reminder/notification features

## 10. Future Enhancements

Potential future improvements:
- [ ] Add schedule notifications 1 hour before due date
- [ ] Allow custom schedule offset (not just 1 hour)
- [ ] Create multiple schedules (milestones) for long courses
- [ ] Add schedule to student calendars
- [ ] Email reminders for upcoming due dates

## Notes

- Schedule creation is silent - doesn't interrupt course creation
- Failed schedule creation is logged but doesn't show error to user
- Schedule status is set to 'scheduled' by default
- Can be extended to create multiple schedules (start date, mid-term, end date, etc.)
