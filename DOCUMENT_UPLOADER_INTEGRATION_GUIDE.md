# Integration Guide: Adding DocumentUploader to Course Management

## Step-by-Step Integration

### Step 1: Import the Component

In your course management file (e.g., `CourseManagement.vue` or `CourseShow.vue`):

```vue
<script setup lang="ts">
import DocumentUploader from '@/document/DocumentUploader.vue';
// ... other imports
</script>
```

### Step 2: Add to Template

Add a section for file uploads in your template:

```vue
<template>
  <div class="course-page">
    <!-- Existing course content -->
    <div class="course-header">
      <h1>{{ course.title }}</h1>
      <!-- ... -->
    </div>

    <!-- NEW: Add this section -->
    <div class="course-documents-section">
      <h2>Course Documents</h2>
      
      <DocumentUploader
        model-type="course"
        :foreign-key-id="course.id"
        @upload-success="handleUploadSuccess"
        @upload-error="handleUploadError"
      />
      
      <!-- Display uploaded documents -->
      <div v-if="documents.length > 0" class="documents-list">
        <div v-for="doc in documents" :key="doc.id" class="document-item">
          <span>{{ doc.name }}</span>
          <button @click="downloadDocument(doc.id)">Download</button>
        </div>
      </div>
    </div>
  </div>
</template>
```

### Step 3: Add Event Handlers

```vue
<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import DocumentUploader from '@/document/DocumentUploader.vue';

// Props from backend
const props = defineProps<{
  course: {
    id: number;
    title: string;
    code: string;
  };
  documents: Array<{
    id: number;
    name: string;
    file_path: string;
    file_size_human: string;
  }>;
}>();

// State
const message = ref('');

// Event handlers
const handleUploadSuccess = (files: File[]) => {
  message.value = `Successfully uploaded ${files.length} file(s)`;
  
  // Refresh the page to show new documents
  router.reload({ only: ['documents'] });
  
  // Clear message after 5 seconds
  setTimeout(() => {
    message.value = '';
  }, 5000);
};

const handleUploadError = (error: string) => {
  message.value = `Error: ${error}`;
  
  setTimeout(() => {
    message.value = '';
  }, 5000);
};

const downloadDocument = (docId: number) => {
  window.open(`/documents/${docId}/download`, '_blank');
};
</script>
```

### Step 4: Add Styling

```vue
<style scoped>
.course-documents-section {
  margin-top: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.course-documents-section h2 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #111827;
}

.documents-list {
  margin-top: 1.5rem;
}

.document-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  margin-bottom: 0.5rem;
}

.document-item:hover {
  background-color: #f9fafb;
}

.document-item button {
  padding: 0.5rem 1rem;
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
}

.document-item button:hover {
  background-color: #1d4ed8;
}
</style>
```

### Step 5: Update Backend Controller

Add this method to your `CourseController`:

```php
// app/Http/Controllers/CourseController.php

public function show(Course $course)
{
    return Inertia::render('Courses/Show', [
        'course' => $course,
        'documents' => $course->documents()
            ->with('document.uploader')
            ->latest()
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->document->id,
                'name' => $doc->document->name,
                'file_path' => $doc->document->file_path,
                'file_size_human' => $doc->document->file_size_human,
                'uploaded_by' => $doc->document->uploader->name,
                'created_at' => $doc->created_at,
            ]),
    ]);
}
```

### Step 6: Create Upload Controller

Create a new controller for handling uploads:

```php
// app/Http/Controllers/DocumentController.php

<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\CourseDocument;
use App\Models\ActivityDocument;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'model_type' => 'required|in:course,activity,lesson,report,project,assessment',
            'foreign_key_id' => 'required|integer',
            'foreign_key_name' => 'required|string',
            'visibility' => 'required|in:public,students,instructors,private',
            'is_required' => 'boolean',
            'files.*' => 'required|file|max:10240', // 10MB
        ]);

        $uploadedDocuments = [];

        foreach ($request->file('files') as $file) {
            // Store file
            $path = $file->store('documents', 'public');
            
            // Create document
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

            // Create relationship based on model type
            $this->createDocumentRelationship(
                $request->model_type,
                $document->id,
                $request->foreign_key_id,
                $request->foreign_key_name,
                $request->visibility,
                $request->boolean('is_required')
            );

            $uploadedDocuments[] = $document;
        }

        return back()->with('success', count($uploadedDocuments) . ' document(s) uploaded successfully');
    }

    private function createDocumentRelationship(
        string $modelType,
        int $documentId,
        int $foreignKeyId,
        string $foreignKeyName,
        string $visibility,
        bool $isRequired
    ) {
        $modelClass = match($modelType) {
            'course' => CourseDocument::class,
            'activity' => ActivityDocument::class,
            // Add other types as needed
            default => throw new \Exception('Unknown model type'),
        };

        $modelClass::create([
            'document_id' => $documentId,
            $foreignKeyName => $foreignKeyId,
            'visibility' => $visibility,
            'is_required' => $isRequired,
        ]);
    }

    public function download(Document $document)
    {
        // Check permissions here
        
        return response()->download(
            storage_path('app/public/' . $document->file_path),
            $document->original_name
        );
    }

    public function destroy(Document $document)
    {
        // Check permissions here
        
        $document->delete(); // Soft delete
        
        return back()->with('success', 'Document deleted successfully');
    }
}
```

### Step 7: Add Routes

```php
// routes/web.php

use App\Http\Controllers\DocumentController;

Route::middleware('auth')->group(function () {
    // Document upload
    Route::post('/api/documents/upload', [DocumentController::class, 'upload'])
        ->name('documents.upload');
    
    // Document download
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
    
    // Document delete
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])
        ->name('documents.destroy');
});
```

### Step 8: Update Course Model

Make sure your Course model has the relationship:

```php
// app/Models/Course.php

public function documents()
{
    return $this->hasMany(CourseDocument::class);
}

public function documentFiles()
{
    return $this->hasManyThrough(Document::class, CourseDocument::class, 'course_id', 'id', 'id', 'document_id');
}
```

## Testing

### Test 1: Upload Single File
1. Navigate to course page
2. Click upload area
3. Select one file
4. Click "Upload"
5. Verify file appears in list

### Test 2: Upload Multiple Files
1. Select multiple files
2. Upload
3. Verify all appear in list

### Test 3: Drag and Drop
1. Drag file from desktop
2. Drop on upload area
3. Verify upload works

### Test 4: Download
1. Click download button
2. Verify file downloads

### Test 5: Delete
1. Click delete button
2. Confirm deletion
3. Verify file removed from list

### Test 6: Validation
1. Try uploading file > max size
2. Verify error message shows
3. Try wrong file type
4. Verify error message shows

## Common Issues & Solutions

### Issue: "413 Payload Too Large"
**Solution**: Increase `upload_max_filesize` in php.ini
```ini
upload_max_filesize = 20M
post_max_size = 25M
```

### Issue: Files not showing after upload
**Solution**: Reload data in success handler
```typescript
router.reload({ only: ['documents'] });
```

### Issue: Download not working
**Solution**: Check storage link exists
```bash
php artisan storage:link
```

### Issue: Permission denied
**Solution**: Check storage permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Advanced: Multiple Upload Areas

If you need multiple upload areas for different document types:

```vue
<template>
  <div>
    <!-- Course Materials -->
    <section>
      <h3>Course Materials</h3>
      <DocumentUploader
        model-type="course"
        :foreign-key-id="course.id"
        accepted-types=".pdf,.doc,.docx"
        @upload-success="onMaterialsUploaded"
      />
    </section>

    <!-- Images -->
    <section>
      <h3>Course Images</h3>
      <DocumentUploader
        model-type="course"
        :foreign-key-id="course.id"
        accepted-types=".jpg,.png,.gif"
        :max-file-size="5"
        @upload-success="onImagesUploaded"
      />
    </section>
  </div>
</template>
```

## Next Steps

1. ✅ Component integrated
2. ✅ Backend controller created
3. ✅ Routes added
4. ✅ Tested upload/download/delete
5. ⬜ Add document categories/tags
6. ⬜ Add document preview
7. ⬜ Add document search
8. ⬜ Add bulk operations

## Complete!

Your course management page now has full document upload/management capabilities! 🎉
