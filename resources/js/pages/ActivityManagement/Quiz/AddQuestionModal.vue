<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { X, Plus, Trash2 } from 'lucide-vue-next';

interface Props {
    show: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
    submit: [data: any];
}>();

import { QUESTION_TYPE_OPTIONS, QUESTION_TYPES, QuestionType } from '@/constants/questionTypes';

const questionTypes = QUESTION_TYPE_OPTIONS;

const formData = ref<{
    question_text: string;
    question_type: QuestionType;
    points: number;
    correct_answer: string;
    options: Array<{ option_text: string; is_correct: boolean }>;
}>({
    question_text: '',
    question_type: QUESTION_TYPES.MULTIPLE_CHOICE,
    points: 1,
    correct_answer: '',
    options: [],
});

const showOptions = computed(() => formData.value.question_type === QUESTION_TYPES.MULTIPLE_CHOICE);
const showCorrectAnswer = computed(() => 
    formData.value.question_type === QUESTION_TYPES.TRUE_FALSE || 
    formData.value.question_type === QUESTION_TYPES.ENUMERATION
);

const resetForm = () => {
    formData.value = {
        question_text: '',
        question_type: 'multiple_choice',
        points: 1,
        correct_answer: '',
        options: [],
    };
};

watch(() => props.show, (newVal) => {
    if (!newVal) {
        resetForm();
    }
});

watch(() => formData.value.question_type, (newType) => {
    // Reset options and correct answer when changing type
    formData.value.options = [];
    formData.value.correct_answer = '';
    
    if (newType === QUESTION_TYPES.MULTIPLE_CHOICE && formData.value.options.length === 0) {
        formData.value.options = [
            { option_text: '', is_correct: false },
            { option_text: '', is_correct: false },
        ];
    }
    if (newType === QUESTION_TYPES.TRUE_FALSE) {
        formData.value.correct_answer = 'true';
    }
});

const addOption = () => {
    formData.value.options.push({ option_text: '', is_correct: false });
};

const removeOption = (index: number) => {
    formData.value.options.splice(index, 1);
};

const handleSubmit = () => {
    if (!formData.value.question_text) {
        alert('Please enter a question');
        return;
    }

    if (showOptions.value && formData.value.options.length < 2) {
        alert('Please add at least 2 options');
        return;
    }

    if (showOptions.value && !formData.value.options.some(o => o.is_correct)) {
        alert('Please mark at least one option as correct');
        return;
    }

    if (formData.value.question_type === QUESTION_TYPES.ENUMERATION && !formData.value.correct_answer) {
        alert('Please provide the correct answer for enumeration');
        return;
    }

    // Don't send empty options array for true_false and enumeration questions
    const submitData: any = { ...formData.value };
    if (submitData.question_type === QUESTION_TYPES.TRUE_FALSE || submitData.question_type === QUESTION_TYPES.ENUMERATION) {
        delete submitData.options;
    }

    emit('submit', submitData);
    // Reset form after successful submission
    resetForm();
};
</script>

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
                v-if="show"
                class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
                @click.self="emit('close')"
            >
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="show"
                        class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-xl max-h-[90vh] overflow-y-auto"
                    >
                        <!-- Header -->
                        <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Add Question
                            </h3>
                            <button
                                @click="emit('close')"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            >
                                <X :size="20" />
                            </button>
                        </div>

                        <!-- Body -->
                        <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                            <!-- Question Text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Question <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="formData.question_text"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter your question"
                                    required
                                />
                            </div>

                            <!-- Question Type & Points -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Question Type <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        v-model="formData.question_type"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    >
                                        <option
                                            v-for="type in questionTypes"
                                            :key="type.value"
                                            :value="type.value"
                                        >
                                            {{ type.label }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Points <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        v-model.number="formData.points"
                                        type="number"
                                        min="1"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Options (for multiple-choice) -->
                            <div v-if="showOptions" class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Answer Options <span class="text-red-500">*</span>
                                    </label>
                                    <button
                                        type="button"
                                        @click="addOption"
                                        class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                                    >
                                        <Plus :size="16" />
                                        Add Option
                                    </button>
                                </div>

                                <div
                                    v-for="(option, index) in formData.options"
                                    :key="index"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="option.is_correct"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                                        title="Mark as correct"
                                    />
                                    <input
                                        v-model="option.option_text"
                                        type="text"
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                        :placeholder="`Option ${index + 1}`"
                                        required
                                    />
                                    <button
                                        v-if="formData.options.length > 2"
                                        type="button"
                                        @click="removeOption(index)"
                                        class="text-red-600 hover:text-red-700 dark:text-red-400"
                                    >
                                        <Trash2 :size="18" />
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Check the box to mark the correct answer(s)
                                </p>
                            </div>

                            <!-- Correct Answer (for true-false and enumeration) -->
                            <div v-if="showCorrectAnswer">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Correct Answer <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-if="formData.question_type === QUESTION_TYPES.TRUE_FALSE"
                                    v-model="formData.correct_answer"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                                <textarea
                                    v-else-if="formData.question_type === QUESTION_TYPES.ENUMERATION"
                                    v-model="formData.correct_answer"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter the correct answer(s) for enumeration (one per line)"
                                    required
                                />
                                <p v-if="formData.question_type === QUESTION_TYPES.ENUMERATION" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    For multiple answers, enter each on a new line
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    @click="emit('close')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    Add Question
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
