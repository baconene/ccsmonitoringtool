<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import type { User } from '@/types';
import NewUserModal from '@/pages/rolemanagement/NewUserModal.vue';
import EditUserModal from '@/pages/rolemanagement/EditUserModal.vue';
import DeleteConfirmationModal from '@/pages/rolemanagement/DeleteConfirmationModal.vue';
import UserFilter from '@/pages/rolemanagement/UserFilter.vue';

const props = defineProps<{
  users: User[];
}>();

const showNewUserModal = ref(false);
const showEditUserModal = ref(false);
const showDeleteModal = ref(false);
const userToEdit = ref<User | null>(null);
const userToDelete = ref<User | null>(null);

// Filter states
const searchQuery = ref('');
const selectedRoles = ref<string[]>([]);
const selectedStatuses = ref<string[]>([]);
const selectedDateRange = ref<string>('all');

const emit = defineEmits<{
  (e: 'addUser', user: any): void;
  (e: 'editUser', user: any): void;
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

// Get role badge color
const getRoleBadgeColor = (roleName: string | undefined) => {
  if (!roleName) return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
  
  switch (roleName) {
    case 'admin': return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200';
    case 'instructor': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200';
    case 'student': return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
  }
};

const openNewUserModal = () => {
  showNewUserModal.value = true;
};

const closeNewUserModal = () => {
  showNewUserModal.value = false;
};

const handleNewUserSubmit = (userData: any) => {
  emit('addUser', userData);
  closeNewUserModal();
};

const openEditUserModal = (user: User) => {
  userToEdit.value = user;
  showEditUserModal.value = true;
};

const closeEditUserModal = () => {
  showEditUserModal.value = false;
  userToEdit.value = null;
};

const handleEditUserSubmit = (userData: any) => {
  emit('editUser', userData);
  closeEditUserModal();
};

const openDeleteModal = (user: User) => {
  userToDelete.value = user;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  userToDelete.value = null;
};

const confirmDeleteUser = () => {
  if (userToDelete.value) {
    emit('deleteUser', userToDelete.value.id);
    closeDeleteModal();
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

      <!-- User Filter Component -->
      <UserFilter
        :results-count="filteredUsers.length"
        :total-count="users.length"
        @update:search-query="searchQuery = $event"
        @update:selected-roles="selectedRoles = $event"
        @update:selected-statuses="selectedStatuses = $event"
        @update:selected-date-range="selectedDateRange = $event"
        @clear-filters="clearFilters"
      />
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
                  @click="openEditUserModal(user)"
                  class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button
                  @click="openDeleteModal(user)"
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
    <NewUserModal
      :show-modal="showNewUserModal"
      @close="closeNewUserModal"
      @submit="handleNewUserSubmit"
    />

    <!-- Edit User Modal -->
    <EditUserModal
      :show-modal="showEditUserModal"
      :user="userToEdit"
      @close="closeEditUserModal"
      @submit="handleEditUserSubmit"
    />
    
    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show-delete-modal="showDeleteModal"
      :user-to-delete="userToDelete"
      @close="closeDeleteModal"
      @confirm="confirmDeleteUser"
    />
  </div>
</template>
