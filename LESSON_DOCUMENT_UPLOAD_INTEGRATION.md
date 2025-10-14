# Lesson Document Upload Integration

## Overview

Successfully integrated the **DocumentUploader** component into lesson documents, replacing the old `RelatedDocumentContainer` with a modern, embedded document upload and display system.

## Changes Made

### 1. Backend - Lesson Model (`app/Models/Lesson.php`)

**Updated document relationships** to use the pivot model pattern (same as Module):

**Before**:
```php
public function documents(): BelongsToMany
{
    return $this->belongsToMany(Document::class, 'lesson_document');
}
```

**After**:
```php
// Lesson documents relationship - returns LessonDocument pivot records
public function documents()
{
    return $this->hasMany(LessonDocument::class);
}

// Get all document files through LessonDocument
public function documentFiles()
{
    return $this->hasManyThrough(Document::class, LessonDocument::class, 'lesson_id', 'id', 'id', 'document_id');
}
```

**Benefits**:
- Uses `LessonDocument` pivot model for better control
- Supports visibility and is_required fields
- Consistent with `ModuleDocument` pattern
- Better data structure for frontend

### 2. Backend - ModuleController (`app/Http/Controllers/ModuleController.php`)

**Updated `index()` method** to return full document details:

**Before**:
```php
public function index(\App\Models\Module $module)
{
    $lessons = $module->lessons()->with('documents')->get();
    return response()->json($lessons);
}
```

**After**:
```php
public function index(\App\Models\Module $module)
{
    // eager load documents with full details and uploader info
    $lessons = $module->lessons()->with(['documents.document.uploader'])->get();

    // Map lessons to include formatted document data
    $lessons = $lessons->map(function ($lesson) {
        $documents = $lesson->documents->map(function ($lessonDoc) {
            $doc = $lessonDoc->document;
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'original_name' => $doc->original_name,
                'file_path' => $doc->file_path,
                'file_size' => $doc->file_size,
                'file_size_human' => $doc->file_size_human,
                'mime_type' => $doc->mime_type,
                'extension' => $doc->extension,
                'document_type' => $doc->document_type,
                'doc_type' => $doc->document_type, // Legacy field
                'uploaded_by' => $doc->uploader ? $doc->uploader->name : 'Unknown',
                'visibility' => $lessonDoc->visibility,
                'is_required' => $lessonDoc->is_required,
            ];
        });

        return [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'description' => $lesson->description,
            'order' => $lesson->order,
            'duration' => $lesson->duration,
            'content_type' => $lesson->content_type,
            'created_at' => $lesson->created_at,
            'updated_at' => $lesson->updated_at,
            'documents' => $documents,
        ];
    });

    return response()->json($lessons);
}
```

**What This Does**:
- Loads documents with full relationship chain
- Includes uploader information
- Returns human-readable file sizes
- Includes visibility and required status
- Provides all document metadata needed by frontend

### 3. Frontend - lessonList.vue

**Completely redesigned the documents section**:

#### Replaced Component

**Before**:
```vue
<RelatedDocumentContainer v-model="lesson.documents" />
```

**After**:
```vue
<!-- Header with Upload Button -->
<div class="flex items-center justify-between mb-3">
  <div class="flex items-center gap-2">
    <svg>...</svg>
    <h4>Related Documents</h4>
    <span v-if="lesson.documents && lesson.documents.length > 0">
      ({{ lesson.documents.length }})
    </span>
  </div>
  <button @click="toggleUploader(lesson.id)">
    {{ showUploader[lesson.id] ? 'Hide Uploader' : '+ Upload Files' }}
  </button>
</div>

<!-- Embedded Document Uploader -->
<div v-if="showUploader[lesson.id]">
  <DocumentUploader
    model-type="lesson"
    :foreign-key-id="lesson.id"
    :multiple="true"
    @upload-success="handleUploadSuccess(lesson.id)"
    @upload-error="handleUploadError"
  />
</div>

<!-- Document List -->
<div v-if="lesson.documents && lesson.documents.length > 0">
  <a v-for="doc in lesson.documents" :href="`/documents/${doc.id}/download`">
    <!-- Document Card with metadata -->
  </a>
</div>

<!-- Empty State -->
<div v-else>
  <p>No documents uploaded yet</p>
</div>
```

#### Updated Script Section

**Added**:
- `DocumentUploader` component import
- `LessonDocument` interface with proper types
- `showUploader` reactive record for per-lesson toggling
- `toggleUploader()` function
- `handleUploadSuccess()` function with auto-refresh
- `handleUploadError()` function

**TypeScript Interface**:
```typescript
interface LessonDocument {
  id: number;
  name: string;
  file_path: string;
  doc_type: string;
  file_size_human?: string;
  extension?: string;
}
```

## Features Added

### Per-Lesson Upload Controls
- Each lesson has its own upload button
- Toggle uploader visibility per lesson
- Upload button shows "Hide Uploader" when open

### Document Display
- Document count badge: "Related Documents (3)"
- Document cards with:
  - Document name
  - File size (human-readable)
  - File extension (PDF, DOCX, etc.)
  - Document type
  - Download button
- Green theme matching lesson color scheme
- Hover effects on document cards
- Dark mode support

### Upload Integration
- Uses centralized `DocumentUploader` component
- Automatic refresh after successful upload
- Error handling with user feedback
- Supports multiple file upload
- File type validation
- Size limits (20MB default)

### Empty State
- Friendly message when no documents
- Icon-based visual
- Clear call-to-action

## User Experience

### Instructor Workflow

1. **View Lessons** in Course Management
2. **See lesson documents** under each lesson
3. **Click "+ Upload Files"** to show uploader
4. **Upload documents** via drag-drop or file picker
5. **Documents appear immediately** after upload
6. **Download any document** by clicking on it

### Visual Design

```
┌─────────────────────────────────────────────────┐
│ 📄 Related Documents (2)        [+ Upload Files]│
├─────────────────────────────────────────────────┤
│ 📄 Lesson 1 Notes.pdf             [Download]    │
│ 2.5 MB • PDF • syllabus                         │
├─────────────────────────────────────────────────┤
│ 📄 Assignment.docx                [Download]    │
│ 1.8 MB • DOCX • assignment                      │
└─────────────────────────────────────────────────┘
```

When Upload is shown:
```
┌─────────────────────────────────────────────────┐
│ 📄 Related Documents (2)        [Hide Uploader] │
├─────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────┐ │
│ │   Drop files here or click to browse        │ │
│ │   [Select Files]                            │ │
│ └─────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────┤
│ 📄 Lesson 1 Notes.pdf             [Download]    │
│ 2.5 MB • PDF • syllabus                         │
└─────────────────────────────────────────────────┘
```

## Data Flow

```
User clicks "+ Upload Files"
  └─> toggleUploader(lessonId)
      └─> showUploader[lessonId] = true
          └─> DocumentUploader appears
              └─> User uploads files
                  └─> POST /api/documents/upload
                      └─> DocumentController@upload
                          └─> Creates Document record
                          └─> Creates LessonDocument record
                              └─> handleUploadSuccess(lessonId)
                                  └─> refreshLessons()
                                      └─> GET /modules/{id}/lessons
                                          └─> ModuleController@index
                                              └─> Returns lessons with documents
                                                  └─> Frontend displays updated list
```

## Comparison: Before vs After

### Before
- Used `RelatedDocumentContainer` component
- Two-step process: toggle, then upload
- Limited document metadata display
- Separate component for display and upload
- Less intuitive UI

### After
- Embedded `DocumentUploader` directly in lesson
- One-click upload access per lesson
- Rich document metadata (size, type, uploader)
- Unified upload and display interface
- Cleaner, more intuitive UI
- Consistent with module documents

## Testing

### Test Document Upload

1. **Open Course Management**
2. **Select a module** with lessons
3. **Click "+ Upload Files"** on any lesson
4. **Upload a document** (PDF, DOCX, etc.)
5. **Verify**:
   - Uploader closes automatically
   - Document appears in list
   - Document count updates
   - Metadata displays correctly
   - Download button works

### Test Multiple Lessons

1. **Upload documents to multiple lessons**
2. **Verify each lesson** shows its own documents
3. **Check that uploaders** toggle independently
4. **Confirm documents** don't mix between lessons

### Test Backend

```bash
php artisan tinker
```

```php
// Get a lesson with documents
$lesson = \App\Models\Lesson::with('documents.document.uploader')->first();

echo "Lesson: {$lesson->title}\n";
echo "Documents: {$lesson->documents->count()}\n";

$lesson->documents->each(function($lessonDoc) {
    $doc = $lessonDoc->document;
    echo "  - {$doc->name} ({$doc->file_size_human})\n";
    echo "    Type: {$doc->extension}\n";
    echo "    Uploaded by: {$doc->uploader->name}\n";
});
```

## Files Modified

1. **Backend**:
   - `app/Models/Lesson.php` - Updated document relationships
   - `app/Http/Controllers/ModuleController.php` - Enhanced index method with full document data

2. **Frontend**:
   - `resources/js/lesson/lessonList.vue` - Replaced RelatedDocumentContainer with DocumentUploader

## Related Documentation

- `MODULE_DOCUMENT_IMPLEMENTATION.md` - ModuleDocument model
- `MODULE_DOCUMENTS_SECTION_IMPLEMENTATION.md` - Module documents display
- `DOCUMENT_UPLOADER_GUIDE.md` - DocumentUploader component
- `MODULE_UPLOAD_FIX.md` - Document upload fixes

## Benefits

✅ **Consistent Pattern** - Lessons now use same document system as modules
✅ **Better UX** - Embedded upload directly in lesson view
✅ **Rich Metadata** - Shows file size, type, uploader information
✅ **Centralized Upload** - Uses same DocumentController for all uploads
✅ **Type Safety** - Full TypeScript interfaces
✅ **Auto Refresh** - Document list updates after upload
✅ **Dark Mode** - Full dark mode support
✅ **Per-Lesson Control** - Each lesson has independent uploader toggle
✅ **Empty States** - Clear messaging when no documents
✅ **Error Handling** - User-friendly error messages

## Summary

Successfully migrated lesson documents from the old `RelatedDocumentContainer` to the new embedded `DocumentUploader` component:

- ✅ Updated Lesson model to use `LessonDocument` pivot pattern
- ✅ Enhanced ModuleController to return full document details
- ✅ Embedded DocumentUploader directly in lesson view
- ✅ Added per-lesson upload controls
- ✅ Implemented auto-refresh on upload
- ✅ Improved document display with rich metadata
- ✅ Consistent with module document pattern

Instructors can now upload and manage lesson documents with a modern, intuitive interface that matches the module document experience!
