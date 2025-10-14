# Module Document Upload - Debugging Guide

## Issue
ModuleDocument instances are not being created in the database when uploading files with `model-type="module"`.

## Fixes Applied

### 1. ✅ Updated TypeScript Types

**DocumentUploader.vue**
```typescript
// Added 'module' to modelType
modelType: 'course' | 'activity' | 'lesson' | 'module' | 'report' | 'project' | 'assessment'
```

**RelatedDocumentContainer.vue**
```typescript
// Added 'module' to modelType
modelType: 'course' | 'activity' | 'lesson' | 'module' | 'report' | 'project' | 'assessment'
```

**types.ts**
```typescript
// Added 'module' to ModelType
export type ModelType = 'course' | 'activity' | 'lesson' | 'module' | 'report' | 'project' | 'assessment' | 'student' | 'instructor';

// Added ModuleDocument interface
export interface ModuleDocument { ... }
```

### 2. ✅ Enhanced DocumentController

Added logging and error handling to `createDocumentRelationship`:

```php
private function createDocumentRelationship(...)
{
    // ... existing code ...
    
    try {
        $data = [
            'document_id' => $documentId,
            $foreignKeyName => $foreignKeyId,
            'visibility' => $visibility,
            'is_required' => $isRequired,
        ];
        
        // Add order field for models that support it
        if (in_array($modelType, ['course', 'activity', 'lesson', 'module'])) {
            $data['order'] = 0;
        }
        
        $result = $modelClass::create($data);
        
        \Log::info('Document relationship created', [
            'model_type' => $modelType,
            'model_class' => $modelClass,
            'document_id' => $documentId,
            'foreign_key' => $foreignKeyName,
            'foreign_key_value' => $foreignKeyId,
            'result' => $result->toArray(),
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to create document relationship', [
            'model_type' => $modelType,
            'error' => $e->getMessage(),
        ]);
        throw $e;
    }
}
```

## How to Test

### Test 1: Upload via DocumentUploader

```vue
<template>
  <DocumentUploader
    model-type="module"
    :foreign-key-id="123"
    @upload-success="handleSuccess"
  />
</template>
```

### Test 2: Upload via RelatedDocumentContainer

```vue
<template>
  <RelatedDocumentContainer
    model-type="module"
    :foreign-key-id="module.id"
    :model-value="[]"
  />
</template>
```

### Test 3: Check the logs

After uploading, check Laravel logs:

```bash
tail -f storage/logs/laravel.log
```

Look for:
```
[timestamp] local.INFO: Document relationship created 
{
    "model_type": "module",
    "model_class": "App\\Models\\ModuleDocument",
    "document_id": 1,
    "foreign_key": "module_id",
    "foreign_key_value": 123,
    "result": {...}
}
```

Or errors:
```
[timestamp] local.ERROR: Failed to create document relationship
{
    "model_type": "module",
    "error": "..."
}
```

### Test 4: Check Database

```bash
php artisan tinker
```

```php
// Check if document was created
\App\Models\Document::latest()->first();

// Check if ModuleDocument was created
\App\Models\ModuleDocument::latest()->first();

// Check specific module's documents
$module = \App\Models\Module::find(1);
$module->documents; // ModuleDocument records
$module->documentFiles; // Document records directly
```

## Debugging Steps

### 1. Check Request Data

Add temporary logging to DocumentController upload method:

```php
public function upload(Request $request)
{
    \Log::info('Upload request received', [
        'model_type' => $request->model_type,
        'foreign_key_id' => $request->foreign_key_id,
        'foreign_key_name' => $request->foreign_key_name,
        'files_count' => count($request->file('files')),
    ]);
    
    // ... rest of method
}
```

### 2. Check Frontend Data

Add console logging in DocumentUploader:

```typescript
const uploadFiles = async () => {
  console.log('Uploading with:', {
    modelType: props.modelType,
    foreignKeyId: props.foreignKeyId,
    foreignKeyName: fkName.value,
  });
  
  // ... rest of method
}
```

### 3. Verify Foreign Key

Make sure the module ID exists:

```php
// In tinker
\App\Models\Module::find(123); // Replace 123 with your module ID
```

### 4. Check Validation

The upload validation requires:
```php
'model_type' => 'required|in:course,activity,lesson,module,report,project,assessment',
```

Make sure 'module' is included (✅ it is now).

### 5. Check Database Table

```bash
php artisan db:table module_documents
```

Verify the table exists with correct columns:
- id
- document_id (FK to documents)
- module_id (FK to modules)
- visibility
- is_required
- order
- timestamps

## Common Issues & Solutions

### Issue 1: "model_type validation failed"
**Cause**: Frontend sending incorrect model type
**Solution**: ✅ Fixed - Added 'module' to all type definitions

### Issue 2: "Foreign key constraint violation"
**Cause**: Module ID doesn't exist
**Solution**: Verify module exists before uploading

### Issue 3: "Unknown column 'order'"
**Cause**: Migration not run or incomplete
**Solution**: ✅ Fixed - Added order field to data array

### Issue 4: "ModuleDocument not created but Document is"
**Cause**: Error in createDocumentRelationship silently failing
**Solution**: ✅ Fixed - Added try-catch with logging

## Verification Checklist

After upload, verify:

- [ ] Document created in `documents` table
- [ ] File stored in `storage/app/public/documents/module/`
- [ ] ModuleDocument created in `module_documents` table
- [ ] `module_documents.document_id` matches document ID
- [ ] `module_documents.module_id` matches your module ID
- [ ] `module_documents.visibility` is set correctly
- [ ] `module_documents.is_required` is set correctly
- [ ] `module_documents.order` is set to 0
- [ ] No errors in `storage/logs/laravel.log`

## Quick Test Script

Create a test route (temporary):

```php
// routes/web.php
Route::get('/test-module-document', function () {
    $module = \App\Models\Module::first();
    
    if (!$module) {
        return 'No modules found';
    }
    
    return view('test-upload', ['module' => $module]);
});
```

Create view `resources/views/test-upload.blade.php`:

```html
<!DOCTYPE html>
<html>
<body>
    <h1>Test Module Document Upload</h1>
    <p>Module: {{ $module->title }}</p>
    
    <form action="/api/documents/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="model_type" value="module">
        <input type="hidden" name="foreign_key_id" value="{{ $module->id }}">
        <input type="hidden" name="foreign_key_name" value="module_id">
        <input type="hidden" name="visibility" value="students">
        <input type="hidden" name="is_required" value="0">
        
        <input type="file" name="files[0]" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
```

Visit `/test-module-document` and try uploading.

## Expected Log Output (Success)

```
[2025-10-15 00:00:00] local.INFO: Upload request received
{
    "model_type": "module",
    "foreign_key_id": 1,
    "foreign_key_name": "module_id",
    "files_count": 1
}

[2025-10-15 00:00:01] local.INFO: Document relationship created
{
    "model_type": "module",
    "model_class": "App\\Models\\ModuleDocument",
    "document_id": 5,
    "foreign_key": "module_id",
    "foreign_key_value": 1,
    "result": {
        "document_id": 5,
        "module_id": 1,
        "visibility": "students",
        "is_required": false,
        "order": 0,
        "updated_at": "2025-10-15T00:00:01.000000Z",
        "created_at": "2025-10-15T00:00:01.000000Z",
        "id": 1
    }
}
```

## Next Steps

If ModuleDocument is still not being created:

1. Check the Laravel logs for errors
2. Run the test upload form to isolate frontend vs backend issues
3. Verify the module ID is correct and exists
4. Check database constraints and foreign keys
5. Test with a simple PHP script to rule out code issues

## Files Modified

1. ✅ `resources/js/document/DocumentUploader.vue` - Added 'module' type
2. ✅ `resources/js/course/RelatedDocumentContainer.vue` - Added 'module' type
3. ✅ `resources/js/document/types.ts` - Added 'module' and ModuleDocument interface
4. ✅ `app/Http/Controllers/DocumentController.php` - Added logging and order field

All changes are backward compatible and won't affect existing uploads for courses, activities, or lessons.
