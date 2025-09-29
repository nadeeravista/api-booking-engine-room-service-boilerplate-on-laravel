<?php

use Illuminate\Support\Facades\Route;

// Simple welcome page for API documentation
Route::get('/', function () {
    return view('welcome');
});
