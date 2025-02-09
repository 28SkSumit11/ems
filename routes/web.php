<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\CheckLoggedIn;

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware([CheckLoggedIn::class])->group(function(){
    Route::get('/', [DataTableController::class, 'index'])->name('index');
    Route::get('/data', [DataTableController::class, 'getData'])->name('data.get');
    Route::get('/view/{id}', [DataTableController::class, 'view'])->name('data.view');
    Route::get('/export', [DataTableController::class, 'exportCSV'])->name('data.export');
});
