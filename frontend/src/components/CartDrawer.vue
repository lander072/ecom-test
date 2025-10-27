<script setup lang="ts">
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const router = useRouter()

// Generate placeholder image
const getProductImage = (id: number) => {
  return `https://picsum.photos/seed/${id}/200/200`
}

// Handle checkout
const handleCheckout = () => {
  if (cartStore.isEmpty) return
  
  // Navigate to checkout page
  cartStore.closeDrawer()
  router.push('/checkout')
}
</script>

<template>
  <!-- Overlay -->
  <Transition name="overlay">
    <div 
      v-if="cartStore.isDrawerOpen" 
      class="overlay"
      @click="cartStore.closeDrawer"
    ></div>
  </Transition>

  <!-- Drawer -->
  <Transition name="drawer">
    <div v-if="cartStore.isDrawerOpen" class="cart-drawer">
      <!-- Header -->
      <div class="drawer-header">
        <div class="header-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <h2>Shopping Cart</h2>
          <span class="item-count">{{ cartStore.cartCount }} {{ cartStore.cartCount === 1 ? 'item' : 'items' }}</span>
        </div>
        <button class="close-btn" @click="cartStore.closeDrawer">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>

      <!-- Cart Items -->
      <div class="drawer-content">
        <!-- Empty State -->
        <div v-if="cartStore.isEmpty" class="empty-cart">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <h3>Your cart is empty</h3>
          <p>Add some products to get started!</p>
          <button @click="cartStore.closeDrawer" class="continue-shopping-btn">
            Continue Shopping
          </button>
        </div>

        <!-- Cart Items List -->
        <div v-else class="cart-items">
          <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
              <!-- Product Image -->
              <div class="item-image">
                <img :src="getProductImage(item.id)" :alt="item.name" />
              </div>

              <!-- Product Details -->
              <div class="item-details">
                <h3 class="item-name">{{ item.name }}</h3>
                <p class="item-price">${{ item.price.toFixed(2) }}</p>
                
                <!-- Quantity Controls -->
                <div class="quantity-controls">
                  <button 
                    @click="cartStore.decreaseQuantity(item.id)"
                    class="qty-btn"
                    :class="{ 'delete-btn': item.quantity === 1 }"
                    :title="item.quantity === 1 ? 'Remove from cart' : 'Decrease quantity'"
                  >
                    <svg v-if="item.quantity === 1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                  </button>
                  
                  <span class="qty-display">{{ item.quantity }}</span>
                  
                  <button 
                    @click="cartStore.increaseQuantity(item.id)"
                    class="qty-btn"
                    :disabled="item.quantity >= item.stock"
                    :title="item.quantity >= item.stock ? 'Max stock reached' : 'Increase quantity'"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="12" y1="5" x2="12" y2="19"></line>
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                  </button>
                </div>

                <div v-if="item.quantity >= item.stock" class="stock-warning">
                  Max stock reached
                </div>
              </div>

              <!-- Item Subtotal & Remove -->
              <div class="item-actions">
                <p class="item-subtotal">${{ (item.price * item.quantity).toFixed(2) }}</p>
                <button 
                  @click="cartStore.removeFromCart(item.id)"
                  class="remove-btn"
                  title="Remove from cart"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

      <!-- Footer -->
      <div v-if="!cartStore.isEmpty" class="drawer-footer">
        <div class="total-section">
          <div class="subtotal">
            <span>Subtotal:</span>
            <span class="amount">${{ cartStore.cartTotal.toFixed(2) }}</span>
          </div>
          <p class="tax-note">Taxes and shipping calculated at checkout</p>
        </div>

        <button @click="handleCheckout" class="checkout-btn">
          <span>Proceed to Checkout</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>

        <button @click="cartStore.clearCart" class="clear-cart-btn">
          Clear Cart
        </button>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
/* Overlay */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1999;
}

/* Drawer */
.cart-drawer {
  position: fixed;
  top: 0;
  right: 0;
  width: 480px;
  max-width: 100vw;
  height: 100vh;
  background: white;
  box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
  z-index: 2000;
  display: flex;
  flex-direction: column;
  transform: translateZ(0);
  backface-visibility: hidden;
  will-change: transform;
}

/* Header */
.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.75rem 2rem;
  border-bottom: 1px solid #e0e0e0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.header-title h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
}

.item-count {
  background: rgba(255, 255, 255, 0.25);
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

/* Content */
.drawer-content {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 1.5rem;
  -webkit-overflow-scrolling: touch;
  will-change: scroll-position;
}

/* Empty Cart */
.empty-cart {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
  gap: 1.5rem;
  height: 100%;
}

.empty-cart svg {
  color: #ccc;
}

.empty-cart h3 {
  font-size: 1.5rem;
  color: #333;
  margin: 0;
}

.empty-cart p {
  color: #666;
  margin: 0;
}

.continue-shopping-btn {
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.continue-shopping-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Cart Items */
.cart-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto;
  gap: 1rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.cart-item:hover {
  background: #f0f0f0;
}

/* Item Image */
.item-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  background: white;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Item Details */
.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.item-name {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  line-height: 1.3;
}

.item-price {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 700;
  color: #667eea;
}

/* Quantity Controls */
.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.25rem;
}

.qty-btn {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.qty-btn:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.qty-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.qty-btn.delete-btn {
  color: #e53e3e;
  border-color: #e53e3e;
}

.qty-btn.delete-btn:hover {
  background: #e53e3e;
  color: white;
}

.qty-display {
  min-width: 30px;
  text-align: center;
  font-weight: 600;
  font-size: 1rem;
}

.stock-warning {
  font-size: 0.75rem;
  color: #f59e0b;
  font-weight: 500;
}

/* Item Actions */
.item-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.5rem;
}

.item-subtotal {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 700;
  color: #333;
}

.remove-btn {
  background: none;
  border: none;
  color: #999;
  cursor: pointer;
  padding: 0.25rem;
  transition: all 0.2s ease;
}

.remove-btn:hover {
  color: #e53e3e;
}

/* Footer */
.drawer-footer {
  border-top: 1px solid #e0e0e0;
  padding: 1.5rem 2rem;
  background: white;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.total-section {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.subtotal {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.125rem;
}

.subtotal .amount {
  font-size: 1.75rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.tax-note {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
}

.checkout-btn {
  width: 100%;
  padding: 1.25rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1.125rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.checkout-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

.clear-cart-btn {
  width: 100%;
  padding: 0.875rem;
  background: white;
  color: #e53e3e;
  border: 1px solid #e53e3e;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-cart-btn:hover {
  background: #e53e3e;
  color: white;
}

/* Transitions */
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.25s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.drawer-enter-active,
.drawer-leave-active {
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.drawer-enter-from,
.drawer-leave-to {
  transform: translateX(100%);
}

/* Responsive */
@media (max-width: 768px) {
  .cart-drawer {
    width: 100vw;
  }

  .cart-item {
    grid-template-columns: 70px 1fr auto;
  }

  .item-image {
    width: 70px;
    height: 70px;
  }
}
</style>
