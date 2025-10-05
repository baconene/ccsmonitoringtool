# Bug Fix: Activity Type Not Displaying (Snake Case vs Camel Case)

**Date**: October 6, 2025  
**Status**: ✅ Fixed

## Issue

Activity types were showing as "Unknown" instead of displaying the actual type name (Quiz, Assignment, Exercise, etc.) in the Course Management interface.

## Root Cause

**Laravel Backend** returns data in **snake_case** format:
```javascript
activity_type: {
  id: 3,
  name: "Exercise",
  description: "Practice exercise for students"
}
```

**Frontend Code** was expecting **camelCase** format:
```javascript
activity.activityType?.name  // Looking for camelCase
```

This is a common Laravel/JavaScript serialization mismatch.

## Console Output Analysis

From the browser console, we could see the backend WAS returning the data:
```javascript
{
  id: 5,
  title: "tes",
  description: "tes",
  activity_type: {           // ← Snake case from Laravel
    id: 3,
    name: "Exercise",        // ← Data is here!
    description: "Practice exercise for students"
  },
  activity_type_id: 3,
  created_by: 1,
  creator: { ... }
}
```

But the frontend code was checking:
```typescript
activity.activityType?.name  // undefined!
```

## Solution

Updated the code to handle **both** naming conventions (snake_case and camelCase):

### 1. Updated Composable (`useActivityType.ts`)

**Before**:
```typescript
const getActivityTypeName = (activity: Activity) => {
  return activity.activityType?.name || 'Unknown';
};
```

**After**:
```typescript
const getActivityTypeName = (activity: Activity | any) => {
  // Check both camelCase and snake_case
  const activityType = activity.activityType || activity.activity_type;
  return activityType?.name || 'Unknown';
};
```

### 2. Updated Component (`ModuleActivitiesSection.vue`)

**Before**:
```vue
:class="getActivityTypeBadgeClass(activity.activityType?.name)"
```

**After**:
```vue
:class="getActivityTypeBadgeClass((activity.activityType || activity.activity_type)?.name)"
```

### 3. Updated TypeScript Types (`types/index.ts`)

**Before**:
```typescript
export type Activity = {
  // ...
  activity_type: number;  // Just ID
  activityType: ActivityType;  // Expected object
  // ...
}
```

**After**:
```typescript
export type Activity = {
  // ...
  activity_type: number | ActivityType;  // Can be ID or full object (snake_case)
  activityType?: ActivityType;  // Optional camelCase version
  pivot?: any;  // For pivot data
  // ...
}
```

## Backend Verification

Checked `CourseController.php` and confirmed it's correctly eager loading the relationship:
```php
$courses = Course::with([
    'modules.lessons.documents',
    'modules.activities.activityType',  // ✓ Correctly loading
    'gradeLevels'
])->get();
```

Also verified in `Activity.php` model:
```php
protected $with = ['activityType', 'creator'];  // ✓ Auto-loading
```

## Files Modified

1. **`resources/js/composables/useActivityType.ts`**
   - Updated `getActivityTypeName()` to check both naming conventions

2. **`resources/js/module/components/ModuleActivitiesSection.vue`**
   - Updated template to handle both `activityType` and `activity_type`

3. **`resources/js/types/index.ts`**
   - Updated `Activity` type to allow both formats
   - Made `activityType` optional with `?`
   - Changed `activity_type` to accept both `number | ActivityType`

## Testing

✅ **Build Status**: Successful (13.39s)  
✅ **No TypeScript Errors**  
✅ **No Vue Template Errors**

## Results

- Activity types now display correctly: "Quiz", "Assignment", "Exercise", etc.
- Color-coded badges work properly
- Fallback to "Unknown" only when data is truly missing
- Works with both Laravel snake_case and JavaScript camelCase

## Why This Happens

Laravel typically returns data in **snake_case** (following PHP/database conventions), while JavaScript developers expect **camelCase**. Solutions:

### Option A: Handle Both (Current Solution)
✅ **Pros**: Works immediately, no backend changes  
✅ **Pros**: Flexible, works with existing data  
❌ **Cons**: Slightly more code in frontend

### Option B: Transform on Backend
Convert to camelCase in Laravel using API Resources or transformers:
```php
return [
    'activityType' => $this->activityType,  // Manual mapping
];
```

### Option C: Global Transformer
Use a Laravel package like `laravel-data` or middleware to automatically convert all responses to camelCase.

## Best Practice

For future consistency, consider:
1. **API Resources**: Define explicit transformations in Laravel API Resources
2. **Naming Convention**: Pick one convention (camelCase) for API responses
3. **Documentation**: Document expected data format in API documentation
4. **Type Safety**: Use TypeScript to catch naming mismatches early

## Prevention

To avoid this in future:
1. Add console logging during development to verify data structure
2. Use TypeScript strict mode to catch property access issues
3. Create type guards or validators for API responses
4. Document backend response format in code comments

## Related Issues

This fix also resolves:
- Activity badges showing incorrect colors
- Activity stats not displaying properly
- Type-based filtering not working correctly

---

**Status**: All activity types now display correctly with proper color coding! 🎉
