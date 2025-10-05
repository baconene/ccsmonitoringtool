 
    <template>
        <div v-if="showDeleteModal" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-full max-w-md border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Delete User</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                            Are you sure you want to delete <strong>{{ userToDelete?.name }}</strong>?
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            This action cannot be undone. All user data will be permanently removed from the system.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-6">
                    <button
                        type="button"
                        @click="closeDeleteModal"
                        class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="confirmDeleteUser"
                        class="w-full sm:w-auto px-6 py-3 text-sm font-medium text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 rounded-lg transition-colors focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete User</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script setup lang="ts">
    interface User {
        id: number;
        name: string;
        email?: string;
    }

    interface Props {
        showDeleteModal: boolean;
        userToDelete: User | null;
    }

    interface Emits {
        (e: 'close'): void;
        (e: 'confirm', user: User): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const closeDeleteModal = () => {
        emit('close');
    };

    const confirmDeleteUser = () => {
        if (props.userToDelete) {
            emit('confirm', props.userToDelete);
        }
    };
    </script>
         