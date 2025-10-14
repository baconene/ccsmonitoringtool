# Document Management System

## Overview

A comprehensive document management system built with a base `Document` model that serves as the central interface for all document types in the AstroLearn LMS.

## Architecture

### Base Model: Document

The `Document` model serves as the central document storage interface. All specific document types link to this model through child relationship models.

**Location:** `app/Models/Document.php`

**Key Features:**
- ✅ Soft deletes for document recovery
- ✅ File metadata storage (size, mime type, extension)
- ✅ Uploader tracking
- ✅ Document type categorization
- ✅ JSON metadata field for flexible storage
- ✅ Helper methods for file operations

**Fields:**
- `id` - Primary key
- `name` - Display name
- `original_name` - Original filename
- `file_path` - Storage path
- `file_size` - File size in bytes
- `mime_type` - MIME type
- `extension` - File extension
- `document_type` - Category (course, lesson, activity, etc.)
- `uploaded_by` - Foreign key to users
- `description` - Optional description
- `metadata` - JSON field for additional data
- `timestamps` - created_at, updated_at
- `deleted_at` - Soft delete timestamp

## Child Document Models

### 1. CourseDocument
**Links:** Documents → Courses

**Use Cases:**
- Course syllabi
- Reference materials
- Course handouts
- Supplementary resources

**Fields:**
- `document_id` - FK to documents
- `course_id` - FK to courses
- `visibility` - public, students, instructors
- `is_required` - Boolean flag
- `order` - Display order

**Location:** `app/Models/CourseDocument.php`

---

### 2. LessonDocument
**Links:** Documents → Lessons

**Use Cases:**
- Lesson materials
- Reading materials
- Lecture notes
- Presentation slides

**Fields:**
- `document_id` - FK to documents
- `lesson_id` - FK to lessons
- `visibility` - public, students, instructors
- `is_required` - Boolean flag
- `order` - Display order

**Location:** `app/Models/LessonDocument.php`

---

### 3. ActivityDocument
**Links:** Documents → Activities

**Use Cases:**
- Activity instructions
- Resource files
- Templates
- Example files

**Fields:**
- `document_id` - FK to documents
- `activity_id` - FK to activities
- `visibility` - public, students, instructors
- `is_required` - Boolean flag
- `order` - Display order

**Location:** `app/Models/ActivityDocument.php`

---

### 4. ReportDocument
**Links:** Documents → Reports (Students/Courses)

**Use Cases:**
- Progress reports
- Grade reports
- Transcripts
- Analytics reports

**Fields:**
- `document_id` - FK to documents
- `student_id` - FK to students (optional)
- `course_id` - FK to courses (optional)
- `report_type` - Type of report
- `generated_by` - FK to users
- `generated_at` - Generation timestamp

**Location:** `app/Models/ReportDocument.php`

---

### 5. ProjectDocument
**Links:** Documents → Student Submissions

**Use Cases:**
- Project submissions
- Assignment uploads
- Homework files
- Student work

**Fields:**
- `document_id` - FK to documents
- `activity_id` - FK to activities
- `student_id` - FK to students
- `submission_date` - Submission timestamp
- `status` - submitted, graded, returned, resubmitted
- `feedback` - Instructor feedback

**Location:** `app/Models/ProjectDocument.php`

---

### 6. AssessmentDocument
**Links:** Documents → Assessments

**Use Cases:**
- Answer sheets
- Grading rubrics
- Solution keys
- Scored assessments

**Fields:**
- `document_id` - FK to documents
- `activity_id` - FK to activities
- `student_id` - FK to students (optional)
- `document_category` - answer_sheet, rubric, solution, etc.
- `score` - Numeric score

**Location:** `app/Models/AssessmentDocument.php`

---

### 7. StudentDocument
**Links:** Documents → Students

**Use Cases:**
- Transcripts
- Student IDs
- Certificates
- Medical records
- Personal documents

**Fields:**
- `document_id` - FK to documents
- `student_id` - FK to students
- `document_category` - transcript, id, certificate, medical, etc.
- `academic_year` - Academic year
- `verified` - Boolean verification flag
- `verified_by` - FK to users
- `verified_at` - Verification timestamp

**Location:** `app/Models/StudentDocument.php`

---

### 8. InstructorDocument
**Links:** Documents → Instructors

**Use Cases:**
- Certifications
- Resumes/CVs
- Licenses
- Professional credentials

**Fields:**
- `document_id` - FK to documents
- `instructor_id` - FK to instructors
- `document_category` - certification, resume, license, etc.
- `expiry_date` - Expiration date (for certifications)
- `verified` - Boolean verification flag
- `verified_by` - FK to users
- `verified_at` - Verification timestamp

**Location:** `app/Models/InstructorDocument.php`

---

## Database Schema

### Migration
**File:** `database/migrations/2025_10_14_235928_create_new_documents_structure_table.php`

**Tables Created:**
1. `documents` - Base document storage
2. `course_documents` - Course-document relationships
3. `lesson_documents` - Lesson-document relationships
4. `activity_documents` - Activity-document relationships
5. `report_documents` - Report-document relationships
6. `project_documents` - Project submission documents
7. `assessment_documents` - Assessment-related documents
8. `student_documents` - Student personal documents
9. `instructor_documents` - Instructor professional documents

**Cleanup:**
- Drops old `lesson_document` pivot table
- Drops old `module_document` pivot table
- Drops old `documents` table

## Usage Examples

### Uploading a Course Document

```php
use App\Models\Document;
use App\Models\CourseDocument;

// Store file
$file = $request->file('document');
$path = $file->store('documents/courses', 'public');

// Create document record
$document = Document::create([
    'name' => 'Course Syllabus',
    'original_name' => $file->getClientOriginalName(),
    'file_path' => $path,
    'file_size' => $file->getSize(),
    'mime_type' => $file->getMimeType(),
    'extension' => $file->getClientOriginalExtension(),
    'document_type' => 'course',
    'uploaded_by' => auth()->id(),
    'description' => 'Course syllabus for Fall 2025',
]);

// Link to course
CourseDocument::create([
    'document_id' => $document->id,
    'course_id' => $courseId,
    'visibility' => 'students',
    'is_required' => true,
    'order' => 1,
]);
```

### Student Submitting an Assignment

```php
use App\Models\Document;
use App\Models\ProjectDocument;

$file = $request->file('submission');
$path = $file->store('documents/projects', 'public');

$document = Document::create([
    'name' => 'Math Assignment Submission',
    'original_name' => $file->getClientOriginalName(),
    'file_path' => $path,
    'file_size' => $file->getSize(),
    'mime_type' => $file->getMimeType(),
    'extension' => $file->getClientOriginalExtension(),
    'document_type' => 'project',
    'uploaded_by' => auth()->id(),
]);

ProjectDocument::create([
    'document_id' => $document->id,
    'activity_id' => $activityId,
    'student_id' => $studentId,
    'submission_date' => now(),
    'status' => 'submitted',
]);
```

### Retrieving Course Documents

```php
use App\Models\Course;

$course = Course::with(['courseDocuments.document'])->find($courseId);

foreach ($course->courseDocuments as $courseDoc) {
    $document = $courseDoc->document;
    echo $document->name;
    echo $document->file_url; // Uses accessor
    echo $document->file_size_human; // e.g., "2.5 MB"
}
```

### Document Helper Methods

```php
$document = Document::find($id);

// Check document type
if ($document->isImage()) {
    // Display as image
}

if ($document->isPdf()) {
    // Open in PDF viewer
}

if ($document->isOfficeDocument()) {
    // Handle Word/Excel/PowerPoint
}

// Get formatted file size
echo $document->file_size_human; // "1.2 MB"

// Get full URL
echo $document->file_url; // asset('storage/...')
```

### Query Scopes

```php
// Filter by document type
$courseDocuments = Document::ofType('course')->get();

// Filter by uploader
$myDocuments = Document::uploadedBy(auth()->id())->get();

// Combine scopes
$recentPdfs = Document::ofType('report')
    ->where('mime_type', 'application/pdf')
    ->where('created_at', '>=', now()->subDays(7))
    ->get();
```

## Benefits

✅ **Centralized Storage:** All documents stored in one table
✅ **Flexible Relationships:** Easy to add new document types
✅ **Type Safety:** Dedicated models for each document category
✅ **Metadata Support:** JSON field for extensibility
✅ **Soft Deletes:** Document recovery capability
✅ **File Type Detection:** Built-in helper methods
✅ **User Tracking:** Know who uploaded what
✅ **Verification Support:** For student/instructor documents

## Migration Steps

1. **Backup existing data** (if any documents exist)
2. Run migration: `php artisan migrate`
3. Update controllers to use new document models
4. Update views to display documents
5. Test file uploads and retrievals

## Next Steps

- [ ] Create DocumentController for CRUD operations
- [ ] Add file upload validation
- [ ] Implement storage cleanup for deleted documents
- [ ] Create document preview components
- [ ] Add bulk upload functionality
- [ ] Implement document versioning
- [ ] Add document sharing features
- [ ] Create document search functionality

---

**Created:** October 14, 2025
**Status:** ✅ Models and migrations ready
