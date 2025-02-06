<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\LoginController;

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('')
Route::get('/', [DataTableController::class, 'index'])->name('index');
Route::post('/data', [DataTableController::class, 'getData'])->name('data.get');
Route::post('/export', [DataTableController::class, 'exportCSV'])->name('data.export');
