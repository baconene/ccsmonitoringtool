# ModuleDocument Model - Implementation Summary

## ✅ Successfully Created ModuleDocument System

The ModuleDocument model has been created to link documents to modules, completing the document management system.

## 📁 Files Created/Modified

### 1. **ModuleDocument Model** ✅
`app/Models/ModuleDocument.php`

```php
class ModuleDocument extends Model
{
    protected $fillable = [
        'document_id',
        'module_id',
        'visibility',
        'is_required',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    // Relationships
    public function document() // BelongsTo Document
    public function module()   // BelongsTo Module
}
```

### 2. **Migration** ✅
`database/migrations/2025_10_15_005153_create_module_documents_table.php`

**Table Structure:**
| Column | Type | Default | Description |
|--------|------|---------|-------------|
| `id` | integer | auto | Primary key |
| `document_id` | integer | - | Foreign key to documents |
| `module_id` | integer | - | Foreign key to modules |
| `visibility` | enum | 'students' | public, students, instructors, private |
| `is_required` | boolean | false | Whether document is required |
| `order` | integer | 0 | Display order |
| `created_at` | timestamp | - | Created timestamp |
| `updated_at` | timestamp | - | Updated timestamp |

**Constraints:**
- ✅ Unique: `(document_id, module_id)` - Same document can't be linked to same module twice
- ✅ Foreign Key: `document_id` references `documents.id` (cascade on delete)
- ✅ Foreign Key: `module_id` references `modules.id` (cascade on delete)

### 3. **Document Model** ✅ Updated
Added relationship method:

```php
public function moduleDocuments()
{
    return $this->hasMany(ModuleDocument::class);
}
```

### 4. **Module Model** ✅ Updated
Updated relationships:

```php
// Get ModuleDocument relationship records
public function documents()
{
    return $this->hasMany(ModuleDocument::class);
}

// Get all document files directly
public function documentFiles()
{
    return $this->hasManyThrough(
        Document::class, 
        ModuleDocument::class, 
        'module_id', 
        'id', 
        'id', 
        'document_id'
    );
}
```

### 5. **DocumentController** ✅ Updated
Updated `createDocumentRelationship` method:

```php
$modelClass = match($modelType) {
    'course' => \App\Models\CourseDocument::class,
    'activity' => \App\Models\ActivityDocument::class,
    'lesson' => \App\Models\LessonDocument::class,
    'module' => \App\Models\ModuleDocument::class,  // ✅ Added
    default => null,
};
```

## 🎯 Database Schema

```
┌─────────────────────────────────┐
│      module_documents           │
├─────────────────────────────────┤
│ id                    INTEGER   │
│ document_id           INTEGER ──┼──→ documents.id (cascade)
│ module_id             INTEGER ──┼──→ modules.id (cascade)
│ visibility            VARCHAR   │
│ is_required           BOOLEAN   │
│ order                 INTEGER   │
│ created_at            TIMESTAMP │
│ updated_at            TIMESTAMP │
└─────────────────────────────────┘
        UNIQUE (document_id, module_id)
```

## 🔧 Usage Examples

### 1. Upload Documents to Module

Using DocumentUploader component:

```vue
<template>
  <DocumentUploader
    model-type="module"
    :foreign-key-id="module.id"
    :max-file-size="20"
    accepted-types=".pdf,.doc,.docx,.ppt"
    @upload-success="handleSuccess"
  />
</template>
```

### 2. Get Module Documents in Controller

```php
// Get module with documents
$module = Module::with('documents.document')->find($id);

// Or get document files directly
$module = Module::with('documentFiles')->find($id);

// Return to view
return Inertia::render('Modules/Show', [
    'module' => $module,
    'documents' => $module->documents->map(function($moduleDoc) {
        return [
            'id' => $moduleDoc->document->id,
            'name' => $moduleDoc->document->name,
            'file_size_human' => $moduleDoc->document->file_size_human,
            'extension' => $moduleDoc->document->extension,
            'visibility' => $moduleDoc->visibility,
            'is_required' => $moduleDoc->is_required,
        ];
    }),
]);
```

### 3. Create Module Document Manually

```php
// After uploading document
$document = Document::create([...]);

// Link to module
ModuleDocument::create([
    'document_id' => $document->id,
    'module_id' => $moduleId,
    'visibility' => 'students',
    'is_required' => true,
    'order' => 1,
]);
```

### 4. Query Module Documents

```php
// Get all documents for a module
$documents = ModuleDocument::where('module_id', $moduleId)
    ->with('document')
    ->orderBy('order')
    ->get();

// Get required documents only
$requiredDocs = ModuleDocument::where('module_id', $moduleId)
    ->where('is_required', true)
    ->with('document')
    ->get();

// Get documents by visibility
$studentDocs = ModuleDocument::where('module_id', $moduleId)
    ->where('visibility', 'students')
    ->with('document')
    ->get();
```

## 📊 Complete Document System

Now you have all document relationship models:

| Model | Links Documents To | Status |
|-------|-------------------|--------|
| `CourseDocument` | Courses | ✅ Working |
| `LessonDocument` | Lessons | ✅ Working |
| `ActivityDocument` | Activities | ✅ Working |
| `ModuleDocument` | Modules | ✅ **NEW** |
| `ReportDocument` | Reports | ✅ Working |
| `ProjectDocument` | Student Submissions | ✅ Working |
| `AssessmentDocument` | Assessments | ✅ Working |
| `StudentDocument` | Student Records | ✅ Working |
| `InstructorDocument` | Instructor Credentials | ✅ Working |

## 🎨 Frontend Integration

The DocumentUploader component now supports modules:

```vue
<RelatedDocumentContainer
  model-type="module"
  :foreign-key-id="module.id"
  :model-value="moduleDocuments"
  :max-file-size="20"
  accepted-types=".pdf,.doc,.docx,.ppt,.pptx"
  visibility="students"
/>
```

## 🚀 Testing

### Test Upload
```bash
# 1. Navigate to module page
# 2. Use DocumentUploader or RelatedDocumentContainer
# 3. Upload a file
# 4. Verify it appears in module documents
```

### Test Relationship
```php
// In tinker
$module = Module::find(1);
$documents = $module->documents; // ModuleDocument records
$files = $module->documentFiles; // Document records directly
```

### Test Validation
```php
// Try to link same document twice - should fail due to unique constraint
ModuleDocument::create([
    'document_id' => 1,
    'module_id' => 1, // Same combo as existing record
]);
// Error: UNIQUE constraint failed
```

## 📝 Migration Status

✅ **Migration executed successfully**
- Table: `module_documents` created
- Columns: 8 total (id, document_id, module_id, visibility, is_required, order, timestamps)
- Indexes: Primary key + Unique compound index
- Foreign Keys: 2 (document_id, module_id) with cascade delete

## 🔄 Database Relationships

```
modules (1) ──< module_documents >── (1) documents
                                      (1) ──< course_documents
                                      (1) ──< activity_documents
                                      (1) ──< lesson_documents
                                      (1) ──< report_documents
                                      (1) ──< project_documents
                                      (1) ──< assessment_documents
                                      (1) ──< student_documents
                                      (1) ──< instructor_documents
```

## ✅ Benefits

1. ✅ **Consistent API** - Same structure as other document models
2. ✅ **Cascade Delete** - Documents removed when module deleted
3. ✅ **Unique Constraint** - Prevents duplicate links
4. ✅ **Visibility Control** - Control who can see documents
5. ✅ **Required Flag** - Mark documents as required
6. ✅ **Ordering** - Control document display order
7. ✅ **Soft Delete Ready** - Works with Document soft deletes

## 🎉 Complete!

The ModuleDocument model is now fully integrated and ready to use! You can now:

- ✅ Upload documents to modules
- ✅ Link existing documents to modules
- ✅ Query module documents with relationships
- ✅ Control visibility and requirements
- ✅ Order documents as needed

**All document relationship models are now implemented!** 🚀
