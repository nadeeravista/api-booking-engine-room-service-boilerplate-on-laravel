# 🏨 **Laravel API Rooms Service**

A comprehensive Laravel-based microservice for managing room rates and prices with advanced features including photo processing, event-driven architecture, and queue-based job processing.

## 📋 **Table of Contents**

-   [Features](#features)
-   [Architecture](#architecture)
-   [Quick Start](#quick-start)
-   [API Documentation](#api-documentation)
-   [Testing](#testing)
-   [Code Quality](#code-quality)
-   [AWS LocalStack Setup](#aws-localstack-setup)
-   [Development Commands](#development-commands)

---

## ✨ **Features**

### **Core Functionality**

-   🏠 **Room Management**
-   💰 **Rate Plans & Pricing** - Price and rete plan assignment
-   📸 **Photo Processing** - S3 integration with moderation and thumbnail generation
-   🔄 **Event-Driven Architecture** - Real-time price change notifications - OTA price distribution
-   ⚡ **Queue Processing** - Background job processing for photo operations, Moderation and thumbnail creation

### **Technical Features**

-   🛡️ **Auth0 Integration** - JWT-based authentication - Auht0 Middleware
-   📚 **OpenAPI Documentation** - Interactive Swagger UI
-   🧪 **Comprehensive Testing** - Unit and Feature, tests with layer mocking
-   📊 **Code Coverage** - test coverage with detailed reports
-   🔧 **Code Quality** - PHPStan, PHP CS Fixer, and linting
-   🏗️ **Repository Pattern** - Clean architecture with dependency injection and repositories

---

## 🏗️ **Architecture**

### **Service Layer**

-   **RoomService** - Business logic for room operations
-   **PhotoService** - Photo processing and S3 integration
-   **Repository Pattern** - Data access abstraction

### **Event System**

-   **RoomRateChanged** - Triggers when prices change
-   **DistributeRatesOnPriceChange** - Listener for price distribution

### **Queue Jobs**

-   **ModeratePhoto** - Photo content moderation
-   **GenerateThumbnail** - Thumbnail generation

### **Database Schema**

-   **rooms** - Room information and metadata
-   **room_rate_plans** - Rate plan definitions
-   **room_prices** - Date-based pricing
-   **room_photos** - Photo metadata and S3 details

---

## 🚀 **Quick Start**

### **Prerequisites**

-   PHP 8.3+
-   PostgreSQL
-   Composer
-   Docker (for LocalStack)

### **Installation**

```bash

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed
```

### **Start Development Environment**

```bash
# Option 1: Full development environment (recommended)
composer run dev

# Option 2: Manual setup
php artisan serve --host=0.0.0.0 --port=8000 &
php artisan queue:work --verbose &
```

### **Verify Installation**

```bash
# Test API endpoint
curl -X GET "http://localhost:8000/api/rooms?property_id=550e8400-e29b-41d4-a716-446655440001" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .

# Run tests
vendor/bin/phpunit --no-coverage
```

---

## 📚 **API Documentation**

### **Interactive Documentation**

-   **Swagger UI**: `http://localhost:8000/api/documentation`
-   **OpenAPI JSON**: `http://localhost:8000/api/documentation.json`

### **API Endpoints**

#### **Rooms**

```bash
# Get all rooms
GET /api/rooms?property_id={property_id}

# Get specific room
GET /api/rooms/{id}

# Create room
POST /api/rooms

# Update room
PUT /api/rooms/{id}
PATCH /api/rooms/{id}

# Delete room
DELETE /api/rooms/{id}

# Get rooms by property
GET /api/properties/{property_id}/rooms
```

#### **Authentication**

-   **Auth0 JWT** - Bearer token authentication
-   **Conditional Auth** - Enable/disable via `AUTH0_ENABLED` environment variable

---

## 🧪 **Testing**

### **Test Coverage**

![Test Coverage](docs/coverage.png)

### **Test Results**

![Test Results](docs/tests.png)

### **Run Tests**

```bash
# Run all tests
vendor/bin/phpunit --no-coverage

# Run with coverage
composer run test:coverage

# Run specific test suites
composer run test:unit
composer run test:feature
```

### **Test Statistics**

-   **✅ 40 tests passing**
-   **✅ 114 assertions**
-   **✅ 100% code coverage**
-   **✅ 0 failures**

---

## 🔧 **Code Quality**

### **Linting & Formatting**

```bash
# Check code formatting issues (dry run)
composer lint

# Fix code formatting automatically
composer lint:fix

# Run static analysis
composer analyze

# Run all quality checks
composer run check
```

### **Quality Tools**

-   **PHP CS Fixer** - Code formatting
-   **PHPStan** - Static analysis
-   **PHPUnit** - Testing framework
-   **Mockery** - Mocking library

---

## ☁️ **AWS LocalStack Setup**

### **Install LocalStack**

```bash
# Run LocalStack container
docker run --rm -it -p 4566:4566 -p 4571:4571 localstack/localstack
```

### **Configure AWS CLI**

```bash
export AWS_ACCESS_KEY_ID=test
export AWS_SECRET_ACCESS_KEY=test
export AWS_DEFAULT_REGION=us-east-1
```

### **S3 Operations**

```bash
# List S3 buckets
aws --endpoint-url=http://localhost:4566 s3 ls

# Create S3 bucket
aws --endpoint-url=http://localhost:4566 s3 mb s3://property-photos
```

### **LocalStack Web UI**

-   **Resources**: https://app.localstack.cloud/inst/default/resources
-   **S3 Browser**: https://app.localstack.cloud/inst/default/resources/s3

### **Screenshots**

![LocalStack AWS](docs/localstack-aws.png)
![LocalStack AWS S3](docs/localstack-aws-s3.png)

---

## 🛠️ **Development Commands**

### **Laravel Commands**

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Generate Swagger docs
php artisan l5-swagger:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### **Composer Scripts**

```bash
# Development
composer run dev          # Start server + queue + logs
composer run test         # Run all tests
composer run test:unit    # Run unit tests
composer run test:feature # Run feature tests
composer run test:coverage # Run with coverage

# Code Quality
composer run lint         # Check code style
composer run lint:fix     # Fix code style
composer run analyze      # Run PHPStan
composer run check        # Run all checks
```

### **Environment Variables**

```bash
# Auth0 Configuration
AUTH0_ENABLED=true
AUTH0_DOMAIN=your-domain.auth0.com
AUTH0_AUDIENCE=your-api-identifier

# Database
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Queue
QUEUE_CONNECTION=database
```

---

## 📊 **Project Structure**

```
app/
├── Contracts/           # Service interfaces
├── Events/             # Event classes
├── Http/
│   ├── Controllers/    # API controllers
│   ├── Middleware/     # Custom middleware
│   ├── Requests/      # Validation requests
│   └── Resources/      # API resources
├── Jobs/               # Queue jobs
├── Listeners/          # Event listeners
├── Models/             # Eloquent models
├── Providers/          # Service providers
├── Repositories/       # Data access layer
└── Services/           # Business logic

tests/
├── Feature/           # Integration tests
└── Unit/              # Unit tests
    ├── Jobs/          # Job tests
    ├── Listeners/     # Listener tests
    └── Services/      # Service tests
```

---

## 🎯 **Key Features summary**

-   ✅ Price change events
-   ✅ Event listeners
-   ✅ Real-time notifications
-   ✅ Serverless identity management
-   ✅ Background processing
-   ✅ Dependency injection
-   ✅ Testable code
-   ✅ Unit tests
-   ✅ Feature tests
-   ✅ Code coverage
-   ✅ Mocking strategies

---

## 📄 **License**

This project is licensed under the MIT License.

---

**Created**: 2025-09-29  
**Version**: 1.0  
**Laravel API Rooms Microservice**
