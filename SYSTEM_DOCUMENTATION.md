# AstroLearn LMS - Complete System Documentation
## System Overview
AstroLearn is a modern Learning Management System built with Laravel 11, Vue 3, TypeScript, and Inertia.js. It provides comprehensive tools for managing courses, tracking student progress, and facilitating online learning.
## Key Features Summary
### 1. **Course Management**
- Hierarchical structure: Courses → Modules → Activities
- Weighted module system for accurate progress tracking
- Multi-grade level assignments
- Drag-and-drop student enrollment
- Real-time progress updates
### 2. **Activity Types**
- **Quizzes**: Multiple question types with auto-grading
- **Assignments**: File submission and manual grading
- **Lessons**: Rich content delivery with media support
- **Assessments**: Comprehensive evaluations
### 3. **User Roles**
- **Administrator**: Full system control
- **Teacher/Instructor**: Course creation and student management
- **Student**: Course access and activity completion
### 4. **Progress Tracking**
- Weight-based course progress calculation
- Module completion tracking
- Quiz scoring with pass/fail determination
- Time tracking for activities
- Comprehensive dashboards for all roles
### 5. **Quiz System**
- 4 question types: Multiple Choice, True/False, Enumeration, Short Answer
- Auto-save functionality
- Automatic grading for objective questions
- Manual grading support for subjective questions
- Detailed results with correct answers shown
### 6. **Grade Management**
- Grade level restrictions (Grade 7-12)
- Course-level grade assignments
- Automatic eligibility filtering
- Student achievement tracking
### 7. **Skill Assessment System**  NEW
- Skill-based learning outcomes tracking
- Automatic skill-to-activity linking
- Multi-component skill evaluation (activities + lessons)
- Real-time skill mastery assessment
- Competency threshold-based mastery levels
- Auto-initialization of skill assessments on enrollment
- Weighted scoring system (activities: 1.0, lessons: 0.5)
- Consistency and improvement tracking
## Skill Assessment System - Detailed Overview
### What is the Skill Assessment System?
The Skill Assessment System is a comprehensive framework for tracking student competency across specific learning outcomes (skills) throughout their course journey. Unlike traditional grade-based systems that only measure activity completion, the skill assessment system evaluates true mastery of specific competencies.
### Core Concepts
#### 1. **Skills**
- Defined learning outcomes or competencies
- Attached to specific modules
- Each skill has a configurable competency threshold (default: 70%)
- Examples: "Problem Solving", "Critical Thinking", "Data Analysis"
#### 2. **Skill-Activity Linking**
- Skills are automatically linked to activities when: - Activities are added to a module that has existing skills - Skills are created in a module that has existing activities
- Each skill-activity link has a weight (default: 1.0)
- Allows different activities to contribute differently to skill mastery
#### 3. **Student Skill Assessments**
- Individual records tracking each student's progress on each skill
- Auto-created when student enrolls in a course
- Initially set to: `final_score = 0`, `mastery_level = 'not_met'`
- Updated in real-time as students complete activities and lessons
### Skill Assessment Components
The system evaluates skills based on two components:
#### **Activities** (Weight: 1.0)
- Quizzes, Assignments, Assessments linked to the skill
- Scored based on student's actual performance
- Extracted from appropriate progress tables: - `StudentQuizProgress` for quizzes - `StudentAssignmentProgress` for assignments - Other activity-specific progress tables
#### **Lessons** (Weight: 0.5)
- Lessons belong to the same module as the skill
- Completed lessons count as 100% score
- Have half the weight of activities (0.5 vs 1.0)
- Encourages content engagement
### Assessment Calculation
#### **Formula**
```
Normalized Score = Σ(Component Score × Component Weight) / Σ(Component Weights)
Final Score = Normalized Score + Feedback Bonus (10% weight) + Peer Review Bonus (10% weight) + Improvement Factor Bonus - Late Penalty
```
#### **Mastery Levels**
Based on final score vs competency threshold:
- **exceeds**: Score ≥ Threshold + 15%
- **meets**: Score ≥ Threshold
- **approaching**: Score ≥ Threshold - 15%
- **not_met**: Score < Threshold - 15%
#### **Example Calculation**
Module has skill "Data Analysis" (threshold: 70%)
Student progress:
- Quiz 1: 85% (weight: 1.0)
- Assignment 1: 90% (weight: 1.0)
- Lesson 1: Completed = 100% (weight: 0.5)
- Lesson 2: Completed = 100% (weight: 0.5)
```
Normalized Score = (85×1.0 + 90×1.0 + 100×0.5 + 100×0.5) / (1.0 + 1.0 + 0.5 + 0.5) = (85 + 90 + 50 + 50) / 3.0 = 275 / 3.0 = 91.67%
Final Score = 91.67% (assuming no bonuses/penalties)
Mastery Level = "exceeds" (91.67% ≥ 70% + 15%)
```
### Automated Workflows
#### **1. Skill Auto-Linking**
**Scenario A: Adding activities to module with existing skills**
```
Teacher adds Activity X to Module Y
↓
System checks: Does Module Y have skills?
↓
If YES: Automatically link all module skills to Activity X with weight 1.0
```
**Scenario B: Creating skills in module with existing activities**
```
Teacher creates Skill Z in Module Y
↓
System checks: Does Module Y have activities?
↓
If YES: Automatically link Skill Z to all module activities with weight 1.0
```
#### **2. Enrollment Initialization**
```
Student enrolls in Course
↓
System fetches all modules in course
↓
For each module: Get all skills
↓
For each skill: Create StudentSkillAssessment record - student_id: enrolled student - skill_id: module skill - final_score: 0 - mastery_level: 'not_met' - attempt_count: 0 - consistency_score: 0
```
#### **3. Real-Time Assessment Updates**
**When student completes an activity:**
```
Student completes Quiz/Assignment
↓
StudentCourseController marks activity complete
↓
Calls StudentAssessmentService::calculateStudentAssessment()
↓
For each skill linked to the activity: - Fetch all activities for skill - Fetch all lessons in skill's module - Calculate weighted score from both - Update StudentSkillAssessment record
```
**When student completes a lesson:**
```
Student marks lesson complete
↓
LessonCompletion record created
↓
Calls StudentAssessmentService::calculateStudentAssessment()
↓
For each skill in lesson's module: - Include lesson completion (100% score, 0.5 weight) - Recalculate skill assessment - Update mastery level if threshold crossed
```
### Additional Metrics
#### **Consistency Score**
Measures how consistent student performance is across all skill components
- Range: 0-100
- Higher score = more consistent performance
- Calculated using standard deviation of component scores
#### **Improvement Factor**
Tracks student's improvement over multiple attempts
- Based on attempt count
- Encourages retry and mastery learning
- Formula: `1 + (attempts - 1) × 0.05` (capped)
#### **Late Penalty**
Penalizes late submissions
- Calculated from days late
- Applied as deduction from final score
- Can be configured per course/activity
### Assessment Metadata
Each `StudentSkillAssessment` record stores:
```json
{ "activity_count": 2, "lesson_count": 2, "total_components": 4, "evaluation_date": "2026-03-04 15:43:26"
}
```
### Use Cases
1. **Competency-Based Learning**: Track specific skills rather than just grades
2. **Learning Analytics**: Identify skills where students struggle
3. **Personalized Learning**: Recommend activities based on skill gaps
4. **Standards Alignment**: Map skills to educational standards
5. **Portfolio Building**: Students can showcase mastered skills
6. **Prerequisite Management**: Require skill mastery before advanced courses
### Database Tables
#### **skills**
```sql
- id
- module_id (foreign key)
- name
- description
- competency_threshold (default: 70.00)
- timestamps
```
#### **activity_skill** (Pivot)
```sql
- activity_id (foreign key)
- skill_id (foreign key)
- weight (default: 1.0)
- timestamps
```
#### **student_skill_assessments**
```sql
- id
- student_id (foreign key)
- skill_id (foreign key)
- normalized_score
- feedback_score
- peer_review_score
- attempt_count
- improvement_factor
- days_late
- final_score
- mastery_level (enum: exceeds, meets, approaching, not_met)
- consistency_score
- assessment_metadata (JSON)
- timestamps
```
### Benefits **Granular Progress Tracking**: Beyond course completion **Real-Time Feedback**: Students see skill mastery immediately **Data-Driven Insights**: Teachers identify skill gaps **Automated Assessment**: No manual skill tracking needed **Holistic Evaluation**: Considers both activities AND lessons **Standards Compliance**: Align with competency-based education **Scalable**: Works with any number of skills and activities
## Technical Architecture
### Backend Stack
- **Laravel 11**: PHP framework with Eloquent ORM
- **MySQL/SQLite**: Database systems
- **Laravel Breeze**: Authentication
- **API Routes**: RESTful endpoints
### Frontend Stack
- **Vue 3**: Progressive JavaScript framework
- **TypeScript**: Type-safe development
- **Inertia.js**: SPA without API complexity
- **Tailwind CSS**: Utility-first styling
### Database Structure
```
courses
├── modules (with weights)
│ ├── skills  NEW
│ │ ├── activity_skill (pivot with weights)  NEW
│ │ └── student_skill_assessments  NEW
│ ├── lessons
│ │ └── lesson_completions
│ └── activities
│ └── questions (for quizzes)
├── course_enrollments
└── student_activities (progress tracking)
```
## Core Workflows
### Course Creation Workflow
1. Teacher creates course with basic info
2. Adds modules with percentage weights (must total ~100%)
3. Creates or links activities to modules
4. Assigns grade level restrictions
5. Enrolls eligible students
### Student Learning Workflow
1. Student views enrolled courses on dashboard
2. Accesses course and navigates modules
3. Completes activities (lessons, quizzes, assignments)
4. Marks modules as complete
5. Course progress automatically updates based on module weights
### Assessment Workflow
1. Teacher creates quiz with questions
2. Student takes quiz (auto-save enabled)
3. Objective questions graded automatically
4. Teacher grades subjective questions
5. Final score calculated and displayed
6. Results shown with correct answers
### Skill Assessment Workflow  NEW
1. Teacher creates skills in module with competency thresholds
2. Skills automatically link to all module activities
3. Student enrolls → StudentSkillAssessment records created (score=0)
4. Student completes lessons → Lesson completions count toward skill (weight: 0.5)
5. Student completes activities → Activity scores count toward skill (weight: 1.0)
6. System calculates weighted skill score in real-time
7. Mastery level updated based on competency threshold
8. Student and teacher view skill progress dashboards
## Progress Calculation
### Course Progress Formula
```
Progress = (Sum of Completed Module Weights / Total Module Weights) × 100
```
**Example:**
- Module 1 (20% weight) - Completed
- Module 2 (30% weight) - Completed
- Module 3 (25% weight) - Not completed
- Module 4 (25% weight) - Not completed
Progress = (20 + 30) / 100 × 100 = **50%**
### Quiz Score Formula
```
Score = (Points Earned / Total Points) × 100
Pass/Fail = Score >= Passing Percentage
```
## User Interface Features
### Dashboard
- Role-specific views
- Quick stats cards
- Enrolled courses list with progress bars
- Pending activities section
- Upcoming schedule
- Recent activity feed
### Course Management
- Card-based course display
- Drag-and-drop module organization
- Visual progress indicators
- Student enrollment interface
- Activity management panel
### Quiz Interface
- Clean, distraction-free design
- Progress indicator
- Auto-save notifications
- Timer display
- Question navigation
- Results page with detailed breakdown
## Security Features
- Role-based access control (RBAC)
- Laravel middleware authentication
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure password hashing
- Session management
## Performance Optimizations
- Lazy loading for large datasets
- Database query optimization
- Asset compilation and minification
- Browser caching
- Responsive image loading
- Efficient state management
## Mobile Responsiveness
- Fully responsive design
- Touch-friendly interfaces
- Mobile-optimized navigation
- Adaptive layouts for all screen sizes
- Progressive enhancement
## Future Enhancements
- Discussion forums
- Live chat support
- Video conferencing integration
- Advanced analytics dashboard with skill mastery heatmaps
- Mobile applications
- Gamification elements (badges for skill mastery)
- Certificate generation based on skill completion
- Email notifications
- Calendar integration
- File sharing system
- Skill-based recommendations (suggest activities for weak skills)
- Prerequisite skill requirements for courses
- Skill portfolio export for students
- Peer skill assessments
## System Limits
- Max course weight: 100%
- Quiz passing percentage: Configurable (default 70%)
- Supported question types: 4
- Grade levels: 7-12
- User roles: 3
## Best Practices
### For Administrators
1. Regular database backups
2. Monitor system performance
3. Review user accounts periodically
4. Keep system updated
5. Manage activity types efficiently
### For Teachers
1. Set realistic module weights
2. Balance course difficulty
3. Provide clear instructions
4. Use varied assessment methods
5. Give timely feedback
6. **Define clear, measurable skills**  NEW
7. **Set appropriate competency thresholds**  NEW
8. **Review skill assessment data to identify learning gaps**  NEW
9. **Align skills with learning objectives and standards**  NEW
### For Students
1. Complete modules in sequence
2. Review quiz results
3. Track progress regularly
4. Submit assignments on time
5. Communicate with teachers
6. **Monitor skill mastery levels**  NEW
7. **Focus on skills marked "not_met" or "approaching"**  NEW
8. **Complete both activities AND lessons for best skill scores**  NEW
## Common Issues & Solutions
### Issue: Course progress not updating
**Solution**: Ensure module weights total ~100%, mark modules as complete
### Issue: Quiz not auto-grading
**Solution**: Verify correct answers are marked for MCQ/True-False questions
### Issue: Student can't enroll
**Solution**: Check grade level eligibility matches course requirements
### Issue: 404 errors on student navigation
**Solution**: Use correct URL format `/student/courses/{id}` not `/student/course/{id}`
### Issue: Skill not linked to activity  NEW
**Solution**: Skills auto-link when activities are added to modules or skills are created. For existing setups, add the activity to the module again or re-save the skill.
### Issue: Student skill assessment not initializing  NEW
**Solution**: Assessments auto-create on enrollment. For existing enrollments, trigger by having student complete any activity in the course.
### Issue: Skill assessment not updating after lesson completion  NEW
**Solution**: Ensure lesson belongs to the same module as the skill. System automatically recalculates on lesson completion.
### Issue: Student skill score is 0 despite completing activities  NEW
**Solution**: Verify activities are linked to skills (check `activity_skill` pivot table). Auto-linking only works for new additions after the feature was implemented.
## API Endpoints
### Courses
- `GET /api/courses` - Get user's courses
- `POST /api/courses` - Create course (teacher/admin)
- `PUT /api/courses/{id}` - Update course
- `DELETE /api/courses/{id}` - Delete course
### Dashboard
- `GET /api/dashboard/stats` - Get dashboard statistics
- `GET /api/dashboard/student-data` - Get student-specific data
- `GET /api/dashboard/instructor-data` - Get instructor-specific data
### Activities
- `GET /api/activities` - List activities
- `POST /api/activities` - Create activity
- `GET /api/student/activities/in-progress` - Get in-progress activities
### Skills  NEW
- `GET /api/modules/{moduleId}/skills` - List skills for a module
- `POST /api/modules/{moduleId}/skills` - Create skill in module
- `PUT /api/skills/{id}` - Update skill
- `DELETE /api/skills/{id}` - Delete skill
- `GET /api/students/{studentId}/skill-assessments` - Get student's skill assessments
- `GET /api/skills/{skillId}/assessment/{studentId}` - Get specific skill assessment
## Database Tables Reference
### Key Tables
- `users` - User accounts
- `courses` - Course information
- `modules` - Course modules with weights
- `skills`  NEW - Learning outcomes/competencies per module
- `activities` - Learning activities
- `quiz_questions` - Quiz questions
- `student_activities` - Progress tracking
- `student_skill_assessments`  NEW - Skill mastery tracking per student
- `activity_skill`  NEW - Pivot table linking activities to skills with weights
- `lesson_completions` - Lesson completion tracking
- `course_enrollments` - Student enrollments
- `schedules` - Scheduled events
## Conclusion
AstroLearn LMS is a comprehensive, modern learning management system designed for educational institutions. Its weighted module system, advanced quiz functionality, **skill-based assessment framework**, and role-based access control make it suitable for K-12 schools, universities, and corporate training programs focused on competency-based education.
**Key Differentiators:**
- Weighted module progress calculation
- Multi-type activity support (quizzes, assignments, lessons, assessments)
- **Automated skill assessment with real-time mastery tracking**
- **Competency-based learning outcome measurement**
- **Holistic evaluation combining activities and lesson engagement**
- Role-based dashboards and analytics
- Modern tech stack (Laravel 11, Vue 3, TypeScript, Inertia.js)
For support and additional resources, please refer to the in-app documentation or contact your system administrator.
