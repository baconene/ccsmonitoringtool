# Assignment System Testing Guide

## 🔍 Pre-Testing Checklist

### ✅ Setup Verified
- [x] Database migration ran successfully
- [x] `instructor_notifications` table exists (0 records)
- [x] NotificationBell moved to AppSidebarHeader (top-right beside breadcrumbs)
- [x] All routes cached and cleared
- [x] Frontend components compiled

### 📋 Test Accounts Needed
- **Instructor Account**: For creating assignments and grading
- **Student Account**: For taking assignments and viewing results

---

## 🧪 Test Scenarios

### Test 1: Notification Bell UI (Visual Check)

**Objective**: Verify notification bell appears correctly in the layout

**Steps**:
1. Log in as **Instructor**
2. Navigate to Dashboard
3. Look at top-right corner (beside breadcrumbs)

**Expected Results**:
- ✅ Bell icon visible in top-right
- ✅ No badge shown (0 notifications)
- ✅ Bell is clickable
- ✅ Dropdown panel opens on click
- ✅ Shows "No notifications yet" message

**Screenshot Location**: (breadcrumbs area with bell icon)

**Status**: ⏳ Pending

---

### Test 2: Assignment Creation

**Objective**: Create a test assignment with multiple question types

**Steps**:
1. Log in as **Instructor**
2. Navigate to a course
3. Go to Activities tab
4. Create a new activity (Assignment type)
5. Click "Create Assignment"
6. Add questions:
   - 1 True/False question (10 points)
   - 1 Multiple Choice question (20 points)
   - 1 Short Answer question (20 points)
7. Set total points: 50
8. Save assignment

**Expected Results**:
- ✅ Assignment created successfully
- ✅ 3 questions saved
- ✅ Total points = 50
- ✅ Assignment visible in activity management

**Test Data**:
```
Assignment Title: "Test Assignment 1"
Questions:
1. True/False: "Is the sky blue?" (Answer: True, 10 points)
2. Multiple Choice: "What is 2+2?" (Options: 3, 4*, 5, 6) (20 points)
3. Short Answer: "What is the capital of France?" (Answer: Paris, 20 points)
```

**Status**: ⏳ Pending

---

### Test 3: Student Takes Assignment

**Objective**: Student completes and submits assignment

**Steps**:
1. Log in as **Student**
2. Navigate to the course
3. Click on the assignment activity
4. Click "Start Assignment"
5. Answer all 3 questions:
   - Question 1: Select "True"
   - Question 2: Select "4"
   - Question 3: Type "Paris"
6. Click "Submit Assignment"
7. Confirm submission

**Expected Results**:
- ✅ All questions answered
- ✅ Submission confirmation dialog appears
- ✅ Redirects to AssignmentResults.vue
- ✅ Status shows "Submitted - Pending Review"
- ✅ Auto-graded questions show scores (Q1: 10/10, Q2: 20/20)
- ✅ Manual-graded question shows pending (Q3: 0/20)

**Database Check**:
```sql
SELECT * FROM student_assignment_progress 
WHERE assignment_id = <ASSIGNMENT_ID> 
AND student_id = <STUDENT_ID>;
-- Should show: status = 'submitted', submitted_at = timestamp

SELECT * FROM instructor_notifications 
WHERE type = 'assignment_submitted'
ORDER BY created_at DESC LIMIT 1;
-- Should show 1 new notification record
```

**Status**: ⏳ Pending

---

### Test 4: Notification Creation & Display

**Objective**: Verify notification is created and appears in bell

**Steps**:
1. **Stay logged in as Student** (or use different browser)
2. Submit the assignment (from Test 3)
3. **Switch to Instructor account**
4. Look at notification bell (top-right)
5. Wait up to 10 seconds (polling interval)

**Expected Results**:
- ✅ Red badge appears on bell icon
- ✅ Badge shows "1"
- ✅ Click bell opens dropdown
- ✅ Notification visible: "Student Name has submitted Test Assignment 1"
- ✅ Blue dot indicator on unread notification
- ✅ Timestamp shows "Just now" or "1m ago"

**Test Notification Data**:
```json
{
  "type": "assignment_submitted",
  "title": "New Assignment Submission",
  "message": "John Doe has submitted Test Assignment 1",
  "data": {
    "student_id": 123,
    "assignment_id": 456,
    "activity_id": 789,
    "requires_grading": true
  }
}
```

**Status**: ⏳ Pending

---

### Test 5: Notification Click Navigation

**Objective**: Clicking notification navigates to submissions tab

**Steps**:
1. Log in as **Instructor**
2. Click the notification bell
3. Click on the assignment submission notification
4. Observe navigation

**Expected Results**:
- ✅ Notification marked as read (blue dot disappears)
- ✅ Badge count decreases to 0
- ✅ Navigates to `/activities/{activity_id}/manage`
- ✅ **Submissions tab is active** (not Assignment Details tab)
- ✅ Student submission visible in table

**Status**: ⏳ Pending

---

### Test 6: Submissions Tab Features

**Objective**: Test filtering, sorting, and search in submissions tab

**Steps**:
1. On Submissions tab, verify statistics cards show:
   - Total Students: 1
   - Not Started: 0
   - In Progress: 0
   - Submitted: 1 ← **This should be highlighted**
   - Graded: 0
2. Test search: Type student name
3. Test filter: Select "Submitted (Needs Grading)"
4. Test sorting: Click "Date" column header

**Expected Results**:
- ✅ Statistics cards accurate
- ✅ Search filters results correctly
- ✅ Status filter works
- ✅ Sorting toggles asc/desc
- ✅ "Review & Grade" button visible for submitted assignment

**Status**: ⏳ Pending

---

### Test 7: Individual Submission Review

**Objective**: Open student submission for grading

**Steps**:
1. Click "Review & Grade" button
2. Observe StudentSubmissionReview.vue page loads
3. Review header information
4. Scroll through all questions

**Expected Results**:
- ✅ Header shows:
  - Student name
  - Submission date
  - Total points (50)
  - Current score (30/50 - from auto-graded)
- ✅ Question 1: Green check, "True" selected, 10 points earned
- ✅ Question 2: Green check, "4" selected, 20 points earned
- ✅ Question 3: Answer "Paris" shown, 0 points (needs grading)
- ✅ Grading inputs visible for each question
- ✅ Overall feedback textarea at bottom

**Status**: ⏳ Pending

---

### Test 8: Grade Individual Question

**Objective**: Test auto-save functionality on question grading

**Steps**:
1. On Question 3 (Short Answer: "Paris"):
2. Type "20" in points input
3. Click outside (blur event)
4. Wait 1 second
5. Type "Correct answer!" in feedback textarea
6. Click outside (blur event)

**Expected Results**:
- ✅ Points input saves (no page refresh)
- ✅ Feedback saves (no page refresh)
- ✅ Total score updates to 50/50 in header
- ✅ Percentage updates to 100%
- ✅ No error messages

**Database Check**:
```sql
SELECT * FROM student_assignment_answers 
WHERE progress_id = <PROGRESS_ID> 
AND question_id = <Q3_ID>;
-- Should show: points_earned = 20, instructor_feedback = 'Correct answer!'
```

**Status**: ⏳ Pending

---

### Test 9: Submit Final Grade

**Objective**: Finalize grading and update student status

**Steps**:
1. Scroll to bottom "Overall Feedback" section
2. Type: "Excellent work! Perfect score!"
3. Verify total score shows 50/50
4. Click "Submit Grade" button
5. Confirmation dialog appears
6. Click "Yes" or "OK"

**Expected Results**:
- ✅ Confirmation dialog: "Are you sure you want to submit this grade?"
- ✅ On confirm: Redirects to activity management
- ✅ Success message: "Grade submitted successfully"
- ✅ Submissions tab shows:
  - Submitted: 0 (decreased)
  - Graded: 1 (increased)
- ✅ Student row shows purple "GRADED" badge
- ✅ Score shows 50/50 (100%)

**Database Check**:
```sql
SELECT * FROM student_assignment_progress 
WHERE id = <PROGRESS_ID>;
-- Should show: 
-- status = 'graded'
-- score = 50
-- graded_at = timestamp
-- instructor_feedback = 'Excellent work! Perfect score!'
```

**Status**: ⏳ Pending

---

### Test 10: Student Views Results

**Objective**: Student sees graded assignment with feedback

**Steps**:
1. Log in as **Student**
2. Navigate to course
3. Assignment shows "View Results" button
4. Click "View Results"
5. Review results page

**Expected Results**:
- ✅ Score card shows: 50/50 points (100%)
- ✅ Letter grade shows: A
- ✅ Status: "Graded" (green badge)
- ✅ Question 1: Green "Correct" badge
- ✅ Question 2: Green "Correct" badge
- ✅ Question 3: Green "Correct" badge, feedback "Correct answer!"
- ✅ Overall feedback: "Excellent work! Perfect score!"
- ✅ Graded date shown

**Status**: ⏳ Pending

---

### Test 11: Notification Features

**Objective**: Test mark as read, mark all as read, delete

**Steps**:
1. Create 3 more assignment submissions (repeat Test 3)
2. Log in as Instructor
3. Bell shows "3"
4. Open dropdown
5. Test "Mark all as read" button
6. Reopen dropdown
7. Hover over one notification
8. Click delete (trash icon)

**Expected Results**:
- ✅ "Mark all read" removes blue dots
- ✅ Badge changes to 0
- ✅ Notifications become faded (read state)
- ✅ Delete removes notification from list
- ✅ Clicking read notification still navigates

**Status**: ⏳ Pending

---

### Test 12: Edge Cases

**Objective**: Test error handling and edge cases

#### 12.1: Partial Credit
**Steps**:
1. Student answers Question 3 incorrectly: "London"
2. Instructor gives 10/20 points (partial credit)
3. Add feedback: "Close, but Paris is the correct answer"

**Expected**: 
- ✅ Points saved as 10
- ✅ Total score = 40/50 (80%)

#### 12.2: Zero Points
**Steps**:
1. Student answers Question 3: "Berlin"
2. Instructor gives 0/20 points
3. Add feedback: "Incorrect. The capital of France is Paris"

**Expected**: 
- ✅ Points saved as 0
- ✅ Total score = 30/50 (60%)
- ✅ Student sees red "Incorrect" badge

#### 12.3: Multiple Students
**Steps**:
1. Have 5 students submit assignment
2. Grade 3 students
3. Check submissions list

**Expected**:
- ✅ Statistics: 5 total, 2 submitted, 3 graded
- ✅ Filter by "Submitted" shows 2
- ✅ Filter by "Graded" shows 3
- ✅ Sort by score works correctly

**Status**: ⏳ Pending

---

### Test 13: Real-time Polling

**Objective**: Verify notification bell updates automatically

**Steps**:
1. Log in as Instructor
2. Note bell shows "0"
3. Keep page open
4. In another browser/incognito: Log in as Student
5. Submit an assignment
6. Watch instructor's bell (wait up to 10 seconds)

**Expected Results**:
- ✅ Bell badge appears within 10 seconds
- ✅ No page refresh needed
- ✅ Badge shows "1"
- ✅ Clicking shows new notification

**Status**: ⏳ Pending

---

### Test 14: Responsive Design

**Objective**: Test on different screen sizes

**Devices to Test**:
- Mobile (375px): iPhone SE
- Tablet (768px): iPad
- Desktop (1440px): Standard laptop

**Areas to Check**:
- ✅ Notification bell visible and accessible
- ✅ Submissions table scrolls horizontally on mobile
- ✅ Statistics cards stack properly (2 cols → 5 cols)
- ✅ Grading inputs stack vertically on mobile
- ✅ All buttons remain clickable

**Status**: ⏳ Pending

---

## 📊 Test Results Summary

### Overall Status
- Total Tests: 14
- Passed: 0
- Failed: 0
- Pending: 14

### Critical Path (Must Pass)
1. ⏳ Test 3: Student submission
2. ⏳ Test 4: Notification creation
3. ⏳ Test 5: Navigation from notification
4. ⏳ Test 8: Question grading
5. ⏳ Test 9: Final grade submission
6. ⏳ Test 10: Student views results

### Nice-to-Have (Can Fix Later)
- Test 11: Notification management
- Test 12: Edge cases
- Test 13: Real-time polling
- Test 14: Responsive design

---

## 🐛 Bug Tracking Template

### Bug Report Format
```markdown
**Bug ID**: BUG-001
**Test**: Test X - Title
**Severity**: Critical / High / Medium / Low
**Description**: What went wrong
**Steps to Reproduce**:
1. Step 1
2. Step 2
3. Step 3
**Expected**: What should happen
**Actual**: What actually happened
**Screenshot**: [Link or attachment]
**Browser**: Chrome 120 / Firefox 121 / Safari 17
**Status**: Open / In Progress / Fixed / Won't Fix
```

### Discovered Bugs
(None yet - testing pending)

---

## ✅ Sign-Off Checklist

Before marking as production-ready:

### Functionality
- [ ] All critical path tests pass
- [ ] No critical or high severity bugs
- [ ] Edge cases handled gracefully
- [ ] Error messages are user-friendly

### Performance
- [ ] Notification polling doesn't cause lag
- [ ] Large submission lists load quickly
- [ ] Auto-save doesn't block UI

### Security
- [ ] Only instructors see notification bell
- [ ] Students can't access instructor grading routes
- [ ] Authorization checks working

### UX
- [ ] All buttons have clear labels
- [ ] Loading states shown where needed
- [ ] Success/error messages appear
- [ ] Navigation is intuitive

### Documentation
- [ ] All new features documented
- [ ] README updated with setup instructions
- [ ] API endpoints listed
- [ ] Known issues documented

---

## 🚀 Production Deployment

### Pre-Deployment
1. [ ] All tests passed
2. [ ] Code reviewed
3. [ ] Database backup created
4. [ ] Rollback plan documented

### Deployment Steps
```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev
npm install

# 3. Run migrations
php artisan migrate --force

# 4. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Build frontend
npm run build

# 6. Restart services
php artisan queue:restart
```

### Post-Deployment
1. [ ] Verify notification bell appears
2. [ ] Test one complete workflow
3. [ ] Monitor error logs
4. [ ] Check database for errors

---

## 📞 Support

If you encounter issues during testing:

1. **Check Logs**:
   - Laravel: `storage/logs/laravel.log`
   - Browser Console: F12 → Console tab
   - Network: F12 → Network tab

2. **Common Issues**:
   - "Route not found" → Run `php artisan route:clear`
   - "Class not found" → Run `composer dump-autoload`
   - "Vue component error" → Run `npm run build`
   - "Notification not appearing" → Check polling in browser console

3. **Documentation**:
   - `NOTIFICATION_SYSTEM_IMPLEMENTATION.md`
   - `ASSIGNMENT_MANAGEMENT_REFACTOR.md`
   - `COMPLETE_ASSIGNMENT_SYSTEM_SUMMARY.md`

---

**Testing Started**: [Date]
**Testing Completed**: [Date]
**Tester Name**: [Name]
**Sign-Off**: [Signature]
