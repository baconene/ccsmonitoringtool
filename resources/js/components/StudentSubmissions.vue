<script setup lang="ts">
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Eye, Clock, CheckCircle2, XCircle, AlertCircle } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

interface StudentSubmission {
    id: number;
    student_id: number;
    student_name: string;
    progress: number; // 0-100
    score: number | null;
    total_score?: number;
    status: 'not_started' | 'in_progress' | 'submitted' | 'graded' | 'late';
    submitted_at?: string;
    graded_at?: string;
}

interface Props {
    submissions: StudentSubmission[];
    activityType: 'assignment' | 'quiz' | 'project';
    activityId: number;
    activityTitle: string;
    courseId?: number;
    loading?: boolean;
}

const props = defineProps<Props>();

// Status configuration
const statusConfig = {
    not_started: {
        label: 'Not Started',
        variant: 'secondary' as const,
        icon: Clock,
        color: 'text-gray-500',
    },
    in_progress: {
        label: 'In Progress',
        variant: 'default' as const,
        icon: AlertCircle,
        color: 'text-blue-500',
    },
    submitted: {
        label: 'Submitted',
        variant: 'default' as const,
        icon: CheckCircle2,
        color: 'text-yellow-500',
    },
    graded: {
        label: 'Graded',
        variant: 'default' as const,
        icon: CheckCircle2,
        color: 'text-green-500',
    },
    late: {
        label: 'Late',
        variant: 'destructive' as const,
        icon: XCircle,
        color: 'text-red-500',
    },
};

// Generate view route based on activity type - using unified submission route
const getViewRoute = (submission: StudentSubmission) => {
    // Use the new unified submission show route
    return `/instructor/submissions/${submission.id}`;
};

// Navigate to submission view
const viewSubmission = (submission: StudentSubmission) => {
    const route = getViewRoute(submission);
    router.visit(route);
};

// Calculate statistics
const statistics = computed(() => {
    const total = props.submissions.length;
    const submitted = props.submissions.filter(s => ['submitted', 'graded'].includes(s.status)).length;
    const graded = props.submissions.filter(s => s.status === 'graded').length;
    const notStarted = props.submissions.filter(s => s.status === 'not_started').length;
    const averageScore = props.submissions
        .filter(s => s.score !== null && s.total_score)
        .reduce((acc, s) => acc + ((s.score! / s.total_score!) * 100), 0) / 
        props.submissions.filter(s => s.score !== null).length || 0;

    return {
        total,
        submitted,
        graded,
        notStarted,
        averageScore: averageScore.toFixed(1),
        submissionRate: total > 0 ? ((submitted / total) * 100).toFixed(1) : '0',
    };
});

// Format date
const formatDate = (dateString?: string) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    }).format(date);
};

// Get progress bar color
const getProgressColor = (progress: number) => {
    if (progress === 0) return 'bg-gray-200';
    if (progress < 50) return 'bg-red-500';
    if (progress < 100) return 'bg-yellow-500';
    return 'bg-green-500';
};
</script>

<template>
    <Card>
        <CardHeader>
            <div class="flex items-center justify-between">
                <div>
                    <CardTitle>Student Submissions</CardTitle>
                    <CardDescription>
                        {{ activityTitle }} - {{ statistics.total }} students enrolled
                    </CardDescription>
                </div>
                <div class="flex gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary">{{ statistics.submitted }}</div>
                        <div class="text-muted-foreground">Submitted</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ statistics.graded }}</div>
                        <div class="text-muted-foreground">Graded</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ statistics.submissionRate }}%</div>
                        <div class="text-muted-foreground">Rate</div>
                    </div>
                    <div v-if="statistics.averageScore !== 'NaN'" class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ statistics.averageScore }}%</div>
                        <div class="text-muted-foreground">Avg Score</div>
                    </div>
                </div>
            </div>
        </CardHeader>
        <CardContent>
            <div v-if="loading" class="flex items-center justify-center py-8">
                <div class="text-muted-foreground">Loading submissions...</div>
            </div>
            
            <div v-else-if="submissions.length === 0" class="flex flex-col items-center justify-center py-8">
                <AlertCircle class="h-12 w-12 text-muted-foreground mb-4" />
                <p class="text-muted-foreground">No students enrolled in this activity</p>
            </div>

            <div v-else class="rounded-md border overflow-hidden">
                <table class="w-full">
                    <thead class="bg-muted/50">
                        <tr class="border-b">
                            <th class="px-4 py-3 text-left text-sm font-medium w-[250px]">Student Name</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-[150px]">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-[200px]">Progress</th>
                            <th class="px-4 py-3 text-center text-sm font-medium w-[120px]">Score</th>
                            <th class="px-4 py-3 text-left text-sm font-medium w-[180px]">Submitted At</th>
                            <th class="px-4 py-3 text-right text-sm font-medium w-[100px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="submission in submissions"
                            :key="submission.id"
                            class="border-b hover:bg-muted/50 cursor-pointer"
                            @click="viewSubmission(submission)"
                        >
                            <!-- Student Name -->
                            <td class="px-4 py-3 font-medium">
                                {{ submission.student_name }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3">
                                <Badge :variant="statusConfig[submission.status].variant">
                                    <component
                                        :is="statusConfig[submission.status].icon"
                                        class="mr-1 h-3 w-3"
                                    />
                                    {{ statusConfig[submission.status].label }}
                                </Badge>
                            </td>

                            <!-- Progress Bar -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                        <div
                                            :class="getProgressColor(submission.progress)"
                                            class="h-full transition-all duration-300"
                                            :style="{ width: `${submission.progress}%` }"
                                        />
                                    </div>
                                    <span class="text-xs text-muted-foreground w-12 text-right">
                                        {{ submission.progress }}%
                                    </span>
                                </div>
                            </td>

                            <!-- Score -->
                            <td class="px-4 py-3 text-center">
                                <template v-if="submission.score !== null && submission.total_score">
                                    <div class="font-semibold">
                                        {{ submission.score }} / {{ submission.total_score }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ ((submission.score / submission.total_score) * 100).toFixed(1) }}%
                                    </div>
                                </template>
                                <template v-else>
                                    <span class="text-muted-foreground">—</span>
                                </template>
                            </td>

                            <!-- Submitted At -->
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ formatDate(submission.submitted_at) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click.stop="viewSubmission(submission)"
                                >
                                    <Eye class="h-4 w-4 mr-1" />
                                    View
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardContent>
    </Card>
</template>
