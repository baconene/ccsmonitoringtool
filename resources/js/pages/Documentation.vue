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
      { id: 'system-overview', title: 'System Overview' },
      { id: 'system-requirements', title: 'System Requirements' },
    ]
  },
  {
    id: 'user-roles',
    title: 'User Roles & Access',
    icon: Users,
    subsections: [
      { id: 'roles-permissions', title: 'Roles & Permissions' },
      { id: 'student-role', title: 'Student Role' },
      { id: 'teacher-role', title: 'Teacher/Instructor Role' },
      { id: 'admin-role', title: 'Admin Role' },
      { id: 'adding-users', title: 'Adding Users' },
    ]
  },
  {
    id: 'course-management',
    title: 'Course Management',
    icon: BookOpen,
    subsections: [
      { id: 'creating-courses', title: 'Creating Courses' },
      { id: 'course-modules', title: 'Course Modules & Weights' },
      { id: 'course-progress', title: 'Course Progress Calculation' },
      { id: 'grade-levels', title: 'Grade Levels' },
    ]
  },
  {
    id: 'activities-system',
    title: 'Activities & Assessments',
    icon: FileText,
    subsections: [
      { id: 'activity-types', title: 'Activity Types' },
      { id: 'creating-activities', title: 'Creating Activities' },
      { id: 'quiz-system', title: 'Quiz System' },
      { id: 'quiz-calculation', title: 'Quiz Score Calculation' },
      { id: 'quiz-taking', title: 'Taking Quizzes' },
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
      { id: 'student-progress', title: 'Student Progress Tracking' },
      { id: 'course-analytics', title: 'Course Analytics' },
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
      title: 'Welcome to Bacon Edu Learning Management System',
      content: `
        <p class="text-lg mb-6">Bacon Edu is a comprehensive Learning Management System (LMS) designed to streamline educational processes for schools, colleges, and training institutions. Built with Laravel 11, Vue 3, TypeScript, and Inertia.js, it provides a modern, intuitive interface for managing courses, activities, and student progress.</p>
        
        <h3 class="text-2xl font-bold mb-4">🎯 Key Features</h3>
        <ul class="list-disc pl-6 space-y-2 mb-6">
          <li><strong>Weighted Module System:</strong> Create courses with modules that have custom weights, allowing flexible progress calculation</li>
          <li><strong>Activity Management:</strong> Support for multiple activity types including quizzes, assignments, lessons, and assessments</li>
          <li><strong>Advanced Quiz System:</strong> Create quizzes with multiple question types (multiple-choice, true-false, enumeration, short-answer)</li>
          <li><strong>Real-time Progress Tracking:</strong> Module-based progress calculation that updates automatically when students complete modules</li>
          <li><strong>Role-Based Access Control:</strong> Three distinct roles (Admin, Teacher/Instructor, Student) with specific permissions</li>
          <li><strong>Grade Level Management:</strong> Assign courses to specific grade levels (Year 1-5, Grade 1-12)</li>
          <li><strong>Student Achievement Tracking:</strong> Track quiz scores, module completions, and overall course progress</li>
          <li><strong>Modern UI/UX:</strong> Built-in dark mode, responsive design, and intuitive drag-and-drop interfaces</li>
        </ul>

        <h3 class="text-2xl font-bold mb-4">🎓 Who is this for?</h3>
        <p class="mb-4">Bacon Edu is perfect for:</p>
        <ul class="list-disc pl-6 space-y-2 mb-6">
          <li>K-12 schools and educational institutions</li>
          <li>Colleges and universities managing multiple courses</li>
          <li>Corporate training and professional development departments</li>
          <li>Online course creators and educators</li>
          <li>Training centers and academies</li>
        </ul>

        <h3 class="text-2xl font-bold mb-4">🚀 Technology Stack</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Backend</h4>
            <ul class="text-sm space-y-1">
              <li>• Laravel 11</li>
              <li>• PHP 8.1+</li>
              <li>• MySQL/SQLite</li>
              <li>• Inertia.js</li>
            </ul>
          </div>
          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Frontend</h4>
            <ul class="text-sm space-y-1">
              <li>• Vue 3 Composition API</li>
              <li>• TypeScript</li>
              <li>• Tailwind CSS</li>
              <li>• Vite</li>
            </ul>
          </div>
        </div>
      `
    },
    'system-overview': {
      title: 'System Overview',
      content: `
        <p class="text-lg mb-6">Understanding the core architecture and workflow of Bacon Edu LMS.</p>
        
        <h3 class="text-2xl font-bold mb-4">📚 System Architecture</h3>
        
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
          <h4 class="text-xl font-bold mb-4">Course Hierarchy</h4>
          <div class="space-y-2 text-sm">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
              <div>
                <strong>Course</strong> - Top-level container assigned to specific grade levels
              </div>
            </div>
            <div class="flex items-center gap-3 ml-4">
              <div class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
              <div>
                <strong>Modules</strong> - Organized sections with custom weights (e.g., 20%, 30%)
              </div>
            </div>
            <div class="flex items-center gap-3 ml-8">
              <div class="w-8 h-8 bg-pink-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
              <div>
                <strong>Activities</strong> - Quizzes, assignments, lessons, assessments
              </div>
            </div>
            <div class="flex items-center gap-3 ml-12">
              <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
              <div>
                <strong>Content</strong> - Questions, materials, resources
              </div>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">🔄 Workflow</h3>
        
        <div class="space-y-4 mb-6">
          <div class="border-l-4 border-blue-500 pl-4 py-2">
            <h4 class="font-bold text-lg mb-1">1. Course Setup (Admin/Teacher)</h4>
            <p class="text-sm">Create course → Add modules with weights → Create activities → Assign to grade levels</p>
          </div>
          
          <div class="border-l-4 border-purple-500 pl-4 py-2">
            <h4 class="font-bold text-lg mb-1">2. Student Enrollment</h4>
            <p class="text-sm">Admin/Teacher enrolls students based on grade level eligibility</p>
          </div>
          
          <div class="border-l-4 border-pink-500 pl-4 py-2">
            <h4 class="font-bold text-lg mb-1">3. Learning Process</h4>
            <p class="text-sm">Students access courses → Complete activities → Take quizzes → Mark modules complete</p>
          </div>
          
          <div class="border-l-4 border-green-500 pl-4 py-2">
            <h4 class="font-bold text-lg mb-1">4. Progress Tracking</h4>
            <p class="text-sm">System automatically calculates progress based on completed module weights</p>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">📊 Key Metrics</h3>
        <ul class="list-disc pl-6 space-y-2">
          <li><strong>Course Progress:</strong> Calculated from sum of completed module weights (0-100%)</li>
          <li><strong>Quiz Scores:</strong> Based on total points earned vs. total possible points</li>
          <li><strong>Module Completion:</strong> Binary state (complete/incomplete)</li>
          <li><strong>Time Tracking:</strong> Records time spent on quizzes and activities</li>
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
      title: 'Roles & Permissions Overview',
      content: `
        <p class="text-lg mb-6">Bacon Edu uses a role-based access control (RBAC) system with three primary roles, each designed for specific functions within the learning management ecosystem.</p>
        
        <h3 class="text-2xl font-bold mb-4">👥 User Roles Summary</h3>
        
        <div class="space-y-6 mb-8">
          <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2 flex items-center gap-2">
              <span class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center text-sm">A</span>
              Admin
            </h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">System administrator with full control over all platform features.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
              <div>✅ Manage all users</div>
              <div>✅ Create/manage all courses</div>
              <div>✅ Access system settings</div>
              <div>✅ View all analytics</div>
              <div>✅ Manage grade levels</div>
              <div>✅ Configure activity types</div>
            </div>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2 flex items-center gap-2">
              <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm">T</span>
              Teacher/Instructor
            </h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">Educator responsible for creating and managing course content.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
              <div>✅ Create courses</div>
              <div>✅ Design modules</div>
              <div>✅ Create activities & quizzes</div>
              <div>✅ Enroll students</div>
              <div>✅ Track student progress</div>
              <div>✅ Grade assessments</div>
            </div>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2 flex items-center gap-2">
              <span class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm">S</span>
              Student
            </h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">Learner who accesses course materials and completes activities.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
              <div>✅ View enrolled courses</div>
              <div>✅ Access course content</div>
              <div>✅ Take quizzes</div>
              <div>✅ Complete activities</div>
              <div>✅ Track own progress</div>
              <div>✅ View achievements</div>
            </div>
          </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
          <h4 class="font-bold mb-2">🔒 Security Note</h4>
          <p class="text-sm">Role permissions are enforced at both the backend (Laravel middleware) and frontend (Inertia.js) levels to ensure secure access control.</p>
        </div>
      `
    },
    'student-role': {
      title: 'Student Role - Learning & Achievement',
      content: `
        <p class="text-lg mb-6">Students are the primary learners in the system. They have access to enrolled courses and can track their learning progress.</p>
        
        <h3 class="text-2xl font-bold mb-4">🎒 Student Capabilities</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">1. Course Access</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>View all courses they are enrolled in</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Access course modules and their activities</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>See course progress based on completed module weights</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Track which modules have been completed</span>
              </li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">2. Activity Participation</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>View all activities within enrolled courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Take quizzes with auto-save functionality</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Submit assignments and assessments</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Mark modules as complete</span>
              </li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">3. Progress & Achievement Tracking</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>View course completion percentage (module weight-based)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>See quiz scores and percentage results</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Review quiz answers and correct solutions</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Track time spent on activities</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>View completed vs. total modules</span>
              </li>
            </ul>
          </div>

          <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">4. Personal Dashboard</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Overview of all enrolled courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Statistics: completed courses, in-progress courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Average progress across all courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Quick access to continue learning</span>
              </li>
            </ul>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">📊 Student Workflow</h3>
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6">
          <ol class="space-y-3">
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</span>
              <div>
                <strong>Log in</strong> and access the student dashboard
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">2</span>
              <div>
                <strong>Select a course</strong> from enrolled courses list
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">3</span>
              <div>
                <strong>Navigate modules</strong> and access activities
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">4</span>
              <div>
                <strong>Complete activities</strong> (quizzes, assignments, lessons)
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">5</span>
              <div>
                <strong>Mark modules complete</strong> to update course progress
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">6</span>
              <div>
                <strong>Review results</strong> and track achievements
              </div>
            </li>
          </ol>
        </div>
      `
    },
    'teacher-role': {
      title: 'Teacher/Instructor Role - Course Creation & Management',
      content: `
        <p class="text-lg mb-6">Teachers/Instructors are responsible for creating educational content, managing courses, and tracking student progress.</p>
        
        <h3 class="text-2xl font-bold mb-4">👨‍🏫 Teacher Capabilities</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">1. Course Creation & Management</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Create new courses with title, description, and grade level assignments</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Edit and update course information</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Delete courses (with confirmation)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Assign courses to multiple grade levels (Year 1-5, Grade 1-12)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>View all courses they've created</span>
              </li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">2. Module Design with Weights</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Create modules within courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Assign module weights (percentage: 0-100%) for progress calculation</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Set module types: Lessons, Activities, Quizzes, Assessments, Mixed</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Organize modules with sequence numbers</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Add descriptions and learning objectives to modules</span>
              </li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">3. Activity & Assessment Creation</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Create activities: Quizzes, Assignments, Lessons, Assessments</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Design quizzes with multiple question types</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Set passing percentages for quizzes (default: 70%)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Add question points for grading</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Attach activities to modules</span>
              </li>
            </ul>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">4. Student Management</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>Enroll students in courses (drag-and-drop interface)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>View eligible students based on grade level</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>Remove students from courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>Bulk enroll/unenroll operations</span>
              </li>
            </ul>
          </div>

          <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">5. Progress Tracking & Analytics</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>View student progress in their courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>See quiz scores and completion rates</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Track module completion by students</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Grade enumeration and short-answer questions manually</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Export student progress reports</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="bg-blue-100 dark:bg-blue-900/30 border-l-4 border-blue-500 p-4 mt-6">
          <h4 class="font-bold mb-2">💡 Teacher Best Practices</h4>
          <ul class="text-sm space-y-1">
            <li>• Set module weights that reflect actual importance (e.g., Final Exam: 30%, Midterm: 20%)</li>
            <li>• Ensure module weights add up to 100% for accurate progress tracking</li>
            <li>• Use descriptive module titles and clear learning objectives</li>
            <li>• Create balanced quizzes with varied question types</li>
            <li>• Regularly review student progress and provide feedback</li>
          </ul>
        </div>
      `
    },
    'admin-role': {
      title: 'Admin Role - System Management & Configuration',
      content: `
        <p class="text-lg mb-6">Administrators have full control over the system, including user management, system configuration, and oversight of all courses and activities.</p>
        
        <h3 class="text-2xl font-bold mb-4">⚙️ Admin Capabilities</h3>
        
        <div class="space-y-6">
          <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">1. User Management (CRUD)</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-red-500 font-bold">•</span>
                <span><strong>Create:</strong> Add new users (Admin, Teacher, Student)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-red-500 font-bold">•</span>
                <span><strong>Read:</strong> View all users with filtering and search</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-red-500 font-bold">•</span>
                <span><strong>Update:</strong> Edit user information, roles, and permissions</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-red-500 font-bold">•</span>
                <span><strong>Delete:</strong> Remove users from the system</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-red-500 font-bold">•</span>
                <span>Assign grade levels and sections to students</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-red-500 font-bold">•</span>
                <span>Bulk user operations (import/export)</span>
              </li>
            </ul>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">2. Course Management (Full Access)</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>View and manage all courses in the system</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Create, edit, and delete any course</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Modify course modules and activities</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Reassign courses to different instructors</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">•</span>
                <span>Archive or restore courses</span>
              </li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">3. Activity Type Configuration</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Manage activity type definitions</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Create custom activity types</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Configure default settings for activities</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500 font-bold">•</span>
                <span>Set system-wide passing percentages</span>
              </li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">4. Grade Level Management</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Manage grade level definitions (Year 1-5, Grade 1-12)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Create custom grade levels or education levels</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">•</span>
                <span>Assign/remove grade levels from courses</span>
              </li>
            </ul>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">5. System Analytics & Reports</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>View system-wide dashboard with all metrics</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>Track user activity and login history</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>Monitor course enrollment statistics</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>View quiz performance across all courses</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-orange-500 font-bold">•</span>
                <span>Generate and export comprehensive reports</span>
              </li>
            </ul>
          </div>

          <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">6. System Configuration</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Configure general system settings</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Manage security and authentication settings</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Set up email notifications</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Configure appearance and branding</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-pink-500 font-bold">•</span>
                <span>Backup and restore data</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mt-6">
          <h4 class="font-bold mb-2">🔐 Admin Responsibilities</h4>
          <ul class="text-sm space-y-1">
            <li>• Regularly monitor system health and performance</li>
            <li>• Ensure data privacy and security compliance</li>
            <li>• Manage user access and permissions appropriately</li>
            <li>• Keep the system updated and backed up</li>
            <li>• Provide technical support to teachers and students</li>
          </ul>
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
    'activity-types': {
      title: 'Activity Types in Bacon Edu',
      content: `
        <p class="text-lg mb-6">Bacon Edu supports multiple activity types that can be attached to course modules. Each activity type serves a different educational purpose.</p>
        
        <h3 class="text-2xl font-bold mb-4">📝 Available Activity Types</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">1. Quiz</h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">Interactive assessments with multiple question types and auto-grading.</p>
            <div class="text-sm space-y-1">
              <div><strong>Purpose:</strong> Test knowledge and understanding</div>
              <div><strong>Grading:</strong> Automatic for MCQ/True-False, Manual for text-based</div>
              <div><strong>Features:</strong> Time tracking, score percentage, pass/fail status</div>
              <div><strong>Question Types:</strong> Multiple-choice, True-False, Enumeration, Short-answer</div>
            </div>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">2. Assignment</h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">Projects or tasks that students complete and submit for grading.</p>
            <div class="text-sm space-y-1">
              <div><strong>Purpose:</strong> Practical application of concepts</div>
              <div><strong>Grading:</strong> Manual teacher review</div>
              <div><strong>Features:</strong> File uploads, submission tracking, due dates</div>
              <div><strong>Use Cases:</strong> Essays, projects, reports, presentations</div>
            </div>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">3. Lesson</h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">Educational content delivery including readings, videos, and materials.</p>
            <div class="text-sm space-y-1">
              <div><strong>Purpose:</strong> Deliver instructional content</div>
              <div><strong>Grading:</strong> Completion-based (no scoring)</div>
              <div><strong>Features:</strong> Rich text content, embedded media, downloads</div>
              <div><strong>Use Cases:</strong> Lectures, readings, video tutorials, documentation</div>
            </div>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">4. Assessment</h4>
            <p class="mb-3 text-gray-700 dark:text-gray-300">Comprehensive evaluations like midterms, finals, or major tests.</p>
            <div class="text-sm space-y-1">
              <div><strong>Purpose:</strong> Evaluate overall learning outcomes</div>
              <div><strong>Grading:</strong> Combination of auto and manual</div>
              <div><strong>Features:</strong> Time limits, proctoring options, weighted scoring</div>
              <div><strong>Use Cases:</strong> Midterm exams, final exams, certification tests</div>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">➕ Adding Activities to Modules</h3>
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6">
          <ol class="space-y-4">
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">1</span>
              <div>
                <strong class="block mb-1">Navigate to Activity Management</strong>
                <span class="text-sm text-gray-600 dark:text-gray-400">Go to <strong>Dashboard → Activity Management</strong></span>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">2</span>
              <div>
                <strong class="block mb-1">Create New Activity</strong>
                <span class="text-sm text-gray-600 dark:text-gray-400">Click "Add Activity" and fill in details:</span>
                <ul class="text-sm mt-2 space-y-1 ml-4">
                  <li>• <strong>Title:</strong> Activity name</li>
                  <li>• <strong>Description:</strong> What students will learn/do</li>
                  <li>• <strong>Activity Type:</strong> Select from Quiz, Assignment, Lesson, Assessment</li>
                  <li>• <strong>Course:</strong> Select target course</li>
                  <li>• <strong>Passing Percentage:</strong> For quizzes/assessments (default: 70%)</li>
                </ul>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">3</span>
              <div>
                <strong class="block mb-1">Configure Activity Content</strong>
                <span class="text-sm text-gray-600 dark:text-gray-400">For Quizzes: Add questions with options and points</span>
                <span class="text-sm text-gray-600 dark:text-gray-400 block">For Assignments: Set instructions and rubrics</span>
                <span class="text-sm text-gray-600 dark:text-gray-400 block">For Lessons: Add educational content</span>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">4</span>
              <div>
                <strong class="block mb-1">Attach to Module</strong>
                <span class="text-sm text-gray-600 dark:text-gray-400">In Course Management, select the course → Module → Link existing activities or create new ones</span>
              </div>
            </li>
          </ol>
        </div>
      `
    },
    'creating-activities': {
      title: 'Creating Activities Step-by-Step',
      content: `
        <p class="text-lg mb-6">Learn how to create engaging activities for your course modules.</p>
        
        <h3 class="text-2xl font-bold mb-4">🎯 Creating a Quiz Activity</h3>
        
        <div class="space-y-4">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 1: Activity Details</h4>
            <ul class="text-sm space-y-1 ml-4">
              <li>• Navigate to <strong>Activity Management → Add Activity</strong></li>
              <li>• Enter <strong>Title</strong>: e.g., "Chapter 1 Quiz"</li>
              <li>• Add <strong>Description</strong>: What the quiz covers</li>
              <li>• Select <strong>Activity Type</strong>: "Quiz"</li>
              <li>• Set <strong>Passing Percentage</strong>: Default 70% (customizable)</li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 2: Add Questions</h4>
            <p class="text-sm mb-2">Click on the created quiz and add questions:</p>
            
            <div class="space-y-3 ml-4">
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm block mb-1">Multiple Choice Questions</strong>
                <ul class="text-xs space-y-1">
                  <li>• Enter question text</li>
                  <li>• Add 2-6 answer options</li>
                  <li>• Mark correct answer with checkbox</li>
                  <li>• Assign points (e.g., 1-5 points)</li>
                </ul>
              </div>
              
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm block mb-1">True/False Questions</strong>
                <ul class="text-xs space-y-1">
                  <li>• Enter question text</li>
                  <li>• System auto-creates True/False options</li>
                  <li>• Select correct answer</li>
                  <li>• Assign points</li>
                </ul>
              </div>
              
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm block mb-1">Enumeration/Short Answer</strong>
                <ul class="text-xs space-y-1">
                  <li>• Enter question text</li>
                  <li>• Add model answer (optional)</li>
                  <li>• Assign points</li>
                  <li>• <em>Requires manual grading</em></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Step 3: Review & Save</h4>
            <ul class="text-sm space-y-1 ml-4">
              <li>• Preview all questions</li>
              <li>• Check total points calculation</li>
              <li>• Verify correct answers are marked</li>
              <li>• Save quiz</li>
            </ul>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">💡 Activity Best Practices</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">✅ Do</h4>
            <ul class="text-sm space-y-1">
              <li>✓ Use clear, unambiguous question wording</li>
              <li>✓ Provide sufficient points for difficulty</li>
              <li>✓ Mix question types for variety</li>
              <li>✓ Include explanations for complex questions</li>
              <li>✓ Test your quiz before assigning to students</li>
            </ul>
          </div>
          
          <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">❌ Don't</h4>
            <ul class="text-sm space-y-1">
              <li>✗ Use trick questions or confusing language</li>
              <li>✗ Make all questions same difficulty</li>
              <li>✗ Forget to mark correct answers</li>
              <li>✗ Assign zero points to questions</li>
              <li>✗ Create quizzes without clear learning objectives</li>
            </ul>
          </div>
        </div>
      `
    },
    'course-modules': {
      title: 'Creating Course Modules with Weights',
      content: `
        <p class="text-lg mb-6">Modules are the building blocks of courses. They organize content and activities, and their weights determine how much they contribute to overall course progress.</p>
        
        <h3 class="text-2xl font-bold mb-4">🏗️ Module Creation Process</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 1: Access Course Management</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">1.</span>
                <span>Go to <strong>Dashboard → Course Management</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">2.</span>
                <span>Click on the course card you want to add modules to</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">3.</span>
                <span>Navigate to the <strong>Modules</strong> tab or section</span>
              </li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 2: Create Module</h4>
            <p class="mb-3 text-sm">Click <strong>"Add Module"</strong> and fill in the following:</p>
            <div class="space-y-3">
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm">Title</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Clear, descriptive name (e.g., "Introduction to Algebra")</p>
              </div>
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm">Description</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">What students will learn in this module</p>
              </div>
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm">Module Type</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Options: Lessons, Activities, Quizzes, Assessments, Mixed</p>
              </div>
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm">Module Percentage (Weight) ⚖️</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">How much this module contributes to course completion (0-100%)</p>
              </div>
              <div class="bg-white dark:bg-gray-800 p-3 rounded">
                <strong class="text-sm">Sequence Number</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Order in which module appears (1, 2, 3...)</p>
              </div>
            </div>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 3: Assign Module Weight</h4>
            <p class="text-sm mb-3"><strong>Module weights determine course progress calculation.</strong> Here's how it works:</p>
            
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg mb-3">
              <h5 class="font-bold text-sm mb-2">Example Course Structure:</h5>
              <table class="w-full text-xs">
                <thead>
                  <tr class="border-b border-gray-300 dark:border-gray-600">
                    <th class="text-left py-2">Module</th>
                    <th class="text-center py-2">Weight</th>
                    <th class="text-left py-2">Rationale</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="py-2">Introduction</td>
                    <td class="text-center">10%</td>
                    <td class="text-gray-600 dark:text-gray-400">Basic concepts</td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="py-2">Core Lessons</td>
                    <td class="text-center">25%</td>
                    <td class="text-gray-600 dark:text-gray-400">Main content</td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="py-2">Practice Activities</td>
                    <td class="text-center">15%</td>
                    <td class="text-gray-600 dark:text-gray-400">Skill building</td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="py-2">Midterm Exam</td>
                    <td class="text-center">20%</td>
                    <td class="text-gray-600 dark:text-gray-400">Progress check</td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="py-2">Final Project</td>
                    <td class="text-center">30%</td>
                    <td class="text-gray-600 dark:text-gray-400">Major assessment</td>
                  </tr>
                  <tr class="font-bold">
                    <td class="py-2">TOTAL</td>
                    <td class="text-center">100%</td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500 p-3 text-sm">
              <strong>⚠️ Important:</strong> Module weights should add up to approximately 100% for accurate progress tracking.
            </div>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 4: Add Activities to Module</h4>
            <p class="text-sm mb-2">After creating the module, add activities:</p>
            <ul class="space-y-2 text-sm ml-4">
              <li>• Click on the module to expand</li>
              <li>• Select <strong>"Add Activity"</strong> or <strong>"Link Existing Activity"</strong></li>
              <li>• Choose activity type and configure</li>
              <li>• Activities can be reordered within the module</li>
            </ul>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">📐 Module Weight Guidelines</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Recommended Weights</h4>
            <ul class="text-sm space-y-1">
              <li>• <strong>Introductory modules:</strong> 5-10%</li>
              <li>• <strong>Core content modules:</strong> 20-30%</li>
              <li>• <strong>Practice modules:</strong> 10-15%</li>
              <li>• <strong>Midterm assessments:</strong> 15-20%</li>
              <li>• <strong>Final assessments:</strong> 25-35%</li>
            </ul>
          </div>
          
          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Weight Distribution Tips</h4>
            <ul class="text-sm space-y-1">
              <li>✓ Major assessments should have higher weights</li>
              <li>✓ Balance theory and practice modules</li>
              <li>✓ Consider time investment required</li>
              <li>✓ Align weights with learning outcomes</li>
              <li>✓ Review and adjust based on feedback</li>
            </ul>
          </div>
        </div>
      `
    },
    'course-progress': {
      title: 'Course Progress Calculation',
      content: `
        <p class="text-lg mb-6">Bacon Edu calculates course progress based on completed module weights, providing an accurate representation of student achievement.</p>
        
        <h3 class="text-2xl font-bold mb-4">📊 How Progress is Calculated</h3>
        
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
          <h4 class="text-xl font-bold mb-4">Formula</h4>
          <div class="bg-white dark:bg-gray-800 p-4 rounded-lg font-mono text-sm mb-4">
            <div class="text-center">
              <div class="mb-2">Course Progress (%) =</div>
              <div class="text-xl font-bold border-t-2 border-gray-300 dark:border-gray-600 pt-2 inline-block px-4">
                Sum of Completed Module Weights
              </div>
              <div class="text-xl font-bold border-b-2 border-gray-300 dark:border-gray-600 pb-2 inline-block px-4">
                Total Module Weights
              </div>
              <div class="mt-2">× 100</div>
            </div>
          </div>
          
          <p class="text-sm text-gray-700 dark:text-gray-300">
            <strong>In simple terms:</strong> The percentage of module weight completed out of the total weight of all modules.
          </p>
        </div>

        <h3 class="text-2xl font-bold mb-4">🧮 Calculation Example</h3>
        
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mb-6">
          <h4 class="font-bold mb-4">Course: "Introduction to Programming"</h4>
          
          <div class="space-y-4">
            <div>
              <h5 class="font-bold text-sm mb-2">Module Structure:</h5>
              <table class="w-full text-sm bg-white dark:bg-gray-700 rounded">
                <thead>
                  <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                    <th class="text-left py-2 px-3">Module</th>
                    <th class="text-center py-2 px-3">Weight</th>
                    <th class="text-center py-2 px-3">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="border-b border-gray-200 dark:border-gray-600">
                    <td class="py-2 px-3">Module 1: Basics</td>
                    <td class="text-center">15%</td>
                    <td class="text-center"><span class="text-green-600 dark:text-green-400">✓ Complete</span></td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-600">
                    <td class="py-2 px-3">Module 2: Variables</td>
                    <td class="text-center">20%</td>
                    <td class="text-center"><span class="text-green-600 dark:text-green-400">✓ Complete</span></td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-600">
                    <td class="py-2 px-3">Module 3: Functions</td>
                    <td class="text-center">25%</td>
                    <td class="text-center"><span class="text-gray-400">○ Incomplete</span></td>
                  </tr>
                  <tr class="border-b border-gray-200 dark:border-gray-600">
                    <td class="py-2 px-3">Module 4: Final Project</td>
                    <td class="text-center">40%</td>
                    <td class="text-center"><span class="text-gray-400">○ Incomplete</span></td>
                  </tr>
                  <tr class="font-bold bg-blue-50 dark:bg-blue-900/30">
                    <td class="py-2 px-3">TOTAL</td>
                    <td class="text-center">100%</td>
                    <td class="text-center">—</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded">
              <h5 class="font-bold text-sm mb-2">Calculation:</h5>
              <div class="font-mono text-sm space-y-1">
                <div>Completed Modules: Module 1 (15%) + Module 2 (20%) = <strong>35%</strong></div>
                <div>Total Module Weight: <strong>100%</strong></div>
                <div class="pt-2 border-t border-blue-200 dark:border-blue-700 mt-2">
                  Course Progress = (35 / 100) × 100 = <strong class="text-lg text-blue-600 dark:text-blue-400">35%</strong>
                </div>
              </div>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">🔄 Real-Time Updates</h3>
        <div class="space-y-4">
          <div class="border-l-4 border-green-500 pl-4 py-2">
            <h4 class="font-bold mb-1">Automatic Recalculation</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">When a student marks a module as complete, the system:</p>
            <ul class="text-sm mt-2 space-y-1 ml-4">
              <li>1. Records the module completion with timestamp</li>
              <li>2. Recalculates the course progress percentage</li>
              <li>3. Updates the progress bar in real-time</li>
              <li>4. Saves the new progress to the database</li>
            </ul>
          </div>

          <div class="border-l-4 border-purple-500 pl-4 py-2">
            <h4 class="font-bold mb-1">Progress Visibility</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">Students can view their progress:</p>
            <ul class="text-sm mt-2 space-y-1 ml-4">
              <li>• On the course list page (all enrolled courses)</li>
              <li>• On the course detail page (with module breakdown)</li>
              <li>• In the personal dashboard (average across all courses)</li>
            </ul>
          </div>

          <div class="border-l-4 border-blue-500 pl-4 py-2">
            <h4 class="font-bold mb-1">Teacher View</h4>
            <p class="text-sm text-gray-700 dark:text-gray-300">Teachers can track student progress:</p>
            <ul class="text-sm mt-2 space-y-1 ml-4">
              <li>• See which modules each student has completed</li>
              <li>• View overall course completion percentages</li>
              <li>• Identify students who need additional support</li>
              <li>• Generate progress reports</li>
            </ul>
          </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mt-6">
          <h4 class="font-bold mb-2">✅ Benefits of Weight-Based Progress</h4>
          <ul class="text-sm space-y-1">
            <li>• Reflects actual importance of each module</li>
            <li>• More accurate than simple module count</li>
            <li>• Allows flexible course design</li>
            <li>• Motivates students with clear milestones</li>
            <li>• Helps teachers balance course difficulty</li>
          </ul>
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
    'quiz-system': {
      title: 'Quiz System Overview',
      content: `
        <p class="text-lg mb-6">The quiz system in Bacon Edu is a comprehensive assessment tool supporting multiple question types with automatic and manual grading capabilities.</p>
        
        <h3 class="text-2xl font-bold mb-4">🎯 Quiz Features</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">4 Question Types</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">1.</span>
                <div>
                  <strong>Multiple Choice:</strong> Single correct answer from multiple options
                  <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Auto-graded ✓</div>
                </div>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">2.</span>
                <div>
                  <strong>True/False:</strong> Binary choice questions
                  <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Auto-graded ✓</div>
                </div>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">3.</span>
                <div>
                  <strong>Enumeration:</strong> List-based answers
                  <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Manual grading required</div>
                </div>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">4.</span>
                <div>
                  <strong>Short Answer:</strong> Brief text responses
                  <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Manual grading required</div>
                </div>
              </li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">Auto-Save Functionality</h4>
            <p class="text-sm mb-2">Student answers are automatically saved as they type or select options:</p>
            <ul class="text-sm space-y-1 ml-4">
              <li>• No "save" button needed</li>
              <li>• Changes saved instantly to database</li>
              <li>• Students can close and resume later</li>
              <li>• Progress preserved even if browser closes</li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">Time Tracking</h4>
            <p class="text-sm">System tracks time spent on quizzes:</p>
            <ul class="text-sm space-y-1 ml-4 mt-2">
              <li>• Starts when student opens quiz</li>
              <li>• Ends when quiz is submitted</li>
              <li>• Displayed in user-friendly format (e.g., "2m 30s")</li>
              <li>• Helps teachers understand quiz difficulty</li>
            </ul>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-6 rounded-r-lg">
            <h4 class="text-xl font-bold mb-2">Scoring & Results</h4>
            <ul class="text-sm space-y-1 ml-4">
              <li>• Total points earned vs. total possible points</li>
              <li>• Percentage score calculation</li>
              <li>• Pass/Fail status based on passing percentage</li>
              <li>• Detailed results with correct answers shown</li>
              <li>• Question-by-question review</li>
            </ul>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">📋 Quiz Workflow</h3>
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="font-bold mb-3">Teacher Side</h4>
              <ol class="text-sm space-y-2">
                <li class="flex gap-2">
                  <span class="font-bold">1.</span>
                  <span>Create quiz activity</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">2.</span>
                  <span>Add questions with points</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">3.</span>
                  <span>Set passing percentage</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">4.</span>
                  <span>Attach to module</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">5.</span>
                  <span>Review student submissions</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">6.</span>
                  <span>Grade text-based questions</span>
                </li>
              </ol>
            </div>
            <div>
              <h4 class="font-bold mb-3">Student Side</h4>
              <ol class="text-sm space-y-2">
                <li class="flex gap-2">
                  <span class="font-bold">1.</span>
                  <span>Access quiz from course module</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">2.</span>
                  <span>Answer questions (auto-save)</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">3.</span>
                  <span>Submit when all answered</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">4.</span>
                  <span>View immediate results</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">5.</span>
                  <span>Review answers & corrections</span>
                </li>
                <li class="flex gap-2">
                  <span class="font-bold">6.</span>
                  <span>Track progress on dashboard</span>
                </li>
              </ol>
            </div>
          </div>
        </div>
      `
    },
    'quiz-calculation': {
      title: 'Quiz Score Calculation',
      content: `
        <p class="text-lg mb-6">Understanding how quiz scores are calculated is essential for both creating fair assessments and interpreting results.</p>
        
        <h3 class="text-2xl font-bold mb-4">🔢 Scoring Formula</h3>
        
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
          <h4 class="text-xl font-bold mb-4">Basic Calculation</h4>
          <div class="bg-white dark:bg-gray-800 p-4 rounded-lg font-mono text-sm mb-4">
            <div class="text-center space-y-3">
              <div>
                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Raw Score:</div>
                <div class="text-lg font-bold">Score = Total Points Earned</div>
              </div>
              <div class="border-t-2 border-gray-300 dark:border-gray-600 pt-3">
                <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">Percentage Score:</div>
                <div class="text-xl font-bold border-t-2 border-gray-300 dark:border-gray-600 pt-2 inline-block px-4">
                  Points Earned
                </div>
                <div class="text-xl font-bold border-b-2 border-gray-300 dark:border-gray-600 pb-2 inline-block px-4">
                  Total Possible Points
                </div>
                <div class="mt-2">× 100</div>
              </div>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">📊 Detailed Example</h3>
        
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mb-6">
          <h4 class="font-bold mb-4">Quiz: "Chapter 3 Assessment"</h4>
          
          <table class="w-full text-sm bg-white dark:bg-gray-700 rounded mb-4">
            <thead>
              <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                <th class="text-left py-2 px-3">#</th>
                <th class="text-left py-2 px-3">Question Type</th>
                <th class="text-center py-2 px-3">Points</th>
                <th class="text-center py-2 px-3">Student Answer</th>
                <th class="text-center py-2 px-3">Earned</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b border-gray-200 dark:border-gray-600">
                <td class="py-2 px-3">1</td>
                <td class="py-2 px-3">Multiple Choice</td>
                <td class="text-center">2</td>
                <td class="text-center"><span class="text-green-600 dark:text-green-400">✓ Correct</span></td>
                <td class="text-center font-bold">2</td>
              </tr>
              <tr class="border-b border-gray-200 dark:border-gray-600">
                <td class="py-2 px-3">2</td>
                <td class="py-2 px-3">True/False</td>
                <td class="text-center">1</td>
                <td class="text-center"><span class="text-green-600 dark:text-green-400">✓ Correct</span></td>
                <td class="text-center font-bold">1</td>
              </tr>
              <tr class="border-b border-gray-200 dark:border-gray-600">
                <td class="py-2 px-3">3</td>
                <td class="py-2 px-3">Multiple Choice</td>
                <td class="text-center">3</td>
                <td class="text-center"><span class="text-red-600 dark:text-red-400">✗ Incorrect</span></td>
                <td class="text-center font-bold">0</td>
              </tr>
              <tr class="border-b border-gray-200 dark:border-gray-600">
                <td class="py-2 px-3">4</td>
                <td class="py-2 px-3">Short Answer</td>
                <td class="text-center">4</td>
                <td class="text-center"><span class="text-yellow-600 dark:text-yellow-400">⏳ Pending</span></td>
                <td class="text-center font-bold">0*</td>
              </tr>
              <tr class="font-bold bg-blue-50 dark:bg-blue-900/30">
                <td colspan="2" class="py-2 px-3">TOTALS</td>
                <td class="text-center">10</td>
                <td class="text-center">—</td>
                <td class="text-center">3</td>
              </tr>
            </tbody>
          </table>

          <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded">
            <h5 class="font-bold text-sm mb-2">Initial Calculation (Auto-graded only):</h5>
            <div class="font-mono text-sm space-y-1">
              <div>Points Earned (auto-graded): <strong>3 points</strong></div>
              <div>Auto-gradable Points: 2 + 1 + 3 = <strong>6 points</strong></div>
              <div class="pt-2 border-t border-blue-200 dark:border-blue-700 mt-2">
                Initial Score = (3 / 10) × 100 = <strong class="text-lg text-blue-600 dark:text-blue-400">30%</strong>
              </div>
              <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">
                *Question 4 pending manual grading (4 points possible)
              </div>
            </div>
          </div>

          <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded mt-4">
            <h5 class="font-bold text-sm mb-2">After Teacher Grades Short Answer (awards 3/4 points):</h5>
            <div class="font-mono text-sm space-y-1">
              <div>Total Points Earned: 3 (auto) + 3 (manual) = <strong>6 points</strong></div>
              <div>Total Possible Points: <strong>10 points</strong></div>
              <div class="pt-2 border-t border-green-200 dark:border-green-700 mt-2">
                Final Score = (6 / 10) × 100 = <strong class="text-lg text-green-600 dark:text-green-400">60%</strong>
              </div>
              <div class="text-xs text-red-600 dark:text-red-400 mt-2">
                Pass/Fail: <strong>FAIL</strong> (requires 70% to pass)
              </div>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">⚖️ Grading Rules</h3>
        
        <div class="space-y-4">
          <div class="border-l-4 border-green-500 pl-4 py-2">
            <h4 class="font-bold mb-1">Multiple Choice & True/False</h4>
            <ul class="text-sm space-y-1 ml-4">
              <li>• <strong>Correct answer:</strong> Full points awarded</li>
              <li>• <strong>Incorrect answer:</strong> Zero points</li>
              <li>• <strong>No partial credit</strong></li>
              <li>• Auto-graded immediately on submission</li>
            </ul>
          </div>

          <div class="border-l-4 border-yellow-500 pl-4 py-2">
            <h4 class="font-bold mb-1">Enumeration & Short Answer</h4>
            <ul class="text-sm space-y-1 ml-4">
              <li>• Initially awarded <strong>0 points</strong> (pending review)</li>
              <li>• Teacher reviews and assigns points (0 to max)</li>
              <li>• <strong>Partial credit possible</strong></li>
              <li>• Score updates when teacher grades</li>
            </ul>
          </div>

          <div class="border-l-4 border-blue-500 pl-4 py-2">
            <h4 class="font-bold mb-1">Pass/Fail Determination</h4>
            <ul class="text-sm space-y-1 ml-4">
              <li>• Default passing percentage: <strong>70%</strong></li>
              <li>• Customizable per quiz by teacher</li>
              <li>• Percentage Score ≥ Passing % = <span class="text-green-600 dark:text-green-400 font-bold">PASS ✓</span></li>
              <li>• Percentage Score < Passing % = <span class="text-red-600 dark:text-red-400 font-bold">FAIL ✗</span></li>
            </ul>
          </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mt-6">
          <h4 class="font-bold mb-2">⚠️ Important Notes</h4>
          <ul class="text-sm space-y-1">
            <li>• Scores with pending manual grading show as "Pending Review"</li>
            <li>• Final score only calculated after all questions graded</li>
            <li>• Students can see which answers need teacher review</li>
            <li>• System recalculates progress when final score is determined</li>
          </ul>
        </div>
      `
    },
    'quiz-taking': {
      title: 'Taking Quizzes - Student Guide',
      content: `
        <p class="text-lg mb-6">A complete guide for students on how to take quizzes effectively in Bacon Edu.</p>
        
        <h3 class="text-2xl font-bold mb-4">📝 Quiz Taking Process</h3>
        
        <div class="space-y-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 1: Access the Quiz</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">1.</span>
                <span>Navigate to <strong>My Courses</strong> from your dashboard</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">2.</span>
                <span>Click on the course containing the quiz</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">3.</span>
                <span>Find the module with the quiz activity</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500 font-bold">4.</span>
                <span>Click <strong>"Start Quiz"</strong> or <strong>"Continue Quiz"</strong></span>
              </li>
            </ul>
            <div class="bg-white dark:bg-gray-800 p-3 rounded mt-3 text-xs">
              <strong>Status Indicators:</strong>
              <ul class="mt-1 space-y-1 ml-4">
                <li>• <span class="text-gray-600 dark:text-gray-400">Not Started</span> - Click "Start Quiz"</li>
                <li>• <span class="text-yellow-600 dark:text-yellow-400">In Progress</span> - Click "Continue Quiz"</li>
                <li>• <span class="text-green-600 dark:text-green-400">Completed</span> - Click "Review Results"</li>
              </ul>
            </div>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 2: Answer Questions</h4>
            
            <div class="space-y-4">
              <div>
                <h5 class="font-bold text-sm mb-2">Multiple Choice Questions:</h5>
                <ul class="text-sm space-y-1 ml-4">
                  <li>• Read the question carefully</li>
                  <li>• Select ONE answer by clicking the radio button</li>
                  <li>• Answer saves automatically when you select</li>
                  <li>• You can change your answer anytime before submitting</li>
                </ul>
              </div>

              <div>
                <h5 class="font-bold text-sm mb-2">True/False Questions:</h5>
                <ul class="text-sm space-y-1 ml-4">
                  <li>• Click either "True" or "False" button</li>
                  <li>• Selected button highlights</li>
                  <li>• Auto-saves your selection</li>
                </ul>
              </div>

              <div>
                <h5 class="font-bold text-sm mb-2">Enumeration Questions:</h5>
                <ul class="text-sm space-y-1 ml-4">
                  <li>• Type your list of answers in the text area</li>
                  <li>• Press Enter or comma to separate items</li>
                  <li>• Auto-saves as you type</li>
                  <li>• Will be graded manually by teacher</li>
                </ul>
              </div>

              <div>
                <h5 class="font-bold text-sm mb-2">Short Answer Questions:</h5>
                <ul class="text-sm space-y-1 ml-4">
                  <li>• Type your answer in the text area</li>
                  <li>• Be clear and concise</li>
                  <li>• Auto-saves as you type</li>
                  <li>• Teacher will review and grade</li>
                </ul>
              </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-3 mt-4 text-sm">
              <strong>💡 Auto-Save Feature:</strong>
              <p class="mt-1">Every answer is saved automatically to the database as soon as you select or type it. You don't need to click any "Save" button!</p>
            </div>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 3: Submit Quiz</h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">1.</span>
                <span>Answer <strong>all questions</strong> (submission button disabled until complete)</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">2.</span>
                <span>Review your answers if needed</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">3.</span>
                <span>Click <strong>"Submit Quiz"</strong> button</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-green-500 font-bold">4.</span>
                <span>Confirm submission in the popup</span>
              </li>
            </ul>
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500 p-3 mt-4 text-sm">
              <strong>⚠️ Warning:</strong> Once you submit, you cannot change your answers!
            </div>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 4: View Results</h4>
            <p class="text-sm mb-3">After submission, you'll immediately see:</p>
            <ul class="space-y-2 text-sm ml-4">
              <li>• <strong>Score:</strong> Points earned / Total points (e.g., 8.5 / 10)</li>
              <li>• <strong>Percentage:</strong> Your score as a percentage (e.g., 85%)</li>
              <li>• <strong>Pass/Fail Status:</strong> Whether you met the passing percentage</li>
              <li>• <strong>Time Spent:</strong> How long you took (e.g., "2m 45s")</li>
              <li>• <strong>Statistics:</strong> Correct, incorrect, and pending answers</li>
            </ul>
          </div>

          <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-6">
            <h4 class="text-xl font-bold mb-3">Step 5: Review Answers</h4>
            <p class="text-sm mb-3">Scroll down to see question-by-question breakdown:</p>
            <ul class="space-y-2 text-sm ml-4">
              <li>• <span class="text-green-600 dark:text-green-400 font-bold">✓ Green</span> = Correct answer</li>
              <li>• <span class="text-red-600 dark:text-red-400 font-bold">✗ Red</span> = Incorrect answer (shows correct answer)</li>
              <li>• <span class="text-yellow-600 dark:text-yellow-400 font-bold">⏳ Yellow</span> = Pending teacher review</li>
            </ul>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">💡 Tips for Success</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">Before the Quiz</h4>
            <ul class="text-sm space-y-1">
              <li>✓ Review course materials thoroughly</li>
              <li>✓ Ensure stable internet connection</li>
              <li>✓ Find a quiet place to focus</li>
              <li>✓ Have any allowed resources ready</li>
            </ul>
          </div>
          
          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2">During the Quiz</h4>
            <ul class="text-sm space-y-1">
              <li>✓ Read each question carefully</li>
              <li>✓ Don't rush - answers auto-save</li>
              <li>✓ If unsure, make your best guess</li>
              <li>✓ Review before submitting</li>
            </ul>
          </div>
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
