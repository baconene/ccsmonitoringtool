# Course Schedule Management Feature

## Overview

Added a complete course schedule management system that allows instructors/admins to create, view, and manage course schedules directly from the course banner.

## Features Implemented

### 1. **Create Course Schedule Button**
- Added a "Create Schedule" button (calendar plus icon) to the CourseBanner component
- Opens a modal form for entering schedule details
- Button positioned next to other action buttons (Manage Students, Edit, Delete)

### 2. **Course Schedule Creation Modal**
- **Required Fields:**
  - Start Date & Time (`from_datetime`)
  - End Date & Time (`to_datetime`)
  
- **Optional Fields:**
  - Title (defaults to course title if empty)
  - Description (defaults to course description if empty)
  - Location (e.g., Room 101, Online, Zoom Link)
  - Session Number (e.g., Session 1, 2, 3)
  - Topics to be Covered
  - Required Materials

- **Recurring Schedule Options:**
  - Checkbox to mark schedule as recurring
  - Recurrence Pattern dropdown (Daily, Weekly, Monthly, custom patterns)
  - Supports iCalendar FREQ rules (FREQ=DAILY, FREQ=WEEKLY, etc.)

### 3. **Schedule Display in Banner**
- Shows upcoming schedules (up to 3) in the course banner
- Each schedule shows:
  - Title (or course name)
  - Date and time range
  - Location (if specified)
  - Session number (if specified)
  - Frequency badge for recurring schedules (Daily, Weekly, Monthly)
  - Visual indicators with icons (Clock, Repeat)

- Shows "+X more schedule(s)" if more than 3 exist

### 4. **Backend Implementation**

#### **Controller: `CourseScheduleController`**
Location: `app/Http/Controllers/CourseScheduleController.php`

**Methods:**
- `index()` - Get all schedules for a course
- `store()` - Create a new schedule
- `update()` - Update existing schedule
- `destroy()` - Delete schedule
- `addCourseStudentsAsParticipants()` - Auto-add participants

**Key Features:**
- Creates both `Schedule` and `ScheduleCourse` records
- Uses schedule_type = 'course' (ID: 2)
- Links schedule to course via polymorphic relationship
- Automatically adds participants:
  - **Creator** (logged-in user) as "organizer" with "confirmed" status
  - **Enrolled students** as "student" with "invited" status
  - **Course instructor** as "instructor" with "confirmed" status
- Prevents duplicate participants
- Transaction-based for data integrity
- Comprehensive error logging

#### **Routes Added**
Location: `routes/web.php`

```php
Route::middleware(['auth'])->prefix('courses')->name('courses.')->group(function () {
    Route::get('/{course}/schedules', [CourseScheduleController::class, 'index']);
    Route::post('/{course}/schedules', [CourseScheduleController::class, 'store']);
    Route::put('/{course}/schedules/{schedule}', [CourseScheduleController::class, 'update']);
    Route::delete('/{course}/schedules/{schedule}', [CourseScheduleController::class, 'destroy']);
});
```

### 5. **Database Structure**

#### **schedules Table**
```
- id
- schedule_type_id (FK to schedule_types, set to 2 for 'course')
- title
- description
- location
- from_datetime (required)
- to_datetime (required)
- is_all_day (boolean)
- is_recurring (boolean)
- recurrence_rule (iCalendar format)
- status ('scheduled' by default)
- created_by (FK to users)
- schedulable_type ('App\Models\Course')
- schedulable_id (course ID)
- metadata (JSON)
- created_at, updated_at, deleted_at
```

#### **schedule_courses Table**
```
- id
- schedule_id (FK to schedules, unique)
- course_id (FK to courses)
- session_number
- topics_covered
- required_materials
- created_at, updated_at
```

#### **schedule_participants Table**
```
- id
- schedule_id (FK to schedules)
- user_id (FK to users)
- role_in_schedule (organizer, instructor, student)
- participation_status (confirmed, invited, declined)
- response_datetime
- attended_at
- notes
- created_at, updated_at
```

## Files Modified

### Backend
1. **`app/Http/Controllers/CourseScheduleController.php`** (NEW)
   - Complete CRUD operations for course schedules
   - Automatic participant management
   - Transaction-based operations

2. **`routes/web.php`**
   - Added 4 new routes for schedule management

3. **`app/Services/CourseService.php`**
   - Updated `getCourses()` to eager-load schedules
   - Loads upcoming schedules (from_datetime >= now)
   - Limits to 5 schedules per course
   - Includes `courseDetails` relationship

### Frontend
1. **`resources/js/course/CourseBanner.vue`** (MODIFIED)
   - Added "Create Schedule" button
   - Added schedule display section
   - Added helper functions:
     - `getFrequencyText()` - Format recurrence pattern
     - `formatScheduleTime()` - Format date/time display
     - `handleScheduleModalClose()` - Refresh after create
   - Imported new icons: `CalendarPlus`, `Clock`, `Repeat`

2. **`resources/js/components/CourseScheduleModal.vue`** (NEW)
   - Complete modal form for schedule creation
   - Real-time validation
   - Conditional fields (recurrence section shows only if is_recurring is checked)
   - Error handling and display
   - Loading states
   - Auto-clears form on close

## Usage

### Creating a Schedule

1. Navigate to Course Management
2. Click the calendar plus icon (📅+) on any course banner
3. Fill in the schedule details:
   - **Required:** Start date/time, End date/time
   - **Optional:** Title, description, location, etc.
4. Check "Recurring Schedule" if needed and select frequency
5. Click "Create Schedule"

### What Happens

1. **Schedule Created:**
   - New record in `schedules` table
   - New record in `schedule_courses` table
   - Polymorphic link: `schedulable_type` = 'App\Models\Course'

2. **Participants Added Automatically:**
   - You (creator) → role: "organizer", status: "confirmed"
   - All enrolled students → role: "student", status: "invited"
   - Course instructor → role: "instructor", status: "confirmed"

3. **Schedule Displayed:**
   - Appears immediately in course banner
   - Shows up to 3 upcoming schedules
   - Sorted by date (earliest first)

### Viewing Schedules

Schedules are automatically displayed in the course banner under "Upcoming Schedules" section.

**Display includes:**
- 📅 Schedule title
- 🕒 Date and time
- 📍 Location (if specified)
- 🔄 Recurring badge (if recurring)
- #️⃣ Session number (if specified)

## Recurrence Rules

Supports iCalendar FREQ format:

- `FREQ=DAILY` - Every day
- `FREQ=WEEKLY` - Every week (same day)
- `FREQ=WEEKLY;BYDAY=MO,WE,FR` - Monday, Wednesday, Friday
- `FREQ=WEEKLY;BYDAY=TU,TH` - Tuesday, Thursday
- `FREQ=MONTHLY` - Monthly (same date)

## API Endpoints

### Get Course Schedules
```
GET /courses/{course}/schedules
```
Returns all schedules for a course with details.

### Create Schedule
```
POST /courses/{course}/schedules
Content-Type: application/json

{
  "title": "Advanced Math - Week 1",
  "description": "Introduction to Calculus",
  "from_datetime": "2025-10-20 09:00:00",
  "to_datetime": "2025-10-20 10:30:00",
  "location": "Room 205",
  "is_recurring": true,
  "recurrence_rule": "FREQ=WEEKLY;BYDAY=MO,WE",
  "session_number": 1,
  "topics_covered": "Limits, Derivatives basics",
  "required_materials": "Textbook Chapter 1, Calculator"
}
```

### Update Schedule
```
PUT /courses/{course}/schedules/{schedule}
Content-Type: application/json

{
  "from_datetime": "2025-10-20 10:00:00",
  "to_datetime": "2025-10-20 11:30:00"
}
```

### Delete Schedule
```
DELETE /courses/{course}/schedules/{schedule}
```

## Security

- All routes require authentication (`auth` middleware)
- Creator ID automatically set to `Auth::id()`
- Schedule-course validation (prevents accessing schedules from other courses)
- Transaction-based creates (rollback on error)
- Comprehensive error logging

## Future Enhancements

Potential improvements:

1. **Edit Schedule** - Modal to update existing schedules
2. **Delete Schedule** - Confirmation modal for deletion
3. **Bulk Schedule Creation** - Create multiple sessions at once
4. **Schedule Templates** - Save common patterns
5. **Calendar View** - Visual calendar displaying all course schedules
6. **Participant Management** - View and manage who's attending
7. **Notifications** - Email/push notifications for upcoming schedules
8. **Attendance Tracking** - Mark students as attended
9. **Schedule Conflicts** - Detect overlapping schedules
10. **Export to iCal** - Download schedules to calendar apps

## Testing

### Test Schedule Creation

```bash
php artisan tinker
```

```php
$course = \App\Models\Course::first();
$user = \App\Models\User::first();
Auth::login($user);

$controller = new \App\Http\Controllers\CourseScheduleController();

// Simulate request
$request = new \Illuminate\Http\Request();
$request->merge([
    'from_datetime' => now()->addDays(1),
    'to_datetime' => now()->addDays(1)->addHours(2),
    'is_recurring' => true,
    'recurrence_rule' => 'FREQ=WEEKLY',
    'session_number' => 1,
]);

$result = $controller->store($request, $course);
```

### Verify Participants

```php
$schedule = \App\Models\Schedule::latest()->first();
$participants = $schedule->participants()->with('user')->get();

foreach ($participants as $participant) {
    echo "{$participant->user->name} - {$participant->role_in_schedule} - {$participant->participation_status}\n";
}
```

### Verify Display

1. Navigate to `/course-management`
2. Check course banner for "Upcoming Schedules" section
3. Should see newly created schedule

## Troubleshooting

### Schedule Not Showing in Banner

**Check:**
- Schedule `from_datetime` is in the future
- `CourseService::getCourses()` includes schedules relationship
- Course prop passed to CourseBanner includes `schedules` array

### Participants Not Added

**Check:**
- Course has enrolled students (`course->students()`)
- Course has assigned instructor (`course->instructor_id`)
- User is authenticated when creating schedule
- Check `schedule_participants` table for records

### Modal Not Opening

**Check:**
- `CourseScheduleModal.vue` file exists in `resources/js/components/`
- Import path is correct in CourseBanner
- `showScheduleModal` ref is properly toggled

## Summary

✅ **Create Schedule Button** - Calendar plus icon in banner  
✅ **Modal Form** - Complete with validation  
✅ **Recurring Support** - Daily, Weekly, Monthly patterns  
✅ **Auto Participants** - Creator, students, instructor added automatically  
✅ **Schedule Display** - Shows in banner with details  
✅ **Backend API** - Full CRUD operations  
✅ **Database Relations** - Properly linked via polymorphic relationships  
✅ **Error Handling** - Comprehensive logging and validation  

The course schedule management system is now fully functional! 🎉
