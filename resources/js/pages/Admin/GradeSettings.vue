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
import { Save, RotateCcw, AlertCircle, CheckCircle2, Search, X, Calculator, TrendingUp } from 'lucide-vue-next';

interface GradeSetting {
    id: number | null;
    course_id?: number;
    setting_type: string;
    setting_key: string;
    display_name: string;
    weight_percentage: number;
    is_active: boolean;
}

interface Course {
    id: number;
    name: string;
    title: string;
    course_code?: string;
    instructor?: {
        id: number;
        name: string;
    };
}

interface Props {
    moduleComponents: GradeSetting[] | any[];
    activityTypes: GradeSetting[] | any[];
    courses: Course[];
    selectedCourse?: Course | null;
    hasCustomSettings: boolean;
    isGlobalSettings: boolean;
}

const props = defineProps<Props>();

// Course search
const searchQuery = ref('');
const showCourseList = ref(false);
const selectedCourse = ref<Course | null>(props.selectedCourse || null);

// Local state for weights
const lessonWeight = ref((props.moduleComponents as any[]).find(s => s.setting_key === 'lessons')?.weight_percentage || 20);
const activityWeight = ref((props.moduleComponents as any[]).find(s => s.setting_key === 'activities')?.weight_percentage || 80);

const quizWeight = ref((props.activityTypes as any[]).find(s => s.setting_key === 'Quiz')?.weight_percentage || 30);
const assignmentWeight = ref((props.activityTypes as any[]).find(s => s.setting_key === 'Assignment')?.weight_percentage || 15);
const assessmentWeight = ref((props.activityTypes as any[]).find(s => s.setting_key === 'Assessment')?.weight_percentage || 35);
const exerciseWeight = ref((props.activityTypes as any[]).find(s => s.setting_key === 'Exercise')?.weight_percentage || 20);

// Grade Simulation
const simLessonScore = ref(90);
const simQuizScore = ref(85);
const simAssignmentScore = ref(92);
const simAssessmentScore = ref(88);
const simExerciseScore = ref(80);

// Simulation: Which components exist
const simHasLessons = ref(true);
const simHasQuizzes = ref(true);
const simHasAssignments = ref(true);
const simHasAssessments = ref(true);
const simHasExercises = ref(true);

// Filtered courses
const filteredCourses = computed(() => {
    if (!searchQuery.value) return props.courses;
    const query = searchQuery.value.toLowerCase();
    return props.courses.filter(course => 
        course.name.toLowerCase().includes(query) ||
        course.title.toLowerCase().includes(query) ||
        course.course_code?.toLowerCase().includes(query) ||
        course.instructor?.name.toLowerCase().includes(query)
    );
});

// Computed validation
const moduleComponentsTotal = computed(() => Number(lessonWeight.value) + Number(activityWeight.value));
const moduleComponentsValid = computed(() => Math.abs(moduleComponentsTotal.value - 100) < 0.01);

const activityTypesTotal = computed(() => 
    Number(quizWeight.value) + Number(assignmentWeight.value) + Number(assessmentWeight.value) + Number(exerciseWeight.value)
);
const activityTypesValid = computed(() => Math.abs(activityTypesTotal.value - 100) < 0.01);

// Dynamic activity type weights based on what exists
const dynamicActivityWeights = computed(() => {
    const weights: Record<string, number> = {};
    let total = 0;

    if (simHasQuizzes.value) {
        weights.Quiz = Number(quizWeight.value);
        total += weights.Quiz;
    }
    if (simHasAssignments.value) {
        weights.Assignment = Number(assignmentWeight.value);
        total += weights.Assignment;
    }
    if (simHasAssessments.value) {
        weights.Assessment = Number(assessmentWeight.value);
        total += weights.Assessment;
    }
    if (simHasExercises.value) {
        weights.Exercise = Number(exerciseWeight.value);
        total += weights.Exercise;
    }

    // Normalize to 100%
    if (total > 0) {
        Object.keys(weights).forEach(key => {
            weights[key] = (weights[key] / total) * 100;
        });
    }

    return weights;
});

// Grade Simulation Calculations
const simulatedActivityScore = computed(() => {
    const hasActivities = simHasQuizzes.value || simHasAssignments.value || 
                         simHasAssessments.value || simHasExercises.value;
    
    if (!hasActivities) return 0;

    let score = 0;
    const weights = dynamicActivityWeights.value;

    if (simHasQuizzes.value && weights.Quiz) {
        score += Number(simQuizScore.value) * (weights.Quiz / 100);
    }
    if (simHasAssignments.value && weights.Assignment) {
        score += Number(simAssignmentScore.value) * (weights.Assignment / 100);
    }
    if (simHasAssessments.value && weights.Assessment) {
        score += Number(simAssessmentScore.value) * (weights.Assessment / 100);
    }
    if (simHasExercises.value && weights.Exercise) {
        score += Number(simExerciseScore.value) * (weights.Exercise / 100);
    }

    return score;
});

// Dynamic module component weights
const dynamicModuleWeights = computed(() => {
    const hasActivities = simHasQuizzes.value || simHasAssignments.value || 
                         simHasAssessments.value || simHasExercises.value;

    if (simHasLessons.value && hasActivities) {
        // Both exist: use configured weights
        return {
            lessons: Number(lessonWeight.value),
            activities: Number(activityWeight.value)
        };
    } else if (simHasLessons.value && !hasActivities) {
        // Only lessons
        return { lessons: 100, activities: 0 };
    } else if (!simHasLessons.value && hasActivities) {
        // Only activities
        return { lessons: 0, activities: 100 };
    } else {
        // Nothing exists (shouldn't happen)
        return { lessons: 50, activities: 50 };
    }
});

const simulatedModuleScore = computed(() => {
    const weights = dynamicModuleWeights.value;
    const lessonContribution = Number(simLessonScore.value) * (weights.lessons / 100);
    const activityContribution = simulatedActivityScore.value * (weights.activities / 100);
    return lessonContribution + activityContribution;
});

const simulatedLetterGrade = computed(() => {
    const score = simulatedModuleScore.value;
    if (score >= 97) return 'A+';
    if (score >= 93) return 'A';
    if (score >= 90) return 'A-';
    if (score >= 87) return 'B+';
    if (score >= 83) return 'B';
    if (score >= 80) return 'B-';
    if (score >= 77) return 'C+';
    if (score >= 73) return 'C';
    if (score >= 70) return 'C-';
    if (score >= 67) return 'D+';
    if (score >= 65) return 'D';
    return 'F';
});

// Course selection
const selectCourse = (course: Course) => {
    selectedCourse.value = course;
    showCourseList.value = false;
    searchQuery.value = '';
    router.get(`/grade-settings?course_id=${course.id}`, {}, {
        preserveState: false,
    });
};

const clearCourseSelection = () => {
    selectedCourse.value = null;
    router.get('/grade-settings', {}, {
        preserveState: false,
    });
};

// Handle weight changes
const handleLessonChange = (event: Event) => {
    const value = Number((event.target as HTMLInputElement).value);
    if (value >= 0 && value <= 100) {
        lessonWeight.value = value;
        activityWeight.value = 100 - value;
    }
};

const handleActivityChange = (event: Event) => {
    const value = Number((event.target as HTMLInputElement).value);
    if (value >= 0 && value <= 100) {
        activityWeight.value = value;
        lessonWeight.value = 100 - value;
    }
};

// Save functions
const saveModuleComponents = () => {
    if (!moduleComponentsValid.value) {
        alert('Module component weights must total 100%');
        return;
    }

    router.post('/grade-settings/module-components', {
        course_id: selectedCourse.value?.id || null,
        lessons: lessonWeight.value,
        activities: activityWeight.value,
    }, {
        preserveScroll: true,
    });
};

const saveActivityTypes = () => {
    if (!activityTypesValid.value) {
        alert('Activity type weights must total 100%');
        return;
    }

    router.post('/grade-settings/activity-types', {
        course_id: selectedCourse.value?.id || null,
        Quiz: quizWeight.value,
        Assignment: assignmentWeight.value,
        Assessment: assessmentWeight.value,
        Exercise: exerciseWeight.value,
    }, {
        preserveScroll: true,
    });
};

const resetToDefaults = () => {
    const message = selectedCourse.value 
        ? 'Reset this course\'s settings to global defaults?' 
        : 'Reset global settings to factory defaults?';
    
    if (confirm(message)) {
        router.post('/grade-settings/reset', {
            course_id: selectedCourse.value?.id || null,
        }, {
            preserveScroll: true,
        });
    }
};

const deleteCourseSettings = () => {
    if (!selectedCourse.value) return;
    
    if (confirm('Delete course-specific settings and revert to global settings?')) {
        router.delete('/grade-settings/course', {
            data: { course_id: selectedCourse.value.id },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Grade Settings" />

    <AppLayout>
        <div class="container mx-auto py-8 px-4 max-w-7xl">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight">Grade Settings</h1>
                <p class="text-muted-foreground mt-2">
                    Configure how student grades are calculated
                    {{ selectedCourse ? `for ${selectedCourse.name}` : '(Global Defaults)' }}
                </p>
            </div>

            <!-- Course Search Section -->
            <Card class="mb-6">
                <CardHeader>
                    <CardTitle>Select Course</CardTitle>
                    <CardDescription>
                        Search for a course to configure course-specific settings
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <!-- Selected Course or Search -->
                        <div v-if="selectedCourse" class="flex items-center justify-between p-4 bg-primary/10 rounded-lg">
                            <div>
                                <h3 class="font-semibold">{{ selectedCourse.name }}</h3>
                                <p class="text-sm text-muted-foreground">{{ selectedCourse.title }}</p>
                                <Badge v-if="hasCustomSettings" class="mt-2">Custom Settings</Badge>
                                <Badge v-else class="mt-2" variant="outline">Using Global</Badge>
                            </div>
                            <div class="flex gap-2">
                                <Button @click="deleteCourseSettings" v-if="hasCustomSettings" variant="outline" size="sm">
                                    Revert to Global
                                </Button>
                                <Button @click="clearCourseSelection" variant="ghost" size="icon">
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>

                        <div v-else class="relative">
                            <div class="flex items-center gap-2">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                <Input
                                    v-model="searchQuery"
                                    @focus="showCourseList = true"
                                    placeholder="Search courses..."
                                    class="pl-10"
                                />
                            </div>

                            <!-- Course Dropdown -->
                            <div v-if="showCourseList && filteredCourses.length > 0" 
                                 class="absolute z-10 w-full mt-2 bg-background border rounded-lg shadow-lg max-h-96 overflow-y-auto">
                                <button
                                    v-for="course in filteredCourses"
                                    :key="course.id"
                                    @click="selectCourse(course)"
                                    class="w-full text-left px-4 py-3 hover:bg-accent border-b last:border-0"
                                >
                                    <div class="font-medium">{{ course.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ course.title }}</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Settings Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Module Components -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>Module Component Weights</CardTitle>
                                    <CardDescription>Lessons vs Activities</CardDescription>
                                </div>
                                <Badge :variant="moduleComponentsValid ? 'default' : 'destructive'">
                                    <CheckCircle2 v-if="moduleComponentsValid" class="w-4 h-4 mr-1" />
                                    <AlertCircle v-else class="w-4 h-4 mr-1" />
                                    {{ moduleComponentsTotal.toFixed(0) }}%
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Lessons</Label>
                                    <span class="text-2xl font-bold text-primary">{{ lessonWeight }}%</span>
                                </div>
                                <Input
                                    type="number"
                                    :value="lessonWeight"
                                    @input="handleLessonChange"
                                    min="0"
                                    max="100"
                                />
                            </div>

                            <Separator />

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Activities</Label>
                                    <span class="text-2xl font-bold text-primary">{{ activityWeight }}%</span>
                                </div>
                                <Input
                                    type="number"
                                    :value="activityWeight"
                                    @input="handleActivityChange"
                                    min="0"
                                    max="100"
                                />
                            </div>

                            <div class="flex justify-end pt-4">
                                <Button @click="saveModuleComponents" :disabled="!moduleComponentsValid">
                                    <Save class="w-4 h-4 mr-2" />
                                    Save
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Activity Types -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle>Activity Type Weights</CardTitle>
                                    <CardDescription>Quiz, Assignment, Assessment, Exercise</CardDescription>
                                </div>
                                <Badge :variant="activityTypesValid ? 'default' : 'destructive'">
                                    <CheckCircle2 v-if="activityTypesValid" class="w-4 h-4 mr-1" />
                                    <AlertCircle v-else class="w-4 h-4 mr-1" />
                                    {{ activityTypesTotal.toFixed(0) }}%
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Quiz</Label>
                                    <span class="text-xl font-bold text-primary">{{ quizWeight }}%</span>
                                </div>
                                <Input v-model="quizWeight" type="number" min="0" max="100" />
                            </div>

                            <Separator />

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Assignment</Label>
                                    <span class="text-xl font-bold text-primary">{{ assignmentWeight }}%</span>
                                </div>
                                <Input v-model="assignmentWeight" type="number" min="0" max="100" />
                            </div>

                            <Separator />

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Assessment</Label>
                                    <span class="text-xl font-bold text-primary">{{ assessmentWeight }}%</span>
                                </div>
                                <Input v-model="assessmentWeight" type="number" min="0" max="100" />
                            </div>

                            <Separator />

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <Label>Exercise</Label>
                                    <span class="text-xl font-bold text-primary">{{ exerciseWeight }}%</span>
                                </div>
                                <Input v-model="exerciseWeight" type="number" min="0" max="100" />
                            </div>

                            <div class="flex justify-end pt-4">
                                <Button @click="saveActivityTypes" :disabled="!activityTypesValid">
                                    <Save class="w-4 h-4 mr-2" />
                                    Save
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Reset -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Reset</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Button variant="outline" @click="resetToDefaults">
                                <RotateCcw class="w-4 h-4 mr-2" />
                                Reset to Defaults
                            </Button>
                        </CardContent>
                    </Card>
                </div>

                <!-- Simulator Column -->
                <div>
                    <Card class="sticky top-4">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Calculator class="w-5 h-5" />
                                Grade Simulator
                            </CardTitle>
                            <CardDescription class="text-xs">
                                Toggle which components exist to see dynamic weight adjustments
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm flex items-center gap-2">
                                        <input type="checkbox" v-model="simHasLessons" class="rounded" />
                                        Lessons
                                    </Label>
                                    <Badge variant="outline" class="text-xs">
                                        {{ dynamicModuleWeights.lessons }}%
                                    </Badge>
                                </div>
                                <Input 
                                    v-model="simLessonScore" 
                                    type="number" 
                                    min="0" 
                                    max="100" 
                                    :disabled="!simHasLessons"
                                    :class="!simHasLessons ? 'opacity-50' : ''"
                                />
                            </div>

                            <Separator />

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold">Activity Scores</h4>
                                    <Badge variant="outline" class="text-xs">
                                        {{ dynamicModuleWeights.activities }}%
                                    </Badge>
                                </div>
                                
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <Label class="text-xs flex items-center gap-2">
                                            <input type="checkbox" v-model="simHasQuizzes" class="rounded" />
                                            Quiz
                                        </Label>
                                        <Badge variant="secondary" class="text-[10px]" v-if="simHasQuizzes">
                                            {{ dynamicActivityWeights.Quiz?.toFixed(1) }}%
                                        </Badge>
                                    </div>
                                    <Input 
                                        v-model="simQuizScore" 
                                        type="number" 
                                        min="0" 
                                        max="100" 
                                        :disabled="!simHasQuizzes"
                                        :class="!simHasQuizzes ? 'opacity-50' : ''"
                                    />
                                </div>
                                
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <Label class="text-xs flex items-center gap-2">
                                            <input type="checkbox" v-model="simHasAssignments" class="rounded" />
                                            Assignment
                                        </Label>
                                        <Badge variant="secondary" class="text-[10px]" v-if="simHasAssignments">
                                            {{ dynamicActivityWeights.Assignment?.toFixed(1) }}%
                                        </Badge>
                                    </div>
                                    <Input 
                                        v-model="simAssignmentScore" 
                                        type="number" 
                                        min="0" 
                                        max="100" 
                                        :disabled="!simHasAssignments"
                                        :class="!simHasAssignments ? 'opacity-50' : ''"
                                    />
                                </div>
                                
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <Label class="text-xs flex items-center gap-2">
                                            <input type="checkbox" v-model="simHasAssessments" class="rounded" />
                                            Assessment
                                        </Label>
                                        <Badge variant="secondary" class="text-[10px]" v-if="simHasAssessments">
                                            {{ dynamicActivityWeights.Assessment?.toFixed(1) }}%
                                        </Badge>
                                    </div>
                                    <Input 
                                        v-model="simAssessmentScore" 
                                        type="number" 
                                        min="0" 
                                        max="100" 
                                        :disabled="!simHasAssessments"
                                        :class="!simHasAssessments ? 'opacity-50' : ''"
                                    />
                                </div>
                                
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <Label class="text-xs flex items-center gap-2">
                                            <input type="checkbox" v-model="simHasExercises" class="rounded" />
                                            Exercise
                                        </Label>
                                        <Badge variant="secondary" class="text-[10px]" v-if="simHasExercises">
                                            {{ dynamicActivityWeights.Exercise?.toFixed(1) }}%
                                        </Badge>
                                    </div>
                                    <Input 
                                        v-model="simExerciseScore" 
                                        type="number" 
                                        min="0" 
                                        max="100" 
                                        :disabled="!simHasExercises"
                                        :class="!simHasExercises ? 'opacity-50' : ''"
                                    />
                                </div>
                            </div>

                            <Separator />

                            <div class="bg-accent/50 p-4 rounded-lg space-y-3">
                                <h4 class="font-semibold text-sm flex items-center gap-2">
                                    <TrendingUp class="w-4 h-4" />
                                    Result
                                </h4>
                                
                                <div class="text-xs space-y-2">
                                    <div class="flex justify-between items-center">
                                        <p class="font-medium">Lesson Contribution:</p>
                                        <p>{{ (simLessonScore * dynamicModuleWeights.lessons / 100).toFixed(2) }}%</p>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="font-medium">Activity Score:</p>
                                        <p>{{ simulatedActivityScore.toFixed(2) }}%</p>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <p class="font-medium">Activity Contribution:</p>
                                        <p>{{ (simulatedActivityScore * dynamicModuleWeights.activities / 100).toFixed(2) }}%</p>
                                    </div>
                                </div>

                                <Separator />

                                <div class="text-center py-4">
                                    <p class="text-sm text-muted-foreground">Module Score</p>
                                    <p class="text-4xl font-bold text-primary">
                                        {{ simulatedModuleScore.toFixed(2) }}%
                                    </p>
                                    <Badge class="mt-2 text-lg px-4 py-1">
                                        {{ simulatedLetterGrade }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

