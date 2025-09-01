<script setup>
import { ref } from "vue"
import { cartStore } from "@/pages/orders/cartStore"
import CartModal from "./CartModal.vue"

const showModal = ref(false)

const toggleModal = () => {
  if (cartStore.count > 0) {
    showModal.value = !showModal.value
  }
}
</script>

<template>
  <div class="relative">
    <button
      class="relative p-2 rounded-full bg-gray-100 hover:bg-gray-200"
      @click="toggleModal"
    >
      🛒
      <span
        v-if="cartStore.count > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5"
      >
        {{ cartStore.count }}
      </span>
    </button>

    <CartModal v-if="showModal" @close="showModal = false" />
  </div>
</template>
