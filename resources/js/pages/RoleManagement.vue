<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, computed, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import type { NewUserData, UserUpdateData, User } from '@/types/index';
import UserListTable from '@/components/UserListTable.vue';
import RolesSection from '@/components/RolesSection.vue';
import Notification from '@/components/Notification.vue';
import { useUserManagement } from '@/composables/useUserManagement';
import { useRoleManagement } from '@/composables/useRoleManagement';
import { useNotification } from '@/composables/useNotification';

// Breadcrumb items
const breadcrumbItems: BreadcrumbItem[] = [
  { title: 'Home', href: '/' },
  { title: 'User Management', href: '/role-management' }
];

// Composables
const { users, loading: usersLoading, fetchUsers, createUser, updateUser, deleteUser } = useUserManagement();
const { roles, loading: rolesLoading, fetchRoles, getRoleBadgeColor } = useRoleManagement();
const { notification, success, error: showError } = useNotification();

// Computed loading state
const loading = computed(() => usersLoading.value || rolesLoading.value);
const mainError = ref<string | null>(null);

// Load all data
const loadData = async () => {
  try {
    await Promise.all([fetchUsers(), fetchRoles()]);
  } catch (err) {
    mainError.value = 'Failed to load role management data';
    console.error('Error loading data:', err);
  }
};

// User management handlers
const handleAddUser = async (userData: NewUserData) => {
  try {
    await createUser(userData);
    success('User created successfully');
  } catch (err: any) {
    const errorMessage = err.response?.data?.message || 
                        err.response?.data?.errors?.password?.[0] || 
                        'Failed to create user';
    showError(errorMessage);
  }
};

const handleEditUser = async (user: User & { password?: string }) => {
  try {
    const updateData: UserUpdateData = {
      name: user.name,
      email: user.email,
      role: user.role_name
    };
    if (user.password) {
      updateData.password = user.password;
    }
    await updateUser(user.id, updateData);
    success('User updated successfully');
  } catch (err: any) {
    const errorMessage = err.response?.data?.message || 'Failed to update user';
    showError(errorMessage);
  }
};

const handleDeleteUser = async (userId: number) => {
  try {
    await deleteUser(userId);
    success('User deleted successfully');
  } catch (err: any) {
    const errorMessage = err.response?.data?.message || 'Failed to delete user';
    showError(errorMessage);
  }
};

const viewStudentDetails = (studentId: number) => {
  router.visit(`/student/${studentId}/details`);
};

// Load data on mount
onMounted(() => {
  loadData();
});
</script>

<template>
  <Head title="Role Management" />

  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="p-6 min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Role Management</h1>
          <p class="text-gray-600 dark:text-gray-300 mt-2">Manage user roles and permissions in the system.</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400"></div>
          <span class="ml-3 text-gray-600 dark:text-gray-300">Loading role management data...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="mainError" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
          <div class="flex items-center">
            <svg class="h-6 w-6 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h3 class="text-lg font-medium text-red-800 dark:text-red-200">Error Loading Data</h3>
              <p class="text-red-600 dark:text-red-300 mt-1">{{ mainError }}</p>
            </div>
          </div>
          <button
            @click="loadData"
            class="mt-4 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          >
            Retry
          </button>
        </div>

        <!-- Content -->
        <div v-else class="space-y-8">
          <!-- Roles Section -->
          <RolesSection :roles="roles" :get-role-badge-color="getRoleBadgeColor" />

          <!-- Users Section -->
          <UserListTable
            :users="users"
            @add-user="handleAddUser"
            @edit-user="handleEditUser"
            @delete-user="handleDeleteUser"
            @view-student-details="viewStudentDetails"
          />
        </div>
      </div>
    </div>

    <!-- Notification -->
    <Notification :notification="notification" />
  </AppLayout>
</template>
