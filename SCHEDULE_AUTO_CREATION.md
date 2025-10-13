# Automatic Schedule Creation for Activities

## Overview
This feature automatically creates a schedule entry whenever an activity is created with a due date. The schedule is also automatically updated when the activity's due date changes and deleted when the activity is deleted.

## Implementation Details

### Schedule Timing
- **End Time (to_datetime)**: Set to the activity's due date
- **Start Time (from_datetime)**: Set to 1 hour before the due date
- **Duration**: 1 hour

### Example
If an activity has a due date of `2025-10-20 14:00:00`:
- Schedule start: `2025-10-20 13:00:00`
- Schedule end: `2025-10-20 14:00:00`

## Modified Files

### 1. `app/Http/Controllers/ActivityController.php`
Added automatic schedule management:

#### New Imports
```php
use App\Models\Schedule;
use App\Models\ScheduleActivity;
use Illuminate\Support\Facades\DB;
```

#### Modified Methods

**`store()` method**:
- Wrapped in database transaction
- Automatically creates schedule if `due_date` is provided
- Calls `createScheduleForActivity()` helper method

**`update()` method**:
- Wrapped in database transaction
- Calls `syncScheduleForActivity()` to update/create/delete schedule based on due date changes

#### New Helper Methods

**`createScheduleForActivity(Activity $activity)`**:
- Creates a new Schedule record with type "Activity" (schedule_type_id = 1)
- Sets from_datetime to 1 hour before due_date
- Sets to_datetime to due_date
- Creates ScheduleActivity record linking the schedule to the activity
- Automatically adds the activity creator as a participant (organizer role, accepted status)

**`syncScheduleForActivity(Activity $activity)`**:
- Finds existing schedule for the activity
- If due_date exists:
  - Updates existing schedule OR creates new one
  - Updates schedule times based on new due_date
- If due_date is null:
  - Deletes associated schedule

### 2. `app/Models/Activity.php`
Added schedule relationships and cleanup:

#### Modified `boot()` method
- Added cascade deletion for associated schedules when an activity is deleted
- Deletes ScheduleActivity records
- Deletes Schedule participants
- Deletes Schedule records

#### New Relationships
```php
// Get all schedules for this activity
public function schedules()

// Get the primary schedule for this activity (type: Activity)
public function schedule()
```

## Database Schema

### Tables Involved
1. **schedules** - Main schedule records
2. **schedule_activities** - Links schedules to activities with additional details
3. **schedule_participants** - (Future) Links schedules to users

### Key Fields

**schedules table**:
- `schedule_type_id`: 1 (Activity type)
- `schedulable_type`: "App\Models\Activity"
- `schedulable_id`: Activity ID
- `from_datetime`: Due date - 1 hour
- `to_datetime`: Due date
- `status`: "scheduled"

**schedule_activities table**:
- `schedule_id`: Foreign key to schedules
- `activity_id`: Foreign key to activities
- `submission_deadline`: Same as activity due_date
- `passing_score`: From activity.passing_percentage

**schedule_participants table**:
- `schedule_id`: Foreign key to schedules
- `user_id`: Foreign key to users (automatically set to activity creator)
- `role_in_schedule`: "organizer" (activity creator)
- `participation_status`: "accepted" (automatically accepted for creator)

## Usage

### Creating an Activity with Schedule
```php
// Via Controller
$activity = Activity::create([
    'title' => 'Quiz 1',
    'description' => 'First quiz of the semester',
    'activity_type_id' => 1,
    'due_date' => '2025-10-20 14:00:00', // Schedule auto-created
    'created_by' => auth()->id(),
]);
```

### Updating Activity Due Date
```php
// Update due date - schedule automatically updates
$activity->update([
    'due_date' => '2025-10-25 16:00:00'
]);
```

### Removing Due Date
```php
// Remove due date - schedule automatically deleted
$activity->update([
    'due_date' => null
]);
```

### Deleting an Activity
```php
// Delete activity - schedule automatically deleted
$activity->delete();
```

## Testing Results

✅ **Create Test**: Activity with due_date creates schedule correctly
- Activity due: 2025-10-20 14:00:00
- Schedule from: 2025-10-20 13:00:00
- Schedule to: 2025-10-20 14:00:00

✅ **Update Test**: Changing due_date updates schedule times
- Old: 13:00-14:00
- New: 15:30-16:30 (1 hour before new due date)

✅ **Delete Test**: Deleting activity removes schedule and schedule_activity records
- Schedules before: 1, after: 0
- Schedule activities before: 1, after: 0

## Benefits

1. **Automatic Synchronization**: No manual schedule management needed
2. **Data Consistency**: Schedule always reflects activity due date
3. **Calendar Integration**: Activities automatically appear in user schedules
4. **Clean Deletion**: No orphaned schedule records
5. **Transactional Safety**: Database transactions ensure data integrity

## Future Enhancements

1. **Automatic Participant Assignment**: Add students enrolled in the course to schedule_participants
2. **Notification System**: Send reminders as schedule approaches
3. **Custom Duration**: Allow configuring schedule duration (currently fixed at 1 hour)
4. **Multiple Schedules**: Support creating multiple schedule types per activity (e.g., review session, due date, late submission deadline)
