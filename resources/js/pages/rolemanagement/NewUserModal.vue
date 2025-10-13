   <template>

<div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New User</h3>
        <button
        @click="closeModal"
        class="p-2 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        title="Close"
        >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Name Field -->
        <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Name <span class="text-red-500">*</span>
        </label>
        <input
          type="text"
          v-model="formData.name"
          required
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          placeholder="Enter full name..."
        >
        </div>

        <!-- Email Field -->
        <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Email <span class="text-red-500">*</span>
        </label>
        <input
          type="email"
          v-model="formData.email"
          required
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          placeholder="Enter email address..."
        >
        </div>

        <!-- Password Field -->
        <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Password <span class="text-red-500">*</span>
        </label>
        <input
          type="password"
          v-model="formData.password"
          required
          minlength="8"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          placeholder="Minimum 8 characters..."
        >
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Password must be at least 8 characters long.
        </p>
        </div>

        <!-- Role Field -->
        <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Role <span class="text-red-500">*</span>
        </label>
        <select
          v-model="formData.role"
          required
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
        >
          <option value="student">Student</option>
          <option value="instructor">Instructor</option>
          <option value="admin">Admin</option>
        </select>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Select the user's role in the system.
        </p>
        </div>
        
        <!-- Grade Level - Only show for students -->
        <div v-if="formData.role === 'student'" class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Grade Level
        </label>
        <select
          v-model="formData.grade_level_id"
          :disabled="loadingGradeLevels"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
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
          <span v-else>Select the student's grade level.</span>
        </p>
        </div>
        
        <!-- Section - Only show for students -->
        <div v-if="formData.role === 'student'" class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
          Section
        </label>
        <input
          type="text"
          v-model="formData.section"
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
          placeholder="e.g., Section A, Room 101..."
        >
        <p class="text-xs text-gray-500 dark:text-gray-400">
          Specify the student's section or classroom.
        </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button
          type="button"
          @click="closeModal"
          class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 rounded-lg transition-colors focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Add User</span>
        </button>
        </div>
      </form>
    </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  showModal: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'submit'])

const formData = reactive({
  name: '',
  email: '',
  password: '',
  role: 'student',
  grade_level_id: null,
  section: ''
})

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

const closeModal = () => {
  emit('close')
}

const handleSubmit = () => {
  // Get the selected grade level display name for backward compatibility
  const selectedGradeLevel = gradeLevels.value.find(gl => gl.id === formData.grade_level_id)
  const submitData = {
    ...formData,
    grade_level: selectedGradeLevel ? selectedGradeLevel.display_name : ''
  }
  
  emit('submit', submitData)
  // Reset form
  Object.assign(formData, {
    name: '',
    email: '',
    password: '',
    role: 'student',
    grade_level_id: null,
    section: ''
  })
}
</script>