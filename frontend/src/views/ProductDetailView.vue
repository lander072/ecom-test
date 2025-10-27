<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { API_ENDPOINTS } from '../config/api'

interface Product {
  id: number
  name: string
  description: string
  price: string
  stock: number
  image_url?: string | null
  category?: string
  is_active?: boolean
  created_at?: string
  updated_at?: string
}

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const product = ref<Product | null>(null)
const loading = ref(true)
const error = ref('')
const quantity = ref(1)

// Get product ID from route params
const productId = computed(() => route.params.id)

// Fetch single product details
const fetchProduct = async () => {
  try {
    loading.value = true
    error.value = ''
    const response = await fetch(API_ENDPOINTS.product(Number(productId.value)))
    
    if (!response.ok) {
      throw new Error('Product not found')
    }
    
    const data = await response.json()
    product.value = data.data
  } catch (err: any) {
    error.value = err.message || 'Failed to load product'
    console.error('Error fetching product:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProduct()
})

// Generate placeholder image
const getProductImage = (id: number) => {
  return `https://picsum.photos/seed/${id}/800/600`
}

// Increase quantity
const increaseQuantity = () => {
  if (product.value && quantity.value < product.value.stock) {
    quantity.value++
  }
}

// Decrease quantity
const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

// Add to cart
const addToCart = () => {
  if (product.value) {
    cartStore.addToCart(product.value, quantity.value)
    // Reset quantity after adding to cart
    quantity.value = 1
  }
}

// Buy now
const buyNow = () => {
  if (product.value) {
    cartStore.addToCart(product.value, quantity.value)
    quantity.value = 1
    // TODO: Navigate to checkout page
    // For now, just open the cart drawer
  }
}

// Go back to products list
const goBack = () => {
  router.push('/')
}
</script>

<template>
  <div class="product-detail-container">
    <!-- Loading State -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Loading product details...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <h2>{{ error }}</h2>
      <button @click="goBack" class="back-btn">Back to Products</button>
    </div>

    <!-- Product Details -->
    <div v-else-if="product" class="product-detail">
      <!-- Breadcrumb Navigation -->
      <nav class="breadcrumb">
        <button @click="goBack" class="breadcrumb-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
          Back to Products
        </button>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ product.name }}</span>
      </nav>

      <!-- Product Content Grid -->
      <div class="product-grid">
        <!-- Product Image Section -->
        <div class="image-section">
          <div class="main-image">
            <img :src="getProductImage(product.id)" :alt="product.name" />
            <div v-if="product.stock < 20 && product.stock > 0" class="stock-badge low-stock">
              Only {{ product.stock }} left!
            </div>
            <div v-else-if="product.stock > 0" class="stock-badge in-stock">
              In Stock
            </div>
            <div v-else class="stock-badge out-of-stock">
              Out of Stock
            </div>
          </div>
        </div>

        <!-- Product Info Section -->
        <div class="info-section">
          <div class="product-header">
            <h1 class="product-title">{{ product.name }}</h1>
            <div class="product-meta">
              <span v-if="product.category" class="category-badge">{{ product.category }}</span>
              <span class="product-id">ID: #{{ product.id }}</span>
            </div>
          </div>

          <div class="price-section">
            <div class="price">${{ parseFloat(product.price).toFixed(2) }}</div>
            <div v-if="product.stock > 0" class="stock-info">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              {{ product.stock }} units available
            </div>
            <div v-else class="stock-info out">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              Out of stock
            </div>
          </div>

          <div class="description-section">
            <h3>Description</h3>
            <p>{{ product.description }}</p>
          </div>

          <div class="divider"></div>

          <!-- Quantity Selector -->
          <div v-if="product.stock > 0" class="quantity-section">
            <label class="quantity-label">Quantity</label>
            <div class="quantity-controls">
              <button 
                @click="decreaseQuantity" 
                class="quantity-btn"
                :disabled="quantity <= 1"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
              <input 
                type="number" 
                v-model.number="quantity" 
                min="1" 
                :max="product.stock"
                class="quantity-input"
              />
              <button 
                @click="increaseQuantity" 
                class="quantity-btn"
                :disabled="quantity >= product.stock"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button 
              @click="addToCart" 
              class="add-to-cart-btn"
              :disabled="product.stock === 0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
            <button 
              @click="buyNow"
              class="buy-now-btn" 
              :disabled="product.stock === 0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="13 17 18 12 13 7"></polyline>
                <polyline points="6 17 11 12 6 7"></polyline>
              </svg>
              Buy Now
            </button>
          </div>

          <!-- Additional Info -->
          <div class="additional-info">
            <div class="info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>Free shipping on orders over $50</span>
            </div>
            <div class="info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
              </svg>
              <span>30-day money-back guarantee</span>
            </div>
            <div class="info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <span>Secure checkout</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.product-detail-container {
  width: 100%;
  min-height: calc(100vh - 200px);
  padding: 2rem 4rem;
}

/* Loading State */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6rem 0;
  gap: 1rem;
}

.spinner {
  width: 60px;
  height: 60px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error State */
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6rem 2rem;
  gap: 1.5rem;
  text-align: center;
}

.error-state svg {
  color: #e53e3e;
}

.error-state h2 {
  color: #e53e3e;
  font-size: 1.75rem;
}

.back-btn {
  padding: 1rem 2rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background: #5568d3;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 2.5rem;
  font-size: 0.95rem;
}

.breadcrumb-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #667eea;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.breadcrumb-link:hover {
  color: #5568d3;
}

.breadcrumb-separator {
  color: #999;
}

.breadcrumb-current {
  color: #666;
}

/* Product Grid Layout */
.product-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  background: white;
  border-radius: 20px;
  padding: 3rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
}

/* Image Section */
.image-section {
  position: sticky;
  top: 100px;
  height: fit-content;
}

.main-image {
  position: relative;
  width: 100%;
  aspect-ratio: 4/3;
  border-radius: 16px;
  overflow: hidden;
  background: #f8f9fa;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.main-image:hover img {
  transform: scale(1.05);
}

.stock-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  font-size: 0.95rem;
  font-weight: 700;
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stock-badge.in-stock {
  background: rgba(76, 175, 80, 0.95);
  color: white;
}

.stock-badge.low-stock {
  background: rgba(255, 152, 0, 0.95);
  color: white;
}

.stock-badge.out-of-stock {
  background: rgba(244, 67, 54, 0.95);
  color: white;
}

/* Info Section */
.info-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.product-header {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.product-title {
  font-size: 2.75rem;
  font-weight: 700;
  color: #1a1a1a;
  line-height: 1.2;
  margin: 0;
}

.product-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.category-badge {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
}

.product-id {
  color: #999;
  font-size: 0.875rem;
}

/* Price Section */
.price-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.price {
  font-size: 3rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.stock-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #4caf50;
  font-weight: 500;
}

.stock-info.out {
  color: #f44336;
}

/* Description */
.description-section h3 {
  font-size: 1.25rem;
  color: #1a1a1a;
  margin-bottom: 1rem;
}

.description-section p {
  color: #666;
  font-size: 1.05rem;
  line-height: 1.8;
}

.divider {
  height: 1px;
  background: #e0e0e0;
  margin: 1rem 0;
}

/* Quantity Section */
.quantity-section {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.quantity-label {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.quantity-btn {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.quantity-btn:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.quantity-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.quantity-input {
  width: 80px;
  height: 48px;
  text-align: center;
  font-size: 1.25rem;
  font-weight: 600;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  padding: 0 1rem;
}

.quantity-input:focus {
  outline: none;
  border-color: #667eea;
}

/* Remove number input arrows */
.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.quantity-input[type=number] {
  -moz-appearance: textfield;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.add-to-cart-btn,
.buy-now-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1.25rem 2rem;
  font-size: 1.1rem;
  font-weight: 700;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-to-cart-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.add-to-cart-btn:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

.buy-now-btn {
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
}

.buy-now-btn:hover:not(:disabled) {
  background: #667eea;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
}

.add-to-cart-btn:disabled,
.buy-now-btn:disabled {
  background: #ccc;
  color: #666;
  border-color: #ccc;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Additional Info */
.additional-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
  margin-top: 1rem;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #666;
  font-size: 0.95rem;
}

.info-item svg {
  color: #667eea;
  flex-shrink: 0;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .product-grid {
    grid-template-columns: 1fr;
    gap: 2.5rem;
  }

  .image-section {
    position: relative;
    top: 0;
  }

  .product-title {
    font-size: 2.25rem;
  }

  .price {
    font-size: 2.5rem;
  }
}

@media (max-width: 768px) {
  .product-detail-container {
    padding: 1rem;
  }

  .product-grid {
    padding: 2rem 1.5rem;
  }

  .product-title {
    font-size: 1.75rem;
  }

  .price {
    font-size: 2rem;
  }

  .action-buttons {
    flex-direction: column;
  }

  .add-to-cart-btn,
  .buy-now-btn {
    width: 100%;
  }
}
</style>
