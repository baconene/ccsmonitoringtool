# Lesson Document Upload Reactivity Fix

## Problem
When documents were uploaded to lessons in Course Management, the uploaded documents were not appearing in the UI until a full page refresh. The documents were successfully saved to the database, but the frontend UI wasn't updating to reflect the changes.

## Root Cause
The issue was a **broken event chain** in the component hierarchy. When a document was uploaded to a lesson:

1. `RelatedDocumentContainer.vue` (in lesson) emitted `document-uploaded` event
2. `lessonList.vue` caught this event and called `refreshLessons()` 
3. `refreshLessons()` fetched updated data from `/modules/{id}/lessons` API
4. `lessonList.vue` updated its local `localLessons` state and emitted `update:lessons`
5. **BUT** the parent components weren't listening to this event!

The event chain was broken at:
- `ModuleLessonsSection.vue` → Didn't listen to `update:lessons` from LessonList
- `ModuleDetailsMain.vue` → Didn't forward the event
- `CourseManagement.vue` → Didn't reload courses data

## Component Hierarchy
```
CourseManagement.vue (Page)
  └─ ModuleDetailsMain.vue
      └─ ModuleLessonsSection.vue
          └─ lessonList.vue
              └─ RelatedDocumentContainer.vue
```

## Solution Implemented

### 1. Fixed ModuleController Backend (Prerequisite)
**File**: `app/Http/Controllers/ModuleController.php`

Added null-safety filtering to prevent errors when lesson documents have been deleted:

```php
$documents = $lesson->documents
    ->filter(function ($lessonDoc) {
        // Filter out documents that have been deleted
        return $lessonDoc->document !== null;
    })
    ->map(function ($lessonDoc) {
        $doc = $lessonDoc->document;
        return [
            'id' => $doc->id,
            'name' => $doc->name,
            // ... other fields
        ];
    })
    ->values(); // Reset array keys after filtering
```

This fixed the 500 error when fetching lessons with deleted documents.

### 2. Connected Event Chain in Frontend

#### ModuleLessonsSection.vue
**Location**: `resources/js/module/components/ModuleLessonsSection.vue`

Added event listener and emitter:

```vue
<template>
  <LessonList 
    :module-id="moduleId" 
    :lessons="lessons" 
    @update:lessons="handleLessonsUpdate"
  />
</template>

<script setup lang="ts">
const emit = defineEmits<{
  (e: 'add'): void;
  (e: 'update:lessons', lessons: any[]): void; // Added
}>();

// Forward lessons update to parent
function handleLessonsUpdate(updatedLessons: any[]) {
  emit('update:lessons', updatedLessons);
}
</script>
```

#### ModuleDetailsMain.vue
**Location**: `resources/js/module/ModuleDetailsMain.vue`

Added event listener, emitter, and handler:

```vue
<template>
  <ModuleLessonsSection
    v-if="allowsLessons"
    :module-id="module.id"
    :lessons="module.lessons || []"
    @add="emit('add-lesson', module)"
    @update:lessons="handleLessonsUpdate"
  />
</template>

<script setup lang="ts">
const emit = defineEmits<{
  // ... existing emits
  (e: "update:lessons", lessons: any[]): void; // Added
}>();

// Handle lessons update (when documents are uploaded/deleted)
const handleLessonsUpdate = (updatedLessons: any[]) => {
  emit('update:lessons', updatedLessons);
};
</script>
```

#### CourseManagement.vue
**Location**: `resources/js/pages/CourseManagement.vue`

Added event listener and handler to reload courses:

```vue
<template>
  <ModuleDetailsMain
    :module="activeModule ? { ...activeModule, created_by: activeModule.created_by ?? 0 } : null"
    @edit="showEditModuleModal = true"
    @remove="showRemoveModuleModal = true"
    @add-lesson="showAddLessonModal = true"
    @add-activity="showAddActivityModal = true"
    @upload-document="showUploadDocumentModal = true"
    @remove-activity="handleRemoveActivity"
    @update:lessons="handleLessonsUpdate"
  />
</template>

<script setup lang="ts">
// Handle lessons update (when documents are uploaded/deleted in lessons)
function handleLessonsUpdate(updatedLessons: any[]) {
  console.log('Lessons updated, reloading courses data...', updatedLessons);
  reloadCourses(); // Uses Inertia router.reload() to refresh data
}
</script>
```

## How It Works Now

### Upload Flow:
1. User uploads document in `RelatedDocumentContainer.vue`
2. Document saved to database via API
3. `@document-uploaded` event emitted
4. `lessonList.vue` catches event, calls `refreshLessons()`
5. `refreshLessons()` fetches updated data from `/modules/{id}/lessons`
6. Updated data stored in `localLessons`
7. `emit('update:lessons', normalizedData)` fires
8. **NEW**: `ModuleLessonsSection.vue` catches event, emits up
9. **NEW**: `ModuleDetailsMain.vue` catches event, emits up  
10. **NEW**: `CourseManagement.vue` catches event, calls `reloadCourses()`
11. `reloadCourses()` calls `router.reload({ only: ['courses', 'availableActivities'] })`
12. Inertia reloads data from backend
13. All components receive fresh props with updated lesson documents
14. UI updates with newly uploaded document visible

### Delete Flow:
Same event chain - when a document is deleted, the same `update:lessons` event propagates up and triggers a reload.

## Files Modified

### Backend
1. **app/Http/Controllers/ModuleController.php**
   - Added null-safety filtering in `index()` method (lines 19-38)
   - Prevents 500 errors when lesson documents are deleted

### Frontend
2. **resources/js/module/components/ModuleLessonsSection.vue**
   - Added `@update:lessons` event listener
   - Added `update:lessons` emitter
   - Added `handleLessonsUpdate()` function

3. **resources/js/module/ModuleDetailsMain.vue**
   - Added `@update:lessons` event listener on ModuleLessonsSection
   - Added `update:lessons` to emits
   - Added `handleLessonsUpdate()` function

4. **resources/js/pages/CourseManagement.vue**
   - Added `@update:lessons` event listener on ModuleDetailsMain
   - Added `handleLessonsUpdate()` function that calls `reloadCourses()`

## Benefits

1. **Real-time Updates**: Documents appear immediately after upload without manual refresh
2. **Consistent State**: All components stay in sync with database state
3. **Better UX**: Users see their changes reflected instantly
4. **Defensive Programming**: Backend handles orphaned document relationships gracefully
5. **Scalable Pattern**: Event chain pattern can be reused for other similar features

## Technical Notes

- Uses **Inertia.js partial reloads** (`router.reload({ only: ['courses'] })`) to refresh data efficiently
- **Event-driven architecture** maintains separation of concerns
- **Null-safety filtering** in backend prevents runtime errors from data inconsistencies
- TypeScript ensures type safety throughout the event chain

## Testing Checklist

- [x] Backend compiles without errors
- [x] Frontend builds successfully (16.21s)
- [x] TypeScript validation passes
- [ ] Upload document to lesson → Document appears immediately
- [ ] Delete document from lesson → Document disappears immediately
- [ ] Multiple documents upload → All appear correctly
- [ ] Navigate between modules → Correct documents shown
- [ ] Refresh page → Documents persist

## Related Fixes

This fix complements other document-related improvements:
- **Document Viewer**: Full-screen preview with multi-format support
- **Document Filtering**: `validDocuments` computed property removes invalid entries
- **Data Binding**: Changed `v-model` to `:model-value` for one-way binding
- **Null Safety**: Backend filters orphaned document relationships
