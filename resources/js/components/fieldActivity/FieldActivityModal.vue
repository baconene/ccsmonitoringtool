<script setup> 
import FieldActivityDetails from './FieldActivityDetails.vue';

const props = defineProps({
  activityId: {
    type: String,
    required: true
  }
})
</script>

<template>
           <div v-if="showModal" class="fixed inset-0 z-40 bg-black/50 flex justify-end">
            <div class="bg-white h-full w-full sm:w-1/2 max-w-full p-6 relative z-50 shadow-xl overflow-y-auto">
                <button class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-2xl" @click="closeModal">
                    &times;
                </button>
                <h3 class="text-lg font-semibold mb-4 mt-2 pr-8">Activity Details</h3>
                <div v-if="selectedActivity">
                    <FieldActivityDetails :activityId="activityId" />
                </div>
                <button class="mt-6 bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 block sm:hidden"
                    @click="closeModal">
                    Close
                </button>
            </div>
        </div>
</template>