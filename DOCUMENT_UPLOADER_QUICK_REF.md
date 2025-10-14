# DocumentUploader - Quick Reference

## 📦 Import

```typescript
import DocumentUploader from '@/document/DocumentUploader.vue';
// or
import { DocumentUploader } from '@/document';
```

## 🚀 Basic Usage

```vue
<DocumentUploader
  model-type="course"
  :foreign-key-id="123"
  @upload-success="handleSuccess"
/>
```

## 🎯 Common Scenarios

### Course Materials
```vue
<DocumentUploader
  model-type="course"
  :foreign-key-id="courseId"
  :max-file-size="20"
  accepted-types=".pdf,.ppt,.doc"
  visibility="students"
/>
```

### Activity Instructions
```vue
<DocumentUploader
  model-type="activity"
  :foreign-key-id="activityId"
  :multiple="false"
  :is-required="true"
/>
```

### Student Submission
```vue
<DocumentUploader
  model-type="project"
  :foreign-key-id="activityId"
  :auto-upload="true"
  :max-file-size="10"
/>
```

### Images Only
```vue
<DocumentUploader
  model-type="lesson"
  :foreign-key-id="lessonId"
  accepted-types=".jpg,.png,.gif"
  :max-file-size="5"
/>
```

## 📋 Props Reference

| Prop | Type | Default | Required |
|------|------|---------|----------|
| `model-type` | `'course' \| 'activity' \| 'lesson'` | - | ✅ |
| `foreign-key-id` | `number` | - | ✅ |
| `foreign-key-name` | `string` | auto | ❌ |
| `upload-url` | `string` | `/api/documents/upload` | ❌ |
| `multiple` | `boolean` | `true` | ❌ |
| `max-file-size` | `number` | `10` (MB) | ❌ |
| `accepted-types` | `string` | `.pdf,.doc,...` | ❌ |
| `auto-upload` | `boolean` | `false` | ❌ |
| `visibility` | `'public' \| 'students'` | `'students'` | ❌ |
| `is-required` | `boolean` | `false` | ❌ |

## 🎪 Events

```typescript
// Upload successful
@upload-success="(files: File[]) => { }"

// Upload failed
@upload-error="(error: string) => { }"

// Files selected (before upload)
@files-selected="(files: File[]) => { }"
```

## 🎨 File Type Presets

```typescript
// Documents only
accepted-types=".pdf,.doc,.docx"

// Presentations
accepted-types=".ppt,.pptx,.pdf"

// Images only
accepted-types=".jpg,.jpeg,.png,.gif"

// Spreadsheets
accepted-types=".xls,.xlsx,.csv"

// All files
accepted-types="*"
```

## 🔧 Backend Data

The component sends this FormData:

```php
[
    'model_type' => 'course',
    'foreign_key_id' => 123,
    'foreign_key_name' => 'course_id',
    'visibility' => 'students',
    'is_required' => false,
    'files' => [File, File, ...]
]
```

## 💾 Controller Example

```php
public function upload(Request $request)
{
    $request->validate([
        'files.*' => 'required|file|max:10240',
    ]);

    foreach ($request->file('files') as $file) {
        $path = $file->store('documents', 'public');
        
        $document = Document::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->extension(),
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

## 🛣️ Required Routes

```php
// Upload
POST /api/documents/upload

// Download
GET /documents/{id}/download

// Delete
DELETE /documents/{id}
```

## ✅ Validation Rules

- File size: Up to `maxFileSize` MB
- File type: Must match `acceptedTypes`
- Multiple: Depends on `multiple` prop

## 🎯 Complete Example

```vue
<template>
  <div>
    <h3>Upload Course Documents</h3>
    
    <DocumentUploader
      model-type="course"
      :foreign-key-id="course.id"
      :multiple="true"
      :max-file-size="15"
      accepted-types=".pdf,.doc,.docx,.ppt,.pptx"
      upload-url="/api/courses/documents/upload"
      visibility="students"
      :is-required="false"
      @upload-success="onSuccess"
      @upload-error="onError"
    />
    
    <div v-if="message" class="mt-4">
      {{ message }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import DocumentUploader from '@/document/DocumentUploader.vue';

const course = { id: 123, title: 'Math 101' };
const message = ref('');

const onSuccess = (files: File[]) => {
  message.value = `Uploaded ${files.length} file(s)!`;
  router.reload({ only: ['documents'] });
};

const onError = (error: string) => {
  message.value = `Error: ${error}`;
};
</script>
```

## 📚 See Also

- `DOCUMENT_UPLOADER_GUIDE.md` - Full documentation
- `DOCUMENT_UPLOAD_IMPLEMENTATION.md` - Implementation summary
- `CourseFileUploadExample.vue` - Complete working example
- `types.ts` - TypeScript definitions

## 🐛 Common Issues

### Files not uploading
- Check `upload-url` is correct
- Verify backend route exists
- Check file size limits
- Verify CSRF token

### Validation errors
- Ensure file types match `accepted-types`
- Check file size < `max-file-size` MB
- Verify backend validation matches

### Progress not showing
- Check browser console for errors
- Verify Inertia is properly configured
- Ensure `onProgress` callback works

## 💡 Tips

✅ Always validate on backend too
✅ Use appropriate file size limits
✅ Show clear error messages
✅ Provide upload progress
✅ Handle errors gracefully
✅ Test with large files
✅ Test with multiple files
✅ Test drag and drop
