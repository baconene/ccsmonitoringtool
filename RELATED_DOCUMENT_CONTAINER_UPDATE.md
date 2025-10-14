# RelatedDocumentContainer - Updated Implementation

## ✅ Successfully Integrated DocumentUploader

The `RelatedDocumentContainer.vue` component has been updated to use the new DocumentUploader system!

## What Changed

### Before
- Simple button to add placeholder documents
- Manual document entry
- No file upload functionality
- Simple array of `{ name, doc_type }`

### After
- **Full file upload functionality** with DocumentUploader
- **Drag and drop support**
- **File validation** (size, type)
- **Upload progress tracking**
- **Success/error messages**
- **Delete functionality** with server integration
- **Real document storage**

## Updated Props

```typescript
interface Props {
  // Required
  modelType: 'course' | 'activity' | 'lesson' | 'report' | 'project' | 'assessment';
  foreignKeyId: number;
  
  // Optional (documents data)
  modelValue?: DocumentData[];
  
  // Upload configuration
  maxFileSize?: number;              // Default: 20 MB
  acceptedTypes?: string;            // Default: common doc types
  visibility?: 'public' | 'students' | 'instructors' | 'private';  // Default: 'students'
  isRequired?: boolean;              // Default: false
}
```

## Events Emitted

```typescript
// When modelValue changes
@update:modelValue

// When files are successfully uploaded
@document-uploaded (files: File[])

// When a document is deleted
@document-deleted (documentId: number)
```

## Usage Examples

### 1. Course Management

```vue
<template>
  <div class="course-page">
    <h1>{{ course.title }}</h1>
    
    <!-- Course Documents Section -->
    <RelatedDocumentContainer
      model-type="course"
      :foreign-key-id="course.id"
      :model-value="courseDocuments"
      :max-file-size="20"
      accepted-types=".pdf,.doc,.docx,.ppt,.pptx"
      visibility="students"
      @document-uploaded="handleDocumentUploaded"
      @document-deleted="handleDocumentDeleted"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import RelatedDocumentContainer from '@/course/RelatedDocumentContainer.vue';

const props = defineProps<{
  course: {
    id: number;
    title: string;
    code: string;
  };
  courseDocuments: Array<{
    id: number;
    name: string;
    file_path: string;
    file_size_human: string;
    extension: string;
  }>;
}>();

const handleDocumentUploaded = (files: File[]) => {
  console.log('Uploaded:', files);
  // Additional actions if needed
};

const handleDocumentDeleted = (docId: number) => {
  console.log('Deleted document:', docId);
  // Additional actions if needed
};
</script>
```

### 2. Activity Management

```vue
<template>
  <RelatedDocumentContainer
    model-type="activity"
    :foreign-key-id="activity.id"
    :model-value="activityDocuments"
    :max-file-size="10"
    accepted-types=".pdf,.doc,.docx"
    :is-required="true"
  />
</template>

<script setup lang="ts">
const activity = { id: 123, title: 'Assignment 1' };
const activityDocuments = ref([]);
</script>
```

### 3. Lesson Resources

```vue
<template>
  <RelatedDocumentContainer
    model-type="lesson"
    :foreign-key-id="lesson.id"
    :model-value="lessonDocuments"
    accepted-types=".pdf,.ppt,.pptx,.jpg,.png"
    visibility="students"
  />
</template>
```

### 4. Images Only

```vue
<template>
  <RelatedDocumentContainer
    model-type="lesson"
    :foreign-key-id="lessonId"
    :model-value="images"
    :max-file-size="5"
    accepted-types=".jpg,.jpeg,.png,.gif"
  />
</template>
```

## Features

### 1. Upload Button
- Click "Upload Files" to show/hide the uploader
- Button toggles between "Upload Files" and "Hide Uploader"

### 2. File Upload Area
- Drag and drop files
- Click to browse files
- Supports multiple files
- Shows file preview before upload

### 3. Success/Error Messages
- Green success message when files upload
- Red error message on failure
- Auto-dismiss after 5 seconds

### 4. Document List
- Shows all uploaded documents
- Each document has a remove button
- Empty state when no documents

### 5. Delete Functionality
- Click remove button on any document
- Confirms before deleting
- Sends DELETE request to `/documents/{id}`
- Updates UI on success

## Backend Requirements

### 1. Upload Endpoint

```php
// POST /api/documents/upload
Route::post('/api/documents/upload', [DocumentController::class, 'upload']);
```

Controller method:
```php
public function upload(Request $request)
{
    $request->validate([
        'model_type' => 'required|in:course,activity,lesson,report,project,assessment',
        'foreign_key_id' => 'required|integer',
        'files.*' => 'required|file|max:20480',
    ]);

    foreach ($request->file('files') as $file) {
        $path = $file->store('documents', 'public');
        
        $document = Document::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'document_type' => $request->model_type,
            'uploaded_by' => auth()->id(),
        ]);

        CourseDocument::create([
            'document_id' => $document->id,
            'course_id' => $request->foreign_key_id,
            'visibility' => $request->visibility,
        ]);
    }

    return back();
}
```

### 2. Delete Endpoint

```php
// DELETE /documents/{document}
Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
```

Controller method:
```php
public function destroy(Document $document)
{
    // Check permissions
    if (auth()->id() !== $document->uploaded_by && !auth()->user()->isAdmin()) {
        abort(403);
    }
    
    $document->delete(); // Soft delete
    
    return back()->with('success', 'Document deleted');
}
```

### 3. Controller Example (Complete)

```php
// app/Http/Controllers/CourseController.php

public function show(Course $course)
{
    return Inertia::render('Courses/Show', [
        'course' => $course,
        'courseDocuments' => $course->documents()
            ->with('document')
            ->get()
            ->map(function ($courseDoc) {
                return [
                    'id' => $courseDoc->document->id,
                    'name' => $courseDoc->document->name,
                    'file_path' => $courseDoc->document->file_path,
                    'file_size_human' => $courseDoc->document->file_size_human,
                    'extension' => $courseDoc->document->extension,
                    'mime_type' => $courseDoc->document->mime_type,
                    'uploaded_by' => $courseDoc->document->uploader->name,
                    'created_at' => $courseDoc->document->created_at->format('M d, Y'),
                ];
            }),
    ]);
}
```

## Data Flow

```
1. User clicks "Upload Files"
   ↓
2. DocumentUploader component shown
   ↓
3. User selects/drops files
   ↓
4. Files validated (size, type)
   ↓
5. User clicks "Upload" (or auto-upload)
   ↓
6. FormData sent to /api/documents/upload
   ↓
7. Backend stores files
   ↓
8. Document & CourseDocument records created
   ↓
9. Success event emitted
   ↓
10. Page reloads documents
    ↓
11. Updated list displayed
```

## Styling

The component uses:
- Tailwind CSS classes
- Dark mode support
- Responsive design
- Smooth transitions

## Testing

### Test Upload
1. Navigate to page with RelatedDocumentContainer
2. Click "Upload Files"
3. Select a file
4. Verify upload works
5. Check file appears in list

### Test Delete
1. Click remove button on a document
2. Confirm deletion
3. Verify document removed from list
4. Check deleted on server

### Test Validation
1. Try uploading file > max size
2. Verify error message
3. Try wrong file type
4. Verify error message

## Migration from Old Version

If you have existing code using the old RelatedDocumentContainer:

### Old Usage
```vue
<RelatedDocumentContainer v-model="documents" />
```

### New Usage (Required Changes)
```vue
<RelatedDocumentContainer
  model-type="course"
  :foreign-key-id="course.id"
  v-model="documents"
/>
```

**Required props**: `modelType` and `foreignKeyId`

## Benefits

✅ Real file uploads (not just metadata)
✅ Drag and drop support
✅ File validation
✅ Progress tracking
✅ Error handling
✅ Server integration
✅ Delete functionality
✅ Success/error messages
✅ Dark mode support
✅ Responsive design

## Next Steps

1. ✅ Component updated
2. ✅ DocumentUploader integrated
3. ⬜ Create DocumentController
4. ⬜ Add routes for upload/delete
5. ⬜ Test in course management pages
6. ⬜ Add download functionality
7. ⬜ Add document preview

## Complete!

The RelatedDocumentContainer now has full file upload capabilities using the DocumentUploader component! 🎉
