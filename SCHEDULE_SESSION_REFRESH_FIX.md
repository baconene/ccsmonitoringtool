# Schedule Page Session Refresh Fix

## Problem
Users were experiencing a login/logout loop when navigating to the `/schedule` page. The session was being refreshed/invalidated during navigation, causing authentication issues.

## Root Causes

### 1. **Axios API Calls Instead of Inertia**
The `UserSchedule.vue` component was using axios to fetch schedule data from `/api/users/{userId}/schedules/upcoming`. This created a separate HTTP request outside of Inertia's navigation system, which could cause session handling issues.

### 2. **Laravel Cache Not Recognizing `schedules()` Method**
Laravel's cache had an outdated version where the `Course::schedules()` method wasn't recognized, causing a "Call to undefined method" error that resulted in 500 errors.

### 3. **Missing Proper Authentication Middleware**
The `api_schedules.php` routes were using `middleware(['api'])` instead of `middleware(['auth:sanctum'])`, which could cause authentication context issues.

### 4. **Null Reference in StudentCourseController**
At line 175, accessing `$doc->id` without proper null checking was causing errors when document relationships were null.

## Solutions Implemented

### 1. **Use Inertia for Schedule Page (routes/web.php)**
Changed from simple Inertia render to passing initial data:

```php
// Before
Route::get('schedule', function () {
    return Inertia::render('SchedulingManagement/UserSchedule');
})->middleware(['auth', 'verified'])->name('schedule.index');

// After
Route::get('schedule', function () {
    $user = auth()->user();
    
    // Fetch initial schedule data
    $schedules = \App\Models\Schedule::with([
        'scheduleType',
        'creator:id,name,email',
        'participants.user:id,name,email',
        'schedulable',
    ])
    ->forUser($user->id)
    ->upcoming()
    ->withTrashed() // Include cancelled schedules
    ->get()
    ->map(function ($schedule) use ($user) {
        // ... mapping logic ...
    });
    
    return Inertia::render('SchedulingManagement/UserSchedule', [
        'initialSchedules' => $schedules,
    ]);
})->middleware(['auth', 'verified'])->name('schedule.index');
```

**Benefits:**
- ✅ Single page load with Inertia's navigation system
- ✅ Proper session handling through Inertia
- ✅ No separate axios calls on initial load
- ✅ Maintains authentication context

### 2. **Updated UserSchedule.vue Component**

#### Added Props Definition
```typescript
interface Props {
  initialSchedules?: Schedule[];
}

const props = withDefaults(defineProps<Props>(), {
  initialSchedules: () => []
});
```

#### Initialize State with Props
```typescript
// Before
const schedules = ref<Schedule[]>([]);
const loading = ref(true);

// After
const schedules = ref<Schedule[]>(props.initialSchedules || []);
const loading = ref(false);
```

#### Watch for Prop Changes
```typescript
watch(() => props.initialSchedules, (newSchedules) => {
  if (newSchedules) {
    schedules.value = newSchedules;
    loading.value = false;
  }
}, { immediate: true });
```

#### Use Inertia Router for Refresh
```typescript
// Before (using axios)
const fetchSchedules = async () => {
  const response = await axios.get(`/api/users/${user.value.id}/schedules/upcoming?include_cancelled=true`);
  schedules.value = response.data.data || [];
};

// After (using Inertia)
const fetchSchedules = async () => {
  loading.value = true;
  error.value = null;
  router.reload({ only: ['initialSchedules'] });
};
```

**Benefits:**
- ✅ Uses Inertia's built-in data management
- ✅ Maintains session properly
- ✅ Reduces network requests
- ✅ Better TypeScript support

### 3. **Fixed API Routes Authentication (routes/api_schedules.php)**
```php
// Before
Route::prefix('api')->middleware(['api'])->group(function () {

// After
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
```

**Benefits:**
- ✅ Proper Sanctum authentication
- ✅ Consistent auth handling with other API routes
- ✅ Better security

### 4. **Fixed Null Reference in StudentCourseController**
```php
// Added defensive null check
$documents = $module->documents->filter(function ($moduleDoc) {
    return $moduleDoc->document !== null;
})->map(function ($moduleDoc) {
    $doc = $moduleDoc->document;
    
    // Additional safety check
    if (!$doc) {
        return null;
    }
    
    return [
        'id' => $doc->id,
        // ... rest of mapping
    ];
})->filter()->values(); // Filter out null values and re-index
```

**Benefits:**
- ✅ Prevents "Attempt to read property 'id' on null" errors
- ✅ Handles edge cases gracefully
- ✅ No 500 errors on missing documents

### 5. **Cleared Laravel Cache**
```bash
php artisan optimize:clear
```

This cleared:
- ✅ Config cache
- ✅ Route cache
- ✅ View cache
- ✅ Compiled classes
- ✅ Events cache

## Testing

### Before Fix
- ❌ Navigating to `/schedule` caused login/logout loop
- ❌ Laravel logs showed "Call to undefined method App\Models\Course::schedules()"
- ❌ Session was being invalidated

### After Fix
- ✅ `/schedule` page loads successfully (Status 200)
- ✅ No session refresh issues
- ✅ No authentication errors
- ✅ Proper Inertia navigation
- ✅ Calendar displays correctly

## Files Changed

1. **routes/web.php** - Changed schedule route to use Inertia with initial data
2. **routes/api_schedules.php** - Added proper Sanctum authentication middleware
3. **resources/js/Pages/SchedulingManagement/UserSchedule.vue** - Updated to use Inertia props instead of axios
4. **app/Http/Controllers/Student/StudentCourseController.php** - Added null safety checks

## Commit Information

**Commit:** a03c86f  
**Message:** "Fix: Use Inertia for schedule page to prevent session refresh issues"  
**Branch:** main  
**Remote:** https://github.com/baconene/ccsmonitoringtool.git

## Key Takeaways

1. **Always use Inertia for page navigation** - Don't mix axios API calls with Inertia navigation unless necessary
2. **Pass initial data through Inertia props** - This ensures single page load and proper session handling
3. **Use `router.reload()` for data refresh** - Instead of separate API calls
4. **Clear Laravel cache after model changes** - Especially when adding new relationships
5. **Add defensive null checks** - Prevent null reference errors with proper filtering

## Future Recommendations

1. **Consider using Inertia's `preserveState` and `preserveScroll`** when refreshing schedule data
2. **Implement optimistic UI updates** for schedule changes before server confirmation
3. **Add loading states** during `router.reload()` operations
4. **Consider using Inertia's partial reloads** more extensively for better performance

## Related Issues Fixed

- ✅ Login/logout loop on schedule navigation
- ✅ "Call to undefined method App\Models\Course::schedules()" error
- ✅ "Attempt to read property 'id' on null" in StudentCourseController
- ✅ Session refresh during page navigation
