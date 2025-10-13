 
erDiagram
    users ||--o{ courses : "creates"
    users ||--o{ courses : "instructs"
    users ||--o{ students : "has"
    users ||--o{ instructors : "has"
    users ||--o{ course_enrollments : "enrolls"
    users ||--o{ module_completions : "completes"
    users ||--o{ lesson_completions : "completes"
    
    students }o--|| grade_levels : "belongs to"
    students ||--o{ course_enrollments : "enrolled"
    students ||--o{ student_activities : "performs"
    students ||--o{ student_quiz_progress : "attempts"
    students ||--o{ student_quiz_answers : "submits"
    students ||--o{ module_completions : "completes"
    students ||--o{ lesson_completions : "completes"
    
    courses ||--o{ modules : "contains"
    courses ||--o{ lessons : "contains"
    courses ||--o{ course_enrollments : "has"
    courses }o--|| grade_levels : "assigned to"
    courses ||--o{ course_grade_level : "available for"
    
    modules }o--|| courses : "belongs to"
    modules ||--o{ lessons : "contains"
    modules ||--o{ module_activities : "has"
    modules ||--o{ module_document : "has"
    modules ||--o{ module_completions : "tracked by"
    
    lessons }o--|| courses : "belongs to"
    lessons }o--|| modules : "belongs to"
    lessons ||--o{ lesson_document : "has"
    lessons ||--o{ lesson_module : "linked to"
    lessons ||--o{ module_lesson_activities : "has"
    lessons ||--o{ lesson_completions : "tracked by"

    %% Activities & Types
    activities }o--|| activity_types : "has type"
    activities }o--|| users : "created by"
    activities ||--o{ module_activities : "in modules"
    activities ||--o{ module_lesson_activities : "in lessons"
    activities ||--o{ quizzes : "has quiz"
    activities ||--o{ assignments : "has assignment"
    activities ||--o{ student_activities : "student progress"
    activities ||--o{ student_quiz_progress : "quiz attempts"

    %% Quizzes & Questions
    quizzes }o--|| activities : "belongs to"
    quizzes }o--|| users : "created by"
    quizzes ||--o{ questions : "contains"
    quizzes ||--o{ student_quiz_progress : "student attempts"
    questions }o--|| quizzes : "belongs to"
    questions ||--o{ question_options : "has options"
    questions ||--o{ student_quiz_answers : "student answers"
    question_options }o--|| questions : "belongs to"
    question_options ||--o{ student_quiz_answers : "selected"

    %% Assignments
    assignments }o--|| activities : "belongs to"
    assignments }o--|| users : "created by"
    assignments }o--|| documents : "has document"

    %% Documents
    documents ||--o{ lesson_document : "attached to lessons"
    documents ||--o{ module_document : "attached to modules"
    documents ||--o{ assignments : "used in"

    %% Student Progress Tracking
    student_activities }o--|| students : "belongs to"
    student_activities }o--|| modules : "in module"
    student_activities }o--|| courses : "in course"
    student_activities }o--|| activities : "tracks"
    student_activities ||--o| student_assignment_progress : "has details"
    student_activities ||--o| student_project_progress : "has details"
    student_activities ||--o| student_assessment_progress : "has details"

    %% Quiz Progress
    student_quiz_progress }o--|| students : "belongs to"
    student_quiz_progress }o--|| quizzes : "for quiz"
    student_quiz_progress }o--|| activities : "for activity"
    student_quiz_progress ||--o{ student_quiz_answers : "has answers"

    %% Student Answers
    student_quiz_answers }o--|| students : "submitted by"
    student_quiz_answers }o--|| student_quiz_progress : "part of attempt"
    student_quiz_answers }o--|| questions : "answers"
    student_quiz_answers }o--|| question_options : "selected option"

    %% Course Enrollments
    course_enrollments }o--|| users : "enrolled user"
    course_enrollments }o--|| students : "enrolled student"
    course_enrollments }o--|| courses : "enrolled in"
    course_enrollments }o--|| users : "enrolled by instructor"

    %% Completions
    module_completions }o--|| users : "completed by user"
    module_completions }o--|| students : "completed by student"
    module_completions }o--|| modules : "module completed"
    module_completions }o--|| courses : "in course"

    lesson_completions }o--|| users : "completed by user"
    lesson_completions }o--|| students : "completed by student"
    lesson_completions }o--|| lessons : "lesson completed"
    lesson_completions }o--|| courses : "in course"

    %% Entity Definitions
    users {
        int id PK
        string name
        string email UK
        string password
        int role_id FK
        timestamp created_at
    }

    roles {
        int id PK
        string name UK
        string display_name
        boolean is_active
    }

    students {
        int id PK
        string student_id_text UK
        int user_id FK
        string enrollment_number UK
        int grade_level_id FK
        string section
        string status
        date enrollment_date
    }

    instructors {
        int id PK
        string instructor_id UK
        int user_id FK
        string employee_id UK
        string department
        string title
    }

    grade_levels {
        int id PK
        string name UK
        string display_name
        int level UK
        boolean is_active
    }

    courses {
        int id PK
        string name
        string title
        text description
        int instructor_id FK
        int created_by FK
        int grade_level_id FK
        string course_code
        date start_date
        date end_date
        boolean is_active
    }

    modules {
        int id PK
        int course_id FK
        string name
        text description
        int sequence
        string module_type
        decimal module_percentage
        int created_by FK
    }

    lessons {
        int id PK
        string title
        text description
        int course_id FK
        int module_id FK
        int duration
        int order
        string content_type
    }

    activities {
        int id PK
        string title
        text description
        int activity_type_id FK
        int created_by FK
        int passing_percentage
        datetime due_date
    }

    activity_types {
        int id PK
        string name UK
        text description
    }

    quizzes {
        int id PK
        int activity_id FK
        int created_by FK
        string title
        text description
    }

    questions {
        int id PK
        int quiz_id FK
        text question_text
        string question_type
        int points
        string correct_answer
    }

    question_options {
        int id PK
        int question_id FK
        text option_text
        boolean is_correct
    }

    assignments {
        int id PK
        int activity_id FK
        int created_by FK
        string title
        text description
        int document_id FK
    }

    documents {
        int id PK
        string name
        string file_path
        string doc_type
    }

    course_enrollments {
        int id PK
        int user_id FK
        int student_id FK
        int course_id FK
        int instructor_id FK
        datetime enrolled_at
        decimal progress
        boolean is_completed
        datetime completed_at
    }

    student_activities {
        int id PK
        int student_id FK
        int module_id FK
        int course_id FK
        int activity_id FK
        string activity_type
        string status
        decimal score
        decimal max_score
        decimal percentage_score
        datetime started_at
        datetime completed_at
        datetime submitted_at
    }

    student_quiz_progress {
        int id PK
        int student_id FK
        int quiz_id FK
        int activity_id FK
        datetime started_at
        boolean is_completed
        boolean is_submitted
        int completed_questions
        int total_questions
        decimal score
        decimal percentage_score
        int time_spent
    }

    student_quiz_answers {
        int id PK
        int student_id FK
        int quiz_progress_id FK
        int question_id FK
        int selected_option_id FK
        text answer_text
        boolean is_correct
        decimal points_earned
        datetime answered_at
    }

    student_assignment_progress {
        int id PK
        int student_activity_id FK
        text submission_content
        text attachment_files
        string submission_status
        datetime due_date
        datetime submission_date
        text instructor_comments
        decimal points_earned
        decimal points_possible
    }

    student_project_progress {
        int id PK
        int student_activity_id FK
        text project_phases
        int current_phase
        decimal overall_progress_percentage
        text deliverables
        datetime project_start_date
        datetime project_due_date
    }

    student_assessment_progress {
        int id PK
        int student_activity_id FK
        string assessment_type
        text assessment_criteria
        decimal proficiency_level
        string mastery_level
        datetime assessment_date
    }

    module_completions {
        int id PK
        int user_id FK
        int student_id FK
        int module_id FK
        int course_id FK
        datetime completed_at
    }

    lesson_completions {
        int id PK
        int user_id FK
        int student_id FK
        int lesson_id FK
        int course_id FK
        datetime completed_at
    }

    module_activities {
        int id PK
        int module_id FK
        int activity_id FK
        int order
    }

    module_lesson_activities {
        int id PK
        int module_lesson_id FK
        int activity_id FK
        int order
    }

    course_grade_level {
        int id PK
        int course_id FK
        int grade_level_id FK
    }

    lesson_document {
        int id PK
        int lesson_id FK
        int document_id FK
    }

    module_document {
        int id PK
        int module_id FK
        int document_id FK
    }

    lesson_module {
        int id PK
        int lesson_id FK
        int module_id FK
    } 
 