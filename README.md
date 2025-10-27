# E-Commerce Microservices Platform

A modern e-commerce system built with microservices architecture.

## Architecture

This system consists of 3 independent microservices:

- **Catalog Service** - Product catalog management and browsing
- **Checkout Service** - Order processing and management  
- **Email Service** - Automated email notifications

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2)
- **Frontend**: Vue.js 3 with TypeScript
- **Database**: MySQL 8.0
- **Containerization**: Docker & Docker Compose

## System Diagram

```
Frontend (Vue.js) → Catalog Service → MySQL
                  → Checkout Service → Email Service
```

## Prerequisites

- Docker Desktop
- Docker Compose
- Git

## Getting Started

### 1. Configure Environment

```bash
# Copy the Docker environment template
cp .env.docker.example .env.docker

# Edit .env.docker and set your MySQL passwords
```

### 2. Start Services

```bash
docker-compose up -d --build
```

### 3. Run Migrations

```bash
docker-compose exec catalog-service php artisan migrate --force
docker-compose exec checkout-service php artisan migrate --force
docker-compose exec email-service php artisan migrate --force
```

### 4. Access Application

- Frontend: http://localhost:8080
- Catalog API: http://localhost:8001
- Checkout API: http://localhost:8002
- Email API: http://localhost:8003

## API Endpoints

**Catalog Service**
- `GET /api/products` - List products
- `GET /api/products/{id}` - Get product details

**Checkout Service**
- `POST /api/orders` - Create order
- `GET /api/orders/{id}` - Get order details

**Email Service**
- `POST /api/order-confirmation` - Send order confirmation

## Configuration

Each service has a `.env` file for configuration. See `.env.example` files in each service directory for setup details.

## Project Structure

```
├── catalog-service/     # Product catalog API
├── checkout-service/    # Order management API
├── email-service/       # Email notification API
├── frontend/            # Vue.js frontend
└── docker-compose.yml   # Docker configuration
```
