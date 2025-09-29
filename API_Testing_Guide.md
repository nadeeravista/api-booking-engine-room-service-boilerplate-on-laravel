# 🚀 **Complete API Testing Guide**

## 📋 **Table of Contents**

1. [Server Management](#server-management)
2. [API Endpoints Testing](#api-endpoints-testing)
3. [Event System Testing](#event-system-testing)
4. [Queue System Testing](#queue-system-testing)
5. [Database Testing](#database-testing)
6. [Log Monitoring](#log-monitoring)

---

## 🖥️ **Server Management**

### **Start Laravel Server**

```bash
php artisan serve --host=0.0.0.0 --port=8000 &
```

### **Stop Laravel Server**

```bash
pkill -f "php artisan serve"
```

### **Check Server Status**

```bash
curl -s http://localhost:8000/api/rooms?property_id=550e8400-e29b-41d4-a716-446655440001 | head -1
```

---

## 🔗 **API Endpoints Testing**

### **1. GET All Rooms**

```bash
curl -X GET "http://localhost:8000/api/rooms?property_id=550e8400-e29b-41d4-a716-446655440001" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .
```

### **2. GET Specific Room**

```bash
curl -X GET "http://localhost:8000/api/rooms/01999416-09e1-70d5-9435-05884399000f" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .
```

### **3. POST Create Room**

```bash
curl -X POST "http://localhost:8000/api/rooms" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" \
  -d '{
    "name": "API Test Room",
    "description": "A room created via API testing",
    "size": "Medium",
    "amenities": ["wifi", "ac", "tv", "minibar"],
    "max_occupancy": 3,
    "base_occupancy": 2,
    "property_id": "550e8400-e29b-41d4-a716-446655440001",
    "is_active": true,
    "photos": [
      {
        "filename": "api_test_room.jpg",
        "url": "https://s3.amazonaws.com/bucket/api_test_room.jpg",
        "width": 1920,
        "height": 1080,
        "sort_order": 0,
        "is_primary": true,
        "mime_type": "image/jpeg",
        "file_size": 750000
      },
      {
        "filename": "api_test_bathroom.jpg",
        "url": "https://s3.amazonaws.com/bucket/api_test_bathroom.jpg",
        "width": 1600,
        "height": 1200,
        "sort_order": 1,
        "is_primary": false,
        "mime_type": "image/jpeg",
        "file_size": 650000
      }
    ]
  }' | jq .
```

### **4. PUT Update Room**

```bash
curl -X PUT "http://localhost:8000/api/rooms/01999416-09e1-70d5-9435-05884399000f" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" \
  -d '{
    "name": "Updated API Test Room",
    "description": "Updated description via API",
    "size": "Large",
    "amenities": ["wifi", "ac", "tv", "balcony", "kitchenette"],
    "max_occupancy": 4,
    "base_occupancy": 2,
    "property_id": "550e8400-e29b-41d4-a716-446655440001",
    "is_active": true
  }' | jq .
```

### **5. PATCH Update Room (Partial)**

```bash
curl -X PATCH "http://localhost:8000/api/rooms/01999416-09e1-70d5-9435-05884399000f" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" \
  -d '{
    "name": "Partially Updated Room",
    "max_occupancy": 5
  }' | jq .
```

### **6. DELETE Room**

```bash
curl -X DELETE "http://localhost:8000/api/rooms/01999416-09e1-70d5-9435-05884399000f" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .
```

### **7. GET Rooms by Property**

```bash
curl -X GET "http://localhost:8000/api/properties/550e8400-e29b-41d4-a716-446655440001/rooms" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .
```

### **8. Test Validation Errors**

```bash
# Missing property_id
curl -X GET "http://localhost:8000/api/rooms" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .

# Invalid room data
curl -X POST "http://localhost:8000/api/rooms" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fake-token-for-testing" \
  -d '{
    "name": "",
    "max_occupancy": "invalid"
  }' | jq .
```

---

## 🎯 **Event System Testing**

### **Test 1: Direct Price Update (Triggers Event)**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Testing price update event...' . PHP_EOL;
\$price = \App\Models\RoomPrice::find('01999416-1279-7242-b05f-0aa8c7caf06a');
if (\$price) {
    echo 'Current price: ' . \$price->price . PHP_EOL;
    \$price->price = 300.00;
    \$price->save();
    echo 'Updated price to: ' . \$price->price . PHP_EOL;
    echo '✅ Event triggered! Check logs for: Price distribution triggered' . PHP_EOL;
} else {
    echo '❌ Price not found!' . PHP_EOL;
}
"
```

### **Test 2: Multiple Price Updates**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Testing multiple price updates...' . PHP_EOL;
\$prices = \App\Models\RoomPrice::take(3)->get();
foreach(\$prices as \$index => \$price) {
    \$newPrice = 100 + (\$index * 50);
    \$price->price = \$newPrice;
    \$price->save();
    echo 'Updated price ' . (\$index + 1) . ' to: ' . \$newPrice . PHP_EOL;
}
echo '✅ All events triggered! Check logs.' . PHP_EOL;
"
```

### **Test 3: Currency Change Event**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Testing currency change event...' . PHP_EOL;
\$price = \App\Models\RoomPrice::first();
if (\$price) {
    echo 'Current currency: ' . \$price->currency . PHP_EOL;
    \$price->currency = 'EUR';
    \$price->save();
    echo 'Updated currency to: ' . \$price->currency . PHP_EOL;
    echo '✅ Currency change event triggered!' . PHP_EOL;
} else {
    echo '❌ No prices found!' . PHP_EOL;
}
"
```

### **Test 4: Availability Change Event**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Testing availability change event...' . PHP_EOL;
\$price = \App\Models\RoomPrice::first();
if (\$price) {
    echo 'Current availability: ' . (\$price->is_available ? 'true' : 'false') . PHP_EOL;
    \$price->is_available = !\$price->is_available;
    \$price->save();
    echo 'Updated availability to: ' . (\$price->is_available ? 'true' : 'false') . PHP_EOL;
    echo '✅ Availability change event triggered!' . PHP_EOL;
} else {
    echo '❌ No prices found!' . PHP_EOL;
}
"
```

---

## ⚡ **Queue System Testing**

### **Test 1: Check Queue Configuration**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Queue Configuration:' . PHP_EOL;
echo 'Driver: ' . config('queue.default') . PHP_EOL;
echo 'Connection: ' . config('queue.connections.database.connection') . PHP_EOL;
echo 'Table: ' . config('queue.connections.database.table') . PHP_EOL;
"
```

### **Test 2: Dispatch Queue Jobs**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Dispatching queue jobs...' . PHP_EOL;

// Dispatch ModeratePhoto job
\App\Jobs\ModeratePhoto::dispatch(
    'room-123',
    'photo-456',
    'rooms/room-123/photo.jpg'
);
echo '✅ ModeratePhoto job dispatched' . PHP_EOL;

// Dispatch GenerateThumbnail job
\App\Jobs\GenerateThumbnail::dispatch(
    'room-123',
    'photo-456',
    'rooms/room-123/photo.jpg'
);
echo '✅ GenerateThumbnail job dispatched' . PHP_EOL;

echo 'Check jobs table for pending jobs!' . PHP_EOL;
"
```

### **Test 3: Run Queue Worker**

```bash
# Start queue worker (run in separate terminal)
php artisan queue:work --verbose --tries=3 --timeout=90
```

### **Test 4: Process Jobs Manually**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Processing jobs manually...' . PHP_EOL;

// Create mock repository
\$mockRepo = \Mockery::mock(\App\Contracts\PhotoRepositoryInterface::class);
\$mockRepo->shouldReceive('update')->andReturn(new \App\Models\RoomPhoto());

// Process ModeratePhoto job
\$job = new \App\Jobs\ModeratePhoto('room-123', 'photo-456', 'rooms/room-123/photo.jpg');
\$job->handle(\$mockRepo);
echo '✅ ModeratePhoto job processed' . PHP_EOL;

// Process GenerateThumbnail job
\$job = new \App\Jobs\GenerateThumbnail('room-123', 'photo-456', 'rooms/room-123/photo.jpg');
\$job->handle(\$mockRepo);
echo '✅ GenerateThumbnail job processed' . PHP_EOL;

echo '✅ All jobs processed successfully!' . PHP_EOL;
"
```

### **Test 5: Check Queue Status**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Queue Status:' . PHP_EOL;
echo 'Pending jobs: ' . \DB::table('jobs')->count() . PHP_EOL;
echo 'Failed jobs: ' . \DB::table('failed_jobs')->count() . PHP_EOL;

\$jobs = \DB::table('jobs')->get();
foreach(\$jobs as \$job) {
    echo 'Job: ' . \$job->queue . ' - ' . \$job->payload . PHP_EOL;
}
"
```

---

## 🗄️ **Database Testing**

### **Test 1: Check Database Connection**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Database Connection Test:' . PHP_EOL;
echo 'Connection: ' . \DB::connection()->getDatabaseName() . PHP_EOL;
echo 'Driver: ' . \DB::connection()->getDriverName() . PHP_EOL;
echo 'Status: ' . (\DB::connection()->getPdo() ? '✅ Connected' : '❌ Failed') . PHP_EOL;
"
```

### **Test 2: Check Table Counts**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Database Table Counts:' . PHP_EOL;
echo 'Rooms: ' . \App\Models\Room::count() . PHP_EOL;
echo 'Rate Plans: ' . \App\Models\RoomRatePlan::count() . PHP_EOL;
echo 'Prices: ' . \App\Models\RoomPrice::count() . PHP_EOL;
echo 'Photos: ' . \App\Models\RoomPhoto::count() . PHP_EOL;
echo 'Jobs: ' . \DB::table('jobs')->count() . PHP_EOL;
"
```

### **Test 3: Create Test Data**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Creating test data...' . PHP_EOL;

// Create a test room
\$room = \App\Models\Room::create([
    'name' => 'Test Room for API',
    'description' => 'A room created for API testing',
    'size' => 'Medium',
    'amenities' => ['wifi', 'ac', 'tv'],
    'max_occupancy' => 3,
    'base_occupancy' => 2,
    'property_id' => '550e8400-e29b-41d4-a716-446655440001',
    'is_active' => true
]);
echo '✅ Room created: ' . \$room->id . PHP_EOL;

// Create a rate plan
\$ratePlan = \App\Models\RoomRatePlan::create([
    'room_id' => \$room->id,
    'name' => 'Standard Rate',
    'description' => 'Standard rate plan',
    'is_active' => true
]);
echo '✅ Rate plan created: ' . \$ratePlan->id . PHP_EOL;

// Create prices
for (\$i = 0; \$i < 7; \$i++) {
    \$price = \App\Models\RoomPrice::create([
        'room_rate_plan_id' => \$ratePlan->id,
        'date' => now()->addDays(\$i),
        'price' => 100 + (\$i * 10),
        'currency' => 'USD',
        'is_available' => true
    ]);
}
echo '✅ 7 prices created' . PHP_EOL;

echo '✅ Test data created successfully!' . PHP_EOL;
"
```

---

## 📊 **Log Monitoring**

### **Monitor All Logs**

```bash
tail -f storage/logs/laravel.log
```

### **Check Event Logs**

```bash
grep "Price distribution triggered" storage/logs/laravel.log
```

### **Check Queue Logs**

```bash
grep -E "(ModeratePhoto|GenerateThumbnail)" storage/logs/laravel.log
```

### **Check API Logs**

```bash
grep -E "(GET|POST|PUT|DELETE)" storage/logs/laravel.log
```

### **Clear Logs**

```bash
echo "" > storage/logs/laravel.log
```

---

## 🧪 **Complete Test Suite**

### **Run All Tests**

```bash
# 1. Start server
php artisan serve --host=0.0.0.0 --port=8000 &

# 2. Test API endpoints
curl -X GET "http://localhost:8000/api/rooms?property_id=550e8400-e29b-41d4-a716-446655440001" \
  -H "Authorization: Bearer fake-token-for-testing" | jq .

# 3. Test event system
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$price = \App\Models\RoomPrice::first();
\$price->price = 999.99;
\$price->save();
echo 'Event triggered!' . PHP_EOL;
"

# 4. Test queue system
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\App\Jobs\ModeratePhoto::dispatch('room-123', 'photo-456', 'test.jpg');
echo 'Job dispatched!' . PHP_EOL;
"

# 5. Check logs
tail -n 10 storage/logs/laravel.log

# 6. Run unit tests
vendor/bin/phpunit --no-coverage
```

---

## 🎯 **Quick Verification Commands**

### **Health Check**

```bash
curl -s http://localhost:8000/api/rooms?property_id=550e8400-e29b-41d4-a716-446655440001 | jq '. | length'
```

### **Event System Check**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$price = \App\Models\RoomPrice::first();
\$price->price = 123.45;
\$price->save();
" && tail -n 1 storage/logs/laravel.log
```

### **Queue System Check**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\App\Jobs\ModeratePhoto::dispatch('test', 'test', 'test');
" && echo "Jobs in queue: $(php -r 'require \"vendor/autoload.php\"; \$app = require \"bootstrap/app.php\"; \$app->make(\"Illuminate\Contracts\Console\Kernel\")->bootstrap(); echo \DB::table(\"jobs\")->count();')"
```

---

## 📝 **Notes**

-   **Server**: Make sure Laravel server is running on port 8000
-   **Database**: Ensure PostgreSQL connection is working
-   **Queue**: Use `database` driver for queue testing
-   **Logs**: All events and jobs are logged to `storage/logs/laravel.log`
-   **Testing**: Use `jq` for JSON formatting in curl responses

This comprehensive guide covers all API endpoints, event system, queue system, and database testing! 🚀

---

## 🔧 **Environment Setup**

### **Prerequisites**

-   PHP 8.3+
-   PostgreSQL
-   Composer
-   jq (for JSON formatting)

### **Install Dependencies**

```bash
composer install
```

### **Database Setup**

```bash
php artisan migrate
php artisan db:seed
```

### **Queue Setup**

```bash
php artisan queue:table
php artisan migrate
```

---

## 📚 **Additional Resources**

-   **Laravel Documentation**: https://laravel.com/docs
-   **API Documentation**: http://localhost:8000/api/documentation
-   **Swagger UI**: http://localhost:8000/api/documentation
-   **Queue Documentation**: https://laravel.com/docs/queues

---

**Created**: $(date)
**Version**: 1.0
**Laravel API Rooms Microservice Testing Guide**
