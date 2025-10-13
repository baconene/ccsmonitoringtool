<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ActivityType } from '@/types';
import { ArrowLeft, Plus, Upload, Download, FileText, X } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import CosmicBackground from '@/components/CosmicBackground.vue';

interface Props {
    activityTypes: ActivityType[];
}

const props = defineProps<Props>();

const createWithCsv = ref(false);
const selectedFile = ref<File | null>(null);
const dragActive = ref(false);
const fileInputRef = ref<HTMLInputElement>();

const form = useForm({
    title: '',
    description: '',
    activity_type_id: '',
    create_with_csv: false,
    quiz_title: '',
    quiz_description: '',
    csv_file: null as File | null,
});

const isQuizType = computed(() => {
    const selectedType = props.activityTypes.find(type => type.id.toString() === form.activity_type_id);
    return selectedType?.name === 'quiz';
});

const canShowBulkUpload = computed(() => {
    return isQuizType.value && form.activity_type_id;
});

const handleBack = () => {
    router.visit('/activity-management');
};

const downloadCsvTemplate = () => {
    window.open('/quizzes/csv-template', '_blank');
};

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
    // Check file type
    if (!file.name.toLowerCase().endsWith('.csv') && !file.name.toLowerCase().endsWith('.txt')) {
        alert('Please select a CSV or TXT file');
        return;
    }
    
    // Check file size (500MB = 524,288,000 bytes)
    if (file.size > 524288000) {
        alert('File size must be less than 500MB');
        return;
    }
    
    selectedFile.value = file;
    form.csv_file = file;
};

const removeFile = () => {
    selectedFile.value = null;
    form.csv_file = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const handleSubmit = () => {
    // Update form data
    form.create_with_csv = createWithCsv.value;
    
    // If creating with CSV, ensure quiz title is set
    if (createWithCsv.value && !form.quiz_title) {
        form.quiz_title = form.title; // Default to activity title
    }
    
    form.post('/activities', {
        onSuccess: () => {
            router.visit('/activity-management');
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="relative min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors duration-300">
            <CosmicBackground />
            
            <div class="relative z-10 py-6 px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <button
                        @click="handleBack"
                        class="mb-4 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors group"
                    >
                        <ArrowLeft :size="20" class="group-hover:-translate-x-1 transition-transform" />
                        <span>Back to Activities</span>
                    </button>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                        Create New Activity
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Create a new quiz, assignment, or exercise for your course
                    </p>
                </div>

                <!-- Create Form -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-4 sm:p-6 lg:p-8">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <Label for="title" class="text-sm font-medium">
                            Activity Title <span class="text-red-500">*</span>
                        </Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="Enter activity title"
                            required
                            class="mt-1"
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <Label for="description" class="text-sm font-medium">
                            Description
                        </Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter activity description"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.description }"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Activity Type -->
                    <div>
                        <Label for="activity_type" class="text-sm font-medium">
                            Activity Type <span class="text-red-500">*</span>
                        </Label>
                        <select
                            id="activity_type"
                            v-model="form.activity_type_id"
                            required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.activity_type_id }"
                        >
                            <option value="" disabled>Select activity type</option>
                            <option
                                v-for="type in activityTypes"
                                :key="type.id"
                                :value="type.id"
                            >
                                {{ type.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.activity_type_id" class="mt-1 text-sm text-red-500">
                            {{ form.errors.activity_type_id }}
                        </p>
                    </div>

                    <!-- Bulk Upload Option (Only for Quiz type) -->
                    <div v-if="canShowBulkUpload" class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center gap-3 mb-4">
                            <input
                                id="create_with_csv"
                                v-model="createWithCsv"
                                type="checkbox"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                            <Label for="create_with_csv" class="text-sm font-medium">
                                Create quiz with bulk upload (CSV)
                            </Label>
                        </div>

                        <!-- Bulk Upload Fields -->
                        <div v-if="createWithCsv" class="space-y-4 ml-7">
                            <!-- Info Banner -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <Upload class="text-blue-600 dark:text-blue-400 mt-0.5" :size="20" />
                                    <div class="text-sm text-blue-800 dark:text-blue-200">
                                        <p class="font-medium mb-2">Create Activity and Quiz Together:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>Upload a CSV file to create the activity with quiz questions automatically</li>
                                            <li>Download the template to see the required format</li>
                                            <li>First column "correct_answer1" will be marked as correct</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Download Template Button -->
                            <div class="flex justify-start">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="downloadCsvTemplate"
                                    class="flex items-center gap-2"
                                >
                                    <Download :size="16" />
                                    Download CSV Template
                                </Button>
                            </div>

                            <!-- Quiz Title -->
                            <div>
                                <Label for="quiz_title" class="text-sm font-medium">
                                    Quiz Title <span class="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="quiz_title"
                                    v-model="form.quiz_title"
                                    type="text"
                                    placeholder="Enter quiz title"
                                    class="mt-1"
                                    :class="{ 'border-red-500': form.errors.quiz_title }"
                                />
                                <p v-if="form.errors.quiz_title" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.quiz_title }}
                                </p>
                            </div>

                            <!-- Quiz Description -->
                            <div>
                                <Label for="quiz_description" class="text-sm font-medium">
                                    Quiz Description
                                </Label>
                                <textarea
                                    id="quiz_description"
                                    v-model="form.quiz_description"
                                    rows="3"
                                    placeholder="Enter quiz description (optional)"
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                ></textarea>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <Label class="text-sm font-medium">
                                    CSV File <span class="text-red-500">*</span>
                                </Label>
                                
                                <!-- File Drop Zone -->
                                <div
                                    v-if="!selectedFile"
                                    @drop="handleDrop"
                                    @dragover.prevent="dragActive = true"
                                    @dragleave="dragActive = false"
                                    @click="fileInputRef?.click()"
                                    class="mt-1 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                                    :class="{
                                        'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20': dragActive,
                                        'border-red-500': form.errors.csv_file
                                    }"
                                >
                                    <Upload class="mx-auto text-gray-400 mb-2" :size="32" />
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Drop your CSV file here, or click to browse
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Supports CSV and TXT files up to 500MB
                                    </p>
                                </div>

                                <!-- Selected File Display -->
                                <div
                                    v-else
                                    class="mt-1 border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <FileText class="text-blue-600 dark:text-blue-400" :size="20" />
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ selectedFile.name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ formatFileSize(selectedFile.size) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            @click="removeFile"
                                            type="button"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            <X :size="16" />
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
                                
                                <p v-if="form.errors.csv_file" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.csv_file }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
                        >
                            <Plus v-if="!createWithCsv" :size="16" />
                            <Upload v-else :size="16" />
                            {{ 
                                form.processing 
                                    ? (createWithCsv ? 'Creating with Quiz...' : 'Creating...') 
                                    : (createWithCsv ? 'Create Activity with Quiz' : 'Create Activity')
                            }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="handleBack"
                            :disabled="form.processing"
                            class="border-purple-300 dark:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20"
                        >
                            Cancel
                        </Button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
