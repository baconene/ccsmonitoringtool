<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Book, 
  Home, 
  Users, 
  BookOpen, 
  GraduationCap, 
  Settings, 
  BarChart, 
  FileText,
  ChevronRight,
  Menu,
  X
} from 'lucide-vue-next';

interface DocSection {
  id: string;
  title: string;
  icon: any;
  subsections?: {
    id: string;
    title: string;
  }[];
}

const sections: DocSection[] = [
  {
    id: 'getting-started',
    title: 'Getting Started',
    icon: Home,
    subsections: [
      { id: 'introduction', title: 'Introduction' },
      { id: 'system-requirements', title: 'System Requirements' },
      { id: 'installation', title: 'Installation' },
    ]
  },
  {
    id: 'user-management',
    title: 'User Management',
    icon: Users,
    subsections: [
      { id: 'roles-permissions', title: 'Roles & Permissions' },
      { id: 'adding-users', title: 'Adding Users' },
      { id: 'managing-students', title: 'Managing Students' },
      { id: 'managing-instructors', title: 'Managing Instructors' },
    ]
  },
  {
    id: 'course-management',
    title: 'Course Management',
    icon: BookOpen,
    subsections: [
      { id: 'creating-courses', title: 'Creating Courses' },
      { id: 'course-modules', title: 'Course Modules' },
      { id: 'course-lessons', title: 'Course Lessons' },
      { id: 'course-documents', title: 'Course Documents' },
      { id: 'grade-levels', title: 'Grade Levels' },
    ]
  },
  {
    id: 'student-enrollment',
    title: 'Student Enrollment',
    icon: GraduationCap,
    subsections: [
      { id: 'enrolling-students', title: 'Enrolling Students' },
      { id: 'managing-enrollments', title: 'Managing Enrollments' },
      { id: 'grade-level-restrictions', title: 'Grade Level Restrictions' },
    ]
  },
  {
    id: 'analytics',
    title: 'Analytics & Reports',
    icon: BarChart,
    subsections: [
      { id: 'dashboard-overview', title: 'Dashboard Overview' },
      { id: 'student-progress', title: 'Student Progress' },
      { id: 'course-analytics', title: 'Course Analytics' },
    ]
  },
  {
    id: 'system-settings',
    title: 'System Settings',
    icon: Settings,
    subsections: [
      { id: 'general-settings', title: 'General Settings' },
      { id: 'security-settings', title: 'Security Settings' },
      { id: 'appearance-settings', title: 'Appearance Settings' },
    ]
  },
];

const activeSection = ref('introduction');
const sidebarOpen = ref(false);

const activeContent = computed(() => {
  // Return content based on active section
  return getContentForSection(activeSection.value);
});

function getContentForSection(sectionId: string) {
  const contentMap: Record<string, any> = {
    'introduction': {
      title: 'Welcome to Bacon Edu Documentation',
      content: `
        <p class="text-lg mb-6">Bacon Edu is a comprehensive Learning Management System (LMS) designed to streamline educational processes for schools, colleges, and training institutions.</p>
        
        <h3 class="text-2xl font-bold mb-4">Key Features</h3>
        <ul class="list-disc pl-6 space-y-2 mb-6">
          <li><strong>Course Management:</strong> Create and organize courses with modules, lessons, and documents</li>
          <li><strong>User Management:</strong> Manage students, instructors, and administrators with role-based access</li>
          <li><strong>Student Enrollment:</strong> Enroll students in courses with grade level restrictions</li>
          <li><strong>Analytics:</strong> Track student progress and course performance</li>
          <li><strong>Dark Mode:</strong> Built-in dark mode support for comfortable learning</li>
        </ul>

        <h3 class="text-2xl font-bold mb-4">Who is this for?</h3>
        <p class="mb-4">Bacon Edu is perfect for:</p>
        <ul class="list-disc pl-6 space-y-2">
          <li>Educational institutions looking to digitize their learning processes</li>
          <li>Corporate training departments</li>
          <li>Online course creators and educators</li>
          <li>Schools and universities managing multiple courses and students</li>
        </ul>
      `
    },
    'system-requirements': {
      title: 'System Requirements',
      content: `
        <p class="text-lg mb-6">Before installing Bacon Edu, ensure your system meets the following requirements:</p>
        
        <h3 class="text-2xl font-bold mb-4">Server Requirements</h3>
        <ul class="list-disc pl-6 space-y-2 mb-6">
          <li>PHP 8.1 or higher</li>
          <li>Laravel 10.x</li>
          <li>MySQL 8.0 or PostgreSQL 13+</li>
          <li>Node.js 18.x or higher</li>
          <li>NPM or Yarn package manager</li>
        </ul>

        <h3 class="text-2xl font-bold mb-4">Browser Requirements</h3>
        <ul class="list-disc pl-6 space-y-2 mb-6">
          <li>Modern browsers (Chrome, Firefox, Safari, Edge)</li>
          <li>JavaScript enabled</li>
          <li>Cookies enabled</li>
        </ul>

        <h3 class="text-2xl font-bold mb-4">Recommended Specifications</h3>
        <ul class="list-disc pl-6 space-y-2">
          <li>2GB RAM minimum (4GB recommended)</li>
          <li>10GB disk space</li>
          <li>SSL certificate for production</li>
        </ul>
      `
    },
    'roles-permissions': {
      title: 'Roles & Permissions',
      content: `
        <p class="text-lg mb-6">Bacon Edu uses a role-based access control system to manage user permissions.</p>
        
        <h3 class="text-2xl font-bold mb-4">Available Roles</h3>
        
        <div class="space-y-6">
          <div class="border-l-4 border-red-500 pl-4">
            <h4 class="text-xl font-bold mb-2">Admin</h4>
            <p class="mb-2">Full system access with the ability to:</p>
            <ul class="list-disc pl-6 space-y-1">
              <li>Manage all users (create, edit, delete)</li>
              <li>Create and manage all courses</li>
              <li>Access system settings</li>
              <li>View all analytics and reports</li>
              <li>Manage grade levels</li>
            </ul>
          </div>

          <div class="border-l-4 border-blue-500 pl-4">
            <h4 class="text-xl font-bold mb-2">Instructor</h4>
            <p class="mb-2">Course management access with the ability to:</p>
            <ul class="list-disc pl-6 space-y-1">
              <li>Create and manage own courses</li>
              <li>Enroll students in their courses</li>
              <li>View student progress in their courses</li>
              <li>Upload course materials</li>
            </ul>
          </div>

          <div class="border-l-4 border-green-500 pl-4">
            <h4 class="text-xl font-bold mb-2">Student</h4>
            <p class="mb-2">Learning access with the ability to:</p>
            <ul class="list-disc pl-6 space-y-1">
              <li>View enrolled courses</li>
              <li>Access course materials</li>
              <li>Track own progress</li>
              <li>View personal dashboard</li>
            </ul>
          </div>
        </div>
      `
    },
    'adding-users': {
      title: 'Adding Users',
      content: `
        <p class="text-lg mb-6">Learn how to add new users to your Bacon Edu system.</p>
        
        <h3 class="text-2xl font-bold mb-4">Step-by-Step Guide</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 1: Navigate to User Management</h4>
            <p>Go to <strong>Dashboard → Role Management</strong> from the main navigation.</p>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 2: Click "Add User"</h4>
            <p>Click the <strong>"Add User"</strong> button at the top of the user list.</p>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 3: Fill in User Details</h4>
            <ul class="list-disc pl-6 space-y-1 mt-2">
              <li><strong>Name:</strong> Full name of the user</li>
              <li><strong>Email:</strong> Valid email address (will be used for login)</li>
              <li><strong>Password:</strong> Must be at least 8 characters</li>
              <li><strong>Role:</strong> Select Admin, Instructor, or Student</li>
              <li><strong>Grade Level:</strong> (Students only) e.g., Grade 7, Grade 8, etc.</li>
              <li><strong>Section:</strong> (Students only) e.g., Section A, Room 101</li>
            </ul>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 4: Save</h4>
            <p>Click <strong>"Add User"</strong> to create the user account. The user can now log in with their email and password.</p>
          </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mt-6">
          <h4 class="font-bold mb-2">💡 Pro Tip</h4>
          <p>Student grade levels are important for course enrollment restrictions. Make sure to set them accurately!</p>
        </div>
      `
    },
    'creating-courses': {
      title: 'Creating Courses',
      content: `
        <p class="text-lg mb-6">Create comprehensive courses with modules, lessons, and materials.</p>
        
        <h3 class="text-2xl font-bold mb-4">Course Creation Process</h3>
        
        <div class="space-y-6">
          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 1: Navigate to Course Management</h4>
            <p>Go to <strong>Dashboard → Course Management</strong>.</p>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 2: Create New Course</h4>
            <p>Click <strong>"Add Course"</strong> and fill in the course details:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
              <li><strong>Name:</strong> Course code or identifier</li>
              <li><strong>Title:</strong> Full course title</li>
              <li><strong>Description:</strong> Course overview and objectives</li>
              <li><strong>Grade Levels:</strong> Select which grades can enroll (can select multiple)</li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 3: Add Modules</h4>
            <p>After creating the course, add modules to organize content:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
              <li>Click on the course card</li>
              <li>Click "Add Module"</li>
              <li>Enter module description and sequence number</li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 4: Add Lessons</h4>
            <p>Within each module, add lessons with content and materials.</p>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">Best Practices</h3>
        <ul class="list-disc pl-6 space-y-2">
          <li>Use clear, descriptive course titles</li>
          <li>Organize content logically with modules</li>
          <li>Set appropriate grade level restrictions</li>
          <li>Add comprehensive descriptions to help students understand course goals</li>
        </ul>
      `
    },
    'grade-levels': {
      title: 'Grade Levels',
      content: `
        <p class="text-lg mb-6">Bacon Edu uses grade levels to control which students can enroll in specific courses.</p>
        
        <h3 class="text-2xl font-bold mb-4">How Grade Levels Work</h3>
        
        <p class="mb-4">The system supports multiple grade levels per course, allowing you to create courses that are available to students across different grades.</p>

        <h3 class="text-2xl font-bold mb-4 mt-6">Available Grade Levels</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
            <p class="font-bold">Grade 7</p>
          </div>
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
            <p class="font-bold">Grade 8</p>
          </div>
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
            <p class="font-bold">Grade 9</p>
          </div>
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
            <p class="font-bold">Grade 10</p>
          </div>
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
            <p class="font-bold">Grade 11</p>
          </div>
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center">
            <p class="font-bold">Grade 12</p>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">Assigning Grade Levels to Courses</h3>
        <ol class="list-decimal pl-6 space-y-2 mb-6">
          <li>When creating or editing a course, you'll see grade level checkboxes</li>
          <li>Select all grade levels that should have access to the course</li>
          <li>Multiple grade levels can be selected for cross-grade courses</li>
          <li>If no grade levels are selected, the course will be available to all students</li>
        </ol>

        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
          <h4 class="font-bold mb-2">✅ Example Use Case</h4>
          <p>A "Basic Mathematics" course might be assigned to Grade 7 and Grade 8, while "Advanced Calculus" would only be assigned to Grade 11 and Grade 12.</p>
        </div>
      `
    },
    'enrolling-students': {
      title: 'Enrolling Students',
      content: `
        <p class="text-lg mb-6">Learn how to enroll students in courses using the drag-and-drop interface.</p>
        
        <h3 class="text-2xl font-bold mb-4">Enrollment Process</h3>
        
        <div class="space-y-6">
          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 1: Navigate to Manage Students</h4>
            <p>Go to <strong>Course Management → Click on a Course → Manage Students</strong></p>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 2: View Eligible Students</h4>
            <p>The system will automatically filter and show only students whose grade level matches the course requirements.</p>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 3: Enroll Students</h4>
            <p>You can enroll students in two ways:</p>
            <ul class="list-disc pl-6 space-y-1 mt-2">
              <li><strong>Drag & Drop:</strong> Drag student cards from "Available Students" to "Enrolled Students"</li>
              <li><strong>Bulk Enroll:</strong> Select multiple students and click "Enroll Selected"</li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 4: Remove Students (if needed)</h4>
            <p>To unenroll students, drag them from "Enrolled Students" back to "Available Students" or use the "Remove Selected" button.</p>
          </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mt-6">
          <h4 class="font-bold mb-2">⚠️ Important</h4>
          <p>Students can only be enrolled if their grade level matches one of the course's assigned grade levels. The system will prevent enrollment of ineligible students.</p>
        </div>
      `
    },
    'dashboard-overview': {
      title: 'Dashboard Overview',
      content: `
        <p class="text-lg mb-6">Your dashboard provides a comprehensive overview of the system's key metrics and activities.</p>
        
        <h3 class="text-2xl font-bold mb-4">Dashboard Components</h3>
        
        <div class="space-y-4">
          <div class="border-l-4 border-blue-500 pl-4">
            <h4 class="text-xl font-bold mb-2">User Statistics</h4>
            <p>View the total number of admins, instructors, and students in the system.</p>
          </div>

          <div class="border-l-4 border-purple-500 pl-4">
            <h4 class="text-xl font-bold mb-2">Course Statistics</h4>
            <p>Track the total number of courses and their average completion rates.</p>
          </div>

          <div class="border-l-4 border-green-500 pl-4">
            <h4 class="text-xl font-bold mb-2">Recent Activity</h4>
            <p>See the latest enrollments, course creations, and user registrations.</p>
          </div>

          <div class="border-l-4 border-orange-500 pl-4">
            <h4 class="text-xl font-bold mb-2">Quick Actions</h4>
            <p>Access frequently used features directly from the dashboard.</p>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">Customization</h3>
        <p>Dashboard widgets can be customized based on your role. Administrators see system-wide metrics, while instructors see course-specific data.</p>
      `
    }
  };

  return contentMap[sectionId] || contentMap['introduction'];
}

function selectSection(sectionId: string) {
  activeSection.value = sectionId;
  sidebarOpen.value = false;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
  <Head title="Documentation - Bacon Edu" />
  
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <Link href="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-violet-500 rounded-lg flex items-center justify-center">
              <Book class="w-5 h-5 text-white" />
            </div>
            <span class="text-xl font-bold text-gray-900 dark:text-white">Bacon Edu Docs</span>
          </Link>

          <!-- Mobile Menu Button -->
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <Menu v-if="!sidebarOpen" class="w-6 h-6" />
            <X v-else class="w-6 h-6" />
          </button>

          <!-- Desktop Actions -->
          <div class="hidden lg:flex items-center space-x-4">
            <Link
              href="/"
              class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            >
              Back to Home
            </Link>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex gap-8">
        <!-- Sidebar -->
        <aside 
          :class="[
            'fixed lg:sticky top-16 left-0 h-[calc(100vh-4rem)] w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto transition-transform duration-300 z-20',
            sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
          ]"
        >
          <nav class="p-4 space-y-2">
            <template v-for="section in sections" :key="section.id">
              <div class="space-y-1">
                <button
                  @click="selectSection(section.subsections?.[0]?.id || section.id)"
                  class="w-full flex items-center gap-3 px-4 py-3 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors font-medium"
                >
                  <component :is="section.icon" class="w-5 h-5 flex-shrink-0" />
                  <span>{{ section.title }}</span>
                </button>
                
                <div v-if="section.subsections" class="ml-4 pl-4 border-l-2 border-gray-200 dark:border-gray-700 space-y-1">
                  <button
                    v-for="subsection in section.subsections"
                    :key="subsection.id"
                    @click="selectSection(subsection.id)"
                    :class="[
                      'w-full flex items-center gap-2 px-4 py-2 text-left text-sm rounded-lg transition-colors',
                      activeSection === subsection.id
                        ? 'bg-gradient-to-r from-pink-500 to-violet-500 text-white font-medium'
                        : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                    ]"
                  >
                    <ChevronRight class="w-4 h-4 flex-shrink-0" />
                    <span>{{ subsection.title }}</span>
                  </button>
                </div>
              </div>
            </template>
          </nav>
        </aside>

        <!-- Overlay for mobile -->
        <div
          v-if="sidebarOpen"
          @click="sidebarOpen = false"
          class="fixed inset-0 bg-black/50 z-10 lg:hidden"
        ></div>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
              {{ activeContent.title }}
            </h1>
            
            <div 
              class="prose prose-lg dark:prose-invert max-w-none"
              v-html="activeContent.content"
            ></div>
          </div>

          <!-- Navigation Footer -->
          <div class="mt-8 flex justify-between items-center">
            <Link
              href="/"
              class="flex items-center gap-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
              <ChevronRight class="w-4 h-4 rotate-180" />
              <span>Back to Home</span>
            </Link>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
aside::-webkit-scrollbar {
  width: 6px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

aside::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.dark aside::-webkit-scrollbar-thumb {
  background: #4b5563;
}

aside::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.dark aside::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
