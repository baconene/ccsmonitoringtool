<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { X, Upload, FileText, AlertCircle } from 'lucide-vue-next';

interface Props {
    show: boolean;
    activity: any;
}

interface Emits {
    (e: 'close'): void;
    (e: 'submit', formData: FormData): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const quizTitle = ref('');
const quizDescription = ref('');
const csvFile = ref<File | null>(null);
const dragActive = ref(false);
const errors = ref<Record<string, string>>({});

const fileInputRef = ref<HTMLInputElement>();

// Check if activity already exists (has ID)
const isExistingActivity = computed(() => {
    return props.activity?.id !== null && props.activity?.id !== undefined;
});

// Watch for activity changes and pre-fill the form
watch(() => props.activity, (newActivity) => {
    if (newActivity && isExistingActivity.value) {
        // Pre-fill with activity data when creating quiz for existing activity
        quizTitle.value = newActivity.title || '';
        quizDescription.value = newActivity.description || '';
    }
}, { immediate: true });

// Get effective values to send to backend
const effectiveQuizTitle = computed(() => {
    if (isExistingActivity.value) {
        // Use user input or fallback to activity title
        return quizTitle.value.trim() || props.activity.title || '';
    }
    // New activity - use user input
    return quizTitle.value.trim();
});

const effectiveQuizDescription = computed(() => {
    if (isExistingActivity.value) {
        // Use user input or fallback to activity description
        return quizDescription.value.trim() || props.activity.description || '';
    }
    // New activity - use user input
    return quizDescription.value.trim();
});

const isValid = computed(() => {
    if (isExistingActivity.value) {
        // Existing activity - valid if we have a CSV file and either user entered title OR activity has title
        return csvFile.value && (quizTitle.value.trim().length >= 3 || (props.activity.title && props.activity.title.length >= 3));
    }
    // New activity - just need CSV file
    return csvFile.value !== null;
});

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        validateAndSetFile(file);
    }
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    dragActive.value = false;
    
    const file = event.dataTransfer?.files[0];
    if (file) {
        validateAndSetFile(file);
    }
};

const validateAndSetFile = (file: File) => {
    errors.value = {};
    
    // Check file type
    if (!file.name.toLowerCase().endsWith('.csv') && !file.name.toLowerCase().endsWith('.txt')) {
        errors.value.file = 'Please select a CSV or TXT file';
        return;
    }
    
    // Check file size (500MB = 524,288,000 bytes)
    if (file.size > 524288000) {
        errors.value.file = 'File size must be less than 500MB';
        return;
    }
    
    csvFile.value = file;
};

const removeFile = () => {
    csvFile.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const handleSubmit = () => {
    errors.value = {};
    
    // Validate inputs
    if (isExistingActivity.value && quizTitle.value.trim().length < 3 && (!props.activity.title || props.activity.title.length < 3)) {
        errors.value.title = 'Quiz title must be at least 3 characters';
    }
    
    if (!csvFile.value) {
        errors.value.file = 'Please select a CSV file';
    }
    
    if (Object.keys(errors.value).length > 0) {
        return;
    }
    
    // Create FormData
    const formData = new FormData();
    formData.append('activity_id', props.activity.id.toString());
    
    // Always send quiz title and description using effective values
    formData.append('quiz_title', effectiveQuizTitle.value);
    formData.append('quiz_description', effectiveQuizDescription.value);
    
    formData.append('csv_file', csvFile.value!);
    
    emit('submit', formData);
};

const closeModal = () => {
    // Reset form
    quizTitle.value = '';
    quizDescription.value = '';
    csvFile.value = null;
    errors.value = {};
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
    
    emit('close');
};

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>

<template>
    <!-- Modal Backdrop -->
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
                v-if="show"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4"
                @click.self="closeModal"
            >
                <!-- Modal Content -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div v-if="show" class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Bulk Upload Quiz Questions
                            </h3>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <X :size="24" />
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-6">
                            <!-- Info Banner -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <AlertCircle class="text-blue-600 dark:text-blue-400 mt-0.5" :size="20" />
                                    <div class="text-sm text-blue-800 dark:text-blue-200">
                                        <p class="font-medium mb-2">CSV Format Requirements:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>Headers: Question Number, quiz_text, quiz_type, points, correct_answer1, answer2, answer3, answer4</li>
                                            <li>correct_answer1 will be marked as the correct answer</li>
                                            <li>Other answer columns (answer2-4) will be incorrect options</li>
                                            <li>Maximum file size: 500MB</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Details (only show when activity exists) -->
                            <div v-if="isExistingActivity" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Quiz Title *
                                    </label>
                                    <input
                                        v-model="quizTitle"
                                        type="text"
                                        placeholder="Enter quiz title"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        :class="{ 'border-red-500': errors.title }"
                                    />
                                    <p v-if="errors.title" class="text-red-600 text-sm mt-1">{{ errors.title }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Quiz Description
                                    </label>
                                    <textarea
                                        v-model="quizDescription"
                                        rows="3"
                                        placeholder="Enter quiz description (optional)"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    ></textarea>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CSV File *
                                </label>
                                
                                <!-- File Drop Zone -->
                                <div
                                    v-if="!csvFile"
                                    @drop="handleDrop"
                                    @dragover.prevent="dragActive = true"
                                    @dragleave="dragActive = false"
                                    @click="fileInputRef?.click()"
                                    class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                                    :class="{
                                        'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20': dragActive,
                                        'border-red-500': errors.file
                                    }"
                                >
                                    <Upload class="mx-auto text-gray-400 mb-4" :size="48" />
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Drop your CSV file here, or click to browse
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Supports CSV and TXT files up to 500MB
                                    </p>
                                </div>

                                <!-- Selected File Display -->
                                <div
                                    v-else
                                    class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <FileText class="text-blue-600 dark:text-blue-400" :size="24" />
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">
                                                    {{ csvFile.name }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ formatFileSize(csvFile.size) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            @click="removeFile"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                        >
                                            <X :size="20" />
                                        </button>
                                    </div>
                                </div>

                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    accept=".csv,.txt"
                                    @change="handleFileSelect"
                                    class="hidden"
                                />
                                
                                <p v-if="errors.file" class="text-red-600 text-sm mt-1">{{ errors.file }}</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-end gap-4 p-6 border-t border-gray-200 dark:border-gray-700">
                            <button
                                @click="closeModal"
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="handleSubmit"
                                :disabled="!isValid"
                                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
                            >
                                {{ isExistingActivity ? 'Upload & Create Quiz' : 'Upload Questions' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>