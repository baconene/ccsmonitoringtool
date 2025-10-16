# AppSidebar Update - Student Management Link

## Changes Made

### File Modified
`resources/js/components/AppSidebar.vue`

### Changes

#### 1. Added UserCog Icon Import
```typescript
import { BookOpen, Folder, LayoutGrid, GraduationCap, ClipboardList, Users, Calendar, UserCog } from 'lucide-vue-next';
```

#### 2. Added Student Management to Instructor Navigation
```typescript
const instructorNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Schedule',
        href: '/schedule',
        icon: Calendar,
    },
    {
        title: 'Course Management',
        href: "/course-management",
        icon: GraduationCap,
    },
    {
        title: 'Student Management',  // ← NEW
        href: "/student-management",  // ← NEW
        icon: UserCog,                // ← NEW
    },
    {
        title: 'Activities',
        href: "/activity-management",
        icon: ClipboardList,
    },
    // ... rest of items
];
```

#### 3. Added Student Management to Admin Navigation
```typescript
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Schedule',
        href: '/schedule',
        icon: Calendar,
    },
    {
        title: 'Course Management',
        href: "/course-management",
        icon: GraduationCap,
    },
    {
        title: 'Student Management',  // ← NEW
        href: "/student-management",  // ← NEW
        icon: UserCog,                // ← NEW
    },
    {
        title: 'Assessment Tool',
        href: "/assessment-tool",
        icon: BookOpen,
    },
    // ... rest of items
];
```

## Navigation Order

### For Instructors (Updated)
1. Dashboard
2. Schedule
3. Course Management
4. **Student Management** ← NEW (positioned after Course Management)
5. Activities
6. Assessment Tool
7. Grade Reports

### For Admins (Updated)
1. Dashboard
2. Schedule
3. Course Management
4. **Student Management** ← NEW (positioned after Course Management)
5. Assessment Tool
6. Activities
7. Role Management
8. Grade Reports

### For Students (Unchanged)
- No changes to student navigation

## Icon Used
**UserCog** from Lucide Icons - Represents user management/monitoring functionality

## Visual Position
The "Student Management" link appears:
- **After**: Course Management (logical flow: manage courses → manage students in those courses)
- **Before**: Activities (students are monitored through their activities)

## Access Control
- ✅ **Instructors**: Can access (see their students)
- ✅ **Admins**: Can access (see all students)
- ❌ **Students**: Cannot access (not in their navigation)

## Testing
To verify the changes:
1. Login as **Instructor**
2. Check sidebar - Should see "Student Management" link
3. Click the link - Should navigate to `/student-management`
4. Verify the page loads correctly

## Next Steps
1. Refresh the browser (or run `npm run build`)
2. Login as instructor
3. See the new "Student Management" link in the sidebar
4. Click it to access the student monitoring dashboard

## Notes
- Link uses the UserCog icon which visually represents user/student management
- Positioned strategically after "Course Management" for logical workflow
- Available to both admins and instructors (both roles need student monitoring)
- Not available to students (they don't need to manage other students)
