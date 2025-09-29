<?php

use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Strict REST API Routes for Room Rates & Prices Microservice
// Auth0 middleware is conditionally enabled via AUTH0_ENABLED environment variable

// Manual Route Definitions (Alternative to apiResource)
Route::middleware('auth0')->group(function () {
    Route::get('rooms', [RoomController::class, 'index']);           // GET /api/rooms
    Route::post('rooms', [RoomController::class, 'store']);         // POST /api/rooms
    Route::get('rooms/{id}', [RoomController::class, 'show']);      // GET /api/rooms/{id}
    Route::put('rooms/{id}', [RoomController::class, 'update']);    // PUT /api/rooms/{id}
    Route::patch('rooms/{id}', [RoomController::class, 'update']);  // PATCH /api/rooms/{id}
    Route::delete('rooms/{id}', [RoomController::class, 'destroy']); // DELETE /api/rooms/{id}

    // Additional RESTful endpoints
    Route::get('properties/{property}/rooms', [RoomController::class, 'getRoomsByProperty']);
});

// Or use apiResource for automatic REST routes
// Route::middleware('auth0')->apiResource('rooms', RoomController::class);
