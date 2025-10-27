<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { 
    ArrowLeft, CheckCircle2, XCircle, Clock, AlertCircle, 
    FileText, User, Calendar, Award, Edit, Save, X 
} from 'lucide-vue-next';

interface Question {
    id: number;
    question_text: string;
    question_type: 'true_false' | 'multiple_choice' | 'enumeration' | 'short_answer';
    points: number;
    student_answer?: string;
    student_answers?: string[];
    correct_answer?: string;
    correct_answers?: string[];
    is_correct?: boolean;
    earned_points?: number;
    feedback?: string;
}

interface Props {
    activity: {
        id: number;
        title: string;
        description?: string;
        activity_type: string;
    };
    submission: {
        id: number;
        student_id: number;
        student: {
            id: number;
            name: string;
            email: string;
        };
        status: 'in_progress' | 'submitted' | 'graded';
        progress: number;
        score: number | null;
        total_score: number;
        submitted_at?: string;
        graded_at?: string;
        answers: Question[];
    };
    activityType: 'assignment' | 'quiz' | 'project';
}

const props = defineProps<Props>();

// State
const isGrading = ref(false);
const gradingData = ref<Record<number, { earned_points: number; feedback: string }>>({});

// Initialize grading data
props.submission.answers.forEach(answer => {
    gradingData.value[answer.id] = {
        earned_points: answer.earned_points || 0,
        feedback: answer.feedback || '',
    };
});

// Computed
const totalEarnedPoints = computed(() => {
    return Object.values(gradingData.value).reduce((sum, item) => sum + item.earned_points, 0);
});

const canGrade = computed(() => {
    return props.submission.status === 'submitted';
});

const statusConfig = computed(() => {
    const configs = {
        in_progress: {
            label: 'In Progress',
            variant: 'default' as const,
            icon: Clock,
            color: 'text-blue-600 dark:text-blue-400',
        },
        submitted: {
            label: 'Submitted - Awaiting Grade',
            variant: 'default' as const,
            icon: AlertCircle,
            color: 'text-yellow-600 dark:text-yellow-400',
        },
        graded: {
            label: 'Graded',
            variant: 'default' as const,
            icon: CheckCircle2,
            color: 'text-green-600 dark:text-green-400',
        },
    };
    return configs[props.submission.status];
});

// Methods
const goBack = () => {
    router.visit(`/activities/${props.activity.id}`);
};

const startGrading = () => {
    isGrading.value = true;
};

const cancelGrading = () => {
    isGrading.value = false;
    // Reset to original values
    props.submission.answers.forEach(answer => {
        gradingData.value[answer.id] = {
            earned_points: answer.earned_points || 0,
            feedback: answer.feedback || '',
        };
    });
};

const submitGrade = () => {
    const gradeData = {
        grades: Object.entries(gradingData.value).map(([questionId, data]) => ({
            question_id: parseInt(questionId),
            earned_points: data.earned_points,
            feedback: data.feedback,
        })),
        total_score: totalEarnedPoints.value,
    };

    router.post(`/instructor/submissions/${props.submission.id}/grade`, gradeData, {
        onSuccess: () => {
            isGrading.value = false;
        },
    });
};

const formatDate = (dateString?: string) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    }).format(date);
};

const getQuestionIcon = (answer: Question) => {
    if (answer.is_correct === true) return CheckCircle2;
    if (answer.is_correct === false) return XCircle;
    return AlertCircle;
};

const getQuestionColor = (answer: Question) => {
    if (answer.is_correct === true) return 'text-green-600 dark:text-green-400';
    if (answer.is_correct === false) return 'text-red-600 dark:text-red-400';
    return 'text-yellow-600 dark:text-yellow-400';
};
</script>

<template>
    <AppLayout title="Student Submission">
        <div class="container max-w-6xl mx-auto py-8 px-4 space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="sm" @click="goBack">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Activity
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ activity.title }}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            Student Submission Review
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Badge :variant="statusConfig.variant" class="text-sm">
                        <component :is="statusConfig.icon" class="h-4 w-4 mr-1" />
                        {{ statusConfig.label }}
                    </Badge>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Student Info Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <User class="h-5 w-5" />
                                Student Information
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Name</label>
                                    <p class="text-lg font-semibold">{{ submission.student.name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Email</label>
                                    <p class="text-lg">{{ submission.student.email }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-muted-foreground">Submitted At</label>
                                    <p class="text-lg flex items-center gap-2">
                                        <Calendar class="h-4 w-4" />
                                        {{ formatDate(submission.submitted_at) }}
                                    </p>
                                </div>
                                <div v-if="submission.graded_at">
                                    <label class="text-sm font-medium text-muted-foreground">Graded At</label>
                                    <p class="text-lg flex items-center gap-2">
                                        <Calendar class="h-4 w-4" />
                                        {{ formatDate(submission.graded_at) }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Answers -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle class="flex items-center gap-2">
                                    <FileText class="h-5 w-5" />
                                    Student Answers
                                </CardTitle>
                                <div v-if="canGrade && !isGrading">
                                    <Button @click="startGrading" variant="default">
                                        <Edit class="h-4 w-4 mr-2" />
                                        Start Grading
                                    </Button>
                                </div>
                                <div v-if="isGrading" class="flex gap-2">
                                    <Button @click="submitGrade" variant="default">
                                        <Save class="h-4 w-4 mr-2" />
                                        Submit Grade
                                    </Button>
                                    <Button @click="cancelGrading" variant="outline">
                                        <X class="h-4 w-4 mr-2" />
                                        Cancel
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div
                                v-for="(answer, index) in submission.answers"
                                :key="answer.id"
                                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-3"
                            >
                                <!-- Question -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                                {{ index + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                            {{ answer.question_text }}
                                        </p>
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                            <span>Type: {{ answer.question_type.replace('_', ' ') }}</span>
                                            <span>•</span>
                                            <span>Points: {{ answer.points }}</span>
                                        </div>
                                    </div>
                                    <component
                                        :is="getQuestionIcon(answer)"
                                        :class="[getQuestionColor(answer), 'h-5 w-5']"
                                    />
                                </div>

                                <!-- Student Answer -->
                                <div class="ml-11 space-y-2">
                                    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded">
                                        <label class="text-xs font-medium text-muted-foreground">Student's Answer:</label>
                                        <p class="text-sm mt-1">
                                            {{ answer.student_answer || answer.student_answers?.join(', ') || 'No answer provided' }}
                                        </p>
                                    </div>

                                    <!-- Grading Section -->
                                    <div v-if="isGrading" class="space-y-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-medium text-muted-foreground">Points Earned</label>
                                                <input
                                                    v-model.number="gradingData[answer.id].earned_points"
                                                    type="number"
                                                    :max="answer.points"
                                                    min="0"
                                                    step="0.5"
                                                    class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                                                />
                                            </div>
                                            <div class="flex items-end">
                                                <span class="text-sm text-muted-foreground">out of {{ answer.points }} points</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium text-muted-foreground">Feedback (Optional)</label>
                                            <textarea
                                                v-model="gradingData[answer.id].feedback"
                                                rows="2"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                                                placeholder="Provide feedback for the student..."
                                            ></textarea>
                                        </div>
                                    </div>

                                    <!-- Display Grade (if graded and not in grading mode) -->
                                    <div v-else-if="answer.earned_points !== undefined" class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium">
                                                Earned: {{ answer.earned_points }} / {{ answer.points }} points
                                            </span>
                                        </div>
                                        <p v-if="answer.feedback" class="text-sm text-muted-foreground mt-1">
                                            <span class="font-medium">Feedback:</span> {{ answer.feedback }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Score Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Award class="h-5 w-5" />
                                Score Summary
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="text-center py-6">
                                <div class="text-5xl font-bold text-primary">
                                    {{ isGrading ? totalEarnedPoints : (submission.score || 0) }}
                                </div>
                                <div class="text-muted-foreground mt-2">
                                    out of {{ submission.total_score }} points
                                </div>
                                <div class="mt-4">
                                    <div class="text-2xl font-semibold">
                                        {{ Math.round(((isGrading ? totalEarnedPoints : (submission.score || 0)) / submission.total_score) * 100) }}%
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Progress</span>
                                    <span class="font-medium">{{ submission.progress }}%</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Total Questions</span>
                                    <span class="font-medium">{{ submission.answers.length }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted-foreground">Answered</span>
                                    <span class="font-medium">
                                        {{ submission.answers.filter(a => a.student_answer || a.student_answers?.length).length }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Activity Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Activity Details</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <label class="text-muted-foreground">Activity Type</label>
                                <p class="font-medium capitalize">{{ activityType }}</p>
                            </div>
                            <div v-if="activity.description">
                                <label class="text-muted-foreground">Description</label>
                                <p class="text-sm">{{ activity.description }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
