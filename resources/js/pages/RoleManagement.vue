<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import type { Role, User } from '@/types';

// Reactive state
const roles = ref<Role[]>([]);
const users = ref<User[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Load roles and users
const loadData = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    // Simulated API calls - replace with actual API endpoints
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Mock roles data
    roles.value = [
      { id: 1, name: 'admin', display_name: 'Admin', description: 'System administrator', is_active: true },
      { id: 2, name: 'instructor', display_name: 'Instructor', description: 'Course instructor', is_active: true },
      { id: 3, name: 'student', display_name: 'Student', description: 'Course student', is_active: true },
    ];
    
    // Mock users data with role relationships
    users.value = [
      { 
        id: 1, 
        name: 'Admin User', 
        email: 'admin@example.com', 
        role: roles.value[0],
        role_id: 1,
        role_name: 'admin',
        role_display_name: 'Admin',
        email_verified_at: null, 
        created_at: '', 
        updated_at: '' 
      },
      { 
        id: 2, 
        name: 'Instructor User', 
        email: 'instructor@example.com', 
        role: roles.value[1],
        role_id: 2,
        role_name: 'instructor',
        role_display_name: 'Instructor',
        email_verified_at: null, 
        created_at: '', 
        updated_at: '' 
      },
      { 
        id: 3, 
        name: 'Student User', 
        email: 'student@example.com', 
        role: roles.value[2],
        role_id: 3,
        role_name: 'student',
        role_display_name: 'Student',
        email_verified_at: null, 
        created_at: '', 
        updated_at: '' 
      },
    ];
    
  } catch (err) {
    error.value = 'Failed to load role management data';
    console.error('Error:', err);
  } finally {
    loading.value = false;
  }
};

// Load data on component mount
onMounted(() => {
  loadData();
});

// Get role badge color based on role name
const getRoleBadgeColor = (roleName: string) => {
  switch (roleName) {
    case 'admin': return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200';
    case 'instructor': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200';
    case 'student': return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
  }
};
</script>

<template>
  <Head title="Role Management" />

  <AppLayout>
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
        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 dark:text-red-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error</h3>
              <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ error }}</p>
              <button @click="loadData" class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 font-medium">
                Try again
              </button>
            </div>
          </div>
        </div>

        <!-- Content Grid -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          <!-- Roles Section -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">System Roles</h2>
            
            <div class="space-y-4">
              <div
                v-for="role in roles"
                :key="role.id"
                class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-sm transition-shadow"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center space-x-3">
                    <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleBadgeColor(role.name)}`">
                      {{ role.display_name }}
                    </span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ role.name }}</span>
                  </div>
                  <span :class="`inline-flex items-center px-2 py-1 text-xs font-medium rounded ${role.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'}`">
                    {{ role.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ role.description }}</p>
              </div>
            </div>
          </div>

          <!-- Users Section -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Users with Roles</h2>
            
            <div class="space-y-4">
              <div
                v-for="user in users"
                :key="user.id"
                class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:shadow-sm transition-shadow"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                      <span class="text-white text-sm font-semibold">{{ user.name.charAt(0) }}</span>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                    </div>
                  </div>
                  <span v-if="user.role && typeof user.role === 'object'" :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleBadgeColor(user.role.name)}`">
                    {{ user.role.display_name }}
                  </span>
                  <span v-else-if="user.role_display_name" :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleBadgeColor(user.role_name || 'instructor')}`">
                    {{ user.role_display_name }}
                  </span>
                </div>
                
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                  <span>Role ID: {{ user.role_id }}</span> • 
                  <span>Role Name: {{ user.role_name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Role Statistics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-full">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Admins</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.filter(u => u.role_name === 'admin').length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-full">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Instructors</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.filter(u => u.role_name === 'instructor').length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
              <div class="p-3 bg-green-100 dark:bg-green-900/50 rounded-full">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Students</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ users.filter(u => u.role_name === 'student').length }}</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>