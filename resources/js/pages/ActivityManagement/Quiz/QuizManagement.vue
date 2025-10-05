<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import QuestionList from '@/pages/ActivityManagement/Quiz/QuestionList.vue';
import AddQuestionModal from '@/pages/ActivityManagement/Quiz/AddQuestionModal.vue';
import DeleteQuestionModal from '@/pages/ActivityManagement/Quiz/DeleteQuestionModal.vue';

interface Props {
    activity: any;
    quiz?: any;
}

const props = defineProps<Props>();

const showAddQuestionModal = ref(false);
const showDeleteQuestionModal = ref(false);
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
</script>

<template>
    <div class="space-y-6">
        <!-- Quiz Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Quiz Management</h2>
                <button
                    v-if="quiz"
                    @click="showAddQuestionModal = true"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <Plus :size="18" />
                    Add Question
                </button>
            </div>

            <!-- Create Quiz if it doesn't exist -->
            <div v-if="!quiz" class="text-center py-8">
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    No quiz has been created for this activity yet.
                </p>
                <button
                    @click="handleCreateQuiz"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Create Quiz
                </button>
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
    </div>
</template>
