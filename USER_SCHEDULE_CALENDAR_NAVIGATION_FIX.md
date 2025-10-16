# User Schedule: Calendar Event Display Fix

## Issue
Calendar was showing "1 schedule(s) loaded | 1 event(s) formatted for calendar" but the event was not visible on the calendar grid.

## Root Cause
**VueCal was defaulting to the current week (October 2025) while the scheduled event was in a different week (February 2025).**

When no `selected-date` is provided, VueCal shows the current date's week by default. If all your events are in different weeks (past or future), they won't be visible unless you manually navigate to those weeks.

## Solution

### 1. Added Automatic Date Navigation

**Added state for selected date:**
```typescript
const selectedDate = ref<Date | string | null>(null);
```

**Auto-navigate to first event on load:**
```typescript
// Watch for prop changes
watch(() => props.initialSchedules, (newSchedules) => {
  if (newSchedules) {
    schedules.value = newSchedules;
    loading.value = false;
    
    // ✅ Auto-navigate to first event date
    if (newSchedules.length > 0) {
      selectedDate.value = new Date(newSchedules[0].from_datetime);
    }
  }
}, { immediate: true });

onMounted(() => {
  loading.value = false;
  
  // ✅ Set initial date to first event
  if (schedules.value.length > 0) {
    selectedDate.value = new Date(schedules.value[0].from_datetime);
  }
});
```

### 2. Passed Selected Date to VueCal

```vue
<VueCal
  :events="calendarEvents"
  :selected-date="selectedDate"  <!-- ✅ Navigate to event date -->
  default-view="week"
  <!-- ... other props ... -->
/>
```

### 3. Enhanced Debug Information

**Before:**
```
1 schedule(s) loaded | 1 event(s) formatted for calendar
```

**After:**
```
1 schedule(s) loaded | 1 event(s) formatted for calendar
First event: 2/14/2025, 11:00:00 PM | Showing: 2/14/2025, 11:00:00 PM
```

Now you can see:
- When the first event is scheduled
- What date the calendar is showing
- Verify they match

## How It Works

1. **On page load**, UserSchedule.vue receives `initialSchedules` from backend
2. **Watch/onMounted hooks** detect schedules and extract the first event's date
3. **selectedDate ref** is set to the first event's `from_datetime`
4. **VueCal** receives `:selected-date` prop and automatically navigates to that week
5. **Event becomes visible** in the correct week

## VueCal Selected Date Prop

The `selected-date` prop accepts:
- `Date` object: `new Date('2025-02-14')`
- ISO string: `'2025-02-14T23:00:00'`
- JavaScript timestamp

When provided, VueCal will:
- Navigate to the week/month/day containing that date
- Highlight that date
- Display any events in that time range

## Testing

1. **Create a schedule** for any future date (e.g., next month)
2. **Navigate to** `/schedule`
3. **Calendar should automatically show** the week of your scheduled event
4. **Debug info** should display:
   - First event date
   - Currently showing date (should match)
5. **Event should be visible** in the calendar grid

## Edge Cases Handled

### Multiple Events in Different Weeks
Currently navigates to the **first event's week**. Users can manually navigate to other weeks using calendar arrows.

### No Events
If `schedules.length === 0`, `selectedDate` remains `null` and VueCal shows the current week (default behavior).

### Event in the Past
The backend query uses `upcoming()` scope which filters `from_datetime >= now()`, so past events shouldn't appear. However, if they do (e.g., created with a past date), the calendar will still navigate to that date.

## Alternative Solutions Considered

### 1. Show Month View by Default
```vue
<VueCal default-view="month" />
```
**Pros:** Shows entire month, easier to see events  
**Cons:** Less detail, harder to see exact times

### 2. Add Quick Navigation Buttons
```vue
<button @click="goToToday()">Today</button>
<button @click="goToNextEvent()">Next Event</button>
```
**Pros:** User control, flexible navigation  
**Cons:** More UI complexity

### 3. Show Event List Below Calendar
Already implemented in List view toggle.

## Files Modified
- ✅ `resources/js/Pages/SchedulingManagement/UserSchedule.vue`
  - Added `selectedDate` ref
  - Added auto-navigation logic in watch/onMounted
  - Passed `:selected-date` to VueCal
  - Enhanced debug info

## Related Issues
- If events still don't show after this fix, check:
  - Event date is in the future (check database)
  - Event time is within `time-from` (7am) and `time-to` (11pm)
  - Event class has proper styling (check CSS)
  - Console for any JavaScript errors

## Documentation Links
- VueCal Documentation: https://antoniandre.github.io/vue-cal/
- VueCal Selected Date: https://antoniandre.github.io/vue-cal/#ex--selected-date
