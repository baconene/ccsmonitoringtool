# Database Seeder Documentation

## Overview

The database seeding has been consolidated into a **single comprehensive seeder** that handles all data seeding in a logical order.

## Files

- **`database/seeders/DatabaseSeeder.php`** - Main entry point that calls the comprehensive seeder
- **`database/seeders/SingleComprehensiveSeeder.php`** - Contains all seeding logic

## What Gets Seeded

### Foundation Data
1. **Roles** (3 roles)
   - Admin
   - Instructor
   - Student

2. **Grade Levels** (17 levels)
   - Year 1-5 (Primary)
   - Grade 1-12 (Elementary through High School)

3. **Activity Types** (4 types)
   - Quiz
   - Assignment
   - Assessment
   - Exercise

4. **Question Types** (4 types)
   - Multiple Choice
   - True/False
   - Short Answer
   - Enumeration

5. **Assignment Types** (4 types)
   - Homework
   - Project
   - Essay
   - Research

6. **Schedule Types** (6 types)
   - Activity
   - Course
   - Personal/Adhoc
   - Exam
   - Office Hours
   - Course Due Date

### User Data
- **3 Admin Users**
  - Email: admin1@example.com, admin2@example.com, admin3@example.com
  - Password: `password`

- **5 Instructors**
  - Email: instructor1@example.com through instructor5@example.com
  - Password: `password`
  - Each has full profile with department, specialization, bio, etc.

- **15 Students**
  - Email: student1@example.com through student15@example.com
  - Password: `password`
  - Each assigned to random grade levels and sections (A-E)

### Course Content
- **3 Courses**
  1. Advanced Mathematics (Course 1) - Grade 10
  2. Physics Fundamentals (Course 2) - Grade 9
  3. Computer Programming (Course 3) - Grade 11

- **7 Modules** (distributed across courses)
  - 3 for Math course
  - 2 for Physics course
  - 2 for Programming course

- **10 Lessons** with Lorem Ipsum content

- **8 Activities**
  - 4 Quizzes (Activities 1, 3, 5, 7)
  - 4 Assignments/Projects (Activities 2, 4, 6, 8)

- **4 Quizzes with Questions**
  - Algebra Quiz: 3 questions (15 points total)
  - Calculus Quiz: 2 questions (18 points total)
  - Physics Quiz: 3 questions (18 points total)
  - Programming Quiz: 3 questions (18 points total)

### Enrollment & Progress
- **45 Course Enrollments** (All 15 students enrolled in all 3 courses)
- **120 Student Activities** (Each student has 8 activities)
- **44 Activity Progress Records** (Quiz progress using new StudentActivityProgress model)
- **120 Quiz Answers** (For students who started quizzes)
- **Course Schedules** (Auto-created for each course with participants)

## Seeding Order

The seeder follows this order to respect foreign key constraints:

1. Clear all existing data (PRAGMA foreign_keys=OFF)
2. Foundation data (Roles, Grade Levels, Activity Types, Schedule Types)
3. Users (Admin, Instructor, Student accounts)
4. Students & Instructors (Profile records)
5. Courses
6. Modules
7. Lessons
8. Activities & Module-Activity links
9. Quizzes & Questions
10. Course Enrollments & Schedules
11. Student Activities (Progress tracking)
12. Quiz Progress & Answers

## Running the Seeder

### Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

### Run Seeder Only (without migration)
```bash
php artisan db:seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=SingleComprehensiveSeeder
```

## Data Characteristics

### Realistic Random Data
- Student progress statuses: `not_started`, `in_progress`, `completed`, `submitted`
- Random scores between 60-100
- Random enrollment dates within last 3 months
- Random completion dates for completed activities
- Realistic time_spent values (10-45 minutes per quiz)

### Quiz Answers
- Each quiz answer references the new `StudentActivityProgress` table
- Randomly selects correct/incorrect answers
- Calculates points earned automatically
- Updates overall quiz score in progress record

### Course Schedules
- Automatically creates schedules based on course end dates
- Adds instructors as participants (status: accepted)
- Adds enrolled students as participants (status: invited)

## New Consolidated Model

**StudentActivityProgress** replaces 4 old models:
- ❌ StudentQuizProgress (removed)
- ❌ StudentAssignmentProgress (removed)
- ❌ StudentProjectProgress (removed)
- ❌ StudentAssessmentProgress (removed)
- ✅ **StudentActivityProgress** (new unified model)

### StudentActivityProgress Fields
- `activity_type`: quiz, assignment, project, or assessment
- `quiz_data`: JSON field for quiz-specific data (quiz_id, time_spent, etc.)
- `assignment_data`: JSON field for assignment-specific data
- `project_data`: JSON field for project-specific data
- `assessment_data`: JSON field for assessment-specific data

## Benefits of Single Seeder

1. **Easier Maintenance** - All seeding logic in one place
2. **Correct Order** - Proper dependency management
3. **No Duplication** - Single source of truth
4. **Better Performance** - Optimized data creation
5. **Clearer Documentation** - Easy to understand data flow

## Notes

- All passwords are hashed using `Hash::make('password')`
- Faker library used for realistic random data
- Foreign key constraints properly handled
- Unique constraints respected (no duplicate enrollments)
- Schedule participants auto-synced for all enrolled students
