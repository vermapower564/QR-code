<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\PublicProfileController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\OnboardingController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\QRCodeController;
use App\Http\Controllers\Dashboard\AnalyticsController;
use App\Http\Controllers\Dashboard\BillingController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminTemplateController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| 1. Public Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicProfileController::class, 'home'])->name('home');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('contact.submit');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/cookie-policy', [PageController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/refund-policy', [PageController::class, 'refundPolicy'])->name('refund-policy');

/*
|--------------------------------------------------------------------------
| 2. Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->name('verification.send');

    // Onboarding Flow Routes
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
});

/*
|--------------------------------------------------------------------------
| 3. Public QR Profile Core Routes
|--------------------------------------------------------------------------
*/
Route::get('/p/{slug}', [PublicProfileController::class, 'show'])->name('profile.show');
Route::get('/p/{slug}/booking', [PublicProfileController::class, 'showBooking'])->name('profile.booking');
Route::get('/p/{slug}/contact', [PublicProfileController::class, 'downloadContact'])->name('profile.contact');
Route::get('/click/{profile}/{link}', [PublicProfileController::class, 'trackClick'])->name('profile.click');

/*
|--------------------------------------------------------------------------
| 4-10. User Dashboard Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // QR Profile Management
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/create', [ProfileController::class, 'create'])->name('profiles.create');
    Route::post('/profiles', [ProfileController::class, 'store'])->name('profiles.store');
    Route::get('/profiles/{id}/edit', [ProfileController::class, 'edit'])->name('profiles.edit');
    Route::put('/profiles/{id}', [ProfileController::class, 'update'])->name('profiles.update');
    Route::patch('/profiles/{id}', [ProfileController::class, 'update']);
    Route::delete('/profiles/{id}', [ProfileController::class, 'destroy'])->name('profiles.destroy');
    Route::post('/profiles/{id}/duplicate', [ProfileController::class, 'duplicate'])->name('profiles.duplicate');
    Route::patch('/profiles/{id}/status', [ProfileController::class, 'toggleStatus'])->name('profiles.status');
    Route::post('/profiles/{id}/toggle', [ProfileController::class, 'toggleStatus'])->name('profiles.toggle');
    Route::get('/profiles/{id}/preview', [ProfileController::class, 'edit'])->name('profiles.preview');
    Route::get('/profiles/{id}/analytics', [AnalyticsController::class, 'show'])->name('profiles.analytics');
    Route::get('/profiles/{id}/qr', [QRCodeController::class, 'show'])->name('profiles.qr');
    Route::get('/profiles/{id}/qr/download/{format}', [QRCodeController::class, 'download'])->name('profiles.qr.download.format');
    Route::get('/profiles/{id}/qr/download', [QRCodeController::class, 'download'])->name('profiles.qr.download');
    Route::post('/profiles/{id}/qr/generate', [QRCodeController::class, 'update'])->name('profiles.qr.generate');
    Route::post('/profiles/{id}/qr', [QRCodeController::class, 'update'])->name('profiles.qr.update');

    // Social Links & Custom Links
    Route::post('/profiles/{id}/social-links', [ProfileController::class, 'addSocialLink'])->name('profiles.social.add');
    Route::delete('/profiles/{profile}/social-links/{link}', [ProfileController::class, 'deleteSocialLink'])->name('profiles.social.delete');
    Route::post('/profiles/{id}/links', [ProfileController::class, 'addCustomLink'])->name('profiles.custom.add');
    Route::post('/profiles/{id}/custom-links', [ProfileController::class, 'addCustomLink']);
    Route::delete('/profiles/{profile}/custom-links/{link}', [ProfileController::class, 'deleteCustomLink'])->name('profiles.custom.delete');

    // Billing & Subscriptions Module
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/plans', [BillingController::class, 'index'])->name('billing.plans');
    Route::get('/billing/invoices', [BillingController::class, 'invoices'])->name('billing.invoices');
    Route::get('/billing/invoices/{id}/download', [BillingController::class, 'downloadInvoice'])->name('billing.invoices.download');
    Route::get('/billing/payment-methods', [BillingController::class, 'paymentMethods'])->name('billing.payment-methods');
    Route::post('/billing/payment-methods/default', [BillingController::class, 'setDefaultPaymentMethod'])->name('billing.payment-methods.default');
    Route::post('/billing/payment-methods/add', [BillingController::class, 'addPaymentMethod'])->name('billing.payment-methods.add');
    Route::delete('/billing/payment-methods/{id}', [BillingController::class, 'removePaymentMethod'])->name('billing.payment-methods.remove');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::post('/billing/upgrade', [BillingController::class, 'subscribe'])->name('billing.upgrade');
    Route::post('/billing/downgrade', [BillingController::class, 'subscribe'])->name('billing.downgrade');
    Route::post('/billing/cancel', [BillingController::class, 'subscribe'])->name('billing.cancel');
    Route::post('/billing/renew', [BillingController::class, 'subscribe'])->name('billing.renew');

    // Account Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword']);
});

/*
|--------------------------------------------------------------------------
| 11. Payment Webhooks (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripe'])->name('webhooks.stripe');
Route::post('/webhooks/razorpay', [WebhookController::class, 'handleRazorpay'])->name('webhooks.razorpay');

/*
|--------------------------------------------------------------------------
| 14-22. Admin Panel Routes (Auth + Admin Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::patch('/users/{id}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');
    Route::post('/users/{id}/plan', [AdminUserController::class, 'updatePlan'])->name('users.plan');

    // Profiles Moderation
    Route::get('/profiles', [AdminProfileController::class, 'index'])->name('profiles.index');
    Route::post('/profiles/{id}/toggle', [AdminProfileController::class, 'toggleStatus'])->name('profiles.toggle');
    Route::patch('/profiles/{id}/status', [AdminProfileController::class, 'toggleStatus'])->name('profiles.status');

    // Plans
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');

    // Templates
    Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [AdminTemplateController::class, 'store'])->name('templates.store');

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

    // Global Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [AdminSettingsController::class, 'index']);
});
