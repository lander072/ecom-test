<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { useCartStore } from './stores/cart'
import CartDrawer from './components/CartDrawer.vue'

const cartStore = useCartStore()
</script>

<template>
  <div class="app-container">
    <!-- Professional Navigation Header -->
    <header class="header">
      <div class="header-content">
        <div class="logo-section">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <h1 class="brand-name">E-Commerce Store</h1>
        </div>

        <div class="cart-section">
          <button @click="cartStore.toggleDrawer" class="cart-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="21" r="1"></circle>
              <circle cx="20" cy="21" r="1"></circle>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span v-if="cartStore.cartCount > 0" class="cart-badge">{{ cartStore.cartCount }}</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <RouterView />
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-content">
        <p>&copy; 2025 E-Commerce Store. All rights reserved.</p>
        <p class="footer-tech">Built with Vue.js, Laravel & Docker</p>
      </div>
    </footer>

    <!-- Cart Drawer -->
    <CartDrawer />
  </div>
</template>

<style scoped>
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8f9fa;
}

/* Header Styles */
.header {
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-content {
  width: 100%;
  padding: 1.25rem 4rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 3rem;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  min-width: 250px;
}

.logo-icon {
  color: #667eea;
}

.brand-name {
  font-size: 1.75rem;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0;
  white-space: nowrap;
}

/* Navigation */
.nav {
  display: flex;
  gap: 2rem;
  align-items: center;
  flex: 1;
  justify-content: center;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  color: #666;
  text-decoration: none;
  font-weight: 500;
  font-size: 1.05rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #667eea;
  background: rgba(102, 126, 234, 0.1);
}

.nav-link.router-link-active {
  color: #667eea;
  background: rgba(102, 126, 234, 0.15);
}

/* Cart Section */
.cart-section {
  display: flex;
  align-items: center;
  min-width: 250px;
  justify-content: flex-end;
}

.cart-btn {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 0.875rem 1.25rem;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.cart-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #e53e3e;
  color: white;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  min-width: 20px;
  text-align: center;
}

/* Main Content */
.main-content {
  flex: 1;
  width: 100%;
}

/* Footer */
.footer {
  background: white;
  border-top: 1px solid #e0e0e0;
  margin-top: 4rem;
}

.footer-content {
  width: 100%;
  padding: 2.5rem 4rem;
  text-align: center;
  color: #666;
}

.footer-content p {
  margin: 0.5rem 0;
  font-size: 1rem;
}

.footer-tech {
  font-size: 0.95rem;
  color: #999;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .header-content {
    padding: 1rem 2rem;
  }
  
  .footer-content {
    padding: 2rem;
  }
}

@media (max-width: 768px) {
  .header-content {
    flex-wrap: wrap;
    padding: 1rem;
    gap: 1rem;
  }

  .brand-name {
    font-size: 1.25rem;
  }

  .logo-section,
  .cart-section {
    min-width: auto;
  }

  .nav {
    order: 3;
    width: 100%;
    justify-content: center;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
    gap: 1rem;
  }

  .nav-link {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }
}
</style>
