<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, computed, ref } from 'vue';
import type { NewUserData, UserUpdateData, User, BreadcrumbItem } from '@/types/index';
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

// CSV Upload state
const csvFile = ref<File | null>(null);
const uploading = ref(false);
const uploadResults = ref<{
  success: number;
  failed: number;
  skipped: number;
  errors: Array<{ line: number; email: string; error: string }>;
  info: Array<{ line: number; email: string; message: string }>;
} | null>(null);
const showUploadModal = ref(false);

// Debug response state
const debugResponse = ref<any>(null);
const debugError = ref<string | null>(null);

// Load all data
const loadData = async () => {
  mainError.value = null;
  debugResponse.value = null;
  debugError.value = null;
  
  try {
    // Load both users and roles in parallel, but handle failures independently
    const userPromise = fetchUsers().catch(err => {
      console.error('Failed to load users:', err);
      debugError.value = 'User fetch error: ' + JSON.stringify({
        message: err.message,
        status: err.response?.status,
        statusText: err.response?.statusText,
        data: err.response?.data,
      }, null, 2);
      throw err; // Re-throw to make Promise.allSettled see it as rejected
    });
    
    const rolePromise = fetchRoles().catch(err => {
      console.error('Failed to load roles:', err);
      throw err;
    });
    
    const [usersResult, rolesResult] = await Promise.allSettled([userPromise, rolePromise]);
    
    // Log the results for debugging
    console.log('Users Result:', usersResult);
    console.log('Users value:', users.value);
    debugResponse.value = {
      usersResult,
      users: users.value,
      rolesResult,
      roles: roles.value
    };
    
    // Check if both failed
    if (usersResult.status === 'rejected' && rolesResult.status === 'rejected') {
      mainError.value = 'Failed to load role management data. Please check your connection and try again.';
    } else if (usersResult.status === 'rejected') {
      // Users failed but roles might have loaded - show warning but allow partial display
      const errorMsg = usersResult.reason?.response?.data?.message || 'Failed to load users';
      console.warn('Users load failed:', errorMsg);
      // Don't block the UI, just warn
      if (roles.value.length === 0) {
        showError('Failed to load users: ' + errorMsg);
      }
    } else if (rolesResult.status === 'rejected') {
      // Roles failed but users loaded - show warning
      console.warn('Roles load failed');
      if (users.value.length > 0) {
        showError('Some role data may be incomplete or unavailable.');
      }
    }
  } catch (e) {
    console.error('Unexpected error in loadData:', e);
    debugError.value = JSON.stringify(e, null, 2);
  }
};

// CSV Upload handlers
const fileInputRef = ref<HTMLInputElement | null>(null);

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    csvFile.value = target.files[0];
    console.log('File selected:', csvFile.value.name, csvFile.value.size, 'bytes');
  }
};

const handleUploadCSV = async () => {
  if (!csvFile.value) {
    showError('Please select a CSV file');
    return;
  }

  // Store file reference before async operations
  const fileToUpload = csvFile.value;
  
  uploading.value = true;
  uploadResults.value = null;

  try {
    const formData = new FormData();
    formData.append('csv_file', fileToUpload);

    console.log('Uploading file:', fileToUpload.name);

    // Use axios which automatically handles CSRF tokens
    const axios = (await import('axios')).default;
    
    const response = await axios.post('/api/users/bulk-upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    console.log('Upload response:', response.data);

    uploadResults.value = response.data.results;
    
    let message = '';
    if (response.data.results.success > 0) {
      message += `Successfully created ${response.data.results.success} user(s)`;
    }
    if (response.data.results.skipped > 0) {
      if (message) message += ', ';
      message += `${response.data.results.skipped} user(s) skipped (already exist)`;
    }
    if (message) {
      success(message);
      await fetchUsers(); // Reload users
    }

    if (response.data.results.failed > 0) {
      showError(`Failed to create ${response.data.results.failed} user(s). Check details below.`);
    }

  } catch (err: any) {
    console.error('Upload error:', err);
    const errorMessage = err.response?.data?.message || err.message || 'Failed to upload CSV file';
    showError(errorMessage);
    
    // Log detailed error info
    if (err.response?.data?.errors) {
      console.error('Validation errors:', err.response.data.errors);
    }
  } finally {
    uploading.value = false;
  }
};

const closeUploadModal = () => {
  showUploadModal.value = false;
  csvFile.value = null;
  uploadResults.value = null;
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

const handleEditUser = async (user: User & { password?: string; grade_level_id?: number | null; email_verified?: boolean }) => {
  try {
    console.log('handleEditUser received user:', user);
    
    const updateData: UserUpdateData = {
      name: user.name,
      email: user.email,
      role: typeof user.role === 'string' ? user.role : user.role?.name || 'user'
    };
    
    // Include password if provided
    if (user.password) {
      updateData.password = user.password;
    }
    
    // Include grade_level_id and section if provided (for students)
    if (user.grade_level_id !== undefined) {
      updateData.grade_level_id = user.grade_level_id;
    }
    if (user.section !== undefined) {
      updateData.section = user.section;
    }
    
    // Include email_verified if it exists
    if ('email_verified' in user) {
      updateData.email_verified = user.email_verified;
    }
    
    console.log('handleEditUser sending updateData:', updateData);
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
        <div class="mb-8 flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Role Management</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Manage user roles and permissions in the system.</p>
          </div>
          <button
            @click="showUploadModal = true"
            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-md flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Bulk Upload CSV
          </button>
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

        <!-- DEBUG: Display API Response -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
          <h3 class="font-bold text-blue-900 dark:text-blue-200 mb-2">DEBUG INFO</h3>
          <div class="text-sm font-mono bg-white dark:bg-gray-800 p-3 rounded border border-blue-200 dark:border-blue-800 max-h-64 overflow-auto">
            <pre v-if="debugResponse">{{ JSON.stringify(debugResponse, null, 2) }}</pre>
            <pre v-else-if="debugError" class="text-red-600">{{ debugError }}</pre>
            <p v-else class="text-gray-500">Loading...</p>
          </div>
          <div class="mt-2 text-sm">
            <p>Users Count: {{ users.length }}</p>
            <p>Roles Count: {{ roles.length }}</p>
          </div>
        </div>

        <!-- Content -->
        <div class="space-y-8">
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

    <!-- CSV Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-4 rounded-t-xl flex justify-between items-center">
          <h2 class="text-xl font-bold">Bulk Upload Users (CSV)</h2>
          <button @click="closeUploadModal" class="text-white hover:text-gray-200 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
          <!-- Instructions -->
          <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">CSV Format Requirements:</h3>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
              <li><strong>Admin (role_id=1):</strong> role_id, name, email, password</li>
              <li><strong>Instructor (role_id=2):</strong> role_id, name, email, password, title, department, specialization, bio, office_location, phone, office_hours, hire_date, employment_type, status, salary, education_level, certifications, years_experience</li>
              <li><strong>Student (role_id=3):</strong> role_id, name, email, password, grade_level_id, section, program, department</li>
            </ul>
            <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">* All role types require: role_id, name, email, password</p>
          </div>

          <!-- File Upload -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Select CSV File
            </label>
            <input
              type="file"
              accept=".csv"
              @change="handleFileChange"
              class="block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>

          <!-- Upload Button -->
          <div class="flex gap-3">
            <button
              @click="handleUploadCSV"
              :disabled="!csvFile || uploading"
              class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all font-semibold flex items-center justify-center gap-2"
            >
              <svg v-if="uploading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ uploading ? 'Uploading...' : 'Upload CSV' }}</span>
            </button>
            <button
              @click="closeUploadModal"
              class="px-4 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            >
              Cancel
            </button>
          </div>

          <!-- Results -->
          <div v-if="uploadResults" class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-4">
            <h3 class="font-semibold text-gray-900 dark:text-white text-lg">Upload Results</h3>
            
            <!-- Success Count -->
            <div v-if="uploadResults.success > 0" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
              <div class="flex items-center">
                <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-green-800 dark:text-green-200 font-medium">
                  Successfully created {{ uploadResults.success }} user(s)
                </span>
              </div>
            </div>

            <!-- Skipped Info -->
            <div v-if="uploadResults.skipped > 0" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span class="text-blue-800 dark:text-blue-200 font-medium">
                  Skipped {{ uploadResults.skipped }} user(s) (already exist)
                </span>
              </div>
              <div v-if="uploadResults.info && uploadResults.info.length > 0" class="max-h-60 overflow-y-auto space-y-2 mt-3">
                <div v-for="(info, index) in uploadResults.info" :key="index" class="text-sm text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/30 rounded p-2">
                  <span class="font-medium">Row {{ info.line }} ({{ info.email }}):</span> {{ info.message }}
                </div>
              </div>
            </div>

            <!-- Error Details -->
            <div v-if="uploadResults.failed > 0" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <svg class="h-5 w-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span class="text-red-800 dark:text-red-200 font-medium">
                  Failed to create {{ uploadResults.failed }} user(s)
                </span>
              </div>
              <div class="max-h-60 overflow-y-auto space-y-2 mt-3">
                <div v-for="(err, index) in uploadResults.errors" :key="index" class="text-sm text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900/30 rounded p-2">
                  <span class="font-medium">Row {{ err.line }} ({{ err.email }}):</span> {{ err.error }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
