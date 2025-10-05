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
});

const resetForm = () => {
    formData.value = {
        title: '',
        description: '',
        activity_type_id: null,
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
                        class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-xl"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Create New Activity
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
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="formData.title"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter activity title"
                                    required
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Description
                                </label>
                                <textarea
                                    v-model="formData.description"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Enter activity description"
                                />
                            </div>

                            <!-- Activity Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Activity Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="formData.activity_type_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
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
