<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\AuthController;

// Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DataTableController::class, 'index'])->name('dashboard');
    Route::get('/data', [DataTableController::class, 'getData'])->name('data.get');
    Route::get('/export', [DataTableController::class, 'exportCSV'])->name('data.export');
});
