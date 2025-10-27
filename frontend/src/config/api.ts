// API Configuration
export const API_CONFIG = {
  CATALOG: import.meta.env.VITE_API_CATALOG || 'http://localhost:8001',
  CHECKOUT: import.meta.env.VITE_API_CHECKOUT || 'http://localhost:8002',
  EMAIL: import.meta.env.VITE_API_EMAIL || 'http://localhost:8003'
}

export const API_ENDPOINTS = {
  // Catalog Service
  products: `${API_CONFIG.CATALOG}/api/products`,
  product: (id: number) => `${API_CONFIG.CATALOG}/api/products/${id}`,
  
  // Checkout Service
  orders: `${API_CONFIG.CHECKOUT}/api/orders`,
  order: (id: number) => `${API_CONFIG.CHECKOUT}/api/orders/${id}`,
  
  // Email Service
  orderConfirmation: `${API_CONFIG.EMAIL}/api/order-confirmation`
}
