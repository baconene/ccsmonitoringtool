<script setup>
import { ref } from "vue"
import AppLayout from "@/layouts/AppLayout.vue"
import { Link } from "@inertiajs/vue3"
import CartBadge from "./CartBadge.vue"
import { cartStore } from "@/pages/orders/cartStore"
import Toast from "@/components/Toast.vue"
import { useToast } from "@/composables/useToast"

const props = defineProps({
  products: Array,
})

const quantities = ref({})
const { showMessage } = useToast()

const addToCart = (product) => {
  const qty = quantities.value[product.id] || 1
  cartStore.addItem(product.id, qty)
  showMessage("Item added to cart!")
  quantities.value[product.id] = 1
}
</script>

<template>
  <AppLayout title="Ordering Dashboard">
    <div class="min-h-screen bg-gray-100">
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="font-bold text-xl">My App</h1>
        <nav class="flex items-center gap-6">
          <Link href="/inventory" class="text-gray-800 font-medium hover:text-blue-600">Inventory</Link>
          <Link href="/orders" class="text-gray-800 font-medium hover:text-blue-600">Orders</Link>
          <CartBadge />
        </nav>
      </header>

      <main class="p-6">
        <h1 class="text-2xl font-bold mb-6">Ordering Dashboard</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="p in props.products"
            :key="p.id"
            class="border rounded-xl shadow hover:shadow-lg transition bg-white p-4 flex flex-col justify-between"
          >
            <div>
              <h2 class="text-lg font-semibold">{{ p.product_name }}</h2>
              <p class="text-sm text-gray-500">SKU: {{ p.sku }}</p>
              <p class="text-sm text-gray-600 mt-2">Stock: {{ p.stock }}</p>
              <p class="text-lg font-bold mt-2">₱{{ p.price }}</p>
            </div>

            <div class="mt-4 flex items-center gap-2">
              <input
                type="number"
                min="1"
                :max="p.stock"
                v-model.number="quantities[p.id]"
                class="w-16 border rounded px-2 py-1"
                placeholder="Qty"
              />
              <button
                @click="addToCart(p)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                :disabled="p.stock <= 0"
              >
                {{ p.stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
              </button>
            </div>
          </div>
        </div>
      </main>

      <Toast />
    </div>
  </AppLayout>
</template>
