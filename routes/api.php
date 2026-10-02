<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Khusus untuk Admin yang diakses dari halaman web/ajax
// (Sudah dipindah ke routes/web.php untuk menghindari masalah auth:sanctum)


// API Publik tanpa middleware (Webhook)
Route::post('webhook/qris', [\App\Http\Controllers\WebhookController::class, 'handleQris']);
