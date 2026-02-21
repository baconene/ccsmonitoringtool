# Quick Reference: Dashboard & Activity System

## How Everything Works Together

### 📝 When a Student Takes an Activity

```
1. Opens Activity
   └─> StudentActivity created (status: 'in_progress')
   
2. Answers Questions
   └─> Auto-saves each answer (no button needed!)
   └─> Objective questions auto-graded instantly
   
3. Submits Activity
   └─> Final calculations performed
   └─> StudentActivity.status → 'completed'
   └─> StudentActivity::boot() event fires
   └─> CourseEnrollment::updateProgress() called
   └─> Progress = (completed activities / total activities) × 100
   
4. Views Results
   └─> Redirected to /student/activities/{id}/results
   └─> Shows score, percentage, breakdown
```

### 🎯 Module Completion Requirements

**A module can only be marked as complete when:**
- ✅ ALL lessons are completed
- ✅ ALL activities are completed (status = 'completed')

**Frontend checks:**
- Button disabled if requirements not met
- Shows which items are still pending

**Backend validates:**
- Verifies all lessons have LessonCompletion records
- Verifies all activities have StudentActivity with status='completed'
- Returns error with specific missing items if validation fails

### 📊 Progress Calculation

**Course Progress:**
```
progress = (completed_activities / total_activities) × 100
```

Where:
- `completed_activities` = Count of StudentActivity records with status='completed'
- `total_activities` = Count of all activities in all course modules

**Updates When:**
- Student completes any activity
- Triggered by StudentActivity::boot() saved event
- Automatically recalculates and saves to database

### 🎨 Auto-Grading Rules

| Question Type | Auto-Graded? | When |
|--------------|--------------|------|
| Multiple Choice | ✅ Yes | Instantly on save |
| True/False | ✅ Yes | Instantly on save |
| Enumeration | ✅ Yes | Matched on save |
| Short Answer | ✅ Yes | Matched on save |
| Essay | ❌ No | Instructor reviews |
| File Upload | ❌ No | Instructor reviews |

### 🔄 Auto-Save Behavior

**Quiz & Assignment:**
- Watches for answer changes
- Saves automatically after change detected
- No save button needed
- Real-time feedback on correctness

### 📁 Key Files

**Backend:**
- `StudentCourseController.php` - Course/module views & completion
- `StudentQuizController.php` - Quiz taking & submission
- `StudentAssignmentController.php` - Assignment taking & submission
- `StudentActivity.php` - Progress update trigger (boot event)
- `CourseEnrollment.php` - Progress calculation logic

**Frontend:**
- `CourseDetail.vue` - Course overview, module list
- `QuizTaking.vue` - Quiz interface with auto-save
- `TakeAssignment.vue` - Assignment interface with auto-save
- `ActivityResults.vue` - Unified results display
- `Dashboard.vue` - Student dashboard with progress

### 🚀 Testing Steps

1. **Test Auto-Save:**
   - Open quiz or assignment
   - Answer a question
   - Navigate away (or check network tab)
   - Verify answer was saved

2. **Test Auto-Grade:**
   - Answer a multiple-choice question
   - Check if it's marked correct/incorrect immediately
   - Check score updates

3. **Test Progress Update:**
   - Note current course progress
   - Complete an activity
   - Check dashboard - progress should increase

4. **Test Module Completion:**
   - Try to complete module early → Should fail with details
   - Complete all requirements
   - Mark module complete → Should succeed

5. **Test Results Display:**
   - Complete quiz → Should show results page
   - Complete assignment → Should show results page
   - Verify score, percentage, breakdown all shown

### ⚠️ Important Notes

- Use `student_id` (not `user_id`) for all student-related queries
- StudentActivity `status` must be 'completed' to count toward progress
- Module completion requires BOTH lessons AND activities complete
- Auto-grading happens on save, not just on submit
- Results page uses `student_activity_id` (not activity_id or progress_id)

### 🐛 Debugging

**Progress not updating?**
- Check StudentActivity.status is 'completed' (not 'submitted')
- Verify StudentActivity::boot() event is firing
- Check course_enrollments.progress field directly

**Module won't mark complete?**
- Check error message for specific missing items
- Verify all StudentActivity records have status='completed'
- Verify all LessonCompletion records exist

**Auto-save not working?**
- Check browser console for errors
- Verify watch listener is active
- Check network tab for save requests

**Auto-grading not working?**
- Verify question type is supported (multiple_choice, true_false, etc.)
- Check if correct_answer is set in database
- Review saveAnswer() logic in controller
