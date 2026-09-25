<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('OWNER')) {
        return redirect()->route('owner.dashboard');
    }
    if ($user->hasRole('ADMIN')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->hasRole('KASIR')) {
        return redirect()->route('kasir.dashboard');
    }
    abort(403, 'Unauthorized action.');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:OWNER'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/operasional', [\App\Http\Controllers\Owner\OperationalController::class, 'index'])->name('operasional');
    Route::get('/inventaris', [\App\Http\Controllers\Owner\InventoryController::class, 'index'])->name('inventaris');
    Route::get('/keuangan', [\App\Http\Controllers\Owner\FinanceController::class, 'index'])->name('keuangan');
    Route::get('/laporan', [\App\Http\Controllers\Owner\ReportController::class, 'index'])->name('laporan');
    Route::resource('pengguna', \App\Http\Controllers\Owner\UserController::class);
});

Route::middleware(['auth', 'role:ADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Placeholder Routes
    Route::post('booking/{id}/accept', [\App\Http\Controllers\Admin\DashboardController::class, 'acceptBooking'])->name('booking.accept');
    Route::resource('customer', \App\Http\Controllers\Admin\CustomerController::class);
    Route::resource('vehicle', \App\Http\Controllers\Admin\VehicleController::class);
    Route::resource('service-order', \App\Http\Controllers\Admin\ServiceOrderController::class);
    Route::resource('inspection', \App\Http\Controllers\Admin\InspectionController::class);
    Route::resource('service', \App\Http\Controllers\Admin\ServiceController::class);
    Route::resource('mechanic', \App\Http\Controllers\Admin\MechanicController::class);
    Route::resource('sparepart', \App\Http\Controllers\Admin\SparepartController::class);
    Route::resource('inventory-transaction', \App\Http\Controllers\Admin\InventoryTransactionController::class)->only(['index', 'create', 'store']);
    Route::post('invoice/{invoice}/send-qris', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendQris'])->name('invoice.send-qris');
    Route::get('invoice/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('invoice.print');
    Route::resource('invoice', \App\Http\Controllers\Admin\InvoiceController::class);
    Route::get('/laporan', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('laporan.index');
    Route::get('/whatsapp', [\App\Http\Controllers\Admin\WhatsappController::class, 'index'])->name('whatsapp.index');
    Route::post('/whatsapp/disconnect', [\App\Http\Controllers\Admin\WhatsappController::class, 'disconnect'])->name('whatsapp.disconnect');
    Route::get('/whatsapp/config', [\App\Http\Controllers\Admin\WhatsappController::class, 'config'])->name('whatsapp.config');
    Route::post('/whatsapp/config', [\App\Http\Controllers\Admin\WhatsappController::class, 'updateConfig'])->name('whatsapp.updateConfig');
    Route::get('/whatsapp/template', [\App\Http\Controllers\Admin\WhatsappController::class, 'template'])->name('whatsapp.template');
    Route::put('/whatsapp/template/{id}', [\App\Http\Controllers\Admin\WhatsappController::class, 'updateTemplate'])->name('whatsapp.updateTemplate');
});

Route::middleware(['auth', 'role:KASIR'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Kasir\DashboardController::class, 'index'])->name('dashboard');
    
    // Kasir Placeholder Routes
    Route::get('/invoice', function() { return view('placeholder', ['title' => 'Invoice']); })->name('invoice.index');
    Route::get('/payment', function() { return view('placeholder', ['title' => 'Pembayaran']); })->name('payment.index');
    Route::get('/transaction-history', function() { return view('placeholder', ['title' => 'Riwayat Transaksi']); })->name('transaction-history.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
