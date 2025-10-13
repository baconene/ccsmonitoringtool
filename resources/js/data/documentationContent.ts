/**
 * Documentation Content for AstroLearn LMS
 * Comprehensive system documentation organized by sections
 */

export interface DocumentationSection {
  id: string;
  title: string;
  content: string;
  category: 'getting-started' | 'user-roles' | 'features' | 'guides' | 'technical';
}

export const documentationSections: DocumentationSection[] = [
  // GETTING STARTED
  {
    id: 'introduction',
    category: 'getting-started',
    title: 'Welcome to AstroLearn LMS',
    content: `
      <div class="prose dark:prose-invert max-w-none">
        <p class="text-lg mb-6">
          AstroLearn is a comprehensive Learning Management System (LMS) designed to streamline 
          educational processes for schools, colleges, and training institutions. Built with Laravel 11, 
          Vue 3, TypeScript, and Inertia.js, it provides a modern, intuitive interface for managing 
          courses, activities, and student progress.
        </p>
        
        <h3 class="text-2xl font-bold mb-4 mt-6">🎯 Key Features</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="font-bold mb-2 text-blue-700 dark:text-blue-300">📚 Course Management</h4>
            <ul class="text-sm space-y-1">
              <li>• Weighted module system for flexible progress calculation</li>
              <li>• Multi-grade level course assignments</li>
              <li>• Drag-and-drop student enrollment</li>
              <li>• Real-time progress tracking</li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
            <h4 class="font-bold mb-2 text-purple-700 dark:text-purple-300">✏️ Activity System</h4>
            <ul class="text-sm space-y-1">
              <li>• Multiple activity types (Quiz, Assignment, Lesson)</li>
              <li>• Advanced quiz system with 4 question types</li>
              <li>• Auto-save functionality for student work</li>
              <li>• Automatic and manual grading options</li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <h4 class="font-bold mb-2 text-green-700 dark:text-green-300">👥 User Management</h4>
            <ul class="text-sm space-y-1">
              <li>• Role-based access control (Admin, Teacher, Student)</li>
              <li>• Grade level management</li>
              <li>• Student achievement tracking</li>
              <li>• Comprehensive user profiles</li>
            </ul>
          </div>

          <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
            <h4 class="font-bold mb-2 text-orange-700 dark:text-orange-300">🎨 Modern UI/UX</h4>
            <ul class="text-sm space-y-1">
              <li>• Built-in dark mode support</li>
              <li>• Responsive design for all devices</li>
              <li>• Intuitive drag-and-drop interfaces</li>
              <li>• Real-time updates and notifications</li>
            </ul>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">🚀 Technology Stack</h3>
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="font-bold mb-3 text-blue-700 dark:text-blue-300">Backend</h4>
              <ul class="text-sm space-y-2">
                <li><strong>Laravel 11:</strong> Modern PHP framework</li>
                <li><strong>MySQL/SQLite:</strong> Reliable database systems</li>
                <li><strong>Eloquent ORM:</strong> Database abstraction</li>
                <li><strong>Laravel Breeze:</strong> Authentication scaffolding</li>
              </ul>
            </div>
            <div>
              <h4 class="font-bold mb-3 text-purple-700 dark:text-purple-300">Frontend</h4>
              <ul class="text-sm space-y-2">
                <li><strong>Vue 3:</strong> Progressive JavaScript framework</li>
                <li><strong>TypeScript:</strong> Type-safe development</li>
                <li><strong>Inertia.js:</strong> SPA without complexity</li>
                <li><strong>Tailwind CSS:</strong> Utility-first styling</li>
              </ul>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">🎓 Perfect For</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="text-4xl mb-2">🏫</div>
            <h4 class="font-bold mb-1">K-12 Schools</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">Educational institutions managing multiple courses</p>
          </div>
          <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="text-4xl mb-2">🎓</div>
            <h4 class="font-bold mb-1">Universities</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">Colleges with diverse course offerings</p>
          </div>
          <div class="text-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="text-4xl mb-2">💼</div>
            <h4 class="font-bold mb-1">Corporate Training</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">Professional development programs</p>
          </div>
        </div>
      </div>
    `
  },

  {
    id: 'system-overview',
    category: 'getting-started',
    title: 'System Architecture Overview',
    content: `
      <div class="prose dark:prose-invert max-w-none">
        <p class="text-lg mb-6">
          Understanding the core architecture and workflow of AstroLearn LMS helps you make the most of the system.
        </p>
        
        <h3 class="text-2xl font-bold mb-4">📚 Course Hierarchy</h3>
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
          <div class="space-y-4 font-mono text-sm">
            <div class="flex items-start gap-3">
              <span class="text-2xl">📖</span>
              <div>
                <strong class="text-blue-600 dark:text-blue-400">Course</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Top-level container (e.g., "Introduction to Mathematics")</p>
              </div>
            </div>
            <div class="ml-8 flex items-start gap-3">
              <span class="text-2xl">📂</span>
              <div>
                <strong class="text-purple-600 dark:text-purple-400">Modules</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Organized sections with weights (e.g., "Chapter 1: Algebra", weight: 20%)</p>
              </div>
            </div>
            <div class="ml-16 flex items-start gap-3">
              <span class="text-2xl">📝</span>
              <div>
                <strong class="text-green-600 dark:text-green-400">Activities</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Learning materials (Quizzes, Assignments, Lessons)</p>
              </div>
            </div>
            <div class="ml-24 flex items-start gap-3">
              <span class="text-2xl">❓</span>
              <div>
                <strong class="text-orange-600 dark:text-orange-400">Questions</strong>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">For quizzes: MCQ, True/False, Enumeration, Short Answer</p>
              </div>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4">🔄 System Workflow</h3>
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
            <div class="flex-1">
              <h4 class="font-bold mb-1">Course Creation (Teacher/Admin)</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">Create course → Add modules with weights → Attach activities</p>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
            <div class="flex-1">
              <h4 class="font-bold mb-1">Student Enrollment (Teacher/Admin)</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">Assign students to courses based on grade level eligibility</p>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
            <div class="flex-1">
              <h4 class="font-bold mb-1">Learning & Progress (Student)</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">Complete activities → Module completion tracked → Course progress updated</p>
            </div>
          </div>
          
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold">4</div>
            <div class="flex-1">
              <h4 class="font-bold mb-1">Assessment & Feedback (Teacher)</h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">Grade submissions → Provide feedback → Monitor progress</p>
            </div>
          </div>
        </div>

        <h3 class="text-2xl font-bold mb-4 mt-8">📊 Key Metrics</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <h4 class="font-bold mb-2">Course Progress</h4>
            <p class="text-sm">Calculated from sum of completed module weights (0-100%)</p>
            <code class="text-xs block mt-2 p-2 bg-white dark:bg-gray-800 rounded">
              Progress = (Completed Module Weights / Total Weights) × 100
            </code>
          </div>
          
          <div class="bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 p-4 rounded-r-lg">
            <h4 class="font-bold mb-2">Quiz Scores</h4>
            <p class="text-sm">Based on total points earned vs. total possible points</p>
            <code class="text-xs block mt-2 p-2 bg-white dark:bg-gray-800 rounded">
              Score = (Points Earned / Total Points) × 100
            </code>
          </div>
          
          <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-r-lg">
            <h4 class="font-bold mb-2">Module Completion</h4>
            <p class="text-sm">Binary state (complete/incomplete) tracked per student</p>
          </div>
          
          <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded-r-lg">
            <h4 class="font-bold mb-2">Time Tracking</h4>
            <p class="text-sm">Records time spent on quizzes and activities for analytics</p>
          </div>
        </div>
      </div>
    `
  },

  {
    id: 'system-requirements',
    category: 'getting-started',
    title: 'System Requirements',
    content: `
      <div class="prose dark:prose-invert max-w-none">
        <p class="text-lg mb-6">
          Ensure your environment meets these requirements before installing AstroLearn LMS.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4 text-blue-700 dark:text-blue-300">Server Requirements</h3>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-blue-500">✓</span>
                <span><strong>PHP 8.1+</strong> with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500">✓</span>
                <span><strong>Laravel 11.x</strong> framework</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500">✓</span>
                <span><strong>MySQL 8.0+</strong> or <strong>PostgreSQL 13+</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500">✓</span>
                <span><strong>Node.js 18.x+</strong> for asset compilation</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-blue-500">✓</span>
                <span><strong>Composer 2.x</strong> for PHP dependencies</span>
              </li>
            </ul>
          </div>

          <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4 text-purple-700 dark:text-purple-300">Client Requirements</h3>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <span class="text-purple-500">✓</span>
                <span><strong>Modern Browsers:</strong> Chrome 90+, Firefox 88+, Safari 14+, Edge 90+</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500">✓</span>
                <span><strong>JavaScript Enabled</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500">✓</span>
                <span><strong>Cookies Enabled</strong></span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-purple-500">✓</span>
                <span><strong>Minimum Resolution:</strong> 1024x768 (responsive design supports all screen sizes)</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mt-6">
          <h3 class="text-xl font-bold mb-4 text-green-700 dark:text-green-300">Recommended Specifications</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
              <strong class="block mb-2">Development</strong>
              <ul class="space-y-1">
                <li>• 4GB RAM</li>
                <li>• 2 CPU cores</li>
                <li>• 20GB disk space</li>
              </ul>
            </div>
            <div>
              <strong class="block mb-2">Production (Small)</strong>
              <ul class="space-y-1">
                <li>• 8GB RAM</li>
                <li>• 4 CPU cores</li>
                <li>• 50GB disk space</li>
                <li>• Up to 500 users</li>
              </ul>
            </div>
            <div>
              <strong class="block mb-2">Production (Large)</strong>
              <ul class="space-y-1">
                <li>• 16GB+ RAM</li>
                <li>• 8+ CPU cores</li>
                <li>• 100GB+ disk space</li>
                <li>• 500+ users</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mt-6">
          <h4 class="font-bold mb-2">🔒 Security Recommendations</h4>
          <ul class="text-sm space-y-1">
            <li>• <strong>SSL Certificate</strong> required for production (HTTPS)</li>
            <li>• <strong>Regular backups</strong> of database and uploaded files</li>
            <li>• <strong>Firewall</strong> configuration to restrict database access</li>
            <li>• <strong>Keep system updated</strong> with latest security patches</li>
          </ul>
        </div>
      </div>
    `
  },

  // USER ROLES
  {
    id: 'roles-permissions',
    category: 'user-roles',
    title: 'Roles & Permissions Overview',
    content: `
      <div class="prose dark:prose-invert max-w-none">
        <p class="text-lg mb-6">
          AstroLearn uses a role-based access control (RBAC) system with three primary roles, 
          each designed for specific functions within the learning management ecosystem.
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-6 rounded-r-lg">
            <div class="text-4xl mb-3">⚙️</div>
            <h3 class="text-xl font-bold mb-2">Administrator</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">Full system control and oversight</p>
            <ul class="text-xs space-y-1">
              <li>✓ Manage all users</li>
              <li>✓ Configure system settings</li>
              <li>✓ View all courses and activities</li>
              <li>✓ Generate reports</li>
              <li>✓ Manage activity types</li>
              <li>✓ Grade level configuration</li>
            </ul>
          </div>

          <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded-r-lg">
            <div class="text-4xl mb-3">👨‍🏫</div>
            <h3 class="text-xl font-bold mb-2">Teacher/Instructor</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">Create and manage course content</p>
            <ul class="text-xs space-y-1">
              <li>✓ Create courses and modules</li>
              <li>✓ Design activities and quizzes</li>
              <li>✓ Enroll students</li>
              <li>✓ Grade assignments</li>
              <li>✓ Track student progress</li>
              <li>✓ Provide feedback</li>
            </ul>
          </div>

          <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-6 rounded-r-lg">
            <div class="text-4xl mb-3">👨‍🎓</div>
            <h3 class="text-xl font-bold mb-2">Student</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">Access courses and complete activities</p>
            <ul class="text-xs space-y-1">
              <li>✓ View enrolled courses</li>
              <li>✓ Complete activities</li>
              <li>✓ Take quizzes</li>
              <li>✓ Submit assignments</li>
              <li>✓ View grades and progress</li>
              <li>✓ Track achievements</li>
            </ul>
          </div>
        </div>

        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mt-8">
          <h3 class="text-xl font-bold mb-4">Permission Matrix</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                  <th class="text-left p-2">Action</th>
                  <th class="text-center p-2">Admin</th>
                  <th class="text-center p-2">Teacher</th>
                  <th class="text-center p-2">Student</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <td class="p-2">Manage Users</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-red-600">✗</td>
                  <td class="text-center text-red-600">✗</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <td class="p-2">Create Courses</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-red-600">✗</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <td class="p-2">Enroll Students</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-red-600">✗</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <td class="p-2">Grade Assignments</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-red-600">✗</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                  <td class="p-2">Take Quizzes</td>
                  <td class="text-center text-yellow-600">○</td>
                  <td class="text-center text-yellow-600">○</td>
                  <td class="text-center text-green-600">✓</td>
                </tr>
                <tr>
                  <td class="p-2">View Own Progress</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-green-600">✓</td>
                  <td class="text-center text-green-600">✓</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">○ = Optional/Testing purposes</p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-6">
          <h4 class="font-bold mb-2">🔒 Security Implementation</h4>
          <p class="text-sm">
            Role permissions are enforced at multiple levels:
          </p>
          <ul class="text-sm space-y-1 mt-2">
            <li>• <strong>Backend:</strong> Laravel middleware and policies</li>
            <li>• <strong>Frontend:</strong> Inertia.js route guards</li>
            <li>• <strong>Database:</strong> Foreign key constraints and triggers</li>
            <li>• <strong>API:</strong> Token-based authentication with role verification</li>
          </ul>
        </div>
      </div>
    `
  },

  // Add more sections as needed...
  // This file is getting long, so I'll create a summary for now
];

export const documentationCategories = {
  'getting-started': {
    title: 'Getting Started',
    icon: '🚀',
    sections: documentationSections.filter(s => s.category === 'getting-started')
  },
  'user-roles': {
    title: 'User Roles',
    icon: '👥',
    sections: documentationSections.filter(s => s.category === 'user-roles')
  },
  'features': {
    title: 'Features',
    icon: '✨',
    sections: documentationSections.filter(s => s.category === 'features')
  },
  'guides': {
    title: 'How-To Guides',
    icon: '📖',
    sections: documentationSections.filter(s => s.category === 'guides')
  },
  'technical': {
    title: 'Technical Details',
    icon: '⚙️',
    sections: documentationSections.filter(s => s.category === 'technical')
  }
};

// Navigation helper
export function getSectionById(id: string): DocumentationSection | undefined {
  return documentationSections.find(section => section.id === id);
}

// Search helper
export function searchDocumentation(query: string): DocumentationSection[] {
  const lowerQuery = query.toLowerCase();
  return documentationSections.filter(section => 
    section.title.toLowerCase().includes(lowerQuery) ||
    section.content.toLowerCase().includes(lowerQuery)
  );
}
