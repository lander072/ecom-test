<script setup lang="ts">
import { useRouter } from 'vue-router'
import { onMounted, ref } from 'vue'

const router = useRouter()
const isVisible = ref(false)

// Get order number from session storage or generate fallback
const orderNumber = ref(sessionStorage.getItem('lastOrderNumber') || `ORD-${Date.now()}-${Math.floor(Math.random() * 1000)}`)

onMounted(() => {
  // Clear the stored order number
  sessionStorage.removeItem('lastOrderNumber')
  
  // Trigger animation
  setTimeout(() => {
    isVisible.value = true
  }, 100)
})

const continueShopping = () => {
  router.push('/')
}
</script>

<template>
  <div class="success-container">
    <Transition name="fade-up">
      <div v-if="isVisible" class="success-content">
        <!-- Success Icon -->
        <div class="success-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>

        <!-- Success Message -->
        <h1>Order Placed Successfully!</h1>
        <p class="subtitle">Thank you for your purchase</p>

        <!-- Order Details -->
        <div class="order-details">
          <div class="detail-item">
            <span class="label">Order Number</span>
            <span class="value">{{ orderNumber }}</span>
          </div>
          <div class="detail-item">
            <span class="label">Status</span>
            <span class="value status-confirmed">Confirmed</span>
          </div>
        </div>

        <!-- Info Card -->
        <div class="info-card">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
          <div>
            <h3>What's Next?</h3>
            <p>You will receive an email confirmation shortly with your order details and tracking information.</p>
          </div>
        </div>

        <!-- Actions -->
        <div class="action-buttons">
          <button @click="continueShopping" class="primary-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Continue Shopping
          </button>
        </div>

        <!-- Features -->
        <div class="features">
          <div class="feature">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="1" y="3" width="15" height="13"></rect>
              <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
              <circle cx="5.5" cy="18.5" r="2.5"></circle>
              <circle cx="18.5" cy="18.5" r="2.5"></circle>
            </svg>
            <div>
              <h4>Free Shipping</h4>
              <p>On all orders</p>
            </div>
          </div>
          <div class="feature">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <div>
              <h4>Secure Payment</h4>
              <p>100% protected</p>
            </div>
          </div>
          <div class="feature">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 4 23 10 17 10"></polyline>
              <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
            </svg>
            <div>
              <h4>Easy Returns</h4>
              <p>30-day return policy</p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.success-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.success-content {
  max-width: 600px;
  width: 100%;
  background: white;
  border-radius: 24px;
  padding: 3rem;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

/* Success Icon */
.success-icon {
  width: 120px;
  height: 120px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
  animation: scaleIn 0.5s ease-out;
}

.success-icon svg {
  color: white;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

/* Text */
.success-content h1 {
  font-size: 2.25rem;
  font-weight: 700;
  color: #333;
  margin: 0 0 0.75rem 0;
}

.subtitle {
  font-size: 1.125rem;
  color: #666;
  margin: 0 0 2.5rem 0;
}

/* Order Details */
.order-details {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
}

.label {
  font-size: 1rem;
  color: #666;
  font-weight: 500;
}

.value {
  font-size: 1.125rem;
  color: #333;
  font-weight: 700;
}

.status-confirmed {
  color: #10b981;
  background: rgba(16, 185, 129, 0.1);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.95rem;
}

/* Info Card */
.info-card {
  background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
  border-left: 4px solid #3b82f6;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  display: flex;
  gap: 1rem;
  text-align: left;
}

.info-card svg {
  color: #3b82f6;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.info-card h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.125rem;
  color: #333;
  font-weight: 700;
}

.info-card p {
  margin: 0;
  color: #666;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2.5rem;
}

.primary-btn {
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

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

/* Features */
.features {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
  padding-top: 2rem;
  border-top: 1px solid #e0e0e0;
}

.feature {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  text-align: left;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 10px;
  transition: all 0.2s ease;
}

.feature:hover {
  background: #f0f0f0;
  transform: translateX(4px);
}

.feature svg {
  color: #667eea;
  flex-shrink: 0;
}

.feature h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: #333;
}

.feature p {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
}

/* Transition */
.fade-up-enter-active {
  transition: all 0.5s ease-out;
}

.fade-up-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

/* Responsive */
@media (max-width: 640px) {
  .success-content {
    padding: 2rem 1.5rem;
  }

  .success-content h1 {
    font-size: 1.75rem;
  }

  .success-icon {
    width: 100px;
    height: 100px;
  }

  .success-icon svg {
    width: 60px;
    height: 60px;
  }
}
</style>
