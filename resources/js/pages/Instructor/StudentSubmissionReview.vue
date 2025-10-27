<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import { 
    ArrowLeft, Save, CheckCircle, XCircle, Clock, FileText,
    User, Calendar, MessageSquare, Download, Check, X
} from 'lucide-vue-next';

interface StudentAnswer {
    id: number;
    question_id: number;
    answer_text?: string;
    selected_options?: number[];
    file_path?: string;
    is_correct?: boolean;
    points_earned?: number;
    instructor_feedback?: string;
}

interface Question {
    id: number;
    question_text: string;
    question_type: 'true_false' | 'multiple_choice' | 'enumeration' | 'short_answer';
    points: number;
    correct_answer?: string;
    acceptable_answers?: string[];
    case_sensitive?: boolean;
    explanation?: string;
    options?: QuestionOption[];
}

interface QuestionOption {
    id: number;
    option_text: string;
    is_correct: boolean;
}

interface Props {
    assignment: any;
    progress: any;
    student: any;
    questions: Question[];
    answers: StudentAnswer[];
}

const props = defineProps<Props>();

// State
const isSaving = ref(false);
const overallFeedback = ref(props.progress.instructor_feedback || '');
const questionGrades = ref<Record<number, { points: number; feedback: string }>>({});

// Initialize question grades from existing data
props.answers.forEach(answer => {
    questionGrades.value[answer.question_id] = {
        points: answer.points_earned || 0,
        feedback: answer.instructor_feedback || '',
    };
});

// Calculate total score
const totalScore = computed(() => {
    return Object.values(questionGrades.value).reduce((sum, grade) => sum + (grade.points || 0), 0);
});

// Get question by ID
const getQuestion = (questionId: number): Question | undefined => {
    return props.questions.find(q => q.id === questionId);
};

// Get answer for question
const getAnswer = (questionId: number): StudentAnswer | undefined => {
    return props.answers.find(a => a.question_id === questionId);
};

// Get question type display
const getQuestionTypeDisplay = (type: string) => {
    const types: Record<string, string> = {
        'true_false': 'True/False',
        'multiple_choice': 'Multiple Choice',
        'enumeration': 'Enumeration',
        'short_answer': 'Short Answer'
    };
    return types[type] || type;
};

// Check if answer is correct (auto-graded questions)
const isAnswerCorrect = (question: Question, answer: StudentAnswer): boolean | null => {
    if (answer.is_correct !== undefined) {
        return answer.is_correct;
    }
    return null;
};

// Format date
const formatDate = (date: string) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Save individual question grade
const saveQuestionGrade = async (questionId: number) => {
    const answer = getAnswer(questionId);
    if (!answer) return;

    isSaving.value = true;
    
    try {
        await router.post(`/instructor/assignments/${props.assignment.id}/grade/${props.progress.id}/question`, {
            answer_id: answer.id,
            points_earned: questionGrades.value[questionId].points,
            instructor_feedback: questionGrades.value[questionId].feedback,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                console.log('Question grade saved');
            }
        });
    } finally {
        isSaving.value = false;
    }
};

// Submit final grade
const submitFinalGrade = async () => {
    if (!confirm('Are you sure you want to submit this grade? The student will be notified.')) {
        return;
    }

    isSaving.value = true;

    router.post(`/instructor/assignments/${props.assignment.id}/grade/${props.progress.id}/submit`, {
        total_score: totalScore.value,
        instructor_feedback: overallFeedback.value,
        question_grades: questionGrades.value,
    }, {
        onSuccess: () => {
            router.visit(`/instructor/assignments/${props.assignment.id}/manage`);
        },
        onFinish: () => {
            isSaving.value = false;
        }
    });
};

// Go back to submissions
const goBack = () => {
    router.visit(`/activities/${props.assignment.activity_id}/manage`);
};
</script>

<template>
    <AppHeaderLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-6">
                <button
                    @click="goBack"
                    class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white mb-4"
                >
                    <ArrowLeft :size="20" />
                    Back to Submissions
                </button>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ assignment.title }} - Review
                        </h1>
                        <span
                            :class="[
                                progress.status === 'graded' 
                                    ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'px-3 py-1 text-sm font-medium rounded-full'
                            ]"
                        >
                            {{ progress.status === 'graded' ? 'Graded' : 'Pending Review' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="flex items-center gap-2 text-sm">
                            <User :size="16" class="text-gray-500" />
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Student</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ student.first_name }} {{ student.last_name }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <Calendar :size="16" class="text-gray-500" />
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Submitted</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ formatDate(progress.submitted_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <CheckCircle :size="16" class="text-gray-500" />
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Total Points</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ assignment.total_points }} points
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <Clock :size="16" class="text-gray-500" />
                            <div>
                                <p class="text-gray-600 dark:text-gray-400">Current Score</p>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ totalScore }} / {{ assignment.total_points }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions and Answers -->
            <div class="space-y-6 mb-6">
                <div
                    v-for="(question, index) in questions"
                    :key="question.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                >
                    <!-- Question Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                                    Question {{ index + 1 }}
                                </span>
                                <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                    {{ getQuestionTypeDisplay(question.question_type) }}
                                </span>
                                <span class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded">
                                    {{ question.points }} points
                                </span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ question.question_text }}
                            </h3>
                        </div>
                    </div>

                    <!-- Student Answer -->
                    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student's Answer:</p>
                        
                        <div v-if="getAnswer(question.id)">
                            <!-- Text Answer -->
                            <div v-if="getAnswer(question.id)?.answer_text" class="flex items-start gap-2">
                                <component 
                                    :is="isAnswerCorrect(question, getAnswer(question.id)!) === true ? CheckCircle : isAnswerCorrect(question, getAnswer(question.id)!) === false ? XCircle : FileText"
                                    :size="20"
                                    :class="[
                                        isAnswerCorrect(question, getAnswer(question.id)!) === true ? 'text-green-600' : 
                                        isAnswerCorrect(question, getAnswer(question.id)!) === false ? 'text-red-600' : 
                                        'text-gray-500'
                                    ]"
                                />
                                <p class="flex-1 text-gray-900 dark:text-white">
                                    {{ getAnswer(question.id)?.answer_text }}
                                </p>
                            </div>

                            <!-- Multiple Choice Options -->
                            <div v-if="question.question_type === 'multiple_choice' && getAnswer(question.id)?.selected_options" class="space-y-2">
                                <div
                                    v-for="option in question.options"
                                    :key="option.id"
                                    :class="[
                                        'flex items-center gap-3 p-3 rounded-lg border-2',
                                        getAnswer(question.id)?.selected_options?.includes(option.id)
                                            ? option.is_correct
                                                ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                                : 'border-red-500 bg-red-50 dark:bg-red-900/20'
                                            : option.is_correct
                                                ? 'border-green-300 bg-green-50 dark:bg-green-900/10'
                                                : 'border-gray-200 dark:border-gray-700'
                                    ]"
                                >
                                    <component
                                        :is="getAnswer(question.id)?.selected_options?.includes(option.id) ? (option.is_correct ? Check : X) : (option.is_correct ? Check : '')"
                                        :size="20"
                                        :class="[
                                            getAnswer(question.id)?.selected_options?.includes(option.id)
                                                ? option.is_correct ? 'text-green-600' : 'text-red-600'
                                                : option.is_correct ? 'text-green-600' : ''
                                        ]"
                                    />
                                    <span class="text-gray-900 dark:text-white">{{ option.option_text }}</span>
                                    <span v-if="option.is_correct" class="ml-auto text-xs text-green-600 dark:text-green-400 font-medium">
                                        Correct Answer
                                    </span>
                                    <span v-else-if="getAnswer(question.id)?.selected_options?.includes(option.id)" class="ml-auto text-xs text-red-600 dark:text-red-400 font-medium">
                                        Student Selected
                                    </span>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div v-if="getAnswer(question.id)?.file_path" class="flex items-center gap-2">
                                <FileText :size="20" class="text-gray-500" />
                                <a 
                                    :href="`/storage/${getAnswer(question.id)?.file_path}`"
                                    target="_blank"
                                    class="text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    View Uploaded File
                                </a>
                                <Download :size="16" class="text-gray-400" />
                            </div>
                        </div>
                        <p v-else class="text-gray-400 italic">No answer provided</p>
                    </div>

                    <!-- Correct Answer (if available) -->
                    <div v-if="question.correct_answer && isAnswerCorrect(question, getAnswer(question.id)!) === false" class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-900/50">
                        <p class="text-sm font-medium text-green-700 dark:text-green-300 mb-2">Correct Answer:</p>
                        <p class="text-gray-900 dark:text-white">{{ question.correct_answer }}</p>
                    </div>

                    <!-- Explanation -->
                    <div v-if="question.explanation" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-900/50">
                        <p class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">Explanation:</p>
                        <p class="text-gray-900 dark:text-white">{{ question.explanation }}</p>
                    </div>

                    <!-- Grading Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Grading</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Points Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Points Earned (Max: {{ question.points }})
                                </label>
                                <input
                                    v-model.number="questionGrades[question.id].points"
                                    type="number"
                                    :min="0"
                                    :max="question.points"
                                    step="0.5"
                                    @blur="saveQuestionGrade(question.id)"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                            </div>

                            <!-- Feedback Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Feedback for Student
                                </label>
                                <textarea
                                    v-model="questionGrades[question.id].feedback"
                                    @blur="saveQuestionGrade(question.id)"
                                    rows="2"
                                    placeholder="Optional feedback for this question..."
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Feedback and Submit -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overall Feedback</h3>
                <textarea
                    v-model="overallFeedback"
                    rows="4"
                    placeholder="Provide overall feedback for the student..."
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none mb-4"
                ></textarea>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>Total Score: <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ totalScore }}</span> / {{ assignment.total_points }}</p>
                        <p>Percentage: <span class="font-semibold">{{ Math.round((totalScore / assignment.total_points) * 100) }}%</span></p>
                    </div>

                    <button
                        @click="submitFinalGrade"
                        :disabled="isSaving"
                        class="flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Save :size="20" />
                        {{ progress.status === 'graded' ? 'Update Grade' : 'Submit Grade' }}
                    </button>
                </div>
            </div>
        </div>
    </AppHeaderLayout>
</template>
