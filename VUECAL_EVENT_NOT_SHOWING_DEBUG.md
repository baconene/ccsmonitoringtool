# VueCal Event Not Showing - Troubleshooting Guide

## Current Status
- ✅ Calendar navigates to correct week (February 2026)
- ✅ Debug shows: "1 schedule(s) loaded | 1 event(s) formatted for calendar"
- ❌ Event not visible in calendar grid

## Recent Fixes Applied

### 1. Extended Time Range
**Changed from:**
```vue
:time-to="23 * 60"  <!-- 11:00 PM -->
```

**To:**
```vue
:time-to="24 * 60"  <!-- Midnight -->
```

**Why:** Your event starts at 11:00 PM. If `time-to` is exactly 11:00 PM, VueCal might not show events at that boundary time. Extended to midnight to ensure 11:00 PM events are visible.

### 2. Added Comprehensive Debug Logging
Added detailed console logs to track:
- Raw schedule data from backend
- Date parsing and validation
- Event object creation
- Final events array sent to VueCal

## Debugging Steps

### Step 1: Check Browser Console
Open browser DevTools (F12) → Console tab. You should see:

```
🔍 Raw schedules: [{ id: X, title: "...", from_datetime: "...", ... }]
📅 Processing schedule: { 
  id: X, 
  title: "...",
  from_datetime: "2026-02-14T23:00:00",
  startDate: "Sat Feb 14 2026 23:00:00 GMT+0800",
  isValidStart: true,
  isValidEnd: true 
}
✅ Created event: { start: Date, end: Date, title: "...", class: "..." }
📊 Total events for VueCal: 1 [...]
```

**What to check:**
- ✅ `isValidStart` and `isValidEnd` should be `true`
- ✅ `startDate` should be a valid date string
- ✅ `class` should be something like `schedule-course`

### Step 2: Verify Event Date/Time
In console, check:
```javascript
// Should show your events
calendarEvents.value

// Check first event dates
calendarEvents.value[0].start  // Should be a Date object
calendarEvents.value[0].end    // Should be a Date object
```

### Step 3: Check Schedule Type Name
The CSS class is generated from schedule type name:
```javascript
const typeClass = schedule.type.name.toLowerCase().replace(/\s+/g, '-');
// Example: "Course" → "course" → "schedule-course"
```

**Supported classes:**
- `schedule-activity` → Blue (#3B82F6)
- `schedule-course` → Green (#10B981)
- `schedule-adhoc` → Amber (#F59E0B)
- `schedule-exam` → Red (#EF4444)
- `schedule-office-hours` → Purple (#8B5CF6)

**If type name doesn't match**, the event will have no color styling (might be invisible).

### Step 4: Check VueCal Props
Verify in Vue DevTools:
```
VueCal component props:
- events: Array(1) ← Should have 1 item
- selected-date: Date ← Should match your event date
- time-from: 420 (7:00 AM)
- time-to: 1440 (midnight)
```

## Common Issues & Solutions

### Issue 1: Event Outside Time Range
**Symptom:** Event exists but not visible
**Cause:** Event time is before `time-from` (7 AM) or after `time-to` (midnight)
**Solution:** 
```vue
:time-from="0"      <!-- Start at midnight -->
:time-to="24 * 60"  <!-- End at next midnight -->
```

### Issue 2: Invalid Date Format
**Symptom:** Console shows `isValidStart: false`
**Cause:** Backend sending invalid date string
**Solution:** Check database schedule dates are valid ISO 8601 format:
```sql
SELECT id, title, from_datetime, to_datetime FROM schedules;
-- Should show: 2026-02-14 23:00:00
```

### Issue 3: Missing Schedule Type
**Symptom:** Event has no color, might be transparent
**Cause:** `schedule.type` is null or type name doesn't match CSS classes
**Solution:** 
1. Check schedule type in database:
```sql
SELECT s.id, s.title, st.name as type_name 
FROM schedules s 
JOIN schedule_types st ON s.schedule_type_id = st.id;
```
2. Add fallback styling for unknown types in `<style>`:
```css
:deep(.vuecal__event) {
  background-color: #6B7280 !important; /* Gray fallback */
  color: white !important;
}
```

### Issue 4: Event Duration Too Short
**Symptom:** Event exists but appears as thin line
**Cause:** `to_datetime` is only minutes after `from_datetime`
**Check:**
```javascript
// In console
const event = calendarEvents.value[0];
const duration = (event.end - event.start) / 1000 / 60; // minutes
console.log('Duration:', duration, 'minutes');
```
**Solution:** Ensure events are at least 15-30 minutes long for visibility

### Issue 5: Z-Index or Overlay Issue
**Symptom:** Event rendered but hidden behind other elements
**Solution:** Add to `<style>`:
```css
:deep(.vuecal__event) {
  z-index: 1 !important;
  position: relative !important;
}
```

### Issue 6: VueCal Not Updating
**Symptom:** Old data shown even after refresh
**Cause:** Vue reactivity issue or stale computed
**Solution:** Force re-render by changing key:
```vue
<VueCal :key="`calendar-${schedules.length}-${Date.now()}`" />
```

## Quick Test: Add Static Event

To verify VueCal is working, try adding a hardcoded test event:

```typescript
const calendarEvents = computed(() => {
  // Test event for debugging
  const testEvent = {
    start: new Date(2026, 1, 14, 10, 0), // Feb 14, 2026, 10:00 AM
    end: new Date(2026, 1, 14, 11, 0),   // Feb 14, 2026, 11:00 AM
    title: 'TEST EVENT',
    class: 'schedule-course',
  };
  
  const realEvents = schedules.value.map(schedule => {
    // ... existing mapping code ...
  });
  
  return [testEvent, ...realEvents];
});
```

**If test event shows:** Real events have data format issue  
**If test event doesn't show:** VueCal configuration issue

## Files Modified
- ✅ `resources/js/Pages/SchedulingManagement/UserSchedule.vue`
  - Extended `time-to` from 23:00 to 24:00 (midnight)
  - Added comprehensive debug logging
  - Added console output message in debug banner

## Next Steps

1. **Refresh browser** and check console logs
2. **Copy console output** showing:
   - Raw schedules data
   - Processed event data
   - Any errors
3. **Check database** for schedule type name
4. **Try List View** (toggle button) - does the event show there?
5. If event shows in List View but not Calendar, it's a VueCal-specific issue

## VueCal Documentation
- Events: https://antoniandre.github.io/vue-cal/#ex--events
- Time Range: https://antoniandre.github.io/vue-cal/#ex--time-range
- Styling: https://antoniandre.github.io/vue-cal/#ex--event-colors
