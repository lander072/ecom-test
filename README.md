# E-Commerce Microservices Platform# E-Commerce Microservices System



A modern e-commerce system built with microservices architecture.A complete e-commerce system built with microservices architecture using Laravel, Vue.js, MySQL, and Docker.



## Architecture## Overview



This system consists of 3 independent microservices:This project implements an online e-commerce system with 3 microservices:



- **Catalog Service** - Product catalog management and browsing1. **Catalog Service** - Manages product catalog, enables users to list all available products and view product details

- **Checkout Service** - Order processing and management  2. **Checkout Service** - Handles order placement for one or more products

- **Email Service** - Automated email notifications3. **Email Service** - Sends order confirmation emails to users



## Tech Stack## Tech Stack



- **Backend**: Laravel 11 (PHP 8.2)- **Backend**: PHP 8.2 / Laravel 12

- **Frontend**: Vue.js 3 with TypeScript- **Frontend**: Vue.js 3 with TypeScript

- **Database**: MySQL 8.0- **Database**: MySQL 8.0

- **Containerization**: Docker & Docker Compose- **Containerization**: Docker & Docker Compose

- **Cloud**: AWS (EC2, RDS, CloudFormation/SAM for provisioning)

## System Diagram

## System Diagram

```

Frontend (Vue.js) → Catalog Service → MySQL```

                  → Checkout Service → Email ServiceFrontend (Vue.js) → Catalog Service → MySQL

```                  → Checkout Service → Email Service

```

## Prerequisites

## Prerequisites

- Docker Desktop

- Docker Compose- Docker Desktop

- Git- Docker Compose

- Git

## Getting Started

## Getting Started

### 1. Start Services

### 1. Start Services

```bash

docker-compose up -d --build```bash

```docker-compose up -d --build

```

### 2. Run Migrations

### 2. Run Migrations

```bash

docker-compose exec catalog-service php artisan migrate --force```bash

docker-compose exec checkout-service php artisan migrate --forcedocker-compose exec catalog-service php artisan migrate --force

docker-compose exec email-service php artisan migrate --forcedocker-compose exec checkout-service php artisan migrate --force

```docker-compose exec email-service php artisan migrate --force

```

### 3. Access Application

### 3. Access Application

- Frontend: http://localhost:8080

- Catalog API: http://localhost:8001- Frontend: http://localhost:8080

- Checkout API: http://localhost:8002- Catalog API: http://localhost:8001

- Email API: http://localhost:8003- Checkout API: http://localhost:8002

- Email API: http://localhost:8003

## API Endpoints

## API Endpoints

**Catalog Service**

- `GET /api/products` - List products**Catalog Service**

- `GET /api/products/{id}` - Get product details- `GET /api/products` - List products

- `GET /api/products/{id}` - Get product details

**Checkout Service**

- `POST /api/orders` - Create order**Checkout Service**

- `GET /api/orders/{id}` - Get order details- `POST /api/orders` - Create order

- `GET /api/orders/{id}` - Get order details

**Email Service**

- `POST /api/order-confirmation` - Send order confirmation**Email Service**

- `POST /api/order-confirmation` - Send order confirmation

## Useful Commands

## Useful Commands

```bash

# Stop services```bash

docker-compose down# Stop services

docker-compose down

# View logs

docker-compose logs -f# View logs

docker-compose logs -f

# Access service container

docker-compose exec catalog-service bash# Access service container

```docker-compose exec catalog-service bash

```

## Configuration

## Troubleshooting

Each service has a `.env` file for configuration. See `.env.example` files in each service directory for setup details.

### Port Already in Use

## Project Structure

If you get port conflict errors, you can:

```1. Stop the service using that port

├── catalog-service/     # Product catalog API2. Change the port mapping in `docker-compose.yml`

├── checkout-service/    # Order management API

├── email-service/       # Email notification API### Database Connection Issues

├── frontend/            # Vue.js frontend

└── docker-compose.yml   # Docker configuration```bash

```# Restart MySQL container

docker-compose restart mysql

# Check MySQL logs
docker-compose logs mysql
```

### Service Won't Start

```bash
# Remove all containers and rebuild
docker-compose down
docker-compose up -d --build
```

### Permission Issues (Linux/Mac)

```bash
# Fix permissions for Laravel storage
docker-compose exec catalog-service chmod -R 777 storage bootstrap/cache
docker-compose exec checkout-service chmod -R 777 storage bootstrap/cache
docker-compose exec email-service chmod -R 777 storage bootstrap/cache
```

## AWS Deployment

### Using AWS CloudFormation

CloudFormation templates will be added in the `infrastructure/` directory for provisioning:
- EC2 instances for services
- RDS MySQL database
- Load balancer
- Security groups
- VPC configuration

### Using AWS SAM

SAM templates will be provided for serverless deployment options.

## Project Structure

```
.
├── catalog-service/        # Laravel - Product catalog microservice
├── checkout-service/       # Laravel - Order management microservice
├── email-service/          # Laravel - Email notification microservice
├── frontend/               # Vue.js - User interface
├── docker-compose.yml      # Docker Compose configuration
├── ASSIGNMENT.md          # Assignment requirements
└── README.md              # This file
```

## Environment Variables

Each service has its own `.env` file with configuration. See `.env.example` files in each service directory for required variables:

- `DB_HOST=mysql` - MySQL container hostname
- `DB_DATABASE=catalog_db` - Shared database
- `DB_USERNAME` - Database username (configured in docker-compose.yml)
- `DB_PASSWORD` - Database password (configured in docker-compose.yml)
- `APP_KEY` - Laravel application key (generate with `php artisan key:generate`)
- `MAIL_*` - Email service configuration

## Database Schema

All services share a common MySQL database (`catalog_db`) with separate tables:

- **products** - Product catalog
- **orders** - Order records
- **order_items** - Order line items
- **users** - User information

## Contributing

1. Create a feature branch
2. Make your changes
3. Write/update tests
4. Submit a pull request

## License

This project is created for the Cinch Coding Assignment.

## Support

For issues or questions, please create an issue in the GitHub repository.
