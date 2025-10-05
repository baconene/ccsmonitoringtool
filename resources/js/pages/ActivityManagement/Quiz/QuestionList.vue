<script setup lang="ts">
import { Question } from '@/types';
import { Edit, Trash2 } from 'lucide-vue-next';

interface Props {
    questions: Question[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    update: [questionId: number, data: any];
    delete: [questionId: number, questionText: string];
}>();

const getQuestionTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        'multiple-choice': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'true-false': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'short-answer': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
        'enumeration': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    };
    return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Questions</h3>

            <div v-if="questions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                No questions yet. Add your first question to get started.
            </div>

            <div v-else class="space-y-4">
                <div
                    v-for="(question, index) in questions"
                    :key="question.id"
                    class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ index + 1 }}.
                                </span>
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs rounded-full',
                                        getQuestionTypeColor(question.question_type)
                                    ]"
                                >
                                    {{ question.question_type }}
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    ({{ question.points }} {{ question.points === 1 ? 'point' : 'points' }})
                                </span>
                            </div>
                            <p class="text-gray-900 dark:text-white mb-3">{{ question.question_text }}</p>

                            <!-- Options for multiple-choice -->
                            <div v-if="question.options && question.options.length > 0" class="ml-6 space-y-1">
                                <div
                                    v-for="option in question.options"
                                    :key="option.id"
                                    class="flex items-center gap-2 text-sm"
                                >
                                    <span :class="option.is_correct ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-600 dark:text-gray-400'">
                                        {{ option.is_correct ? '✓' : '○' }}
                                    </span>
                                    <span :class="option.is_correct ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-gray-700 dark:text-gray-300'">
                                        {{ option.option_text }}
                                    </span>
                                </div>
                            </div>

                            <!-- Correct answer for true-false -->
                            <div v-else-if="question.correct_answer" class="ml-6 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Correct answer:</span>
                                <span class="text-green-600 dark:text-green-400 font-semibold ml-2">
                                    {{ question.correct_answer }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 ml-4">
                            <button
                                @click="emit('delete', question.id, question.question_text)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                title="Delete"
                            >
                                <Trash2 :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
