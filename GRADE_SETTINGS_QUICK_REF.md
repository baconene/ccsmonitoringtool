# Grade Settings Quick Reference

## 🎯 What is Grade Settings?

A feature that allows administrators and instructors to customize how student grades are calculated without changing code.

---

## 📍 Where to Find It

**Sidebar Navigation:**
- Admin users: Click **"Grade Settings"** in sidebar (between Role Management and Grade Reports)
- Instructor users: Click **"Grade Settings"** in sidebar (between Assessment Tool and Grade Reports)

**Direct URL:** `/grade-settings`

---

## ⚙️ What Can You Configure?

### 1. Module Component Weights

**Controls:** How lessons vs activities contribute to module grades

**Default:** 
- Lessons: 20%
- Activities: 80%

**Example Usage:**
- Theory-heavy course: Lessons 40%, Activities 60%
- Hands-on course: Lessons 10%, Activities 90%
- Balanced: Lessons 50%, Activities 50%

### 2. Activity Type Weights

**Controls:** How each activity type is weighted within activities

**Default:**
- Quiz: 30%
- Assignment: 15%
- Assessment: 35%
- Exercise: 20%

**Example Usage:**
- Quiz-focused: Quiz 50%, Assignment 20%, Assessment 20%, Exercise 10%
- Assessment-focused: Quiz 20%, Assignment 10%, Assessment 60%, Exercise 10%
- Equal weight: All at 25%

---

## 📝 How to Use

### Change Module Component Weights

1. Navigate to Grade Settings page
2. Locate **"Module Component Weights"** card
3. Enter new percentage in **Lessons** field (0-100)
4. **Activities** auto-adjusts to maintain 100% total
5. Verify green badge shows "100%"
6. Click **"Save Module Weights"** button

### Change Activity Type Weights

1. Navigate to Grade Settings page
2. Locate **"Activity Type Weights"** card
3. Adjust percentages for:
   - Quiz
   - Assignment
   - Assessment
   - Exercise
4. Ensure total equals 100% (shown in badge)
5. Click **"Save Activity Weights"** button

### Reset to Defaults

1. Scroll to **"Reset to Defaults"** card
2. Click **"Reset to Defaults"** button
3. Confirm action in popup
4. All weights restored to factory settings

---

## ✅ Validation Rules

**MUST follow these rules:**

1. ✅ Module component weights MUST total 100%
2. ✅ Activity type weights MUST total 100%
3. ✅ All values must be between 0-100
4. ✅ Save buttons disabled if validation fails

**Visual Indicators:**
- 🟢 Green badge = Valid (total is 100%)
- 🔴 Red badge = Invalid (total is not 100%)

---

## 📊 How Grades are Calculated

### Formula

```
Module Score = (Lesson Score × Lesson %) + (Activity Score × Activity %)

Activity Score = (Quiz Avg × Quiz %) + (Assignment Avg × Assignment %) 
                + (Assessment Avg × Assessment %) + (Exercise Avg × Exercise %)
```

### Example Calculation

**Settings:**
- Lessons: 20%, Activities: 80%
- Quiz: 30%, Assignment: 15%, Assessment: 35%, Exercise: 20%

**Student Performance:**
- Lessons: 90%
- Quizzes: 85%, Assignments: 92%, Assessments: 88%, Exercises: 80%

**Calculation:**
1. Activity Score = (85×30%) + (92×15%) + (88×35%) + (80×20%) = 86.1%
2. Module Score = (90×20%) + (86.1×80%) = **86.88%**

---

## ⚠️ Important Notes

### Before Changing Weights:

1. **Communicate:** Inform students of grading criteria before course starts
2. **Timing:** Avoid mid-course changes unless necessary
3. **Document:** Note why you're making the change
4. **Test:** Consider the impact on student grades

### After Changing Weights:

1. **Immediate Effect:** Changes apply to all grade calculations instantly
2. **No Undo:** Can't revert automatically (but can manually change back)
3. **Audit Trail:** System tracks who made the change and when
4. **Notification:** Students should be notified of grading changes

---

## 🔧 Troubleshooting

### "Save button is disabled"

**Cause:** Weights don't total 100%

**Fix:** Adjust values until badge shows green "100%"

---

### "Changes not showing in grades"

**Cause:** Cache delay

**Fix:** 
1. Wait 1-2 minutes for cache refresh
2. Or contact admin to clear cache

---

### "Can't access Grade Settings page"

**Cause:** Insufficient permissions

**Fix:** Only instructors and admins can access
- If you're an instructor/admin and can't access, contact IT

---

## 💡 Best Practices

### Recommended Configurations

**🎓 Theory Course (Lectures, Reading):**
- Lessons: 40%, Activities: 60%

**🛠️ Lab Course (Hands-on, Projects):**
- Lessons: 10%, Activities: 90%

**📚 Balanced Course:**
- Lessons: 30%, Activities: 70%

**📊 Assessment-Heavy:**
- Quiz: 20%, Assignment: 10%, Assessment: 60%, Exercise: 10%

**📝 Continuous Evaluation:**
- Quiz: 25%, Assignment: 25%, Assessment: 25%, Exercise: 25%

---

## 🔒 Access Control

**Who Can Use Grade Settings:**
- ✅ Administrators
- ✅ Instructors
- ❌ Students (view only in grade reports)

---

## 📞 Need Help?

**Questions about:**
- How to use the feature → Check this guide
- Technical issues → Contact IT Support
- Grading policy → Contact Academic Affairs
- Student concerns → Refer to course syllabus

---

## 📄 Related Documentation

- **Full Guide:** `GRADE_SETTINGS_SYSTEM.md`
- **Implementation:** `GRADE_SETTINGS_IMPLEMENTATION.md`
- **Support:** Contact LMS Administrator

---

**Last Updated:** January 2025  
**Version:** 1.0  
**Quick Access:** `/grade-settings`
