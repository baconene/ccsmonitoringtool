<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus, Upload, Download } from 'lucide-vue-next';
import QuestionList from '@/pages/ActivityManagement/Quiz/QuestionList.vue';
import AddQuestionModal from '@/pages/ActivityManagement/Quiz/AddQuestionModal.vue';
import DeleteQuestionModal from '@/pages/ActivityManagement/Quiz/DeleteQuestionModal.vue';
import BulkUploadModal from './BulkUploadModal.vue';

interface Props {
    activity: any;
    quiz?: any;
}

const props = defineProps<Props>();

const showAddQuestionModal = ref(false);
const showDeleteQuestionModal = ref(false);
const showBulkUploadModal = ref(false);
const questionToDelete = ref<{ id: number; text: string } | null>(null);

const handleCreateQuiz = () => {
    router.post('/quizzes', {
        activity_id: props.activity.id,
        title: props.activity.title,
        description: props.activity.description,
    });
};

const handleAddQuestion = (questionData: any) => {
    router.post('/questions', {
        ...questionData,
        quiz_id: props.quiz.id,
    }, {
        onSuccess: () => {
            showAddQuestionModal.value = false;
        },
    });
};

const handleUpdateQuestion = (questionId: number, questionData: any) => {
    router.put(`/questions/${questionId}`, questionData);
};

const handleDeleteQuestion = (questionId: number, questionText: string) => {
    questionToDelete.value = { id: questionId, text: questionText };
    showDeleteQuestionModal.value = true;
};

const confirmDeleteQuestion = () => {
    if (questionToDelete.value) {
        router.delete(`/questions/${questionToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteQuestionModal.value = false;
                questionToDelete.value = null;
            },
        });
    }
};

const handleBulkUpload = (formData: FormData) => {
    router.post('/quizzes/bulk-upload', formData, {
        onSuccess: () => {
            showBulkUploadModal.value = false;
        },
        onError: (errors) => {
            console.error('Bulk upload errors:', errors);
        },
    });
};

const downloadCsvTemplate = () => {
    window.open('/quizzes/csv-template', '_blank');
};
</script>

<template>
    <div class="space-y-6">
        <!-- Quiz Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Quiz Management</h2>
                <div v-if="quiz" class="flex items-center gap-2">
                    <button
                        @click="downloadCsvTemplate"
                        class="flex items-center gap-1.5 px-3 py-2 text-sm bg-white dark:bg-gray-700 text-green-600 dark:text-green-400 border border-green-300 dark:border-green-600 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
                        title="Download CSV Template"
                    >
                        <Download :size="16" />
                        <span class="hidden sm:inline">Template</span>
                    </button>
                    <button
                        @click="showBulkUploadModal = true"
                        class="flex items-center gap-1.5 px-3 py-2 text-sm bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
                        title="Bulk Upload Questions"
                    >
                        <Upload :size="16" />
                        <span class="hidden sm:inline">Upload</span>
                    </button>
                    <button
                        @click="showAddQuestionModal = true"
                        class="flex items-center gap-1.5 px-3 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                        title="Add Question"
                    >
                        <Plus :size="16" />
                        <span class="hidden sm:inline">Add</span>
                    </button>
                </div>
            </div>

            <!-- Create Quiz if it doesn't exist -->
            <div v-if="!quiz" class="text-center py-8">
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    No quiz has been created for this activity yet.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button
                        @click="handleCreateQuiz"
                        class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <Plus :size="18" />
                        Create Empty Quiz
                    </button>
                    <span class="hidden sm:inline text-gray-400 dark:text-gray-500">or</span>
                    <button
                        @click="showBulkUploadModal = true"
                        class="flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        <Upload :size="18" />
                        Create from CSV
                    </button>
                    <button
                        @click="downloadCsvTemplate"
                        class="flex items-center gap-2 px-4 py-2 text-sm bg-white dark:bg-gray-700 text-green-600 dark:text-green-400 border border-green-300 dark:border-green-600 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
                    >
                        <Download :size="16" />
                        Download Template
                    </button>
                </div>
            </div>

            <!-- Quiz Info -->
            <div v-else>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Total Questions:</strong> {{ quiz.questions?.length || 0 }}</p>
                    <p><strong>Total Points:</strong> {{ quiz.questions?.reduce((sum: number, q: any) => sum + q.points, 0) || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <QuestionList
            v-if="quiz?.questions"
            :questions="quiz.questions"
            @update="handleUpdateQuestion"
            @delete="handleDeleteQuestion"
        />

        <!-- Add Question Modal -->
        <AddQuestionModal
            v-if="quiz"
            :show="showAddQuestionModal"
            @close="showAddQuestionModal = false"
            @submit="handleAddQuestion"
        />

        <!-- Delete Question Modal -->
        <DeleteQuestionModal
            :show="showDeleteQuestionModal"
            :question-text="questionToDelete?.text"
            @close="showDeleteQuestionModal = false; questionToDelete = null"
            @confirm="confirmDeleteQuestion"
        />

        <!-- Bulk Upload Modal -->
        <BulkUploadModal
            :show="showBulkUploadModal"
            :activity="activity"
            @close="showBulkUploadModal = false"
            @submit="handleBulkUpload"
        />
    </div>
</template>
