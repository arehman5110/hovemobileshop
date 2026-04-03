<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhoneDealController;
use App\Http\Controllers\RepairTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

// ── Auth (public) ─────────────────────────────────────────────────
Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout',[AuthController::class, 'logout'])->name('logout');

// ── All protected routes ──────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Stock
    Route::resource('categories',   CategoryController::class)->except(['show','create','edit']);
    Route::resource('parts',        PartController::class)->except(['show']);
    Route::post('parts/{part}/topup',  [PartController::class, 'topup'])->name('parts.topup');
    Route::get('parts-report',         [PartController::class, 'report'])->name('parts.report');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/json', [CustomerController::class, 'json'])->name('customers.json');
    // Repair types
    Route::resource('repair-types', RepairTypeController::class)->except(['show','create','edit']);

    // Vouchers
    Route::resource('vouchers', VoucherController::class)->except(['show','create','edit']);
    Route::get('vouchers/{voucher}/print', [VoucherController::class, 'printView'])->name('vouchers.print');
    Route::post('vouchers/{voucher}/email',[VoucherController::class, 'sendEmail'])->name('vouchers.email');
    Route::post('jobs/{job}/remove-voucher', [JobController::class, 'removeVoucher'])->name('jobs.remove-voucher');
    // Route::post('jobs/{job}/remove-voucher', [JobController::class, 'removeVoucher'])->name('jobs.remove-voucher');
    // Settings
    Route::get('settings',  [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings',  [SettingController::class, 'update'])->name('settings.update');

    // Phone Deals
    Route::resource('phone-deals', PhoneDealController::class);
    Route::post('phone-deals/{phoneDeal}/send-email',  [PhoneDealController::class, 'sendEmail'])->name('phone-deals.send-email');
    Route::get('phone-deals/{phoneDeal}/receipt',      [PhoneDealController::class, 'receipt'])->name('phone-deals.receipt');
    Route::post('phone-deals/{phoneDeal}/payments',    [App\Http\Controllers\PhoneDealController::class, 'addPayment'])->name('deal-payments.store');
    Route::delete('deal-payments/{payment}',           [App\Http\Controllers\PhoneDealController::class, 'deletePayment'])->name('deal-payments.destroy');

    // Inventory
    Route::resource('inventory', App\Http\Controllers\InventoryController::class)->except(['show']);
    Route::resource('device-categories', App\Http\Controllers\DeviceCategoryController::class)->except(['show','create','edit']);

    // Terms & Conditions
    Route::resource('terms', App\Http\Controllers\TermsConditionController::class)->except(['show','create','edit']);
    Route::get('terms-get/{type}', [App\Http\Controllers\TermsConditionController::class, 'get'])->name('terms.get');

    // Jobs
    Route::resource('jobs', JobController::class);
    Route::post('jobs/check-voucher',       [JobController::class, 'checkVoucher'])->name('jobs.check-voucher');
    Route::get('jobs/{job}/receipt',        [JobController::class, 'receipt'])->name('jobs.receipt');
    Route::post('jobs/{job}/apply-voucher', [JobController::class, 'applyVoucher'])->name('jobs.apply-voucher');
    Route::patch('jobs/{job}/status', [JobController::class, 'updateStatus'])->name('jobs.update-status');
    Route::post('jobs/{job}/update-discount', [JobController::class, 'updateDiscount'])->name('jobs.update-discount');
    Route::patch('jobs/{job}/status',         [JobController::class, 'updateStatus'])->name('jobs.update-status');
    Route::patch('devices/{device}/status', [JobController::class, 'updateDeviceStatus'])->name('devices.update-status');
    // Email
    Route::get('jobs/{job}/email-preview',  [EmailController::class, 'preview'])->name('jobs.email-preview');
    Route::post('jobs/{job}/send-email',    [EmailController::class, 'send'])->name('jobs.send-email');

    // Payments
    Route::post('jobs/{job}/payments',    [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('payments/{payment}',      [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('payments/{payment}',   [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Refunds
    Route::post('jobs/{job}/refund',  [App\Http\Controllers\RefundController::class, 'store'])->name('refunds.store');
    Route::delete('refunds/{refund}', [App\Http\Controllers\RefundController::class, 'destroy'])->name('refunds.destroy');

    // User management (admin only)
    Route::get('users',               [AuthController::class, 'users'])->name('users.index');
    Route::post('users',              [AuthController::class, 'storeUser'])->name('users.store');
    Route::put('users/{user}',        [AuthController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{user}',     [AuthController::class, 'destroyUser'])->name('users.destroy');
    Route::post('change-password',    [AuthController::class, 'changePassword'])->name('password.change');
});
