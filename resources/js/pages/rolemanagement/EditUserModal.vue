<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { X } from 'lucide-vue-next';
import type { User } from '@/types';
import axios from 'axios';

const props = defineProps<{
  showModal: boolean;
  user: User | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'submit', user: any): void;
}>();

const formData = ref({
  id: 0,
  name: '',
  email: '',
  role: '',
  grade_level: '',
  grade_level_id: null as number | null,
  section: '',
  password: ''
});

// Grade levels data
const gradeLevels = ref([])
const loadingGradeLevels = ref(false)

// Fetch grade levels from API
const fetchGradeLevels = async () => {
  try {
    loadingGradeLevels.value = true
    const response = await axios.get('/api/grade-levels')
    gradeLevels.value = response.data.grade_levels || []
  } catch (error) {
    console.error('Failed to fetch grade levels:', error)
    gradeLevels.value = []
  } finally {
    loadingGradeLevels.value = false
  }
}

// Load grade levels on mount
onMounted(() => {
  fetchGradeLevels()
})

// Watch for user prop changes and populate form
watch(() => props.user, (newUser) => {
  if (newUser) {
    formData.value = {
      id: newUser.id,
      name: newUser.name,
      email: newUser.email,
      role: (newUser as any).role_name || '',
      grade_level: (newUser as any).grade_level || '',
      grade_level_id: (newUser as any).grade_level_id || null,
      section: (newUser as any).section || '',
      password: '' // Password is optional for editing
    };
  }
}, { immediate: true });

const handleSubmit = () => {
  // Only include password if it's provided
  const dataToSubmit = { ...formData.value };
  if (!dataToSubmit.password) {
    delete (dataToSubmit as any).password;
  }
  emit('submit', dataToSubmit);
};

const handleClose = () => {
  emit('close');
};
</script>

<template>
  <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/50 dark:bg-black/70 transition-opacity" @click="handleClose"></div>
      
      <!-- Modal -->
      <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit User</h3>
          <button
            @click="handleClose"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
          <!-- Name -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
              Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.name"
              type="text"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="Enter full name"
            />
            <p class="text-xs text-gray-500 dark:text-gray-400">The user's full name</p>
          </div>

          <!-- Email -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
              Email <span class="text-red-500">*</span>
            </label>
            <input
              v-model="formData.email"
              type="email"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="user@example.com"
            />
            <p class="text-xs text-gray-500 dark:text-gray-400">A valid email address</p>
          </div>

          <!-- Password (Optional for editing) -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
              Password <span class="text-gray-400 text-xs">(Leave blank to keep current)</span>
            </label>
            <input
              v-model="formData.password"
              type="password"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="Enter new password (optional)"
            />
            <p class="text-xs text-gray-500 dark:text-gray-400">Minimum 8 characters (only if changing password)</p>
          </div>

          <!-- Role -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
              Role <span class="text-red-500">*</span>
            </label>
            <select
              v-model="formData.role"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            >
              <option value="">Select a role</option>
              <option value="admin">Admin</option>
              <option value="instructor">Instructor</option>
              <option value="student">Student</option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400">The user's role in the system</p>
          </div>

          <!-- Grade Level (Only for Students) -->
          <div v-if="formData.role === 'student'" class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
              Grade Level
            </label>
            <select
              v-model="formData.grade_level_id"
              :disabled="loadingGradeLevels"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <option value="">Select a grade level...</option>
              <option 
                v-for="gradeLevel in gradeLevels" 
                :key="gradeLevel.id" 
                :value="gradeLevel.id"
              >
                {{ gradeLevel.display_name }}
              </option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              <span v-if="loadingGradeLevels">Loading grade levels...</span>
              <span v-else>The student's current grade level</span>
            </p>
          </div>

          <!-- Section (Only for Students) -->
          <div v-if="formData.role === 'student'" class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
              Section
            </label>
            <input
              v-model="formData.section"
              type="text"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              placeholder="e.g., Section A"
            />
            <p class="text-xs text-gray-500 dark:text-gray-400">The student's class section</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
              type="button"
              @click="handleClose"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Update User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
