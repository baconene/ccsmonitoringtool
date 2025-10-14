# DocumentUploader Component

A dynamic Vue component for file uploads that integrates with the document management system.

## Features

- **Drag and Drop**: Drag files directly onto the upload area
- **Multiple File Upload**: Support for single or multiple files
- **File Validation**: Size and type validation
- **File Preview**: Visual preview with file icons
- **Progress Tracking**: Real-time upload progress
- **Dynamic Model Binding**: Works with any document model (Course, Activity, Lesson, etc.)
- **Error Handling**: Comprehensive error messages
- **Auto Upload**: Optional automatic upload on file selection

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `modelType` | `'course' \| 'activity' \| 'lesson' \| 'report' \| 'project' \| 'assessment'` | **Required** | The type of model to link documents to |
| `foreignKeyId` | `number` | **Required** | The ID of the parent record |
| `foreignKeyName` | `string` | Auto-generated | Custom foreign key name (e.g., 'course_id') |
| `uploadUrl` | `string` | `/api/documents/upload` | Backend upload endpoint |
| `multiple` | `boolean` | `true` | Allow multiple file selection |
| `maxFileSize` | `number` | `10` | Maximum file size in MB |
| `acceptedTypes` | `string` | `.pdf,.doc,.docx,...` | Accepted file extensions |
| `autoUpload` | `boolean` | `false` | Upload immediately after selection |
| `visibility` | `'public' \| 'students' \| 'instructors' \| 'private'` | `'students'` | Document visibility level |
| `isRequired` | `boolean` | `false` | Mark document as required |

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `upload-success` | `File[]` | Emitted when upload succeeds |
| `upload-error` | `string` | Emitted when upload fails |
| `files-selected` | `File[]` | Emitted when files are selected |

## Usage Examples

### Basic Usage (Course Documents)

```vue
<template>
  <div>
    <h3>Upload Course Materials</h3>
    <DocumentUploader
      model-type="course"
      :foreign-key-id="course.id"
      @upload-success="handleUploadSuccess"
      @upload-error="handleUploadError"
    />
  </div>
</template>

<script setup lang="ts">
import DocumentUploader from '@/document/DocumentUploader.vue';

const props = defineProps<{
  course: { id: number; title: string };
}>();

const handleUploadSuccess = (files: File[]) => {
  console.log('Uploaded files:', files);
  // Refresh course documents list
};

const handleUploadError = (error: string) => {
  console.error('Upload error:', error);
};
</script>
```

### Activity Assignment Upload

```vue
<template>
  <div>
    <h3>Upload Assignment Instructions</h3>
    <DocumentUploader
      model-type="activity"
      :foreign-key-id="activity.id"
      :multiple="false"
      accept-types=".pdf"
      visibility="students"
      :is-required="true"
      @upload-success="onUploadComplete"
    />
  </div>
</template>

<script setup lang="ts">
import DocumentUploader from '@/document/DocumentUploader.vue';

const props = defineProps<{
  activity: { id: number; title: string };
}>();

const onUploadComplete = () => {
  alert('Assignment instructions uploaded!');
};
</script>
```

### Image-Only Upload

```vue
<template>
  <DocumentUploader
    model-type="lesson"
    :foreign-key-id="lessonId"
    accept-types=".jpg,.jpeg,.png,.gif"
    :max-file-size="5"
    @upload-success="handleImageUpload"
  />
</template>

<script setup lang="ts">
import DocumentUploader from '@/document/DocumentUploader.vue';

const lessonId = ref(123);

const handleImageUpload = (files: File[]) => {
  console.log(`Uploaded ${files.length} images`);
};
</script>
```

### Auto-Upload with Custom URL

```vue
<template>
  <DocumentUploader
    model-type="project"
    :foreign-key-id="projectId"
    upload-url="/api/projects/submit"
    :auto-upload="true"
    :multiple="false"
    @upload-success="showSuccessMessage"
  />
</template>

<script setup lang="ts">
const projectId = ref(456);

const showSuccessMessage = () => {
  alert('Project submitted successfully!');
};
</script>
```

### With Custom Foreign Key Name

```vue
<template>
  <DocumentUploader
    model-type="assessment"
    :foreign-key-id="assessmentId"
    foreign-key-name="assessment_id"
    @files-selected="onFilesSelected"
  />
</template>

<script setup lang="ts">
const onFilesSelected = (files: File[]) => {
  console.log('Selected files:', files.map(f => f.name));
};
</script>
```

### Programmatic Upload

```vue
<template>
  <div>
    <DocumentUploader
      ref="uploader"
      model-type="course"
      :foreign-key-id="courseId"
      :auto-upload="false"
    />
    
    <button @click="triggerUpload">
      Upload Now
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const uploader = ref<InstanceType<typeof DocumentUploader> | null>(null);
const courseId = ref(789);

const triggerUpload = () => {
  uploader.value?.uploadFiles();
};
</script>
```

## Backend Integration

The component sends the following data to the backend:

```php
// FormData structure
[
    'model_type' => 'course',           // Model type
    'foreign_key_id' => 123,            // Parent record ID
    'foreign_key_name' => 'course_id',  // Foreign key column name
    'visibility' => 'students',         // Visibility level
    'is_required' => 0,                 // Required flag
    'files' => [                        // Array of files
        0 => UploadedFile,
        1 => UploadedFile,
    ]
]
```

### Example Controller

```php
// app/Http/Controllers/DocumentController.php
public function upload(Request $request)
{
    $request->validate([
        'model_type' => 'required|in:course,activity,lesson,report,project,assessment',
        'foreign_key_id' => 'required|integer',
        'foreign_key_name' => 'required|string',
        'files.*' => 'required|file|max:10240', // 10MB
    ]);

    $uploadedDocuments = [];

    foreach ($request->file('files') as $file) {
        // Store file
        $path = $file->store('documents', 'public');
        
        // Create base document
        $document = Document::create([
            'name' => $file->getClientOriginalName(),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'document_type' => $request->model_type,
            'uploaded_by' => auth()->id(),
        ]);

        // Link to specific model
        $modelClass = match($request->model_type) {
            'course' => CourseDocument::class,
            'activity' => ActivityDocument::class,
            'lesson' => LessonDocument::class,
            // ... other types
        };

        $modelClass::create([
            'document_id' => $document->id,
            $request->foreign_key_name => $request->foreign_key_id,
            'visibility' => $request->visibility,
            'is_required' => $request->is_required,
        ]);

        $uploadedDocuments[] = $document;
    }

    return back()->with('success', 'Documents uploaded successfully');
}
```

## File Type Icons

The component automatically displays appropriate icons for:
- **Images**: Blue image icon (JPG, PNG, GIF, etc.)
- **PDFs**: Red PDF icon
- **Other files**: Gray document icon

## Validation

The component validates:
1. **File size**: Must not exceed `maxFileSize` MB
2. **File type**: Must match one of the `acceptedTypes` extensions
3. **Multiple files**: Respects the `multiple` prop

## Styling

The component uses regular CSS (not Tailwind @apply) for compatibility with Tailwind CSS v4. You can customize the appearance by:

1. **Override CSS variables** in your parent component
2. **Use scoped styles** to target specific elements
3. **Pass custom classes** via slots (if extended)

## Best Practices

1. **Always validate on the backend** - Client-side validation can be bypassed
2. **Use appropriate file size limits** - Balance between user experience and server resources
3. **Restrict file types** - Only allow necessary file formats
4. **Handle errors gracefully** - Provide clear error messages
5. **Show upload progress** - Keep users informed during long uploads
6. **Clean up failed uploads** - Remove partially uploaded files

## Accessibility

The component includes:
- Keyboard navigation support (click to upload)
- Clear visual feedback for drag/drop states
- Error messages with icons
- Loading states during upload

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Requires JavaScript enabled
- Drag and drop supported in all modern browsers
