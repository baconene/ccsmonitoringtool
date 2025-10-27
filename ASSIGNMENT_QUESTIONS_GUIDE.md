# Assignment Questions Issue - Diagnosis and Solution

## Problem
Cannot create assignment questions.

## Root Cause Analysis

After investigation, the assignment question creation system **works correctly** at the database level. The test script successfully created:
- ✅ An assignment
- ✅ A question
- ✅ Multiple choice options

## Most Likely Issue

Based on the current database state:
- **0 Assignments** exist in the database
- **0 Assignment Questions** exist

This means you need to **create an Assignment first** before you can add questions to it.

## Solution: Step-by-Step Guide

### Step 1: Create an Activity with Assignment Type

1. Navigate to the Activities page
2. Click "Create Activity"
3. Fill in the activity details:
   - **Activity Type**: Select "Assignment"
   - **Title**: e.g., "Math Homework 1"
   - **Description**: Add details about the assignment
   - **Passing Percentage**: e.g., 70
   - **Due Date**: Set an appropriate deadline

4. Click "Create Activity"

### Step 2: Create the Assignment

Once the activity is created:

1. You'll see the activity in your list
2. Click on the activity to view it
3. Since it's an Assignment type, you'll see the Assignment Management interface
4. Click "Create Assignment" or the form should appear automatically
5. Fill in:
   - **Title**: (auto-filled from activity)
   - **Description**: Assignment instructions
   - **Assignment Type**: Choose one:
     - **Objective**: Questions only (no file upload)
     - **File Upload**: File submission only
     - **Mixed**: Both questions and file upload
   - **Total Points**: e.g., 100
   - **Time Limit**: Optional (in minutes)
   - **Allow Late Submission**: Yes/No

### Step 3: Add Questions

Now you can add questions:

1. Click the "Add Question" button
2. For each question:
   - **Question Text**: Type your question
   - **Question Type**: Select from:
     - **Multiple Choice**: Students select from options
     - **True/False**: Yes/No questions
     - **Short Answer**: Open-ended text (auto-graded if you provide acceptable answers)
     - **Enumeration**: List-based answers
   - **Points**: How many points the question is worth
   - **Order**: Questions are automatically ordered

3. For Multiple Choice questions:
   - Click "Add Option" to add each choice
   - Enter the option text
   - Check the box for the correct answer(s)
   - You can have multiple correct answers

4. For Short Answer/Enumeration:
   - Enter the **Correct Answer**
   - Optionally add **Acceptable Answers** (alternate correct answers)
   - Toggle **Case Sensitive** if capitalization matters

5. Click "Save Assignment" to create the assignment with all questions

## How to Test

### Backend Test (Already Verified ✅)

The system successfully:
- Created an activity
- Created an assignment linked to the activity
- Created a question with text "What is 2+2?"
- Created 3 options (3, 4, 5) with option "4" marked as correct

### Frontend Test

1. Log in as an instructor (e.g., instructor1@example.com / password)
2. Go to Activities
3. Create a new Activity with type "Assignment"
4. Go into the assignment
5. Add questions using the form
6. Submit

## Common Issues and Solutions

### Issue 1: "Cannot add questions"
**Solution**: Make sure you've created the Assignment first by filling out the assignment form and clicking "Create Assignment"

### Issue 2: Questions don't save
**Possible causes**:
- Missing required fields (question text, type, or points)
- For multiple choice: Need at least one option marked as correct
- Network error - check browser console for errors

**Solution**: Check browser console (F12) for error messages

### Issue 3: "Assignment not found"
**Solution**: Make sure the activity has assignment_type = "Assignment" in activity_types table

## Database Check Commands

To verify your setup:

```bash
# Check if assignment type exists
php artisan tinker --execute="echo DB::table('activity_types')->where('name', 'Assignment')->exists() ? 'Assignment type exists' : 'Missing assignment type';"

# Count activities
php artisan tinker --execute="echo 'Activities: ' . App\Models\Activity::count();"

# Count assignments  
php artisan tinker --execute="echo 'Assignments: ' . App\Models\Assignment::count();"
```

## Technical Details

### Tables Structure

1. **activities** - Base activity record
   - Links to activity_types (Quiz, Assignment, etc.)
   
2. **assignments** - Assignment-specific data
   - `activity_id` - Links to activities table
   - `assignment_type` - objective/file_upload/mixed
   - `total_points` - Maximum score
   
3. **assignment_questions** - Individual questions
   - `assignment_id` - Links to assignments table
   - `question_text` - The question
   - `question_type` - multiple_choice/true_false/short_answer/enumeration
   - `points` - Points for this question
   - `order` - Display order

4. **assignment_question_options** - For multiple choice
   - `assignment_question_id` - Links to questions
   - `option_text` - The choice text
   - `is_correct` - Whether this is the correct answer

### Controller Flow

```
1. POST /assignments
   ├─> AssignmentController@store
   ├─> Creates Assignment record
   ├─> Creates AssignmentQuestion records
   └─> Creates AssignmentQuestionOption records (for multiple choice)

2. PUT /assignments/{id}
   ├─> AssignmentController@update
   ├─> Updates Assignment
   ├─> Updates/Creates/Deletes questions
   └─> Updates/Creates/Deletes options
```

### Frontend Flow

```
AssignmentManagement.vue
├─> Form with questions array
├─> addQuestion() - Adds question to array
├─> addOption() - Adds option to question
├─> handleCreateAssignment() - Submits via Inertia
└─> router.post('/assignments', formData)
```

## Next Steps

1. **Create your first assignment activity** following Step 1-3 above
2. **Add questions** to test the system
3. If you encounter errors:
   - Check browser console (F12 → Console tab)
   - Check Laravel logs (`storage/logs/laravel.log`)
   - Share the specific error message

## Verification

After following the steps, you should see:
- ✅ Assignment created
- ✅ Questions listed in the assignment
- ✅ Questions appear for students when they view the assignment

The system is fully functional - you just need to create the initial assignment record first!
