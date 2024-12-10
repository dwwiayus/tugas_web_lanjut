<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // Add this line

// Define your routes below
Route::get('/admin/profile', [AdminController::class, 'profile']);
