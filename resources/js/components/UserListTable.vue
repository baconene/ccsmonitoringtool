<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Search, Filter, X } from 'lucide-vue-next';
import type { User } from '@/types';

const props = defineProps<{
  users: User[];
}>();

const showNewUserModal = ref(false);
const editingUser = ref<User | null>(null);

const newUser = ref({
  name: '',
  email: '',
  password: '',
  role: 'student',
  grade_level: '',
  section: ''
});

// Filter states
const searchQuery = ref('');
const selectedRoles = ref<string[]>([]);
const selectedStatuses = ref<string[]>([]);
const selectedDateRange = ref<string>('all');
const showFilters = ref(false);

const emit = defineEmits<{
  (e: 'addUser', user: typeof newUser.value): void;
  (e: 'editUser', user: User): void;
  (e: 'deleteUser', userId: number): void;
  (e: 'viewStudentDetails', studentId: number): void;
}>();

// Filtered users based on search and filters
const filteredUsers = computed(() => {
  let result = props.users;

  // Search filter (searches across name, email, section, grade_level, and role)
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim();
    result = result.filter(user => {
      return (
        user.name.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query) ||
        (user.role_name && user.role_name.toLowerCase().includes(query)) ||
        (user.role_display_name && user.role_display_name.toLowerCase().includes(query)) ||
        (user.grade_level && user.grade_level.toLowerCase().includes(query)) ||
        (user.section && user.section.toLowerCase().includes(query))
      );
    });
  }

  // Role filter (multiple selection)
  if (selectedRoles.value.length > 0) {
    result = result.filter(user => selectedRoles.value.includes(user.role_name || ''));
  }

  // Status filter (multiple selection)
  if (selectedStatuses.value.length > 0) {
    result = result.filter(user => {
      return selectedStatuses.value.some(status => {
        if (status === 'verified') {
          return user.email_verified_at !== null;
        } else if (status === 'unverified') {
          return user.email_verified_at === null;
        }
        return false;
      });
    });
  }

  // Date range filter
  if (selectedDateRange.value !== 'all') {
    const now = new Date();
    const filterDate = new Date();

    switch (selectedDateRange.value) {
      case 'today':
        filterDate.setHours(0, 0, 0, 0);
        break;
      case 'week':
        filterDate.setDate(now.getDate() - 7);
        break;
      case 'month':
        filterDate.setMonth(now.getMonth() - 1);
        break;
      case 'year':
        filterDate.setFullYear(now.getFullYear() - 1);
        break;
    }

    if (selectedDateRange.value !== 'all') {
      result = result.filter(user => {
        const createdAt = new Date(user.created_at);
        return createdAt >= filterDate;
      });
    }
  }

  return result;
});

// Check if any filters are active
const hasActiveFilters = computed(() => {
  return searchQuery.value.trim() !== '' ||
         selectedRoles.value.length > 0 ||
         selectedStatuses.value.length > 0 ||
         selectedDateRange.value !== 'all';
});

// Clear all filters
const clearFilters = () => {
  searchQuery.value = '';
  selectedRoles.value = [];
  selectedStatuses.value = [];
  selectedDateRange.value = 'all';
};

// Toggle role selection
const toggleRole = (role: string) => {
  const index = selectedRoles.value.indexOf(role);
  if (index > -1) {
    selectedRoles.value.splice(index, 1);
  } else {
    selectedRoles.value.push(role);
  }
};

// Toggle status selection
const toggleStatus = (status: string) => {
  const index = selectedStatuses.value.indexOf(status);
  if (index > -1) {
    selectedStatuses.value.splice(index, 1);
  } else {
    selectedStatuses.value.push(status);
  }
};

const openNewUserModal = () => {
  showNewUserModal.value = true;
};

const closeNewUserModal = () => {
  showNewUserModal.value = false;
  newUser.value = {
    name: '',
    email: '',
    password: '',
    role: 'student',
    grade_level: '',
    section: ''
  };
};

const handleAddUser = () => {
  console.log('Submitting new user data:', newUser.value);
  emit('addUser', newUser.value);
  closeNewUserModal();
};

const handleEditUser = (user: User) => {
  emit('editUser', user);
};

const handleDeleteUser = (userId: number) => {
  if (confirm('Are you sure you want to delete this user?')) {
    emit('deleteUser', userId);
  }
};

const getRoleBadgeColor = (roleName: string) => {
  switch (roleName) {
    case 'admin': return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200';
    case 'instructor': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200';
    case 'student': return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
  }
};

// Format date for display
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Users</h2>
        <button
          @click="openNewUserModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add User
        </button>
      </div>

      <!-- Search Bar -->
      <div class="relative mb-4">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by name, email, role, grade level, or section..."
            class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Filter Toggle Button -->
      <div class="flex items-center justify-between mb-2">
        <button
          @click="showFilters = !showFilters"
          class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
        >
          <Filter class="w-4 h-4 mr-2" />
          {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
          <span v-if="hasActiveFilters" class="ml-2 px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
            Active
          </span>
        </button>

        <button
          v-if="hasActiveFilters"
          @click="clearFilters"
          class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
        >
          <X class="w-4 h-4 mr-1" />
          Clear Filters
        </button>
      </div>

      <!-- Advanced Filters -->
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="transform opacity-0 -translate-y-2"
        enter-to-class="transform opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="transform opacity-100 translate-y-0"
        leave-to-class="transform opacity-0 -translate-y-2"
      >
        <div v-if="showFilters" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
          <!-- Role Filter (Multi-select) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              Role
              <span v-if="selectedRoles.length > 0" class="ml-2 text-xs text-blue-600 dark:text-blue-400">({{ selectedRoles.length }} selected)</span>
            </label>
            <div class="space-y-2">
              <label class="flex items-center cursor-pointer group">
                <input
                  type="checkbox"
                  :checked="selectedRoles.includes('admin')"
                  @change="toggleRole('admin')"
                  class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Admin</span>
              </label>
              <label class="flex items-center cursor-pointer group">
                <input
                  type="checkbox"
                  :checked="selectedRoles.includes('instructor')"
                  @change="toggleRole('instructor')"
                  class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Instructor</span>
              </label>
              <label class="flex items-center cursor-pointer group">
                <input
                  type="checkbox"
                  :checked="selectedRoles.includes('student')"
                  @change="toggleRole('student')"
                  class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Student</span>
              </label>
            </div>
          </div>

          <!-- Status Filter (Multi-select) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              Status
              <span v-if="selectedStatuses.length > 0" class="ml-2 text-xs text-blue-600 dark:text-blue-400">({{ selectedStatuses.length }} selected)</span>
            </label>
            <div class="space-y-2">
              <label class="flex items-center cursor-pointer group">
                <input
                  type="checkbox"
                  :checked="selectedStatuses.includes('verified')"
                  @change="toggleStatus('verified')"
                  class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Verified</span>
              </label>
              <label class="flex items-center cursor-pointer group">
                <input
                  type="checkbox"
                  :checked="selectedStatuses.includes('unverified')"
                  @change="toggleStatus('unverified')"
                  class="w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2 cursor-pointer"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">Unverified</span>
              </label>
            </div>
          </div>

          <!-- Date Range Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
              Created Date
            </label>
            <select
              v-model="selectedDateRange"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="all">All Time</option>
              <option value="today">Today</option>
              <option value="week">Last 7 Days</option>
              <option value="month">Last 30 Days</option>
              <option value="year">Last Year</option>
            </select>
          </div>
        </div>
      </transition>

      <!-- Results Count -->
      <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        Showing <span class="font-semibold text-gray-900 dark:text-white">{{ filteredUsers.length }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ users.length }}</span> users
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900/50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Grade/Section</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-if="filteredUsers.length === 0">
            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
              <div class="flex flex-col items-center">
                <Search class="w-12 h-12 mb-2 text-gray-300 dark:text-gray-600" />
                <p class="text-sm">No users found matching your filters</p>
                <button
                  v-if="hasActiveFilters"
                  @click="clearFilters"
                  class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:underline"
                >
                  Clear filters
                </button>
              </div>
            </td>
          </tr>
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white text-lg">{{ user.name[0] }}</span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleBadgeColor(user.role_name)}`">
                {{ user.role_display_name }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div v-if="user.role_name === 'student' && (user.grade_level || user.section)" class="text-sm">
                <div v-if="user.grade_level" class="text-gray-900 dark:text-white">{{ user.grade_level }}</div>
                <div v-if="user.section" class="text-gray-500 dark:text-gray-400">{{ user.section }}</div>
              </div>
              <span v-else class="text-sm text-gray-400 dark:text-gray-500">—</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="user.email_verified_at ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
              {{ formatDate(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
              <div class="flex justify-end space-x-3">
                <Link
                  v-if="user.role_name === 'student'"
                  :href="`/student/${user.id}/details`"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                >
                  View Details
                </Link>
                <button
                  @click="handleEditUser(user)"
                  class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  @click="handleDeleteUser(user.id)"
                  class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- New User Modal -->
    <div v-if="showNewUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add New User</h3>
        <form @submit.prevent="handleAddUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input
              type="text"
              v-model="newUser.name"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input
              type="email"
              v-model="newUser.email"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
            <input
              type="password"
              v-model="newUser.password"
              required
              minlength="8"
              placeholder="Minimum 8 characters"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Password must be at least 8 characters long</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
            <select
              v-model="newUser.role"
              required
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
              <option value="student">Student</option>
              <option value="instructor">Instructor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          
          <!-- Grade Level - Only show for students -->
          <div v-if="newUser.role === 'student'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Grade Level</label>
            <input
              type="text"
              v-model="newUser.grade_level"
              placeholder="e.g., Grade 10, Year 1"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
          </div>
          
          <!-- Section - Only show for students -->
          <div v-if="newUser.role === 'student'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Section</label>
            <input
              type="text"
              v-model="newUser.section"
              placeholder="e.g., Section A, Room 101"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            >
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button
              type="button"
              @click="closeNewUserModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Add User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>