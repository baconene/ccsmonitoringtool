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
    studentsProgress?: any[];
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
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p><strong>Total Questions:</strong> {{ quiz.questions?.length || 0 }}</p>
                    <p><strong>Total Points:</strong> {{ quiz.questions?.reduce((sum: number, q: any) => sum + q.points, 0) || 0 }}</p>
                    <p v-if="activity.due_date" class="flex items-center gap-2">
                        <strong>Due Date:</strong> 
                        <span :class="{
                            'text-red-600 dark:text-red-400 font-semibold': new Date(activity.due_date) < new Date(),
                            'text-orange-600 dark:text-orange-400 font-semibold': new Date(activity.due_date) >= new Date() && (new Date(activity.due_date).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24) <= 7,
                        }">
                            {{ new Date(activity.due_date).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                            }) }}
                            <span v-if="new Date(activity.due_date) < new Date()" class="ml-1">⚠️ (Overdue)</span>
                            <span v-else-if="(new Date(activity.due_date).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24) <= 7" class="ml-1">⏰ (Due Soon)</span>
                        </span>
                    </p>
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

        <!-- Student Submissions Table -->
        <div v-if="studentsProgress && studentsProgress.length > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Student Submissions ({{ studentsProgress.length }})
            </h3>
            <div class="overflow-x-auto bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Student Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Email</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Score</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Submission Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr
                            v-for="student in studentsProgress"
                            :key="student.student_id"
                            class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ student.student_name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ student.student_email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': student.status === 'in_progress',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': student.status === 'submitted',
                                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': student.status === 'graded',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400': student.status === 'not_started',
                                        }"
                                        class="px-3 py-1 rounded-full text-xs font-medium w-fit"
                                    >
                                        {{ student.status }}
                                    </span>
                                    <span
                                        :class="{
                                            'text-green-700 dark:text-green-400 font-medium': student.is_taking_activity || student.status === 'graded',
                                            'text-gray-600 dark:text-gray-400': !student.is_taking_activity && student.status === 'not_started',
                                        }"
                                        class="text-xs"
                                    >
                                        {{ student.is_taking_activity || student.status === 'graded' ? '✓ Active' : '○ Pending' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span v-if="student.score !== null" class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ student.score }}/{{ student.max_score }}
                                </span>
                                <span v-else class="text-gray-500 dark:text-gray-400">—</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                {{ student.submitted_at ? new Date(student.submitted_at).toLocaleDateString() : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
