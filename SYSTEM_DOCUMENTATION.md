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
│   └── activities
│       └── questions (for quizzes)
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
- Advanced analytics dashboard
- Mobile applications
- Gamification elements
- Certificate generation
- Email notifications
- Calendar integration
- File sharing system

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

### For Students
1. Complete modules in sequence
2. Review quiz results
3. Track progress regularly
4. Submit assignments on time
5. Communicate with teachers

## Common Issues & Solutions

### Issue: Course progress not updating
**Solution**: Ensure module weights total ~100%, mark modules as complete

### Issue: Quiz not auto-grading
**Solution**: Verify correct answers are marked for MCQ/True-False questions

### Issue: Student can't enroll
**Solution**: Check grade level eligibility matches course requirements

### Issue: 404 errors on student navigation
**Solution**: Use correct URL format `/student/courses/{id}` not `/student/course/{id}`

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

## Database Tables Reference

### Key Tables
- `users` - User accounts
- `courses` - Course information
- `modules` - Course modules with weights
- `activities` - Learning activities
- `quiz_questions` - Quiz questions
- `student_activities` - Progress tracking
- `course_enrollments` - Student enrollments
- `schedules` - Scheduled events

## Conclusion

AstroLearn LMS is a comprehensive, modern learning management system designed for educational institutions. Its weighted module system, advanced quiz functionality, and role-based access control make it suitable for K-12 schools, universities, and corporate training programs.

For support and additional resources, please refer to the in-app documentation or contact your system administrator.
