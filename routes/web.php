<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [LabController::class, 'index'])->name('dashboard');
    Route::post('/patients', [LabController::class, 'storePatient'])->name('patients.store');
    Route::put('/patients/{patient}', [LabController::class, 'updatePatient'])->name('patients.update');
    Route::delete('/patients/{patient}', [LabController::class, 'destroyPatient'])->name('patients.destroy');
    Route::post('/tests', [LabController::class, 'storeTest'])->name('tests.store');
    Route::post('/orders', [LabController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{order}/receipt', [LabController::class, 'printReceipt'])->name('orders.receipt');
    Route::get('/orders/{order}/report', [LabController::class, 'printReport'])->name('orders.report');
    Route::patch('/orders/{order}/status', [LabController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/results', [LabController::class, 'updateResults'])->name('orders.results');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
