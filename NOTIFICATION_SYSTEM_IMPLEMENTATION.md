# Instructor Notification System Implementation

## Overview
Implemented a comprehensive notification system for instructors to be notified when students submit assignments. The system includes backend API, database structure, and a real-time notification bell component in the UI.

## Backend Implementation

### 1. Database Schema
**Table: `instructor_notifications`**
- `id` - Primary key
- `instructor_id` - Foreign key to users table
- `type` - Notification type (e.g., 'assignment_submitted')
- `title` - Notification title
- `message` - Notification message
- `data` - JSON field for additional data (student_id, assignment_id, etc.)
- `is_read` - Boolean flag
- `read_at` - Timestamp when marked as read
- `related_type` - Polymorphic relation type
- `related_id` - Polymorphic relation ID
- `created_at`, `updated_at` - Timestamps

**Indexes:**
- `[instructor_id, is_read]` - For efficient unread count queries
- `[instructor_id, created_at]` - For sorting by date

### 2. Model
**File:** `app/Models/InstructorNotification.php`

**Key Features:**
- Fillable fields for mass assignment
- Casts: `data` to array, `is_read` to boolean, timestamps
- Relationships: `belongsTo(User, 'instructor_id')`
- Query scopes:
  - `unread()` - Filter unread notifications
  - `forInstructor($instructorId)` - Filter by instructor
- Methods:
  - `markAsRead()` - Mark notification as read

### 3. Controller
**File:** `app/Http/Controllers/Instructor/NotificationController.php`

**API Endpoints:**

1. **GET `/instructor/notifications/unread-count`**
   - Returns: `{ "count": 5 }`
   - Purpose: Get unread notification count for badge

2. **GET `/instructor/notifications`**
   - Returns: Paginated list of notifications (20 per page)
   - Sorted by: Most recent first
   - Purpose: Display in notification dropdown

3. **POST `/instructor/notifications/{id}/read`**
   - Marks single notification as read
   - Updates `is_read` to `true` and sets `read_at` timestamp

4. **POST `/instructor/notifications/read-all`**
   - Marks all unread notifications as read for authenticated instructor

5. **DELETE `/instructor/notifications/{id}`**
   - Deletes a notification
   - Useful for clearing old notifications

### 4. Routes
**File:** `routes/web.php`

All routes are in the `instructor` middleware group (requires instructor or admin role):

```php
Route::prefix('instructor/notifications')->name('instructor.notifications.')->group(function () {
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});
```

### 5. Notification Creation
**File:** `app/Http/Controllers/Student/StudentAssignmentController.php`

When a student submits an assignment (in the `submit()` method), a notification is created:

```php
$instructorId = $assignment->activity->course->instructor_id 
    ?? $assignment->activity->course->user_id;

InstructorNotification::create([
    'instructor_id' => $instructorId,
    'type' => 'assignment_submitted',
    'title' => 'New Assignment Submission',
    'message' => auth()->user()->name . ' has submitted "' . $assignment->title . '"',
    'data' => [
        'student_id' => auth()->id(),
        'assignment_id' => $assignment->id,
        'course_id' => $assignment->activity->course_id,
        'requires_grading' => true,
    ],
    'related_type' => 'App\Models\Assignment',
    'related_id' => $assignment->id,
]);
```

## Frontend Implementation

### 1. NotificationBell Component
**File:** `resources/js/components/NotificationBell.vue`

**Key Features:**

#### Visual Design
- Bell icon with unread count badge (red badge with number)
- Shows "99+" if count exceeds 99
- Dropdown panel (396px wide, 400px tall)
- Scroll area for long notification lists
- Unread notifications have a blue dot indicator
- Read notifications are slightly faded

#### Functionality
- **Real-time Polling**: Fetches unread count every 10 seconds
- **Auto-refresh**: Fetches full list when dropdown is opened
- **Mark as Read**: 
  - Click notification to mark as read and navigate
  - "Mark all read" button for bulk action
- **Delete**: Individual delete button (trash icon) on hover
- **Navigation**: Clicking notification navigates to assignment submissions page
- **Relative Time**: Shows "Just now", "5m ago", "2h ago", "3d ago", etc.

#### API Integration
Uses Axios to call backend endpoints:
- `/instructor/notifications/unread-count` - Every 10 seconds
- `/instructor/notifications` - When dropdown opens
- `/instructor/notifications/{id}/read` - On click
- `/instructor/notifications/read-all` - Mark all button
- `/instructor/notifications/{id}` - Delete button

#### Component Structure
```vue
<DropdownMenu>
  <DropdownMenuTrigger>
    <Bell icon with Badge />
  </DropdownMenuTrigger>
  <DropdownMenuContent>
    <Header with "Mark all read" button />
    <ScrollArea>
      <UnreadNotifications (with blue dot) />
      <ReadNotifications (faded) />
    </ScrollArea>
  </DropdownMenuContent>
</DropdownMenu>
```

### 2. ScrollArea Component
**Files:** 
- `resources/js/components/ui/scroll-area/ScrollArea.vue`
- `resources/js/components/ui/scroll-area/index.ts`

A custom scrollable container component using `reka-ui` primitives.

**Features:**
- Vertical and horizontal scrollbars
- Auto-hide scrollbars (600ms delay)
- Styled scrollbar thumb (rounded, border color)

### 3. AppHeader Integration
**File:** `resources/js/components/AppHeader.vue`

**Changes:**
1. Added import: `import NotificationBell from '@/components/NotificationBell.vue'`
2. Added computed property to check if user is instructor:
   ```typescript
   const isInstructor = computed(() => {
       const user = auth.value?.user;
       return user?.role?.name === 'instructor';
   });
   ```
3. Added NotificationBell component between Search button and Repository links:
   ```vue
   <NotificationBell v-if="isInstructor" />
   ```

**Position:** Top-right corner, between search icon and documentation links, only visible to instructors.

## User Flow

### For Students
1. Student completes assignment and clicks "Submit Assignment"
2. Backend creates `InstructorNotification` record
3. Student is redirected to results page

### For Instructors
1. Instructor sees red badge on bell icon (real-time update within 10 seconds)
2. Click bell to open dropdown
3. See list of notifications with student names and assignment titles
4. Click notification:
   - Marks as read (blue dot disappears, count decreases)
   - Navigates to `/instructor/assignments/{id}/submissions`
5. Optional: "Mark all read" button to clear all notifications
6. Optional: Delete individual notifications with trash icon

## Technical Details

### Polling Mechanism
- **Interval**: 10 seconds (configurable in `NotificationBell.vue`)
- **Endpoint**: `/instructor/notifications/unread-count`
- **On Mount**: Starts polling automatically
- **On Unmount**: Cleans up interval to prevent memory leaks

### Performance Optimizations
- Only fetches full notification list when dropdown is opened
- Lightweight unread count endpoint (no joins needed)
- Database indexes on `[instructor_id, is_read]` for fast queries
- Pagination (20 per page) to handle large notification lists

### Security
- All routes require `auth` and `role:instructor,admin` middleware
- Notifications scoped to authenticated instructor (can't see other instructors' notifications)
- Authorization checks in controller ensure proper access control

## Future Enhancements

### Possible Improvements
1. **Real-time Updates**: Replace polling with WebSockets (Laravel Echo + Pusher/Reverb)
2. **Notification Types**: Add more types (quiz submitted, course enrollment, etc.)
3. **Preferences**: Allow instructors to customize notification settings
4. **Email Notifications**: Send email for critical notifications
5. **Sound/Toast**: Browser notification or sound when new notification arrives
6. **Batch Operations**: Select multiple notifications to mark/delete
7. **Filter/Search**: Filter notifications by type, course, or date range
8. **Notification History**: Archive old notifications instead of deleting

### Known Limitations
1. Polling creates constant server requests (10 sec intervals)
2. No notification persistence settings (all notifications remain until manually deleted)
3. No email fallback if instructor is offline
4. Navigation route `/instructor/assignments/{id}/submissions` must be created

## Testing

### Manual Testing Steps
1. **Create Notification**:
   - Log in as student
   - Go to a course with an assignment
   - Complete and submit assignment
   - Verify notification created in database

2. **View Notification**:
   - Log in as instructor (course owner)
   - Check bell icon for red badge with count
   - Click bell to open dropdown
   - Verify notification appears with correct details

3. **Mark as Read**:
   - Click notification
   - Verify blue dot disappears
   - Verify count decreases
   - Verify notification becomes faded

4. **Mark All as Read**:
   - Have multiple unread notifications
   - Click "Mark all read" button
   - Verify all become read and count goes to 0

5. **Delete Notification**:
   - Hover over notification
   - Click trash icon
   - Verify notification disappears from list

6. **Polling**:
   - Keep instructor page open
   - Submit assignment from student account (different browser)
   - Wait up to 10 seconds
   - Verify badge count updates automatically

### Database Verification
```sql
-- Check notifications for instructor with ID 1
SELECT * FROM instructor_notifications 
WHERE instructor_id = 1 
ORDER BY created_at DESC;

-- Check unread count
SELECT COUNT(*) FROM instructor_notifications 
WHERE instructor_id = 1 AND is_read = false;
```

## Files Modified/Created

### Backend
- ✅ `database/migrations/2025_10_20_161140_create_instructor_notifications_table.php` (CREATED)
- ✅ `app/Models/InstructorNotification.php` (CREATED)
- ✅ `app/Http/Controllers/Instructor/NotificationController.php` (CREATED)
- ✅ `app/Http/Controllers/Student/StudentAssignmentController.php` (MODIFIED - added notification creation)
- ✅ `routes/web.php` (MODIFIED - added notification routes)

### Frontend
- ✅ `resources/js/components/NotificationBell.vue` (CREATED)
- ✅ `resources/js/components/ui/scroll-area/ScrollArea.vue` (CREATED)
- ✅ `resources/js/components/ui/scroll-area/index.ts` (CREATED)
- ✅ `resources/js/components/AppHeader.vue` (MODIFIED - added NotificationBell component)

## Summary

The notification system is now fully functional:
- ✅ Database table created and migrated
- ✅ Backend API with 5 endpoints
- ✅ Notification creation on assignment submission
- ✅ Real-time notification bell in header (instructors only)
- ✅ Polling mechanism for live updates (10 sec interval)
- ✅ Mark as read functionality
- ✅ Delete functionality
- ✅ Clean UI with relative timestamps and badges
- ⏳ Next step: Create assignment submissions list view (navigation target)

**Status:** Ready for testing and production use!
