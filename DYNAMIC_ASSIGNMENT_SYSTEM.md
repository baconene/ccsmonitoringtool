# Dynamic Assignment System Implementation

## Overview
Comprehensive assignment system supporting multiple question types and file uploads for the Learning Management System.

## Features

### 1. Assignment Types
- **Objective**: Questions only (True/False, Multiple Choice, Enumeration, Short Answer)
- **File Upload**: Research papers, documents, projects
- **Mixed**: Combination of questions and file uploads

### 2. Question Types Supported
1. **True/False**: Simple true or false questions
2. **Multiple Choice**: Single or multiple correct answers
3. **Enumeration**: Fill-in-the-blank with multiple acceptable answers
4. **Short Answer**: Open-ended text responses

### 3. Key Capabilities
- ✅ Dynamic assignment creation with mixed question types
- ✅ Auto-grading for objective questions
- ✅ Manual grading for file uploads and subjective questions
- ✅ Student progress tracking
- ✅ Answer storage for student review
- ✅ Integration with dynamic grade weight system
- ✅ Time limits and late submission controls
- ✅ Instructor feedback and comments

## Database Structure

### New Tables Created

#### 1. `assignment_questions`
Stores all assignment questions (objective types).

```sql
- id (PK)
- assignment_id (FK → assignments.id)
- question_text
- question_type (enum: true_false, multiple_choice, enumeration, short_answer)
- points (default: 1)
- correct_answer (for T/F, enumeration, short answer)
- acceptable_answers (JSON: multiple acceptable answers for enumeration)
- case_sensitive (boolean: for text answers)
- order (question sequence)
- explanation (shown after submission)
- timestamps
```

#### 2. `assignment_question_options`
Stores options for multiple choice questions.

```sql
- id (PK)
- assignment_question_id (FK → assignment_questions.id)
- option_text
- is_correct (boolean)
- order (option sequence)
- timestamps
```

#### 3. `student_assignment_answers`
Stores all student responses.

```sql
- id (PK)
- student_id (FK → students.id)
- assignment_id (FK → assignments.id)
- assignment_question_id (FK → assignment_questions.id) [nullable for file uploads]
- answer_text (for objective questions)
- selected_options (JSON: array of option IDs for MC)
- file_path (for file uploads)
- original_filename
- is_correct (boolean: auto-calculated for objective)
- points_earned
- instructor_feedback
- answered_at
- timestamps
```

### Updated Tables

#### 1. `assignments` - Added Fields
```sql
- assignment_type (enum: objective, file_upload, mixed)
- total_points (default: 100)
- time_limit (nullable: minutes)
- allow_late_submission (boolean)
- instructions (text)
```

#### 2. `student_assignment_progress` - Added Fields
```sql
- total_questions (integer)
- answered_questions (integer)
- auto_graded_score (decimal)
- requires_grading (boolean)
```

## Models Created

### 1. AssignmentQuestion
- **Purpose**: Represents individual questions in an assignment
- **Key Methods**:
  - `checkAnswer($answer)`: Auto-validates answer correctness
  - `getQuestionTypeDisplayAttribute()`: Human-readable question type

### 2. AssignmentQuestionOption
- **Purpose**: Multiple choice options
- **Relationships**: Belongs to AssignmentQuestion

### 3. StudentAssignmentAnswer
- **Purpose**: Stores student responses
- **Key Features**:
  - Automatic file deletion on answer deletion
  - File URL accessor

### 4. Assignment (Updated)
- **New Methods**:
  - `hasObjectiveQuestions()`: Check if has questions
  - `acceptsFileUploads()`: Check if accepts files
  - `calculateTotalPoints()`: Sum question points
  - `getAssignmentTypeDisplayAttribute()`: Human-readable type

## Controllers

### 1. AssignmentController (Updated)
**Purpose**: Instructor-facing CRUD operations for assignments

**Methods**:
- `show(Assignment)`: Display assignment with student progress
- `store(Request)`: Create assignment with questions
- `update(Request, Assignment)`: Update assignment and questions
- `destroy(Assignment)`: Delete assignment
- `initializeStudentProgress()`: Create progress records for enrolled students
- `getStudentProgress()`: Fetch all student progress data

### 2. StudentAssignmentController (To be created)
**Purpose**: Student-facing operations

**Methods** (Planned):
- `show()`: Display assignment for student to take
- `saveAnswer()`: Save individual question answer
- `submitFileUpload()`: Handle file uploads
- `submit()`: Final submission
- `viewResults()`: View graded assignment

### 3. AssignmentGradingController (To be created)
**Purpose**: Instructor grading interface

**Methods** (Planned):
- `index()`: List submissions requiring grading
- `show()`: View student submission details
- `grade()`: Grade submission and provide feedback
- `return()`: Return graded work to student

## Frontend Components

### Instructor Components (To be created)

#### 1. AssignmentManagement.vue
**Features**:
- Create/Edit assignment
- Add/Remove questions dynamically
- Configure question types and options
- Set time limits and submission rules
- View student progress table
- Access grading interface

#### 2. AssignmentBuilder.vue (Subcomponent)
**Features**:
- Question type selector
- Dynamic question forms based on type
- Option management for multiple choice
- Acceptable answers for enumeration
- Points allocation
- Question reordering (drag & drop)

#### 3. StudentProgressList.vue
**Features**:
- Student list with progress bars
- Submission status badges
- Score display
- "Review" button for submitted work
- Filtering and sorting

#### 4. AssignmentGrading.vue
**Features**:
- View student answers
- View uploaded files
- Score input
- Feedback text area
- Quick grade buttons (full/half/zero credit)

### Student Components (To be created)

#### 1. TakeAssignment.vue
**Features**:
- Question navigation
- Answer input for each type:
  - T/F: Radio buttons
  - MC: Checkboxes/radio based on single/multiple
  - Enumeration: Text input
  - Short answer: Textarea
- File upload dropzone
- Auto-save progress
- Time remaining display (if time limit)
- Submit button with confirmation

#### 2. AssignmentResults.vue
**Features**:
- Score summary card
- Question-by-question review
- Correct/incorrect indicators
- Explanations display
- Instructor feedback
- Uploaded file display
- Download grade report button

## API Endpoints

### Instructor Routes
```
GET    /assignments/{assignment}                 - View assignment
POST   /assignments                               - Create assignment
PUT    /assignments/{assignment}                  - Update assignment
DELETE /assignments/{assignment}                  - Delete assignment
GET    /assignments/{assignment}/submissions      - List submissions
GET    /assignments/{assignment}/submissions/{student} - View submission
POST   /assignments/{assignment}/grade/{student}  - Grade submission
```

### Student Routes
```
GET    /student/assignments/{assignment}          - View assignment to take
POST   /student/assignments/{assignment}/answer   - Save answer
POST   /student/assignments/{assignment}/upload   - Upload file
POST   /student/assignments/{assignment}/submit   - Final submit
GET    /student/assignments/{assignment}/results  - View results
```

## Grading System Integration

### Auto-Grading
- True/False: Exact match
- Multiple Choice: All correct options selected
- Enumeration: Match against acceptable answers (case-sensitive option)
- Short Answer: Exact match or pattern match

### Manual Grading
- File uploads: Instructor assigns score
- Subjective questions: Instructor reviews and scores
- Partial credit: Instructor can override auto-grades

### Grade Calculation
1. **Objective Questions**: Auto-calculated immediately
2. **File Uploads**: Requires instructor grading
3. **Final Score**: Weighted average following dynamic grade weight system
4. **Storage**: Saved to `student_activities.score` and `student_activities.percentage_score`

## Workflow

### Instructor Workflow
1. Create activity with type "Assignment"
2. Access AssignmentManagement page
3. Choose assignment type (objective/file_upload/mixed)
4. Add questions and/or file upload requirement
5. Set points, time limits, and rules
6. Save - system auto-initializes student progress
7. Monitor student submissions
8. Grade submissions (if needed)
9. Provide feedback

### Student Workflow
1. Navigate to assignment from course
2. Start assignment
3. Answer questions and/or upload files
4. Answers auto-saved as progress
5. Submit when complete
6. View auto-graded results immediately (if objective)
7. Wait for instructor grading (if file upload)
8. Review feedback and final score

## Implementation Status

### ✅ Completed
- Database migrations
- Core models (AssignmentQuestion, AssignmentQuestionOption, StudentAssignmentAnswer)
- Updated Assignment and StudentAssignmentProgress models
- AssignmentController with full CRUD

### 🔄 In Progress
- StudentAssignmentController
- AssignmentGradingController

### ⏳ Pending
- Frontend Vue components
- Routes configuration
- Testing and debugging

## Next Steps
1. Create StudentAssignmentController
2. Create AssignmentGradingController
3. Build Vue components for instructor
4. Build Vue components for students
5. Add routes to web.php
6. Run migrations
7. Test complete flow
8. Document edge cases and error handling

## Notes
- All file uploads stored in `storage/app/public/assignment_submissions`
- Maximum file size: 10MB (configurable)
- Accepted file types: PDF, DOCX, DOC, TXT, JPG, PNG
- Auto-save interval: 30 seconds
- Session timeout: Configurable per assignment
