<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MQLAccountController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\LogController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');


// Admin Dashboard
Route::prefix('admin')->middleware('auth', 'isAdmin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // License Management
    Route::resource('licenses', LicenseController::class);

    // Order Management
    Route::resource('orders', OrderController::class);

    // Account Management
    Route::resource('mql-accounts', MQLAccountController::class);

    // User Management
    Route::resource('users', UserController::class);

    // System Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('settings', [SettingsController::class, 'update'])->name('admin.settings.update');

    // Profile Settings
    Route::get('profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('admin.profile.update');

    // Logs and Audits
    Route::get('logs', [LogController::class, 'index'])->name('admin.logs');

    // Logout
    Route::post('logout', [ProfileController::class, 'logout'])->name('admin.logout');
});
