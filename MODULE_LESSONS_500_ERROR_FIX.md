# Module Lessons 500 Error Fix

## Problem
When fetching lessons for a module (`GET /modules/{module}/lessons`), the application returned a 500 Internal Server Error with the message:
```
Attempt to read property "id" on null at ModuleController.php:24
```

## Root Cause
The `ModuleController::index()` method was attempting to access properties on `$lessonDoc->document` without checking if the document exists. This occurred when:
1. A lesson had a document relationship in the `lesson_document` pivot table
2. The actual document had been deleted from the `documents` table
3. The orphaned pivot relationship remained, causing `$lessonDoc->document` to be null

## Solution
Added a filter step before mapping lesson documents to exclude null documents:

### Before
```php
$documents = $lesson->documents->map(function ($lessonDoc) {
    $doc = $lessonDoc->document;
    return [
        'id' => $doc->id,  // ❌ Error when $doc is null
        // ...
    ];
});
```

### After
```php
$documents = $lesson->documents
    ->filter(function ($lessonDoc) {
        // Filter out documents that have been deleted
        return $lessonDoc->document !== null;
    })
    ->map(function ($lessonDoc) {
        $doc = $lessonDoc->document;
        return [
            'id' => $doc->id,  // ✅ Safe - $doc is never null
            // ...
        ];
    })
    ->values(); // Reset array keys after filtering
```

## Files Modified
- `app/Http/Controllers/ModuleController.php` (lines 19-38)

## Technical Details
- **Method**: `ModuleController::index()`
- **Location**: Line 19-38
- **Fix Type**: Defensive programming - null safety
- **Impact**: Prevents 500 errors when orphaned document relationships exist

## Benefits
1. **Stability**: No more 500 errors when fetching lessons with orphaned documents
2. **Data Integrity**: Gracefully handles database inconsistencies
3. **User Experience**: Lessons load successfully even with missing documents
4. **Consistency**: Aligns with frontend filtering in `validDocuments` computed property

## Related Issues
This fix complements the frontend document filtering implemented in:
- `RelatedDocumentContainer.vue` - `validDocuments` computed property
- Filters out "Untitled Document" entries on the frontend

Both frontend and backend now handle invalid/deleted documents gracefully.

## Testing
1. Navigate to Course Management
2. Click on a module that previously had lessons with deleted documents
3. Verify lessons load without 500 error
4. Verify only valid documents appear in lesson document lists

## Prevention
Consider implementing cascade delete on the `lesson_document` table to automatically remove pivot relationships when documents are deleted:

```php
// In the migration or model setup
$table->foreignId('document_id')
    ->constrained('documents')
    ->onDelete('cascade');
```

This would prevent orphaned relationships from occurring in the first place.
