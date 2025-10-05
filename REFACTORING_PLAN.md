# Comprehensive System Refactoring Plan

## Overview
Complete refactoring of the Learning Management System to ensure consistency between models, database, types, and frontend.

## Phase 1: Delete Old Seeders ✅
Delete all existing seeders except:
- RoleSeeder.php (will be updated)
- ActivityTypeSeeder.php (will be updated)

## Phase 2: Review and Align Models
### Models to Review:
1. **Course** - Add missing relationships
2. **Module** - Fix field naming (module_type vs moduleType)
3. **Lesson** - Ensure proper relationships
4. **Activity** - Fix activity_type vs activity_type_id
5. **Quiz** - Add questions relationship
6. **Question** - Add options relationship
7. **User** - Ensure role relationships
8. **StudentQuizProgress** - Already good
9. **StudentQuizAnswer** - Already good

### Relationships to Verify:
- Course hasMany Modules
- Module belongsTo Course
- Module hasMany Lessons
- Module belongsToMany Activities
- Lesson belongsTo Module
- Activity hasOne Quiz
- Quiz hasMany Questions
- Question hasMany QuestionOptions
- User belongsTo Role
- User hasMany StudentQuizProgress

## Phase 3: Update Types (TypeScript)
Add missing types:
```typescript
export type Course = {
  id: number;
  title: string;
  name: string;
  description: string;
  instructor_id: number;
  instructor_name?: string;
  created_at: string;
  updated_at: string;
  modules?: Module[];
  total_lessons?: number;
  completed_lessons?: number;
  progress?: number;
  is_completed?: boolean;
  enrolled_at?: string;
}

export type Lesson = {
  id: number;
  title: string;
  description: string;
  content?: string;
  course_id: number;
  module_id: number;
  order: number;
  duration?: number;
  created_at: string;
  updated_at: string;
  documents?: Document[];
  is_completed?: boolean;
}

export type Document = {
  id: number;
  name: string;
  file_path: string;
  doc_type: string;
  size?: number;
  created_at: string;
  updated_at: string;
}
```

## Phase 4: Create New Comprehensive Seeder
### MasterDatabaseSeeder.php
Will seed:
- 3 Admins
- 5 Instructors
- 10 Students
- 5 Courses (each with):
  - 2-3 Modules per course
  - 3-4 Lessons per module
  - 1-2 Activities (Quizzes) per module
  - 10 Questions per quiz
  - 4 Options per question

### Course Structure:
1. **Biology 101** (Instructor: John Smith)
   - Module 1: Cell Biology (3 lessons, 1 quiz with 10 questions)
   - Module 2: Genetics (3 lessons, 1 quiz with 10 questions)

2. **Chemistry 101** (Instructor: Maria Garcia)
   - Module 1: Atomic Structure (4 lessons, 1 quiz with 10 questions)
   - Module 2: Chemical Reactions (3 lessons, 1 quiz with 10 questions)

3. **Physics 101** (Instructor: Robert Johnson)
   - Module 1: Mechanics (3 lessons, 1 quiz with 10 questions)
   - Module 2: Thermodynamics (3 lessons, 1 quiz with 10 questions)

4. **World History** (Instructor: Emily Chen)
   - Module 1: Ancient Civilizations (4 lessons, 1 quiz with 10 questions)
   - Module 2: Medieval Period (3 lessons, 1 quiz with 10 questions)

5. **English Literature** (Instructor: David Brown)
   - Module 1: Shakespeare (3 lessons, 1 quiz with 10 questions)
   - Module 2: Modern Literature (4 lessons, 1 quiz with 10 questions)

### Enrollment:
- All 10 students enrolled in all 5 courses

## Phase 5: Frontend Alignment
### Pages to Update:
1. **Student/Courses.vue** - Already exists, verify structure
2. **Student/CourseDetail.vue** - Show modules with lessons and activities
3. **Student/QuizTaking.vue** - Already exists, verify
4. **Student/QuizResults.vue** - Already exists, verify

### Ensure These Still Work:
1. **CourseManagement** (Instructor)
2. **ActivityManagement** (Instructor)
3. **RoleManagement** (Admin)

## Phase 6: Database Migrations Check
Verify all migrations are aligned with models and types.

## Implementation Order:
1. Delete old seeders
2. Update RoleSeeder and ActivityTypeSeeder
3. Create MasterDatabaseSeeder
4. Run fresh migration + seed
5. Test Course Management
6. Test Activity Management
7. Test Role Management
8. Test Student views

## Success Criteria:
✅ All seeders run without errors
✅ Course Management page works
✅ Activity Management page works
✅ Role Management page works
✅ Student can view courses with modules
✅ Student can view lessons in modules
✅ Student can take quizzes
✅ Quiz results display correctly
✅ All relationships work properly
