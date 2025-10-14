# Document Management System - ERD

```
┌──────────────────────────────────────────────────────────────────┐
│                          DOCUMENTS                               │
├──────────────────────────────────────────────────────────────────┤
│ • id (PK)                                                        │
│ • name                                                           │
│ • original_name                                                  │
│ • file_path                                                      │
│ • file_size                                                      │
│ • mime_type                                                      │
│ • extension                                                      │
│ • document_type                                                  │
│ • uploaded_by (FK → users)                                       │
│ • description                                                    │
│ • metadata (JSON)                                                │
│ • created_at, updated_at                                         │
│ • deleted_at (soft delete)                                       │
└──────────────────────────────────────────────────────────────────┘
           │
           │ has many
           ├─────────────────────────────────────────────┐
           │                                             │
           ▼                                             ▼
┌─────────────────────────┐                  ┌─────────────────────────┐
│   COURSE_DOCUMENTS      │                  │   LESSON_DOCUMENTS      │
├─────────────────────────┤                  ├─────────────────────────┤
│ • id (PK)               │                  │ • id (PK)               │
│ • document_id (FK)──────┤                  │ • document_id (FK)──────┤
│ • course_id (FK)        │                  │ • lesson_id (FK)        │
│ • visibility            │                  │ • visibility            │
│ • is_required           │                  │ • is_required           │
│ • order                 │                  │ • order                 │
└─────────────────────────┘                  └─────────────────────────┘
           │                                             │
           │                                             │
           ▼                                             ▼
      COURSES                                       LESSONS


           │                                             │
           │ has many                                    │ has many
           ├─────────────────────────────────────────────┤
           │                                             │
           ▼                                             ▼
┌─────────────────────────┐                  ┌─────────────────────────┐
│  ACTIVITY_DOCUMENTS     │                  │   REPORT_DOCUMENTS      │
├─────────────────────────┤                  ├─────────────────────────┤
│ • id (PK)               │                  │ • id (PK)               │
│ • document_id (FK)──────┤                  │ • document_id (FK)──────┤
│ • activity_id (FK)      │                  │ • student_id (FK)       │
│ • visibility            │                  │ • course_id (FK)        │
│ • is_required           │                  │ • report_type           │
│ • order                 │                  │ • generated_by (FK)     │
└─────────────────────────┘                  │ • generated_at          │
           │                                  └─────────────────────────┘
           │                                             │
           ▼                                             │
      ACTIVITIES                                         ▼
                                                    STUDENTS
                                                    COURSES


           │                                             │
           │ has many                                    │ has many
           ├─────────────────────────────────────────────┤
           │                                             │
           ▼                                             ▼
┌─────────────────────────┐                  ┌─────────────────────────┐
│  PROJECT_DOCUMENTS      │                  │ ASSESSMENT_DOCUMENTS    │
├─────────────────────────┤                  ├─────────────────────────┤
│ • id (PK)               │                  │ • id (PK)               │
│ • document_id (FK)──────┤                  │ • document_id (FK)──────┤
│ • activity_id (FK)      │                  │ • activity_id (FK)      │
│ • student_id (FK)       │                  │ • student_id (FK)       │
│ • submission_date       │                  │ • document_category     │
│ • status                │                  │ • score                 │
│ • feedback              │                  └─────────────────────────┘
└─────────────────────────┘                             │
           │                                             │
           │                                             ▼
           ▼                                        ACTIVITIES
      ACTIVITIES                                    STUDENTS
      STUDENTS


           │                                             │
           │ has many                                    │ has many
           ├─────────────────────────────────────────────┤
           │                                             │
           ▼                                             ▼
┌─────────────────────────┐                  ┌─────────────────────────┐
│  STUDENT_DOCUMENTS      │                  │ INSTRUCTOR_DOCUMENTS    │
├─────────────────────────┤                  ├─────────────────────────┤
│ • id (PK)               │                  │ • id (PK)               │
│ • document_id (FK)──────┤                  │ • document_id (FK)──────┤
│ • student_id (FK)       │                  │ • instructor_id (FK)    │
│ • document_category     │                  │ • document_category     │
│ • academic_year         │                  │ • expiry_date           │
│ • verified              │                  │ • verified              │
│ • verified_by (FK)      │                  │ • verified_by (FK)      │
│ • verified_at           │                  │ • verified_at           │
└─────────────────────────┘                  └─────────────────────────┘
           │                                             │
           │                                             │
           ▼                                             ▼
      STUDENTS                                     INSTRUCTORS
```

## Relationship Summary

### Documents Table (Central Hub)
**Has Many Relationships:**
- → course_documents
- → lesson_documents
- → activity_documents
- → report_documents
- → project_documents
- → assessment_documents
- → student_documents
- → instructor_documents

**Belongs To:**
- ← users (uploaded_by)

### Child Document Tables

**CourseDocument**
- Belongs to: Document
- Belongs to: Course
- Unique constraint: (document_id, course_id)

**LessonDocument**
- Belongs to: Document
- Belongs to: Lesson
- Unique constraint: (document_id, lesson_id)

**ActivityDocument**
- Belongs to: Document
- Belongs to: Activity
- Unique constraint: (document_id, activity_id)

**ReportDocument**
- Belongs to: Document
- Belongs to: Student (optional)
- Belongs to: Course (optional)
- Belongs to: User (generated_by)

**ProjectDocument**
- Belongs to: Document
- Belongs to: Activity
- Belongs to: Student
- Unique constraint: (document_id, activity_id, student_id)

**AssessmentDocument**
- Belongs to: Document
- Belongs to: Activity
- Belongs to: Student (optional)

**StudentDocument**
- Belongs to: Document
- Belongs to: Student
- Belongs to: User (verified_by)

**InstructorDocument**
- Belongs to: Document
- Belongs to: Instructor
- Belongs to: User (verified_by)

## Document Type Flow

```
                    ┌────────────────┐
                    │  File Upload   │
                    └────────┬───────┘
                             │
                             ▼
                    ┌────────────────┐
                    │   DOCUMENT     │
                    │  (Base Table)  │
                    └────────┬───────┘
                             │
              ┌──────────────┼──────────────┐
              │              │              │
              ▼              ▼              ▼
         [Course]       [Lesson]      [Activity]
         Documents      Documents      Documents
              │              │              │
              └──────────────┼──────────────┘
                             │
              ┌──────────────┼──────────────┐
              │              │              │
              ▼              ▼              ▼
         [Report]       [Project]    [Assessment]
         Documents      Documents      Documents
              │              │              │
              └──────────────┼──────────────┘
                             │
                    ┌────────┴─────────┐
                    │                  │
                    ▼                  ▼
              [Student]          [Instructor]
              Documents          Documents
```

## Cascade Rules

**On Delete Cascade:**
- When Document is deleted → All child records deleted
- When Course/Lesson/Activity deleted → Related document links deleted
- When Student deleted → Related submissions and documents deleted
- When Instructor deleted → Related documents deleted

**On Delete Set Null:**
- When User (uploader) deleted → uploaded_by set to null
- When User (verifier) deleted → verified_by set to null
- When User (generator) deleted → generated_by set to null

## Index Strategy

**documents table:**
- Primary key: id
- Index: document_type
- Index: uploaded_by

**All child tables:**
- Primary key: id
- Foreign key indexes automatically created
- Unique constraints where applicable

---

**Visual Representation Created:** October 14, 2025
