<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface Props {
    show: boolean;
    title?: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    isDeleting?: boolean;
}

interface Emits {
    (e: 'close'): void;
    (e: 'confirm'): void;
}

withDefaults(defineProps<Props>(), {
    title: 'Confirm Delete',
    message: 'Are you sure you want to delete this item? This action cannot be undone.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
    isDeleting: false,
});

const emit = defineEmits<Emits>();

const handleConfirm = () => {
    emit('confirm');
};

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <Dialog :open="show" @update:open="handleClose">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>
                    {{ message }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex gap-2 sm:gap-0">
                <Button
                    type="button"
                    variant="outline"
                    @click="handleClose"
                    :disabled="isDeleting"
                >
                    {{ cancelText }}
                </Button>
                <Button
                    type="button"
                    variant="destructive"
                    @click="handleConfirm"
                    :disabled="isDeleting"
                >
                    <span v-if="isDeleting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Deleting...
                    </span>
                    <span v-else>{{ confirmText }}</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
