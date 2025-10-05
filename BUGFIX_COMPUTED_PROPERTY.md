# Bug Fix: TypeError "W(...) is not a function"

**Date**: October 5, 2025  
**Status**: ✅ Fixed

## Issue

After the refactoring, the application was showing a console error:

```
TypeError: W(...) is not a function
    at Proxy.<anonymous> (CourseManagement-CPL09EOi.js:59:71848)
```

This error occurred when trying to view module details in the Course Management page.

## Root Cause

In `ModuleDetailsMain.vue`, the code was incorrectly calling `getModuleTypeBadgeClass` as a function:

```vue
<!-- INCORRECT -->
<span :class="getModuleTypeBadgeClass(moduleType)">
```

However, `getModuleTypeBadgeClass` is a **computed property** (ComputedRef), not a function.

## Solution

Changed the template to use the computed property directly without calling it as a function:

```vue
<!-- CORRECT -->
<span :class="getModuleTypeBadgeClass">
```

## Code Change

**File**: `resources/js/module/ModuleDetailsMain.vue`

**Before**:
```vue
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
  :class="getModuleTypeBadgeClass(moduleType)">
  {{ moduleType }}
</span>
```

**After**:
```vue
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
  :class="getModuleTypeBadgeClass">
  {{ moduleType }}
</span>
```

## Why This Happened

In the `useModuleType` composable, `getModuleTypeBadgeClass` is defined as a computed property:

```typescript
const getModuleTypeBadgeClass = computed(() => {
  const type = moduleType.value;
  const classes: Record<string, string> = { /* ... */ };
  return classes[type] || classes['Mixed'];
});
```

Computed properties are automatically unwrapped in templates, so they should be accessed directly, not called as functions.

## Verification

✅ **Build Status**: Successful (10.68s)  
✅ **TypeScript Errors**: 0  
✅ **Vue Template Errors**: 0  
✅ **Bundle Generated**: CourseManagement-DwVRIfp8.js  

## Impact

- Module type badges now display correctly with proper color coding
- No more runtime errors in Course Management page
- Application functions normally

## Lessons Learned

When using Vue 3 Composition API:
- Computed properties (ComputedRef) are accessed directly: `{{ computedValue }}`
- Functions are called: `{{ functionName() }}`
- In templates, Vue automatically unwraps refs and computed properties

## Related Files

- `resources/js/module/ModuleDetailsMain.vue` - Fixed
- `resources/js/composables/useModuleType.ts` - No changes needed
- Build successful: All components working correctly
