<script setup lang="ts">
import { useCartStore } from '../stores/cart'
import { useRouter } from 'vue-router'
import { ref } from 'vue'
import { API_CONFIG } from '../config/api'

const cartStore = useCartStore()
const router = useRouter()
const isProcessing = ref(false)
const errorMessage = ref('')
const customerEmail = ref('')
const customerName = ref('')
const emailError = ref('')

// Redirect if cart is empty
if (cartStore.isEmpty) {
  router.push('/')
}

// Email validation
const isValidEmail = (email: string) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

// Generate placeholder image
const getProductImage = (id: number) => {
  return `https://picsum.photos/seed/${id}/200/200`
}

// Handle order confirmation
const confirmOrder = async () => {
  // Validate email
  emailError.value = ''
  if (!customerEmail.value) {
    emailError.value = 'Email is required'
    return
  }
  if (!isValidEmail(customerEmail.value)) {
    emailError.value = 'Please enter a valid email address'
    return
  }
  if (!customerName.value.trim()) {
    emailError.value = 'Name is required'
    return
  }
  
  isProcessing.value = true
  errorMessage.value = ''
  
  try {
    // Prepare order data
    const orderData = {
      items: cartStore.items.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
      })),
      customer_email: customerEmail.value.trim(),
      customer_name: customerName.value.trim(),
      customer_phone: null,
      payment_method: 'credit_card',
      shipping_address: null,
      billing_address: null,
      notes: null,
    }

    // Call checkout API
    const response = await fetch(`${API_CONFIG.CHECKOUT}/api/checkout`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(orderData),
    })

    const result = await response.json()

    if (!response.ok || !result.success) {
      // Handle validation errors
      if (result.errors) {
        if (Array.isArray(result.errors)) {
          errorMessage.value = result.errors.join('. ')
        } else {
          errorMessage.value = Object.values(result.errors).flat().join('. ')
        }
      } else {
        errorMessage.value = result.message || 'Failed to process order'
      }
      return
    }

    // Store order number for success page
    sessionStorage.setItem('lastOrderNumber', result.data.order.order_number)
    
    // Clear cart after successful order
    cartStore.clearCart()
    
    // Redirect to success page
    router.push('/order-success')
  } catch (error) {
    console.error('Order failed:', error)
    errorMessage.value = error instanceof Error ? error.message : 'Failed to process order. Please try again.'
  } finally {
    isProcessing.value = false
  }
}

const continueShopping = () => {
  router.push('/')
}
</script>

<template>
  <div class="checkout-container">
    <div class="checkout-content">
      <!-- Header -->
      <div class="checkout-header">
        <button @click="router.back()" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Back
        </button>
        <h1>Checkout</h1>
      </div>

      <div class="checkout-grid">
        <!-- Order Summary Section -->
        <div class="order-summary">
          <h2>Order Summary</h2>
          <p class="summary-subtitle">Review your items before confirming</p>

          <!-- Customer Information -->
          <div class="customer-info-section">
            <h3>Contact Information</h3>
            
            <div class="form-group">
              <label for="customer-name">Full Name <span class="required">*</span></label>
              <input
                id="customer-name"
                v-model="customerName"
                type="text"
                placeholder="Enter your full name"
                class="form-input"
                :class="{ 'input-error': emailError && !customerName.trim() }"
              />
            </div>

            <div class="form-group">
              <label for="customer-email">Email Address <span class="required">*</span></label>
              <input
                id="customer-email"
                v-model="customerEmail"
                type="email"
                placeholder="your.email@example.com"
                class="form-input"
                :class="{ 'input-error': emailError && (!customerEmail || !isValidEmail(customerEmail)) }"
              />
              <p v-if="emailError" class="input-error-message">{{ emailError }}</p>
              <p class="input-hint">Order confirmation will be sent to this email</p>
            </div>
          </div>

          <!-- Order Items -->
          <h3>Order Items</h3>
          <div class="order-items">
            <div v-for="item in cartStore.items" :key="item.id" class="order-item">
              <div class="item-image">
                <img :src="getProductImage(item.id)" :alt="item.name" />
              </div>
              
              <div class="item-info">
                <h3 class="item-name">{{ item.name }}</h3>
                <p class="item-meta">Qty: {{ item.quantity }} × ${{ item.price.toFixed(2) }}</p>
              </div>
              
              <div class="item-total">
                ${{ (item.price * item.quantity).toFixed(2) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Price Breakdown Section -->
        <div class="price-breakdown">
          <h2>Price Details</h2>
          
          <div class="price-row">
            <span>Subtotal ({{ cartStore.cartCount }} {{ cartStore.cartCount === 1 ? 'item' : 'items' }})</span>
            <span>${{ cartStore.cartTotal.toFixed(2) }}</span>
          </div>
          
          <div class="price-row">
            <span>Shipping</span>
            <span class="free-text">FREE</span>
          </div>
          
          <div class="price-row">
            <span>Tax (estimated)</span>
            <span>${{ (cartStore.cartTotal * 0.1).toFixed(2) }}</span>
          </div>
          
          <div class="divider"></div>
          
          <div class="price-row total-row">
            <span>Total</span>
            <span class="total-amount">${{ (cartStore.cartTotal * 1.1).toFixed(2) }}</span>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <!-- Error Message -->
            <div v-if="errorMessage" class="error-message">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              {{ errorMessage }}
            </div>

            <button 
              @click="confirmOrder" 
              class="confirm-btn"
              :disabled="isProcessing"
            >
              <span v-if="!isProcessing">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Confirm Order
              </span>
              <span v-else class="processing-state">
                <svg class="spinner" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                </svg>
                Processing...
              </span>
            </button>

            <button @click="continueShopping" class="continue-shopping">
              Continue Shopping
            </button>
          </div>

          <!-- Trust Badges -->
          <div class="trust-badges">
            <div class="badge">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <span>Secure Checkout</span>
            </div>
            <div class="badge">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
              <span>Money-back Guarantee</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.checkout-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  padding: 2rem 0;
}

.checkout-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* Header */
.checkout-header {
  margin-bottom: 2rem;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  border: 1px solid #e0e0e0;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  color: #333;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 1.5rem;
}

.back-btn:hover {
  background: #f8f9fa;
  border-color: #667eea;
  color: #667eea;
}

.checkout-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0;
}

/* Grid Layout */
.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  align-items: start;
}

/* Order Summary */
.order-summary {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.order-summary h2 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #333;
  margin: 0 0 0.5rem 0;
}

.summary-subtitle {
  color: #666;
  margin: 0 0 2rem 0;
  font-size: 1rem;
}

/* Customer Information Form */
.customer-info-section {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 12px;
  margin-bottom: 2rem;
}

.customer-info-section h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #333;
  margin: 0 0 1.25rem 0;
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group:last-child {
  margin-bottom: 0;
}

.form-group label {
  display: block;
  font-size: 0.95rem;
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
}

.required {
  color: #ef4444;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  font-size: 1rem;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  transition: all 0.2s ease;
  background: white;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.input-error {
  border-color: #ef4444;
}

.form-input.input-error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.input-error-message {
  margin: 0.5rem 0 0 0;
  font-size: 0.875rem;
  color: #ef4444;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.input-hint {
  margin: 0.5rem 0 0 0;
  font-size: 0.875rem;
  color: #666;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.order-items {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.order-item {
  display: grid;
  grid-template-columns: 80px 1fr auto;
  gap: 1.25rem;
  padding: 1.25rem;
  background: #f8f9fa;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.order-item:hover {
  background: #f0f0f0;
  transform: translateY(-2px);
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  overflow: hidden;
  background: white;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.5rem;
}

.item-name {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #333;
}

.item-meta {
  margin: 0;
  font-size: 0.95rem;
  color: #666;
}

.item-total {
  display: flex;
  align-items: center;
  font-size: 1.25rem;
  font-weight: 700;
  color: #667eea;
}

/* Price Breakdown */
.price-breakdown {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 2rem;
}

.price-breakdown h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  margin: 0 0 1.5rem 0;
}

.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  font-size: 1rem;
  color: #333;
}

.free-text {
  color: #10b981;
  font-weight: 600;
}

.divider {
  height: 1px;
  background: #e0e0e0;
  margin: 1rem 0;
}

.total-row {
  font-size: 1.25rem;
  font-weight: 700;
  padding: 1rem 0;
}

.total-amount {
  font-size: 1.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 2rem;
}

.error-message {
  background: #fee2e2;
  border: 1px solid #ef4444;
  border-radius: 8px;
  padding: 1rem;
  color: #dc2626;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  animation: slideDown 0.3s ease-out;
}

.error-message svg {
  flex-shrink: 0;
  color: #ef4444;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.confirm-btn {
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
  gap: 0.75rem;
  transition: all 0.3s ease;
}

.confirm-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

.confirm-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.processing-state {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.continue-shopping {
  width: 100%;
  padding: 1rem;
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.continue-shopping:hover {
  background: #667eea;
  color: white;
}

/* Trust Badges */
.trust-badges {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #e0e0e0;
}

.badge {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #666;
  font-size: 0.95rem;
}

.badge svg {
  color: #10b981;
}

/* Responsive */
@media (max-width: 968px) {
  .checkout-grid {
    grid-template-columns: 1fr;
  }

  .price-breakdown {
    position: static;
  }
}

@media (max-width: 640px) {
  .checkout-content {
    padding: 0 1rem;
  }

  .checkout-header h1 {
    font-size: 2rem;
  }

  .order-item {
    grid-template-columns: 60px 1fr auto;
    gap: 1rem;
  }

  .item-image {
    width: 60px;
    height: 60px;
  }
}
</style>
