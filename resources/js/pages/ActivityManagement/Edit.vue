<script setup lang="ts">
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Activity, ActivityType } from '@/types';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import CosmicBackground from '@/components/CosmicBackground.vue';

interface Props {
    activity: Activity & {
        activity_type?: any;
        creator?: any;
    };
    activityTypes: ActivityType[];
}

const props = defineProps<Props>();

const form = useForm({
    title: props.activity.title,
    description: props.activity.description || '',
    activity_type_id: props.activity.activity_type_id,
});

const handleBack = () => {
    router.visit('/activity-management');
};

const handleSubmit = () => {
    form.put(`/activities/${props.activity.id}`, {
        onSuccess: () => {
            router.visit(`/activities/${props.activity.id}`);
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="relative min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-pink-50/30 dark:from-gray-900 dark:via-purple-950/20 dark:to-pink-950/20 transition-colors duration-300">
            <CosmicBackground />
            
            <div class="relative z-10 py-6 px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <button
                        @click="handleBack"
                        class="mb-4 flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors group"
                    >
                        <ArrowLeft :size="20" class="group-hover:-translate-x-1 transition-transform" />
                        <span>Back to Activities</span>
                    </button>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">
                        Edit Activity
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                        Update the details of your activity
                    </p>
                </div>

                <!-- Edit Form -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-purple-200/50 dark:border-purple-700/50 p-4 sm:p-6 lg:p-8">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <Label for="title" class="text-sm font-medium">
                            Activity Title <span class="text-red-500">*</span>
                        </Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="Enter activity title"
                            required
                            class="mt-1"
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <Label for="description" class="text-sm font-medium">
                            Description
                        </Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            placeholder="Enter activity description"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.description }"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Activity Type -->
                    <div>
                        <Label for="activity_type" class="text-sm font-medium">
                            Activity Type <span class="text-red-500">*</span>
                        </Label>
                        <select
                            id="activity_type"
                            v-model="form.activity_type_id"
                            required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.activity_type_id }"
                        >
                            <option value="" disabled>Select activity type</option>
                            <option
                                v-for="type in activityTypes"
                                :key="type.id"
                                :value="type.id"
                            >
                                {{ type.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.activity_type_id" class="mt-1 text-sm text-red-500">
                            {{ form.errors.activity_type_id }}
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105"
                        >
                            <Save :size="16" />
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="handleBack"
                            :disabled="form.processing"
                            class="border-purple-300 dark:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20"
                        >
                            Cancel
                        </Button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
