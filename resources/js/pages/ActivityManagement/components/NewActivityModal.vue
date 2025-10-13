<script setup lang="ts">
import { ref, watch } from 'vue';
import { ActivityType } from '@/types';
import { X } from 'lucide-vue-next';

interface Props {
    show: boolean;
    activityTypes: ActivityType[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
    submit: [data: any];
}>();

const formData = ref({
    title: '',
    description: '',
    activity_type_id: null as number | null,
    due_date: '',
});

const resetForm = () => {
    formData.value = {
        title: '',
        description: '',
        activity_type_id: null,
        due_date: '',
    };
};

watch(() => props.show, (newVal) => {
    if (!newVal) {
        resetForm();
    }
});

const handleSubmit = () => {
    if (!formData.value.title || !formData.value.activity_type_id) {
        alert('Please fill in all required fields');
        return;
    }
    emit('submit', formData.value);
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
                        class="relative w-full max-w-md sm:max-w-lg bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-xl shadow-2xl border border-purple-200/50 dark:border-purple-700/50"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-purple-200/50 dark:border-purple-700/50 px-4 sm:px-6 py-4">
                            <h3 class="text-lg sm:text-xl font-semibold bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
                                Create New Activity
                            </h3>
                            <button
                                @click="emit('close')"
                                class="text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors p-1 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg"
                            >
                                <X :size="20" />
                            </button>
                        </div>

                        <!-- Body -->
                        <form @submit.prevent="handleSubmit" class="p-4 sm:p-6 space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.title"
                                    type="text"
                                    class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700/50 dark:text-white transition-all"
                                    placeholder="Enter activity title"
                                    required
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description
                                </label>
                                <textarea
                                    v-model="formData.description"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700/50 dark:text-white transition-all resize-none"
                                    placeholder="Enter activity description"
                                />
                            </div>

                            <!-- Activity Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Activity Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.activity_type_id"
                                    class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700/50 dark:text-white transition-all"
                                    required
                                >
                                    <option :value="null" disabled>Select activity type</option>
                                    <option
                                        v-for="type in activityTypes"
                                        :key="type.id"
                                        :value="type.id"
                                    >
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Due Date
                                </label>
                                <input
                                    v-model="formData.due_date"
                                    type="datetime-local"
                                    class="w-full px-3 py-2 border border-purple-300 dark:border-purple-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700/50 dark:text-white transition-all"
                                    placeholder="Select due date and time"
                                />
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3 pt-4 border-t border-purple-200/50 dark:border-purple-700/50">
                                <button
                                    type="button"
                                    @click="emit('close')"
                                    class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all hover:scale-105"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 rounded-lg shadow-lg hover:shadow-xl transition-all hover:scale-105"
                                >
                                    Create Activity
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
