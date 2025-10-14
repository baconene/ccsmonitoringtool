# Document Management System - Quick Reference

## Models Created ✅

| Model | Purpose | Key Relationships |
|-------|---------|------------------|
| `Document` | Base document storage | Has many: all child models |
| `CourseDocument` | Course materials | Document → Course |
| `LessonDocument` | Lesson materials | Document → Lesson |
| `ActivityDocument` | Activity resources | Document → Activity |
| `ReportDocument` | Generated reports | Document → Student/Course |
| `ProjectDocument` | Student submissions | Document → Activity → Student |
| `AssessmentDocument` | Assessments/rubrics | Document → Activity |
| `StudentDocument` | Student records | Document → Student |
| `InstructorDocument` | Instructor credentials | Document → Instructor |

## Quick Usage

### Upload Document
```php
$document = Document::create([
    'name' => 'Document Name',
    'original_name' => $file->getClientOriginalName(),
    'file_path' => $file->store('documents', 'public'),
    'file_size' => $file->getSize(),
    'mime_type' => $file->getMimeType(),
    'extension' => $file->getClientOriginalExtension(),
    'document_type' => 'course',
    'uploaded_by' => auth()->id(),
]);
```

### Link to Resource
```php
// Link to course
CourseDocument::create([
    'document_id' => $document->id,
    'course_id' => $courseId,
    'visibility' => 'students',
]);

// Student submission
ProjectDocument::create([
    'document_id' => $document->id,
    'activity_id' => $activityId,
    'student_id' => $studentId,
    'status' => 'submitted',
]);
```

### Retrieve Documents
```php
// Get course documents
$course->courseDocuments()->with('document')->get();

// Get student submissions
$student->projectDocuments()
    ->where('status', 'submitted')
    ->with('document')->get();
```

## Helper Methods

```php
$document->isImage()           // Check if image
$document->isPdf()             // Check if PDF
$document->isOfficeDocument()  // Check if Word/Excel/PPT
$document->file_size_human     // "2.5 MB"
$document->file_url            // Full URL
```

## Query Scopes

```php
Document::ofType('course')->get()
Document::uploadedBy(auth()->id())->get()
```

## Files

- **Models:** `app/Models/Document.php` + 8 child models
- **Migration:** `database/migrations/2025_10_14_235928_create_new_documents_structure_table.php`
- **Docs:** `DOCUMENT_MANAGEMENT_SYSTEM.md`

---
✅ **Status:** Ready to use
📅 **Created:** October 14, 2025
