# Activity Management - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- Logged in as an **Instructor** or **Admin**
- Development server running (`npm run dev`)
- Database migrated and seeded

### Access the System
Navigate to: **`/activity-management`**

Or click **"Activities"** in the sidebar navigation.

---

## 📝 Creating Your First Activity

### Step 1: Create an Activity
1. Click **"New Activity"** button
2. Fill in the form:
   - **Title**: Name of your activity (e.g., "Week 1 Quiz")
   - **Description**: Brief description (optional)
   - **Type**: Select Quiz, Assignment, or Exercise
3. Click **"Create Activity"**

### Step 2: Add Content

#### For Quizzes:
1. After creating the activity, you'll see **"Create Quiz"** button
2. Click to create the quiz
3. Click **"Add Question"** to start adding questions

##### Adding Questions:
- **Question Text**: Enter your question
- **Question Type**: Choose from:
  - **Multiple Choice**: Add 2+ options, check correct answer(s)
  - **True/False**: Select True or False as correct answer
  - **Short Answer**: Students type their answer
  - **Enumeration**: Students list multiple items
- **Points**: Assign point value (default: 1)
- Click **"Add Question"**

#### For Assignments:
1. After creating the activity, click **"Create Assignment"**
2. Edit assignment details:
   - **Title**: Assignment name
   - **Description**: Assignment instructions
   - **Due Date**: Select date and time
   - **Document**: Attach reference materials (optional)
3. Click **"Update Assignment"**

---

## 🔍 Managing Activities

### View All Activities
- Main dashboard shows all activities in a table
- Filter by type using the buttons at top
- See activity type, creator, and creation date

### View Activity Details
- Click the **eye icon** (👁️) to view details
- See full information and manage content
- For quizzes: view all questions
- For assignments: view details and edit

### Edit Activity
- Click the **edit icon** (✏️) on the activity list
- Or click **"Edit"** button on the activity detail page
- Update title, description, or type
- Click **"Update Activity"**

### Delete Activity
- Click the **trash icon** (🗑️) on the activity list
- Or click **"Delete"** button on the activity detail page
- Confirm deletion
- **Note**: This will also delete all associated quizzes/questions or assignments

---

## 💡 Tips & Best Practices

### For Quizzes:
1. **Start Simple**: Begin with 5-10 questions
2. **Mix Question Types**: Use variety for engagement
3. **Point Distribution**: Harder questions = more points
4. **Multiple Choice**: Provide 4 options, only 1 correct is standard
5. **Review Before Publishing**: Check all correct answers are marked

### For Assignments:
1. **Clear Instructions**: Provide detailed description
2. **Reasonable Due Dates**: Give students enough time
3. **Attach Resources**: Upload reference documents
4. **Set Reminders**: Use due dates for tracking

### General:
1. **Descriptive Titles**: Use clear, searchable names
2. **Organize by Week/Unit**: E.g., "Week 3: Variables Quiz"
3. **Regular Backups**: Keep copies of important questions
4. **Test First**: Preview activities before assigning

---

## 🎯 Quick Actions Keyboard Shortcuts

- **New Activity**: Click header button
- **Filter by Type**: Click type badges
- **Quick View**: Click row to view details
- **Bulk Delete**: Select multiple (coming soon)

---

## 📊 Activity Types Explained

### Quiz
- Interactive assessment with multiple questions
- Supports 4 question types
- Automatic point calculation
- Ideal for: Tests, pop quizzes, practice exams

### Assignment
- Document-based submission tasks
- Due date tracking
- File attachments
- Ideal for: Essays, projects, homework

### Exercise
- Practice activities
- Can be used for both quiz and assignment types
- Ideal for: Practice problems, drills

---

## 🐛 Troubleshooting

### "Activity Management" not showing in sidebar
- **Check**: Are you logged in as Instructor or Admin?
- **Solution**: Log out and log in as instructor

### Can't create questions
- **Check**: Did you create the quiz first?
- **Solution**: Click "Create Quiz" button before adding questions

### Questions not saving
- **Check**: Are all required fields filled?
- **Solution**: Ensure question text and at least 2 options for multiple choice

### Route errors (404)
- **Check**: Is dev server running?
- **Solution**: Run `npm run dev` in terminal

### Export errors in Welcome.vue
- **Check**: Is Vite cache causing issues?
- **Solution**: Clear cache: `Remove-Item node_modules/.vite -Recurse -Force`
- **Then**: Restart dev server: `npm run dev`

---

## 📞 Support & Resources

### Documentation
- Full implementation guide: `ACTIVITY_MANAGEMENT_IMPLEMENTATION.md`
- TypeScript types: `resources/js/types/index.ts`
- Database schema: `database/migrations/2025_10_05_132621_create_activity.php`

### Common Issues
1. **Permission Denied**: Only instructors/admins can access
2. **Routes Not Working**: Check middleware in `routes/web.php`
3. **Components Not Loading**: Run `npm run build` or `npm run dev`

---

## 🎓 Example Workflow

### Creating a Weekly Quiz:
1. Create Activity: "Week 5: Functions Quiz"
2. Create Quiz
3. Add 10 questions:
   - 5 multiple choice (2 points each)
   - 3 true/false (1 point each)
   - 2 short answer (5 points each)
4. Review questions
5. Total: 23 points
6. Assign to students (feature coming soon)

### Creating a Project Assignment:
1. Create Activity: "Final Project: Web Application"
2. Create Assignment
3. Set due date: 2 weeks from now
4. Add detailed description with requirements
5. Attach project rubric document
6. Save assignment

---

## 🔄 Recent Updates

### Version 1.0 (October 5, 2025)
- ✅ Initial release
- ✅ Full CRUD for activities
- ✅ Quiz management with 4 question types
- ✅ Assignment management with due dates
- ✅ Activity type filtering
- ✅ Dark mode support
- ✅ Mobile responsive design

---

## 🚀 Coming Soon

- [ ] Student submission tracking
- [ ] Automatic quiz grading
- [ ] Activity templates
- [ ] Bulk question import
- [ ] Activity analytics
- [ ] Student progress reports
- [ ] Assignment feedback system
- [ ] Quiz timer feature

---

**Happy Teaching! 🎉**

For technical support, contact your system administrator or refer to the implementation documentation.
