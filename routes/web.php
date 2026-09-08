<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\PublicProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\QRCodeController;
use App\Http\Controllers\Dashboard\AnalyticsController;
use App\Http\Controllers\Dashboard\BillingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminTemplateController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicProfileController::class, 'home'])->name('home');
Route::get('/p/{slug}', [PublicProfileController::class, 'show'])->name('profile.show');
Route::get('/p/{slug}/contact', [PublicProfileController::class, 'downloadContact'])->name('profile.contact');
Route::get('/click/{profile}/{link}', [PublicProfileController::class, 'trackClick'])->name('profile.click');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| User Dashboard Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Profiles Management
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/create', [ProfileController::class, 'create'])->name('profiles.create');
    Route::post('/profiles', [ProfileController::class, 'store'])->name('profiles.store');
    Route::get('/profiles/{id}/edit', [ProfileController::class, 'edit'])->name('profiles.edit');
    Route::put('/profiles/{id}', [ProfileController::class, 'update'])->name('profiles.update');
    Route::post('/profiles/{id}/toggle', [ProfileController::class, 'toggleStatus'])->name('profiles.toggle');
    Route::post('/profiles/{id}/duplicate', [ProfileController::class, 'duplicate'])->name('profiles.duplicate');
    Route::delete('/profiles/{id}', [ProfileController::class, 'destroy'])->name('profiles.destroy');

    // Profile Links Management
    Route::post('/profiles/{id}/social-links', [ProfileController::class, 'addSocialLink'])->name('profiles.social.add');
    Route::delete('/profiles/{profile}/social-links/{link}', [ProfileController::class, 'deleteSocialLink'])->name('profiles.social.delete');
    Route::post('/profiles/{id}/custom-links', [ProfileController::class, 'addCustomLink'])->name('profiles.custom.add');
    Route::delete('/profiles/{profile}/custom-links/{link}', [ProfileController::class, 'deleteCustomLink'])->name('profiles.custom.delete');

    // Dynamic QR Generator & Download
    Route::get('/profiles/{id}/qr', [QRCodeController::class, 'show'])->name('profiles.qr');
    Route::post('/profiles/{id}/qr', [QRCodeController::class, 'update'])->name('profiles.qr.update');
    Route::get('/profiles/{id}/qr/download', [QRCodeController::class, 'download'])->name('profiles.qr.download');

    // Profile Analytics
    Route::get('/profiles/{id}/analytics', [AnalyticsController::class, 'show'])->name('profiles.analytics');

    // Billing & Subscriptions
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Auth + Admin Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{id}/plan', [AdminUserController::class, 'updatePlan'])->name('users.plan');

    // Plans
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');

    // Templates
    Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [AdminTemplateController::class, 'store'])->name('templates.store');
});
