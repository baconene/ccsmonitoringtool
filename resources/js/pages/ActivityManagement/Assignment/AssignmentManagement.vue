<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { 
    Calendar, FileText, Upload, Plus, Trash2, Save, X, 
    CheckCircle, Clock, AlertCircle, Eye, Edit3, Users,
    ChevronDown, ChevronUp, Settings, GripVertical, Filter,
    Search, ArrowUpDown, CircleAlert
} from 'lucide-vue-next';
import StudentSubmissions from '@/components/StudentSubmissions.vue';
import { useStudentSubmissions } from '@/composables/useStudentSubmissions';
import type { StudentSubmission } from '@/composables/useStudentSubmissions';

interface Question {
    id?: number;
    question_text: string;
    question_type: 'true_false' | 'multiple_choice' | 'enumeration' | 'short_answer';
    points: number;
    correct_answer?: string;
    acceptable_answers?: string[];
    case_sensitive?: boolean;
    explanation?: string;
    order?: number;
    options?: QuestionOption[];
}

interface QuestionOption {
    id?: number;
    option_text: string;
    is_correct: boolean;
    order?: number;
}

interface Props {
    activity: any;
    assignment?: any;
    studentsProgress?: any[];
}

const props = defineProps<Props>();

// Student Submissions Composable
const { submissions, loading, fetchAssignmentSubmissions } = useStudentSubmissions();

// Tab state
const activeTab = ref<'assignment' | 'submissions'>('assignment');

// Load submissions when the submissions tab is activated
const handleTabChange = async (tab: 'assignment' | 'submissions') => {
    activeTab.value = tab;
    if (tab === 'submissions' && props.assignment?.id && submissions.value.length === 0) {
        await fetchAssignmentSubmissions(props.assignment.id);
    }
};

// State
const showCreateForm = ref(!props.assignment);
const showEditMode = ref(false);
const showStudentProgress = ref(true);

// Submissions filter and sort
const submissionsFilter = ref<'all' | 'not_started' | 'in_progress' | 'submitted' | 'graded'>('all');
const submissionsSearchQuery = ref('');
const submissionsSortBy = ref<'name' | 'status' | 'score' | 'date'>('date');
const submissionsSortOrder = ref<'asc' | 'desc'>('desc');

// Form data for creating new assignment
const formData = ref({
    title: props.assignment?.title || props.activity.title,
    description: props.assignment?.description || props.activity.description,
    instructions: props.assignment?.instructions || '',
    assignment_type: props.assignment?.assignment_type || 'objective',
    total_points: props.assignment?.total_points || 100,
    time_limit: props.assignment?.time_limit || null,
    allow_late_submission: props.assignment?.allow_late_submission || false,
    questions: props.assignment?.questions || [] as Question[],
});

// Deleted question IDs (for updates)
const deletedQuestionIds = ref<number[]>([]);

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

const handleCreateAssignment = () => {
    console.log('Creating assignment with data:', {
        activity_id: props.activity.id,
        ...formData.value,
    });
    
    router.post('/assignments', {
        activity_id: props.activity.id,
        ...formData.value,
    }, {
        onSuccess: () => {
            console.log('Assignment created successfully');
            showCreateForm.value = false;
        },
        onError: (errors) => {
            console.error('Assignment creation failed:', errors);
        }
    });
};

const handleUpdateAssignment = () => {
    router.put(`/assignments/${props.assignment.id}`, {
        ...formData.value,
        deleted_question_ids: deletedQuestionIds.value,
    }, {
        onSuccess: () => {
            showEditMode.value = false;
            deletedQuestionIds.value = [];
        }
    });
};

const addQuestion = () => {
    formData.value.questions.push({
        question_text: '',
        question_type: 'multiple_choice',
        points: 10,
        options: [
            { option_text: '', is_correct: false, order: 1 },
            { option_text: '', is_correct: false, order: 2 },
        ]
    });
};

const removeQuestion = (index: number) => {
    const question = formData.value.questions[index];
    if (question.id) {
        deletedQuestionIds.value.push(question.id);
    }
    formData.value.questions.splice(index, 1);
};

const addOption = (questionIndex: number) => {
    const question = formData.value.questions[questionIndex];
    if (!question.options) {
        question.options = [];
    }
    question.options.push({
        option_text: '',
        is_correct: false,
        order: question.options.length + 1
    });
};

const removeOption = (questionIndex: number, optionIndex: number) => {
    formData.value.questions[questionIndex].options?.splice(optionIndex, 1);
};

const getQuestionTypeDisplay = (type: string) => {
    const types: Record<string, string> = {
        'true_false': 'True/False',
        'multiple_choice': 'Multiple Choice',
        'enumeration': 'Enumeration',
        'short_answer': 'Short Answer'
    };
    return types[type] || type;
};

const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
        'not_started': 'text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300',
        'in_progress': 'text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400',
        'submitted': 'text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400',
        'graded': 'text-purple-600 bg-purple-100 dark:bg-purple-900/30 dark:text-purple-400',
    };
    return colors[status] || colors['not_started'];
};

const viewGrading = () => {
    if (props.assignment?.id) {
        router.visit(`/assignments/${props.assignment.id}/grading`);
    }
};

const calculateTotalPoints = computed(() => {
    return formData.value.questions.reduce((sum: number, q: Question) => sum + (q.points || 0), 0);
});

// Filtered and sorted submissions
const filteredSubmissions = computed(() => {
    if (!props.studentsProgress) return [];
    
    let filtered = props.studentsProgress;
    
    // Apply status filter
    if (submissionsFilter.value !== 'all') {
        filtered = filtered.filter(s => s.status === submissionsFilter.value);
    }
    
    // Apply search filter
    if (submissionsSearchQuery.value) {
        const query = submissionsSearchQuery.value.toLowerCase();
        filtered = filtered.filter(s => {
            const studentName = `${s.student?.first_name || ''} ${s.student?.last_name || ''}`.toLowerCase();
            const studentEmail = (s.student?.email || '').toLowerCase();
            return studentName.includes(query) || studentEmail.includes(query);
        });
    }
    
    // Apply sorting
    const sorted = [...filtered].sort((a, b) => {
        let comparison = 0;
        
        switch (submissionsSortBy.value) {
            case 'name':
                const nameA = `${a.student?.first_name || ''} ${a.student?.last_name || ''}`;
                const nameB = `${b.student?.first_name || ''} ${b.student?.last_name || ''}`;
                comparison = nameA.localeCompare(nameB);
                break;
            case 'status':
                const statusOrder = ['not_started', 'in_progress', 'submitted', 'graded'];
                comparison = statusOrder.indexOf(a.status) - statusOrder.indexOf(b.status);
                break;
            case 'score':
                comparison = (a.score || 0) - (b.score || 0);
                break;
            case 'date':
                const dateA = a.submitted_at ? new Date(a.submitted_at).getTime() : 0;
                const dateB = b.submitted_at ? new Date(b.submitted_at).getTime() : 0;
                comparison = dateA - dateB;
                break;
        }
        
        return submissionsSortOrder.value === 'asc' ? comparison : -comparison;
    });
    
    return sorted;
});

// Submission statistics
const submissionStats = computed(() => {
    if (!props.studentsProgress) return {
        total: 0,
        notStarted: 0,
        inProgress: 0,
        submitted: 0,
        graded: 0,
        needsGrading: 0,
    };
    
    return {
        total: props.studentsProgress.length,
        notStarted: props.studentsProgress.filter(s => s.status === 'not_started').length,
        inProgress: props.studentsProgress.filter(s => s.status === 'in_progress').length,
        submitted: props.studentsProgress.filter(s => s.status === 'submitted').length,
        graded: props.studentsProgress.filter(s => s.status === 'graded').length,
        needsGrading: props.studentsProgress.filter(s => s.status === 'submitted').length,
    };
});

// Toggle sort
const toggleSort = (field: 'name' | 'status' | 'score' | 'date') => {
    if (submissionsSortBy.value === field) {
        submissionsSortOrder.value = submissionsSortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        submissionsSortBy.value = field;
        submissionsSortOrder.value = 'desc';
    }
};

// View student submission
const viewStudentSubmission = (progressId: number, assignmentId: number) => {
    router.visit(`/instructor/assignments/${assignmentId}/submissions/${progressId}`);
};
</script>

<template>
    <div class="space-y-6">
        <!-- Tabs Navigation (only show if assignment exists) -->
        <div v-if="assignment" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button
                        @click="handleTabChange('assignment')"
                        :class="[
                            activeTab === 'assignment'
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm transition-colors'
                        ]"
                    >
                        <Settings :size="18" />
                        Assignment Details
                    </button>
                    <button
                        @click="handleTabChange('submissions')"
                        :class="[
                            activeTab === 'submissions'
                                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm transition-colors'
                        ]"
                    >
                        <Users :size="18" />
                        Student Submissions
                        <span
                            v-if="submissionStats.needsGrading > 0"
                            class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
                        >
                            {{ submissionStats.needsGrading }}
                        </span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab: Assignment Details -->
        <div v-if="!assignment || activeTab === 'assignment'">
            <!-- Assignment Overview (if exists) -->
            <div v-if="assignment && !showEditMode" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ assignment.title }}</h3>
                <button
                    @click="showEditMode = true"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <Edit3 :size="16" />
                    Edit Assignment
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="flex items-center gap-2 text-sm">
                    <FileText :size="16" class="text-gray-500" />
                    <span class="text-gray-600 dark:text-gray-400">Type:</span>
                    <span class="font-medium text-gray-900 dark:text-white capitalize">
                        {{ assignment.assignment_type?.replace('_', ' ') }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <CheckCircle :size="16" class="text-gray-500" />
                    <span class="text-gray-600 dark:text-gray-400">Total Points:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ assignment.total_points }}</span>
                </div>
                <div v-if="assignment.time_limit" class="flex items-center gap-2 text-sm">
                    <Clock :size="16" class="text-gray-500" />
                    <span class="text-gray-600 dark:text-gray-400">Time Limit:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ assignment.time_limit }} minutes</span>
                </div>
            </div>

            <div v-if="assignment.instructions" class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ assignment.instructions }}</p>
            </div>

            <!-- Questions Preview -->
            <div v-if="assignment.questions?.length" class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                    Questions ({{ assignment.questions.length }})
                </h4>
                <div class="space-y-2">
                    <div
                        v-for="(question, index) in assignment.questions"
                        :key="question.id"
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                    >
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ index + 1 }}. {{ question.question_text }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ getQuestionTypeDisplay(question.question_type) }} • {{ question.points }} pts
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Assignment Form -->
        <div v-if="showCreateForm || showEditMode" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ showEditMode ? 'Edit Assignment' : 'Create Assignment' }}
                </h3>
                <button
                    v-if="showEditMode"
                    @click="showEditMode = false"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    <X :size="20" />
                </button>
            </div>

            <form @submit.prevent="showEditMode ? handleUpdateAssignment() : handleCreateAssignment()" class="space-y-6">
                <!-- Basic Info -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Assignment Title
                    </label>
                    <input
                        v-model="formData.title"
                        type="text"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea
                        v-model="formData.description"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                </div>

                <!-- Assignment Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Assignment Type
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors"
                            :class="formData.assignment_type === 'objective' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                        >
                            <input
                                type="radio"
                                v-model="formData.assignment_type"
                                value="objective"
                                class="mr-3"
                            />
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Objective</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Questions only (auto-graded)</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors"
                            :class="formData.assignment_type === 'file_upload' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                        >
                            <input
                                type="radio"
                                v-model="formData.assignment_type"
                                value="file_upload"
                                class="mr-3"
                            />
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">File Upload</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Document submission only</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors"
                            :class="formData.assignment_type === 'mixed' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                        >
                            <input
                                type="radio"
                                v-model="formData.assignment_type"
                                value="mixed"
                                class="mr-3"
                            />
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Mixed</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Questions + file upload</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Hint for Objective/Mixed Types -->
                <div v-if="formData.assignment_type === 'objective' || formData.assignment_type === 'mixed'" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <CircleAlert :size="20" class="text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                        <div>
                            <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Add Questions Below</p>
                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                After filling in the basic details and instructions, scroll down to add questions to your assignment.
                                You'll find the "Add Question" button in the Questions section.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Instructions
                    </label>
                    <textarea
                        v-model="formData.instructions"
                        rows="3"
                        placeholder="Provide detailed instructions for students..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                </div>

                <!-- Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Time Limit (minutes)
                        </label>
                        <input
                            v-model.number="formData.time_limit"
                            type="number"
                            min="0"
                            placeholder="No time limit"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="formData.allow_late_submission"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allow late submissions</span>
                        </label>
                    </div>
                </div>

                <!-- Question Builder -->
                <div v-if="formData.assignment_type !== 'file_upload'" class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Questions</h4>
                        <button
                            type="button"
                            @click="addQuestion"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                        >
                            <Plus :size="16" />
                            Add Question
                        </button>
                    </div>

                    <div v-if="formData.questions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No questions added yet. Click "Add Question" to get started.
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(question, qIndex) in formData.questions"
                            :key="qIndex"
                            class="p-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50"
                        >
                            <div class="flex items-start gap-3 mb-4">
                                <GripVertical :size="20" class="text-gray-400 mt-2 cursor-move" />
                                <div class="flex-1 space-y-4">
                                    <!-- Question Header -->
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Question {{ qIndex + 1 }}
                                            </label>
                                            <textarea
                                                v-model="question.question_text"
                                                rows="2"
                                                placeholder="Enter your question..."
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                                required
                                            ></textarea>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeQuestion(qIndex)"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            <Trash2 :size="18" />
                                        </button>
                                    </div>

                                    <!-- Question Type & Points -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Question Type
                                            </label>
                                            <select
                                                v-model="question.question_type"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                            >
                                                <option value="true_false">True/False</option>
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="enumeration">Enumeration</option>
                                                <option value="short_answer">Short Answer</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Points
                                            </label>
                                            <input
                                                v-model.number="question.points"
                                                type="number"
                                                min="1"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                                required
                                            />
                                        </div>
                                    </div>

                                    <!-- True/False Answer -->
                                    <div v-if="question.question_type === 'true_false'">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Correct Answer
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center cursor-pointer">
                                                <input
                                                    type="radio"
                                                    v-model="question.correct_answer"
                                                    value="true"
                                                    class="mr-2"
                                                />
                                                <span class="text-sm text-gray-700 dark:text-gray-300">True</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input
                                                    type="radio"
                                                    v-model="question.correct_answer"
                                                    value="false"
                                                    class="mr-2"
                                                />
                                                <span class="text-sm text-gray-700 dark:text-gray-300">False</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Multiple Choice Options -->
                                    <div v-if="question.question_type === 'multiple_choice'">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Answer Options
                                            </label>
                                            <button
                                                type="button"
                                                @click="addOption(qIndex)"
                                                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                                            >
                                                + Add Option
                                            </button>
                                        </div>
                                        <div class="space-y-2">
                                            <div
                                                v-for="(option, oIndex) in question.options"
                                                :key="oIndex"
                                                class="flex items-center gap-2"
                                            >
                                                <input
                                                    type="checkbox"
                                                    v-model="option.is_correct"
                                                    class="w-4 h-4"
                                                    title="Mark as correct"
                                                />
                                                <input
                                                    v-model="option.option_text"
                                                    type="text"
                                                    placeholder="Option text..."
                                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                                    required
                                                />
                                                <button
                                                    type="button"
                                                    @click="removeOption(qIndex, oIndex)"
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    <X :size="16" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enumeration Answers -->
                                    <div v-if="question.question_type === 'enumeration'">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Acceptable Answers (comma-separated)
                                        </label>
                                        <input
                                            :value="question.acceptable_answers?.join(', ')"
                                            @input="question.acceptable_answers = ($event.target as HTMLInputElement).value.split(',').map(a => a.trim())"
                                            type="text"
                                            placeholder="answer1, answer2, answer3"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                        />
                                        <label class="flex items-center mt-2 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                v-model="question.case_sensitive"
                                                class="w-4 h-4 mr-2"
                                            />
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Case sensitive</span>
                                        </label>
                                    </div>

                                    <!-- Short Answer -->
                                    <div v-if="question.question_type === 'short_answer'">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Expected Answer (optional - for instructor reference)
                                        </label>
                                        <textarea
                                            v-model="question.correct_answer"
                                            rows="2"
                                            placeholder="Expected answer or grading rubric..."
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                        ></textarea>
                                    </div>

                                    <!-- Explanation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Explanation (shown after submission)
                                        </label>
                                        <textarea
                                            v-model="question.explanation"
                                            rows="2"
                                            placeholder="Optional explanation for students..."
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Points Summary -->
                    <div v-if="formData.questions.length > 0" class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300">
                            Total Points: {{ calculateTotalPoints }} points from {{ formData.questions.length }} questions
                        </p>
                    </div>
                </div>

                <!-- File Upload Instructions (if applicable) -->
                <div v-if="formData.assignment_type === 'file_upload' || formData.assignment_type === 'mixed'" 
                     class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">File Upload Requirements</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Students will be able to upload files (PDF, DOCX, DOC, TXT, JPG, PNG). Maximum file size: 10MB.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        v-if="showEditMode"
                        type="button"
                        @click="showEditMode = false"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <Save :size="16" />
                        {{ showEditMode ? 'Save Changes' : 'Create Assignment' }}
                    </button>
                </div>
            </form>
        </div>

            <!-- Create Assignment Button (if no assignment exists) -->
            <div v-if="!assignment && !showCreateForm" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <FileText :size="48" class="mx-auto text-gray-400 mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Assignment Created</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Create a dynamic assignment with questions and/or file uploads</p>
                <button
                    @click="showCreateForm = true"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <Plus :size="20" />
                    Create Assignment
                </button>
            </div>
        </div>

        <!-- Tab: Student Submissions -->
        <div v-if="assignment && activeTab === 'submissions'" class="space-y-6">
            <StudentSubmissions
                :submissions="submissions"
                activity-type="assignment"
                :activity-id="assignment.id"
                :activity-title="assignment.title || activity.title"
                :course-id="activity.course_id"
                :loading="loading"
            />
        </div>
    </div>
</template>
