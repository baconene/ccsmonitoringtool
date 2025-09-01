<template>
  <div
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <div
      class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-6 transition-transform transform scale-100 duration-300 overflow-y-auto max-h-[90vh]"
    >
      <header class="flex justify-between items-center border-b pb-2 mb-4">
        <h2 class="text-lg font-bold">Your Cart</h2>
        <button
          class="text-gray-500 hover:text-gray-700 text-xl font-bold"
          @click="closeModal"
        >
          ✖
        </button>
      </header>

      <div v-if="cartProducts.length > 0" class="space-y-4">
        <div
          v-for="p in cartProducts"
          :key="p.id"
          class="flex gap-4 items-center border rounded-lg p-3 shadow-sm hover:shadow-md transition"
        >
          <!-- Placeholder Image -->
          <div class="w-20 h-20 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
            <span class="text-gray-400 text-sm">Image</span>
          </div>

          <!-- Product Details -->
          <div class="flex-1 flex flex-col justify-between">
            <div>
              <h3 class="font-semibold text-gray-800">{{ p.product_name }}</h3>
              <p class="text-sm text-gray-500 mt-1">₱{{ p.price }}</p>
            </div>

            <div class="flex items-center justify-between mt-2">
              <div class="flex items-center gap-2">
                <button
                  class="px-2 py-1 border rounded"
                  @click="decrementQuantity(p)"
                >
                  -
                </button>
                <span>{{ p.quantity }}</span>
                <button
                  class="px-2 py-1 border rounded"
                  @click="incrementQuantity(p)"
                >
                  +
                </button>
              </div>

              <p class="font-semibold text-gray-800">
                ₱{{ (p.price * p.quantity).toFixed(2) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Total & Checkout -->
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center border-t pt-4">
          <span class="font-bold text-lg">Total: ₱{{ totalPrice.toFixed(2) }}</span>
          <button
            class="mt-3 sm:mt-0 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
            @click="checkout"
          >
            Checkout
          </button>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center py-10">Your cart is empty.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue"
import { cartStore } from "@/pages/orders/cartStore"
import axios from "axios"

const emit = defineEmits(["close"])

const cartProducts = ref<
  Array<{ id: number; product_name: string; price: number; quantity: number }>
>([])

const totalPrice = computed(() =>
  cartProducts.value.reduce((sum, p) => sum + p.price * p.quantity, 0)
)

const fetchCartProducts = async () => {
  const productIds = Array.from(cartStore.items.keys())
  if (!productIds.length) return

  try {
    const products = await Promise.all(
      productIds.map(async (id) => {
        const res = await axios.get(`/inventory/${id}`)
        return {
          id: res.data.id,
          product_name: res.data.product_name,
          price: res.data.price,
          quantity: cartStore.items.get(id) || 1
        }
      })
    )
    cartProducts.value = products
  } catch (err) {
    console.error("Failed to fetch cart products:", err)
  }
}

const closeModal = () => emit("close")

const incrementQuantity = (product: any) => {
  product.quantity++
  cartStore.items.set(product.id, product.quantity)
}

const decrementQuantity = (product: any) => {
  if (product.quantity > 1) {
    product.quantity--
    cartStore.items.set(product.id, product.quantity)
  }
}

const checkout = () => {
  console.log("Proceed to checkout", cartProducts.value)
  // Implement checkout logic here
  closeModal()
}

onMounted(() => {
  fetchCartProducts()
})
</script>
