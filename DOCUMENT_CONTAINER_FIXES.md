# Document Container Fixes

## Issues Fixed

### Issue 1: Documents Disappearing After Upload ❌→✅

**Problem:**
After uploading documents, other document sections would lose their documents. The UI would show empty document lists even though documents existed.

**Root Cause:**
Using `v-model` on computed properties that transform data from props:

```vue
<!-- WRONG: v-model on computed property -->
<RelatedDocumentContainer
  v-model="normalizedDocuments"  <!-- This tries to mutate a computed prop! -->
  ...
/>

<script>
// Computed property from props - READ ONLY
const normalizedDocuments = computed(() => {
  return props.documents.map(doc => ({ ... }));
});
</script>
```

**Why It Failed:**
1. `v-model` is shorthand for `:model-value` + `@update:model-value`
2. When documents change, `@update:model-value` fires
3. Component tries to update `normalizedDocuments` computed property
4. Computed properties are read-only
5. Vue gets confused and loses reactivity
6. Documents disappear from UI

**Solution:**
Use `:model-value` instead of `v-model` for one-way data binding:

```vue
<!-- CORRECT: One-way binding with :model-value -->
<RelatedDocumentContainer
  :model-value="normalizedDocuments"  <!-- Read-only prop binding -->
  @document-uploaded="handleDocumentUploaded"  <!-- Explicit event handling -->
  @document-deleted="handleDocumentDeleted"
  ...
/>
```

**What Changed:**

**ModuleDocumentsSection.vue:**
```diff
  <RelatedDocumentContainer
-   v-model="normalizedDocuments"
+   :model-value="normalizedDocuments"
    model-type="module"
    :foreign-key-id="moduleId"
    @document-uploaded="handleDocumentUploaded"
    @document-deleted="handleDocumentDeleted"
  />
```

**lessonList.vue:**
```diff
  <RelatedDocumentContainer
-   v-model="lesson.documents"
+   :model-value="lesson.documents"
    model-type="lesson"
    :foreign-key-id="lesson.id"
    @document-uploaded="handleDocumentUploaded(lesson.id)"
    @document-deleted="handleDocumentDeleted"
  />
```

**Result:**
- ✅ Documents persist after upload/delete
- ✅ All sections maintain their data
- ✅ Proper one-way data flow
- ✅ Reactivity works correctly

---

### Issue 2: Upload Button Theme Inconsistency 🎨→✨

**Problem:**
Upload button used basic blue styling that didn't match the system's design language.

**Before:**
```vue
<button
  class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm font-medium"
>
  {{ showUploader ? 'Hide Uploader' : '+ Upload Files' }}
</button>
```

**Issues:**
- Basic blue color (not system indigo)
- Plain text "+ Upload Files"
- No icon
- Small padding
- Basic rounded corners
- No shadow
- No dark mode optimization

**After:**
```vue
<button
  class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-sm hover:shadow-md"
>
  <!-- Plus Icon (when closed) -->
  <svg v-if="!showUploader" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
  </svg>
  
  <!-- Close Icon (when open) -->
  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
  </svg>
  
  {{ showUploader ? 'Hide Uploader' : 'Upload Files' }}
</button>
```

**Improvements:**
✅ **System Color**: Indigo-600 (matches system theme)
✅ **Dark Mode**: Separate dark mode colors
✅ **Icons**: Plus icon (closed) / X icon (open)
✅ **Spacing**: Better padding (px-4 py-2)
✅ **Rounded**: Larger rounded-lg
✅ **Shadow**: Subtle shadow with hover effect
✅ **Flexbox**: inline-flex with gap for icon spacing
✅ **Text**: Removed "+" prefix (icon replaces it)

**Visual Comparison:**

```
BEFORE:
┌─────────────────┐
│  + Upload Files │  Plain blue, no icon
└─────────────────┘

AFTER:
┌──────────────────────┐
│ ➕ Upload Files     │  Indigo with icon & shadow
└──────────────────────┘

AFTER (when open):
┌──────────────────────┐
│ ✖️  Hide Uploader    │  Different icon when open
└──────────────────────┘
```

---

## Technical Details

### One-Way vs Two-Way Binding

**Two-Way Binding (v-model):**
```vue
<!-- Parent can send data, child can modify it -->
<Component v-model="data" />

<!-- Expands to: -->
<Component 
  :model-value="data" 
  @update:model-value="data = $event" 
/>
```

**When to use v-model:**
- Child needs to modify parent data directly
- Data is simple (not computed/transformed)
- Parent owns the data source

**One-Way Binding (:model-value):**
```vue
<!-- Parent sends data, child only reads it -->
<Component :model-value="computedData" />

<!-- Child cannot modify parent data -->
<!-- Must use explicit events for changes -->
```

**When to use :model-value:**
- Data is computed/transformed
- Data comes from props
- Need explicit control over updates
- Multiple data sources

### Our Case

**Module Documents:**
```typescript
// Props from parent
const props = defineProps<{
  documents?: ModuleDocument[];  // Nested structure
  moduleId: number;
}>();

// Transform to flat structure
const normalizedDocuments = computed(() => {
  return props.documents.map(doc => ({
    id: doc.document?.id,
    name: doc.document?.name,
    // ... more transformations
  }));
});

// ❌ WRONG: v-model tries to write to computed
// ✅ RIGHT: :model-value only reads
```

**Lesson Documents:**
```typescript
// Reactive array from props
const localLessons = reactive([...props.lessons]);

// Each lesson has documents array
lesson.documents = [...];

// ❌ WRONG: v-model tries to mutate reactive
// ✅ RIGHT: :model-value + events for updates
```

### Data Flow

```
┌─────────────────────────────────────────┐
│           Backend (Laravel)              │
│  CourseService → ModuleDocuments        │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│      Parent Component (Inertia)         │
│  props.documents (nested structure)     │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│   ModuleDocumentsSection.vue            │
│  normalizedDocuments = computed(...)    │  ← Transform here
└──────────────────┬──────────────────────┘
                   │
                   ▼ :model-value (READ ONLY)
┌─────────────────────────────────────────┐
│    RelatedDocumentContainer.vue         │
│  Display documents                      │
│  Handle upload/delete                   │
└──────────────────┬──────────────────────┘
                   │
                   ▼ @document-uploaded
┌─────────────────────────────────────────┐
│   ModuleDocumentsSection.vue            │
│  handleDocumentUploaded()               │
│  router.reload({ only: ['courses'] })   │  ← Refetch from backend
└─────────────────────────────────────────┘
```

## Theme Colors Reference

### System Theme Colors

```css
/* Primary Colors */
bg-indigo-600      /* Main action buttons */
bg-indigo-700      /* Hover state */
dark:bg-indigo-500 /* Dark mode primary */
dark:bg-indigo-600 /* Dark mode hover */

/* Module Colors */
bg-purple-600      /* Module-specific actions */
bg-purple-700      /* Module hover */

/* Lesson Colors */
bg-green-600       /* Lesson-specific actions */
bg-green-700       /* Lesson hover */

/* Activity Colors */
bg-blue-600        /* Activity-specific actions */
bg-blue-700        /* Activity hover */

/* General UI */
bg-gray-50         /* Light backgrounds */
bg-gray-100        /* Cards */
dark:bg-gray-700   /* Dark backgrounds */
dark:bg-gray-800   /* Dark cards */
```

### Updated Button Styling

```vue
<button class="
  inline-flex          <!-- Flexbox for icon + text -->
  items-center         <!-- Vertical center -->
  gap-2               <!-- Space between icon and text -->
  px-4 py-2           <!-- Comfortable padding -->
  bg-indigo-600       <!-- System primary color -->
  dark:bg-indigo-500  <!-- Lighter in dark mode -->
  text-white          <!-- White text -->
  text-sm             <!-- Small text size -->
  font-medium         <!-- Medium weight -->
  rounded-lg          <!-- Larger radius -->
  hover:bg-indigo-700 <!-- Darker on hover -->
  dark:hover:bg-indigo-600  <!-- Hover in dark mode -->
  transition-colors   <!-- Smooth color change -->
  shadow-sm           <!-- Subtle shadow -->
  hover:shadow-md     <!-- More shadow on hover -->
">
  <svg>...</svg>      <!-- Icon -->
  Upload Files        <!-- Text -->
</button>
```

## Testing Results

### Before Fix
- ❌ Upload document → other sections lose documents
- ❌ Button doesn't match system theme
- ❌ No icon on button
- ❌ Poor dark mode support

### After Fix
- ✅ Upload document → all sections keep their documents
- ✅ Button matches system indigo theme
- ✅ Dynamic icon (plus/close)
- ✅ Full dark mode support
- ✅ Better shadows and hover effects
- ✅ Consistent with other action buttons

## Files Modified

1. **RelatedDocumentContainer.vue**
   - Updated button styling to match system theme
   - Added dynamic SVG icons (plus/close)
   - Improved spacing and shadows

2. **ModuleDocumentsSection.vue**
   - Changed `v-model` to `:model-value`
   - Fixed reactivity issues

3. **lessonList.vue**
   - Changed `v-model` to `:model-value`
   - Fixed document persistence

## Summary

### Data Flow Fix
- Changed from **two-way binding** (v-model) to **one-way binding** (:model-value)
- Documents now persist correctly after upload/delete operations
- Proper separation of concerns: parent owns data, child displays it

### Theme Fix
- Button now uses **system indigo** colors
- Added **dynamic icons** (plus when closed, X when open)
- Improved **spacing, shadows, and hover effects**
- Full **dark mode optimization**
- Consistent with rest of application design

Both fixes ensure a more stable, visually consistent, and professional document management system! ✨
