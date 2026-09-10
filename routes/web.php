<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\PublicProfileController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\ReportController;
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
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\BulkImportController;
use App\Http\Controllers\Dashboard\DomainController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminTemplateController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminDomainController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminReportModerationController;
use App\Http\Controllers\Admin\AdminBlockedDomainController;
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
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:3,1');
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
| 3. Public QR Profile Core Routes & Live API
|--------------------------------------------------------------------------
*/
Route::get('/p/{slug}', [PublicProfileController::class, 'show'])->name('profile.show');
Route::post('/p/{slug}/unlock', [PublicProfileController::class, 'unlock'])->name('profile.unlock');
Route::post('/p/{slug}/lead', [PublicProfileController::class, 'submitLead'])->name('profile.lead');
Route::post('/p/{slug}/report', [ReportController::class, 'store'])->name('profile.report');
Route::get('/p/{slug}/booking', [PublicProfileController::class, 'showBooking'])->name('profile.booking');
Route::get('/p/{slug}/contact', [PublicProfileController::class, 'downloadContact'])->name('profile.contact');
Route::get('/u/{slug}', [PublicProfileController::class, 'show'])->name('profile.show.alias');
Route::get('/u/{slug}/booking', [PublicProfileController::class, 'showBooking'])->name('profile.booking.alias');
Route::get('/u/{slug}/contact', [PublicProfileController::class, 'downloadContact'])->name('profile.contact.alias');
Route::get('/click/{profile}/{link}', [PublicProfileController::class, 'trackClick'])->name('profile.click');
Route::get('/api/slugs/check', [PublicProfileController::class, 'checkSlug'])->name('api.slugs.check');

/*
|--------------------------------------------------------------------------
| 4-10. User Dashboard Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // QR Profile Management
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/create', [ProfileController::class, 'create'])->name('profiles.create');
    Route::post('/profiles', [ProfileController::class, 'store'])->name('profiles.store');
    
    // Bulk QR Import
    Route::get('/profiles/bulk', [BulkImportController::class, 'index'])->name('profiles.bulk');
    Route::post('/profiles/bulk/preview', [BulkImportController::class, 'preview'])->name('profiles.bulk.preview');
    Route::post('/profiles/bulk/process', [BulkImportController::class, 'process'])->name('profiles.bulk.process');

    Route::get('/profiles/{id}/edit', [ProfileController::class, 'edit'])->name('profiles.edit');
    Route::get('/profiles/{id}/leads', [ProfileController::class, 'leads'])->name('profiles.leads');
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

    // Profile Data Export
    Route::get('/profiles/{id}/export/json', [ProfileController::class, 'exportJson'])->name('profiles.export.json');
    Route::get('/profiles/{id}/export/csv', [ProfileController::class, 'exportCsv'])->name('profiles.export.csv');

    // Custom Domains
    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');
    Route::post('/domains', [DomainController::class, 'store'])->name('domains.store');
    Route::post('/domains/{id}/verify', [DomainController::class, 'verify'])->name('domains.verify');
    Route::delete('/domains/{id}', [DomainController::class, 'destroy'])->name('domains.destroy');

    // Social Links & Custom Links
    Route::post('/profiles/{id}/social-links', [ProfileController::class, 'addSocialLink'])->name('profiles.social.add');
    Route::delete('/profiles/{profile}/social-links/{link}', [ProfileController::class, 'deleteSocialLink'])->name('profiles.social.delete');
    Route::post('/profiles/{id}/links', [ProfileController::class, 'addCustomLink'])->name('profiles.custom.add');
    Route::post('/profiles/{id}/custom-links', [ProfileController::class, 'addCustomLink']);
    Route::post('/profiles/{id}/custom-links/reorder', [ProfileController::class, 'reorderCustomLinks'])->name('profiles.custom.reorder');
    Route::put('/profiles/{profile}/custom-links/{link}', [ProfileController::class, 'updateCustomLink'])->name('profiles.custom.update');
    Route::delete('/profiles/{profile}/custom-links/{link}', [ProfileController::class, 'deleteCustomLink'])->name('profiles.custom.delete');

    // Billing & Subscriptions Module
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/plans', [BillingController::class, 'index'])->name('billing.plans');
    Route::get('/billing/invoices', [BillingController::class, 'invoices'])->name('billing.invoices');
    Route::get('/billing/invoices/{id}/download', [BillingController::class, 'downloadInvoice'])->name('billing.invoices.download');
    Route::get('/billing/payment-methods', [BillingController::class, 'paymentMethods'])->name('billing.payment-methods');
    Route::get('/billing/payment-method', [BillingController::class, 'paymentMethods']);
    Route::get('/billing/history', [BillingController::class, 'invoices'])->name('billing.history');
    Route::post('/billing/payment-methods/default', [BillingController::class, 'setDefaultPaymentMethod'])->name('billing.payment-methods.default');
    Route::post('/billing/payment-methods/add', [BillingController::class, 'addPaymentMethod'])->name('billing.payment-methods.add');
    Route::delete('/billing/payment-methods/{id}', [BillingController::class, 'removePaymentMethod'])->name('billing.payment-methods.remove');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::post('/billing/upgrade', [BillingController::class, 'subscribe'])->name('billing.upgrade');
    Route::post('/billing/downgrade', [BillingController::class, 'subscribe'])->name('billing.downgrade');
    Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('/billing/renew', [BillingController::class, 'renew'])->name('billing.renew');

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
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::patch('/users/{id}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');
    Route::post('/users/{id}/plan', [AdminUserController::class, 'updatePlan'])->name('users.plan');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Profile Management
    Route::get('/profiles', [AdminProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/{id}', [AdminProfileController::class, 'show'])->name('profiles.show');
    Route::post('/profiles/{id}/toggle', [AdminProfileController::class, 'toggleStatus'])->name('profiles.toggle');
    Route::patch('/profiles/{id}/status', [AdminProfileController::class, 'toggleStatus'])->name('profiles.status');
    Route::delete('/profiles/{id}', [AdminProfileController::class, 'destroy'])->name('profiles.destroy');

    // Plan Management
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');
    Route::post('/plans/{id}/features', [AdminPlanController::class, 'updateFeatures'])->name('plans.features');
    Route::post('/plans/{id}/archive', [AdminPlanController::class, 'archive'])->name('plans.archive');

    // Template Management
    Route::get('/templates', [AdminTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [AdminTemplateController::class, 'store'])->name('templates.store');
    Route::post('/templates/{id}/toggle', [AdminTemplateController::class, 'toggleStatus'])->name('templates.toggle');
    Route::post('/templates/{id}/duplicate', [AdminTemplateController::class, 'duplicate'])->name('templates.duplicate');

    // Domain Moderation
    Route::get('/domains', [AdminDomainController::class, 'index'])->name('domains.index');
    Route::post('/domains/{id}/verify', [AdminDomainController::class, 'verify'])->name('domains.verify');
    Route::post('/domains/{id}/toggle', [AdminDomainController::class, 'toggleStatus'])->name('domains.toggle');

    // Abuse Reports & Blocked Domains
    Route::get('/profile-reports', [AdminReportModerationController::class, 'index'])->name('profile-reports.index');
    Route::patch('/profile-reports/{id}/status', [AdminReportModerationController::class, 'updateStatus'])->name('profile-reports.update-status');
    Route::post('/profile-reports/{id}/suspend', [AdminReportModerationController::class, 'suspendProfile'])->name('profile-reports.suspend');
    Route::get('/blocked-domains', [AdminBlockedDomainController::class, 'index'])->name('blocked-domains.index');
    Route::post('/blocked-domains', [AdminBlockedDomainController::class, 'store'])->name('blocked-domains.store');
    Route::delete('/blocked-domains/{id}', [AdminBlockedDomainController::class, 'destroy'])->name('blocked-domains.destroy');

    // Reports & Analytics Export
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');

    // Audit Logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');

    // Global Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});
