<script setup lang="ts">
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { useCartStore } from '../stores/cart'
import { API_ENDPOINTS } from '../config/api'

interface Product {
  id: number
  name: string
  description: string
  price: number
  stock: number
  image?: string
}

const router = useRouter()
const cartStore = useCartStore()

const products = ref<Product[]>([])
const loading = ref(true)
const error = ref('')

// Fetch products from the catalog service
const fetchProducts = async () => {
  try {
    loading.value = true
    const response = await fetch(API_ENDPOINTS.products)
    const data = await response.json()
    products.value = data.data
  } catch (err) {
    error.value = JSON.stringify(err)
    console.error('Error fetching products:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
})

// Generate placeholder image
const getProductImage = (id: number) => {
  return `https://picsum.photos/seed/${id}/400/300`
}

// Navigate to product details page
const viewProductDetails = (productId: number) => {
  router.push(`/product/${productId}`)
}

const addToCart = (product: Product, event: Event) => {
  // Prevent navigation when clicking "Add to Cart" button
  event.stopPropagation()
  
  // Add product to cart with quantity of 1
  cartStore.addToCart({
    id: product.id,
    name: product.name,
    price: product.price,
    stock: product.stock,
    image_url: null
  }, 1)
}
</script>


<template>
  <div class="product-list-container">
    <!-- Header -->
    <div class="header">
      <h1 class="title">Our Products</h1>
      <p class="subtitle">Discover our amazing collection of products!</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Loading products...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button @click="fetchProducts" class="retry-btn">Retry</button>
    </div>

    <!-- Products Grid -->
    <div v-else class="products-grid">

      <div 
        v-for="product in products" 
        :key="product.id" 
        class="product-card"
        @click="viewProductDetails(product.id)"
      >

        <div class="product-image">
          <img :src="getProductImage(product.id)" :alt="product.name" />
          <div class="stock-badge" :class="{ 'low-stock': product.stock < 20 }">
            {{ product.stock }} in stock
          </div>
        </div>
        
        <div class="product-content">
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-description">{{ product.description }}</p>
          
          <div class="product-footer">
            <div class="price-section">
              <span class="price">${{ product.price }}</span>
            </div>
            <button 
              @click="addToCart(product, $event)" 
              class="add-to-cart-btn"
              :disabled="product.stock === 0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              {{ product.stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && !error && products.length === 0" class="empty-state">
      <p>No products available at the moment.</p>
    </div>
  </div>
</template>

<style scoped>
.product-list-container {
  width: 100%;
  padding: 3rem 4rem;
}

.header {
  text-align: center;
  margin-bottom: 4rem;
}

.title {
  font-size: 3.5rem;
  font-weight: 700;
  color: #1a1a1a;
  margin-bottom: 0.75rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  font-size: 1.35rem;
  color: #666;
  font-weight: 300;
}

/* Loading State */
.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 0;
  gap: 1rem;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Error State */
.error {
  text-align: center;
  padding: 3rem;
  color: #e53e3e;
}

.retry-btn {
  margin-top: 1rem;
  padding: 0.75rem 2rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  background: #5568d3;
  transform: translateY(-2px);
}

/* Products Grid */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2.5rem;
  margin-top: 2rem;
}

/* Product Card */
.product-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  width: 100%;
  height: 280px;
  overflow: hidden;
  background: #f8f9fa;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.stock-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(76, 175, 80, 0.95);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  backdrop-filter: blur(10px);
}

.stock-badge.low-stock {
  background: rgba(255, 152, 0, 0.95);
}

/* Product Content */
.product-content {
  padding: 1.75rem;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.product-name {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 0.75rem;
  line-height: 1.3;
}

.product-description {
  color: #666;
  font-size: 1rem;
  line-height: 1.6;
  margin-bottom: 1.5rem;
  flex: 1;
}

/* Product Footer */
.product-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #e0e0e0;
}

.price-section {
  display: flex;
  flex-direction: column;
}

.price {
  font-size: 2rem;
  font-weight: 700;
  color: #667eea;
}

/* Add to Cart Button */
.add-to-cart-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.add-to-cart-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.add-to-cart-btn:active:not(:disabled) {
  transform: translateY(0);
}

.add-to-cart-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #666;
  font-size: 1.25rem;
}

/* Responsive Design */
@media (max-width: 1400px) {
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  }
}

@media (max-width: 1024px) {
  .product-list-container {
    padding: 2rem;
  }

  .title {
    font-size: 2.5rem;
  }

  .products-grid {
    gap: 2rem;
  }
}

@media (max-width: 768px) {
  .product-list-container {
    padding: 1rem;
  }

  .title {
    font-size: 2rem;
  }

  .subtitle {
    font-size: 1rem;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
  }

  .product-footer {
    flex-direction: column;
    align-items: stretch;
  }

  .add-to-cart-btn {
    justify-content: center;
    width: 100%;
  }
}
</style>
