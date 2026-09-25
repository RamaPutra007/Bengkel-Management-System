<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Khusus untuk Admin yang diakses dari halaman web/ajax
Route::middleware(['auth:sanctum', 'role:ADMIN'])->prefix('admin')->group(function () {
    Route::get('/customer-vehicles/{customer}', [\App\Http\Controllers\Admin\ServiceOrderController::class, 'getCustomerVehicles'])->name('admin.api.customer.vehicles');
});

// API Publik tanpa middleware (Webhook)
Route::post('webhook/qris', [\App\Http\Controllers\WebhookController::class, 'handleQris']);
