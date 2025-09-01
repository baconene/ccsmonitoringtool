import { reactive } from "vue"

export const cartStore = reactive({
  items: new Map(), // productId => quantity
  get count() {
    return this.items.size
  },
  addItem(productId, qty = 1) {
    const current = this.items.get(productId) || 0
    this.items.set(productId, current + qty)
  },
  removeItem(productId) {
    this.items.delete(productId)
  },
  clear() {
    this.items.clear()
  }
})
