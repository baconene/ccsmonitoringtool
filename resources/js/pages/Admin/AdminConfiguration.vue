<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { Textarea } from '@/components/ui/textarea';
import { AlertCircle, CheckCircle2, Plus, Trash2, Edit2, Save, X } from 'lucide-vue-next';

interface GradeLevel {
    id: number;
    name: string;
    display_name: string;
    level: number;
    is_active: boolean;
}

interface ActivityType {
    id: number;
    name: string;
    description: string | null;
    model: string | null;
}

interface QuestionType {
    id: number;
    type: string;
    description: string | null;
}

interface Props {
    gradeLevels: GradeLevel[];
    activityTypes: ActivityType[];
    questionTypes: QuestionType[];
}

const props = defineProps<Props>();

// Active tab
const activeTab = ref<'grades' | 'activities' | 'questions'>('grades');

// ===================== GRADE LEVELS =====================

const gradeLevelForm = ref({
    name: '',
    display_name: '',
    level: '',
    is_active: true,
});

const editingGradeLevel = ref<number | null>(null);
const gradeLevelErrors = ref<Record<string, string>>({});

const submitGradeLevel = () => {
    const data = {
        ...gradeLevelForm.value,
        level: parseInt(gradeLevelForm.value.level),
    };

    const url = editingGradeLevel.value
        ? `/admin/grade-levels/${editingGradeLevel.value}`
        : '/admin/grade-levels';

    const method = editingGradeLevel.value ? 'put' : 'post';

    router[method](url, data, {
        onError: (errors) => {
            gradeLevelErrors.value = errors;
        },
        onSuccess: () => {
            resetGradeLevelForm();
        },
    });
};

const editGradeLevel = (gradeLevel: GradeLevel) => {
    editingGradeLevel.value = gradeLevel.id;
    gradeLevelForm.value = { ...gradeLevel, level: gradeLevel.level.toString() };
};

const deleteGradeLevel = (id: number) => {
    if (confirm('Are you sure you want to delete this grade level?')) {
        router.delete(`/admin/grade-levels/${id}`);
    }
};

const resetGradeLevelForm = () => {
    gradeLevelForm.value = { name: '', display_name: '', level: '', is_active: true };
    editingGradeLevel.value = null;
    gradeLevelErrors.value = {};
};

// ===================== ACTIVITY TYPES =====================

const activityTypeForm = ref({
    name: '',
    description: '',
    model: '',
});

const editingActivityType = ref<number | null>(null);
const activityTypeErrors = ref<Record<string, string>>({});

const submitActivityType = () => {
    const url = editingActivityType.value
        ? `/admin/activity-types/${editingActivityType.value}`
        : '/admin/activity-types';

    const method = editingActivityType.value ? 'put' : 'post';

    router[method](url, activityTypeForm.value, {
        onError: (errors) => {
            activityTypeErrors.value = errors;
        },
        onSuccess: () => {
            resetActivityTypeForm();
        },
    });
};

const editActivityType = (activityType: ActivityType) => {
    editingActivityType.value = activityType.id;
    activityTypeForm.value = {
        name: activityType.name,
        description: activityType.description || '',
        model: activityType.model || '',
    };
};

const deleteActivityType = (id: number) => {
    if (confirm('Are you sure you want to delete this activity type?')) {
        router.delete(`/admin/activity-types/${id}`);
    }
};

const resetActivityTypeForm = () => {
    activityTypeForm.value = { name: '', description: '', model: '' };
    editingActivityType.value = null;
    activityTypeErrors.value = {};
};

// ===================== QUESTION TYPES =====================

const questionTypeForm = ref({
    type: '',
    description: '',
});

const editingQuestionType = ref<number | null>(null);
const questionTypeErrors = ref<Record<string, string>>({});

const submitQuestionType = () => {
    const url = editingQuestionType.value
        ? `/admin/question-types/${editingQuestionType.value}`
        : '/admin/question-types';

    const method = editingQuestionType.value ? 'put' : 'post';

    router[method](url, questionTypeForm.value, {
        onError: (errors) => {
            questionTypeErrors.value = errors;
        },
        onSuccess: () => {
            resetQuestionTypeForm();
        },
    });
};

const editQuestionType = (questionType: QuestionType) => {
    editingQuestionType.value = questionType.id;
    questionTypeForm.value = {
        type: questionType.type,
        description: questionType.description || '',
    };
};

const deleteQuestionType = (id: number) => {
    if (confirm('Are you sure you want to delete this question type?')) {
        router.delete(`/admin/question-types/${id}`);
    }
};

const resetQuestionTypeForm = () => {
    questionTypeForm.value = { type: '', description: '' };
    editingQuestionType.value = null;
    questionTypeErrors.value = {};
};
</script>

<template>
    <Head title="Admin Configuration" />
    <AppLayout>
        <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Admin Configuration</h1>
                    <p class="mt-2 text-gray-600">Manage system configurations including grade levels, activity types, and question types</p>
                </div>

                <!-- Tabs Navigation -->
                <div class="flex gap-3 mb-6 border-b border-gray-200">
                    <button
                        @click="activeTab = 'grades'"
                        :class="[
                            'px-4 py-2 font-medium border-b-2 transition-colors',
                            activeTab === 'grades'
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        Grade Levels
                    </button>
                    <button
                        @click="activeTab = 'activities'"
                        :class="[
                            'px-4 py-2 font-medium border-b-2 transition-colors',
                            activeTab === 'activities'
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        Activity Types
                    </button>
                    <button
                        @click="activeTab = 'questions'"
                        :class="[
                            'px-4 py-2 font-medium border-b-2 transition-colors',
                            activeTab === 'questions'
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        Question Types
                    </button>
                </div>

                <!-- ===================== GRADE LEVELS TAB ===================== -->
                <div v-show="activeTab === 'grades'" class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ editingGradeLevel ? 'Edit Grade Level' : 'Add New Grade Level' }}</CardTitle>
                            <CardDescription>
                                {{ editingGradeLevel ? 'Update the grade level details' : 'Create a new grade level for courses' }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submitGradeLevel" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label for="grade-name">Name *</Label>
                                        <Input
                                            id="grade-name"
                                            v-model="gradeLevelForm.name"
                                            placeholder="e.g., Year 1, Grade 1"
                                            class="mt-1"
                                        />
                                        <span v-if="gradeLevelErrors.name" class="text-red-500 text-xs">{{ gradeLevelErrors.name[0] }}</span>
                                    </div>
                                    <div>
                                        <Label for="grade-display-name">Display Name *</Label>
                                        <Input
                                            id="grade-display-name"
                                            v-model="gradeLevelForm.display_name"
                                            placeholder="e.g., First Year, Grade 1"
                                            class="mt-1"
                                        />
                                        <span v-if="gradeLevelErrors.display_name" class="text-red-500 text-xs">{{ gradeLevelErrors.display_name[0] }}</span>
                                    </div>
                                    <div>
                                        <Label for="grade-level">Level (Number) *</Label>
                                        <Input
                                            id="grade-level"
                                            v-model="gradeLevelForm.level"
                                            type="number"
                                            placeholder="e.g., 1, 2, 3"
                                            class="mt-1"
                                        />
                                        <span v-if="gradeLevelErrors.level" class="text-red-500 text-xs">{{ gradeLevelErrors.level[0] }}</span>
                                    </div>
                                    <div class="flex items-end gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input
                                                v-model="gradeLevelForm.is_active"
                                                type="checkbox"
                                                class="rounded"
                                            />
                                            <span class="text-sm text-gray-700">Active</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Button type="submit" class="gap-2">
                                        <Save class="w-4 h-4" />
                                        {{ editingGradeLevel ? 'Update Grade Level' : 'Add Grade Level' }}
                                    </Button>
                                    <Button
                                        v-if="editingGradeLevel"
                                        @click="resetGradeLevelForm"
                                        type="button"
                                        variant="outline"
                                        class="gap-2"
                                    >
                                        <X class="w-4 h-4" />
                                        Cancel
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Grade Levels Table -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Grade Levels</CardTitle>
                            <CardDescription>Manage all grade levels in the system</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="props.gradeLevels.length === 0" class="text-center py-8 text-gray-500">
                                No grade levels found. Create one to get started.
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-100">
                                        <tr class="border-b">
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Display Name</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Level</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="grade in props.gradeLevels"
                                            :key="grade.id"
                                            class="border-b hover:bg-gray-50 transition-colors"
                                        >
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ grade.name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ grade.display_name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ grade.level }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <Badge v-if="grade.is_active" class="bg-green-100 text-green-800">Active</Badge>
                                                <Badge v-else class="bg-gray-100 text-gray-800">Inactive</Badge>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex gap-2">
                                                    <Button
                                                        @click="editGradeLevel(grade)"
                                                        size="sm"
                                                        variant="outline"
                                                        class="gap-1"
                                                    >
                                                        <Edit2 class="w-3 h-3" />
                                                        Edit
                                                    </Button>
                                                    <Button
                                                        @click="deleteGradeLevel(grade.id)"
                                                        size="sm"
                                                        variant="destructive"
                                                        class="gap-1"
                                                    >
                                                        <Trash2 class="w-3 h-3" />
                                                        Delete
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- ===================== ACTIVITY TYPES TAB ===================== -->
                <div v-show="activeTab === 'activities'" class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ editingActivityType ? 'Edit Activity Type' : 'Add New Activity Type' }}</CardTitle>
                            <CardDescription>
                                {{ editingActivityType ? 'Update the activity type details' : 'Create a new activity type' }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submitActivityType" class="space-y-4">
                                <div>
                                    <Label for="activity-name">Name *</Label>
                                    <Input
                                        id="activity-name"
                                        v-model="activityTypeForm.name"
                                        placeholder="e.g., Quiz, Assignment, Assessment"
                                        class="mt-1"
                                    />
                                    <span v-if="activityTypeErrors.name" class="text-red-500 text-xs">{{ activityTypeErrors.name }}</span>
                                </div>

                                <div>
                                    <Label for="activity-description">Description</Label>
                                    <Textarea
                                        id="activity-description"
                                        v-model="activityTypeForm.description"
                                        placeholder="Brief description of this activity type"
                                        class="mt-1"
                                        :rows="3"
                                    />
                                    <span v-if="activityTypeErrors.description" class="text-red-500 text-xs">{{ activityTypeErrors.description }}</span>
                                </div>

                                <div>
                                    <Label for="activity-model">Model Class</Label>
                                    <Input
                                        id="activity-model"
                                        v-model="activityTypeForm.model"
                                        placeholder="e.g., App\\Models\\Quiz"
                                        class="mt-1"
                                    />
                                    <span v-if="activityTypeErrors.model" class="text-red-500 text-xs">{{ activityTypeErrors.model }}</span>
                                    <p class="text-xs text-gray-500 mt-1">Optional: PHP class path for this activity type</p>
                                </div>

                                <div class="flex gap-2">
                                    <Button type="submit" class="gap-2">
                                        <Save class="w-4 h-4" />
                                        {{ editingActivityType ? 'Update Activity Type' : 'Add Activity Type' }}
                                    </Button>
                                    <Button
                                        v-if="editingActivityType"
                                        @click="resetActivityTypeForm"
                                        type="button"
                                        variant="outline"
                                        class="gap-2"
                                    >
                                        <X class="w-4 h-4" />
                                        Cancel
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Activity Types Table -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Activity Types</CardTitle>
                            <CardDescription>Manage all activity types in the system</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="props.activityTypes.length === 0" class="text-center py-8 text-gray-500">
                                No activity types found. Create one to get started.
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-100">
                                        <tr class="border-b">
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Description</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Model Class</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="activity in props.activityTypes"
                                            :key="activity.id"
                                            class="border-b hover:bg-gray-50 transition-colors"
                                        >
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ activity.name }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ activity.description || '-' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600 font-mono text-xs">{{ activity.model || '-' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex gap-2">
                                                    <Button
                                                        @click="editActivityType(activity)"
                                                        size="sm"
                                                        variant="outline"
                                                        class="gap-1"
                                                    >
                                                        <Edit2 class="w-3 h-3" />
                                                        Edit
                                                    </Button>
                                                    <Button
                                                        @click="deleteActivityType(activity.id)"
                                                        size="sm"
                                                        variant="destructive"
                                                        class="gap-1"
                                                    >
                                                        <Trash2 class="w-3 h-3" />
                                                        Delete
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- ===================== QUESTION TYPES TAB ===================== -->
                <div v-show="activeTab === 'questions'" class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ editingQuestionType ? 'Edit Question Type' : 'Add New Question Type' }}</CardTitle>
                            <CardDescription>
                                {{ editingQuestionType ? 'Update the question type details' : 'Create a new question type' }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <form @submit.prevent="submitQuestionType" class="space-y-4">
                                <div>
                                    <Label for="question-type">Type *</Label>
                                    <Input
                                        id="question-type"
                                        v-model="questionTypeForm.type"
                                        placeholder="e.g., multiple-choice, true-false, short-answer"
                                        class="mt-1"
                                    />
                                    <span v-if="questionTypeErrors.type" class="text-red-500 text-xs">{{ questionTypeErrors.type }}</span>
                                </div>

                                <div>
                                    <Label for="question-description">Description</Label>
                                    <Textarea
                                        id="question-description"
                                        v-model="questionTypeForm.description"
                                        placeholder="Brief description of this question type"
                                        class="mt-1"
                                        :rows="3"
                                    />
                                    <span v-if="questionTypeErrors.description" class="text-red-500 text-xs">{{ questionTypeErrors.description }}</span>
                                </div>

                                <div class="flex gap-2">
                                    <Button type="submit" class="gap-2">
                                        <Save class="w-4 h-4" />
                                        {{ editingQuestionType ? 'Update Question Type' : 'Add Question Type' }}
                                    </Button>
                                    <Button
                                        v-if="editingQuestionType"
                                        @click="resetQuestionTypeForm"
                                        type="button"
                                        variant="outline"
                                        class="gap-2"
                                    >
                                        <X class="w-4 h-4" />
                                        Cancel
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <!-- Question Types Table -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Question Types</CardTitle>
                            <CardDescription>Manage all question types in the system</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="props.questionTypes.length === 0" class="text-center py-8 text-gray-500">
                                No question types found. Create one to get started.
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-100">
                                        <tr class="border-b">
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Description</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="question in props.questionTypes"
                                            :key="question.id"
                                            class="border-b hover:bg-gray-50 transition-colors"
                                        >
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ question.type }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ question.description || '-' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex gap-2">
                                                    <Button
                                                        @click="editQuestionType(question)"
                                                        size="sm"
                                                        variant="outline"
                                                        class="gap-1"
                                                    >
                                                        <Edit2 class="w-3 h-3" />
                                                        Edit
                                                    </Button>
                                                    <Button
                                                        @click="deleteQuestionType(question.id)"
                                                        size="sm"
                                                        variant="destructive"
                                                        class="gap-1"
                                                    >
                                                        <Trash2 class="w-3 h-3" />
                                                        Delete
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
</style>
