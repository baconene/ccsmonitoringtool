<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50"
        @click.self="close"
      >
        <div class="flex items-center justify-center min-h-screen p-4">
          <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="isOpen"
              class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
              @click.stop
            >
              <!-- Header -->
              <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <CalendarPlus class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                  </div>
                  <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                      {{ isEditMode ? 'Edit Course Schedule' : 'Create Course Schedule' }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ courseName }}</p>
                  </div>
                </div>
                <button
                  @click="close"
                  class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                  title="Close"
                >
                  <X class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                </button>
              </div>

              <!-- Form -->
              <form @submit.prevent="handleSubmit" class="p-6 space-y-5 overflow-y-auto max-h-[calc(90vh-180px)]">
                <!-- Title -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Title (Optional)
                  </label>
                  <input
                    v-model="form.title"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Leave empty to use course title"
                  />
                </div>

                <!-- Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description (Optional)
                  </label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Leave empty to use course description"
                  ></textarea>
                </div>

                <!-- Date & Time Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- From DateTime -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Start Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.from_datetime"
                      type="datetime-local"
                      required
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                  </div>

                  <!-- To DateTime -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      End Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.to_datetime"
                      type="datetime-local"
                      required
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                  </div>
                </div>

                <!-- Location -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Location (Optional)
                  </label>
                  <input
                    v-model="form.location"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="e.g., Room 101, Online, Zoom Link"
                  />
                </div>

                <!-- Is Recurring Checkbox -->
                <div class="flex items-start gap-3">
                  <input
                    v-model="form.is_recurring"
                    type="checkbox"
                    id="is_recurring"
                    class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <div>
                    <label for="is_recurring" class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                      Recurring Schedule
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      Check if this schedule repeats on a regular basis
                    </p>
                  </div>
                </div>

                <!-- Recurrence Rule (shown if recurring) -->
                <Transition
                  enter-active-class="transition ease-out duration-200"
                  enter-from-class="opacity-0 -translate-y-2"
                  enter-to-class="opacity-100 translate-y-0"
                  leave-active-class="transition ease-in duration-150"
                  leave-from-class="opacity-100 translate-y-0"
                  leave-to-class="opacity-0 -translate-y-2"
                >
                  <div v-if="form.is_recurring">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Recurrence Pattern <span class="text-red-500">*</span>
                    </label>
                    <select
                      v-model="form.recurrence_rule"
                      required
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                      <option value="">Select frequency...</option>
                      <option value="FREQ=DAILY">Daily</option>
                      <option value="FREQ=WEEKLY">Weekly</option>
                      <option value="FREQ=WEEKLY;BYDAY=MO,WE,FR">Weekly (Mon, Wed, Fri)</option>
                      <option value="FREQ=WEEKLY;BYDAY=TU,TH">Weekly (Tue, Thu)</option>
                      <option value="FREQ=MONTHLY">Monthly</option>
                    </select>
                  </div>
                </Transition>

                <!-- Session Number -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Session Number (Optional)
                  </label>
                  <input
                    v-model.number="form.session_number"
                    type="number"
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="e.g., 1, 2, 3..."
                  />
                </div>

                <!-- Topics Covered -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Topics to be Covered (Optional)
                  </label>
                  <textarea
                    v-model="form.topics_covered"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="List the topics that will be covered in this session..."
                  ></textarea>
                </div>

                <!-- Required Materials -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Required Materials (Optional)
                  </label>
                  <textarea
                    v-model="form.required_materials"
                    rows="2"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="List any materials students should bring..."
                  ></textarea>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                  <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
                </div>
              </form>

              <!-- Footer -->
              <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <button
                  @click="close"
                  type="button"
                  class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                  :disabled="isSubmitting"
                >
                  Cancel
                </button>
                <button
                  @click="handleSubmit"
                  type="button"
                  :disabled="isSubmitting || !isFormValid"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2"
                >
                  <span v-if="isSubmitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                  {{ isSubmitting 
                    ? (isEditMode ? 'Updating...' : 'Creating...') 
                    : (isEditMode ? 'Update Schedule' : 'Create Schedule') 
                  }}
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { CalendarPlus, X } from 'lucide-vue-next';

interface Schedule {
  id: number;
  title: string;
  description?: string;
  from_datetime: string;
  to_datetime: string;
  location?: string;
  is_recurring: boolean;
  recurrence_rule?: string;
  session_number?: number;
  topics_covered?: string;
  required_materials?: string;
}

interface Props {
  isOpen: boolean;
  courseId: number;
  courseName: string;
  existingSchedule?: Schedule | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

// Computed: Is editing mode
const isEditMode = computed(() => !!props.existingSchedule);

// Form state
const form = ref({
  title: '',
  description: '',
  from_datetime: '',
  to_datetime: '',
  location: '',
  is_recurring: false,
  recurrence_rule: '',
  session_number: null as number | null,
  topics_covered: '',
  required_materials: '',
});

const isSubmitting = ref(false);
const errorMessage = ref('');

// Helper: Format datetime for input[type="datetime-local"]
const formatDatetimeLocal = (datetime: string): string => {
  const date = new Date(datetime);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

// Helper: Reset form
const resetForm = () => {
  form.value = {
    title: '',
    description: '',
    from_datetime: '',
    to_datetime: '',
    location: '',
    is_recurring: false,
    recurrence_rule: '',
    session_number: null,
    topics_covered: '',
    required_materials: '',
  };
};

// Watch for existing schedule and populate form
watch(() => props.existingSchedule, (schedule) => {
  if (schedule) {
    // Edit mode - populate form with existing data
    form.value = {
      title: schedule.title || '',
      description: schedule.description || '',
      from_datetime: schedule.from_datetime ? formatDatetimeLocal(schedule.from_datetime) : '',
      to_datetime: schedule.to_datetime ? formatDatetimeLocal(schedule.to_datetime) : '',
      location: schedule.location || '',
      is_recurring: schedule.is_recurring || false,
      recurrence_rule: schedule.recurrence_rule || '',
      session_number: schedule.session_number || null,
      topics_covered: schedule.topics_covered || '',
      required_materials: schedule.required_materials || '',
    };
  } else {
    // Create mode - reset form
    resetForm();
  }
}, { immediate: true });

// Form validation
const isFormValid = computed(() => {
  if (!form.value.from_datetime || !form.value.to_datetime) return false;
  if (form.value.is_recurring && !form.value.recurrence_rule) return false;
  
  // Check that end time is after start time
  if (form.value.from_datetime && form.value.to_datetime) {
    return new Date(form.value.to_datetime) > new Date(form.value.from_datetime);
  }
  
  return true;
});

// Handle form submission
const handleSubmit = () => {
  if (!isFormValid.value || isSubmitting.value) return;

  errorMessage.value = '';
  isSubmitting.value = true;

  const data = {
    title: form.value.title || null,
    description: form.value.description || null,
    from_datetime: form.value.from_datetime,
    to_datetime: form.value.to_datetime,
    location: form.value.location || null,
    is_all_day: false,
    is_recurring: form.value.is_recurring,
    recurrence_rule: form.value.is_recurring ? form.value.recurrence_rule : null,
    session_number: form.value.session_number,
    topics_covered: form.value.topics_covered || null,
    required_materials: form.value.required_materials || null,
  };

  // Determine URL and method based on edit mode
  const url = isEditMode.value
    ? `/courses/${props.courseId}/schedules/${props.existingSchedule!.id}`
    : `/courses/${props.courseId}/schedules`;
  
  const method = isEditMode.value ? 'put' : 'post';

  router[method](
    url,
    data,
    {
      preserveScroll: false,
      preserveState: false,
      onSuccess: (page) => {
        console.log(`✅ Schedule ${isEditMode.value ? 'updated' : 'created'} successfully`);
        close();
        // Force page reload to refresh schedules
        router.reload({ only: ['courses'] });
      },
      onError: (errors) => {
        console.error(`❌ Schedule ${isEditMode.value ? 'update' : 'creation'} error:`, errors);
        if (errors.schedule) {
          errorMessage.value = errors.schedule;
        } else {
          errorMessage.value = Object.values(errors).flat().join(', ');
        }
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    }
  );
};

// Close modal
const close = () => {
  if (!isSubmitting.value) {
    resetForm();
    errorMessage.value = '';
    emit('close');
  }
};
</script>
