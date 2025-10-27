import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface CartItem {
  id: number
  name: string
  price: number
  quantity: number
  stock: number
  image_url?: string | null
}

export const useCartStore = defineStore('cart', () => {
  // State
  const items = ref<CartItem[]>([])
  const isDrawerOpen = ref(false)

  // Getters
  const cartCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const cartTotal = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  })

  const isEmpty = computed(() => items.value.length === 0)

  // Actions
  function addToCart(product: {
    id: number
    name: string
    price: string | number
    stock: number
    image_url?: string | null
  }, quantity: number = 1) {
    const price = typeof product.price === 'string' ? parseFloat(product.price) : product.price

    const existingItem = items.value.find(item => item.id === product.id)

    if (existingItem) {
      // Update quantity if item exists (check stock limit)
      const newQuantity = existingItem.quantity + quantity
      if (newQuantity <= product.stock) {
        existingItem.quantity = newQuantity
      } else {
        existingItem.quantity = product.stock
      }
    } else {
      // Add new item to cart
      items.value.push({
        id: product.id,
        name: product.name,
        price,
        quantity: Math.min(quantity, product.stock),
        stock: product.stock,
        image_url: product.image_url
      })
    }

    // Open drawer when item is added
    openDrawer()
  }

  function removeFromCart(productId: number) {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
    }
  }

  function updateQuantity(productId: number, quantity: number) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      if (quantity <= 0) {
        removeFromCart(productId)
      } else if (quantity <= item.stock) {
        item.quantity = quantity
      } else {
        item.quantity = item.stock
      }
    }
  }

  function increaseQuantity(productId: number) {
    const item = items.value.find(item => item.id === productId)
    if (item && item.quantity < item.stock) {
      item.quantity++
    }
  }

  function decreaseQuantity(productId: number) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      if (item.quantity > 1) {
        item.quantity--
      } else {
        removeFromCart(productId)
      }
    }
  }

  function clearCart() {
    items.value = []
  }

  function openDrawer() {
    isDrawerOpen.value = true
  }

  function closeDrawer() {
    isDrawerOpen.value = false
  }

  function toggleDrawer() {
    isDrawerOpen.value = !isDrawerOpen.value
  }

  return {
    // State
    items,
    isDrawerOpen,
    // Getters
    cartCount,
    cartTotal,
    isEmpty,
    // Actions
    addToCart,
    removeFromCart,
    updateQuantity,
    increaseQuantity,
    decreaseQuantity,
    clearCart,
    openDrawer,
    closeDrawer,
    toggleDrawer
  }
})
