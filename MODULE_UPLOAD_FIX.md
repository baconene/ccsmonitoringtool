# Module Document Upload Fix

## Problem Discovered

After checking the Laravel logs, I found that **module document uploads were not working** because they were using a **different upload route** than other document types.

### Root Cause

The application had **TWO separate upload systems**:

1. **DocumentUploader.vue** (New component)
   - Route: `/api/documents/upload`
   - Controller: `DocumentController@upload`
   - Status: ✅ Works correctly with full logging

2. **UploadDocumentModal.vue** (Old module uploader)
   - Route: `/modules/{module}/documents`
   - Controller: `ModuleController@uploadDocuments`
   - Status: ❌ Failing with NOT NULL constraint error

### Error in Laravel Logs

```
[2025-10-15 00:41:20] local.ERROR: SQLSTATE[23000]: Integrity constraint violation: 19 
NOT NULL constraint failed: documents.original_name (Connection: sqlite, SQL: insert into 
"documents" ("name", "file_path", "uploaded_by", "updated_at", "created_at") values 
(ESC-0037_Payslip_2025JUL.pdf, module-documents/E7XAG8ovqnatU47Trk3eYrF28hAuNsWHRpispzky.pdf, 
4, 2025-10-15 00:41:20, 2025-10-15 00:41:20))
```

**Issue**: The `ModuleController@uploadDocuments` method was only inserting `name`, `file_path`, and `uploaded_by`, but was missing required fields like `original_name`, `file_size`, `mime_type`, `extension`, and `document_type`.

### Why ModuleDocument Was Not Created

The ModuleDocument was never created because:
1. The Document creation itself failed first (NOT NULL constraint)
2. The transaction rolled back before reaching the ModuleDocument creation code
3. No logging was in place to show the actual error

## Solution Implemented

### Changed UploadDocumentModal.vue to Use DocumentController

**File**: `resources/js/module/UploadDocumentModal.vue`

**Before**:
```typescript
router.post(`/modules/${props.moduleId}/documents`, formData, {
```

**After**:
```typescript
// Add required fields for DocumentController
formData.append('model_type', 'module');
formData.append('model_id', String(props.moduleId));
formData.append('visibility', 'students');
formData.append('is_required', 'false');

router.post(`/api/documents/upload`, formData, {
```

### Benefits

1. ✅ **Uses centralized upload logic** - All document types now use the same controller
2. ✅ **Includes all required fields** - No more NOT NULL constraint errors
3. ✅ **Full logging enabled** - Can see exactly what happens during upload
4. ✅ **Automatic ModuleDocument creation** - The enhanced `createDocumentRelationship` method handles it
5. ✅ **Consistent validation** - Same validation rules for all document types
6. ✅ **Better error handling** - Try-catch blocks and proper error messages

## Testing

### Test the Module Upload

1. **Try uploading a document to a module** through the UI

2. **Check Laravel logs** for success/error:
```powershell
Get-Content storage/logs/laravel.log -Tail 50
```

3. **Expected Success Log**:
```
[2025-10-15 XX:XX:XX] local.INFO: Document relationship created  
{
  "model_type": "module",
  "model_class": "App\\Models\\ModuleDocument",
  "document_id": 123,
  "foreign_key": "module_id",
  "foreign_key_value": 1,
  "result": {
    "id": 1,
    "document_id": 123,
    "module_id": 1,
    "visibility": "students",
    "is_required": false,
    "order": 0,
    "created_at": "2025-10-15...",
    "updated_at": "2025-10-15..."
  }
}
```

4. **Verify in database**:
```bash
php artisan tinker
# Check Document was created
\DB::table('documents')->latest('id')->first()

# Check ModuleDocument was created
\DB::table('module_documents')->latest('id')->first()
```

## Files Changed

### Modified
- `resources/js/module/UploadDocumentModal.vue` - Changed to use DocumentController endpoint

### Previously Enhanced (Ready to use)
- `app/Http/Controllers/DocumentController.php` - Already has full logging
- `app/Models/ModuleDocument.php` - Already created
- `resources/js/document/types.ts` - Already includes 'module' type
- `resources/js/document/DocumentUploader.vue` - Already supports 'module' type

## Optional: Remove Old Route

Since module uploads now use the centralized endpoint, you can optionally remove the old route:

**File**: `routes/web.php` (line 355)

```php
// This route can be removed (no longer needed):
Route::post('/modules/{module}/documents', [ModuleController::class, 'uploadDocuments'])
    ->name('modules.documents.upload');
```

And remove the `uploadDocuments` method from `ModuleController.php`.

## Summary

The module document upload issue was caused by using an outdated upload route that went through `ModuleController` instead of the new `DocumentController`. The `ModuleController` method was missing required database fields, causing NOT NULL constraint violations.

By switching to the centralized `DocumentController@upload` endpoint, module uploads now:
- Include all required database fields
- Create both Document and ModuleDocument records
- Have full logging for debugging
- Follow the same pattern as all other document types

**Next Step**: Try uploading a document to a module and verify it works!
