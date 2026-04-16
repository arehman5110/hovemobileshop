<?php

use App\Http\Controllers\PhoneDealController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RepairTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ── Auth (no middleware needed) ───────────────────────────────────
Route::get('login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.submit');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Password change ───────────────────────────────────────────────
Route::post('password/change', [AuthController::class, 'changePassword'])->name('password.change')->middleware('auth');

// ── POS Terminal ──────────────────────────────────────────────────
Route::get('pos',                       [App\Http\Controllers\PosController::class, 'terminal'])->name('pos.terminal');
Route::post('pos',                      [App\Http\Controllers\PosController::class, 'store'])->name('pos.store');
Route::get('pos/history',               [App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
Route::get('pos/{sale}/receipt',        [App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');
Route::delete('pos/{sale}',             [App\Http\Controllers\PosController::class, 'destroy'])->name('pos.destroy');

// POS API (for terminal AJAX)
Route::get('pos/api/categories',                                [App\Http\Controllers\PosController::class, 'apiCategories'])->name('pos.api.categories');
Route::get('pos/api/categories/{category}/brands',              [App\Http\Controllers\PosController::class, 'apiBrands'])->name('pos.api.brands');
Route::get('pos/api/categories/{category}/models',              [App\Http\Controllers\PosController::class, 'apiModels'])->name('pos.api.models');
Route::get('pos/api/categories/{category}/products',            [App\Http\Controllers\PosController::class, 'apiProducts'])->name('pos.api.products');
Route::get('pos/api/search',                                    [App\Http\Controllers\PosController::class, 'apiSearch'])->name('pos.api.search');

// POS Stock Management
Route::get('pos/stock',                 [App\Http\Controllers\PosController::class, 'stockIndex'])->name('pos.stock');
Route::post('pos/stock',                [App\Http\Controllers\PosController::class, 'stockStore'])->name('pos.stock.store');
Route::put('pos/stock/{item}',          [App\Http\Controllers\PosController::class, 'stockUpdate'])->name('pos.stock.update');
Route::post('pos/stock/{item}/topup',   [App\Http\Controllers\PosController::class, 'stockTopup'])->name('pos.stock.topup');
Route::delete('pos/stock/{item}',       [App\Http\Controllers\PosController::class, 'stockDestroy'])->name('pos.stock.destroy');

// POS Setup (Categories / Brands / Models)
Route::get('pos/setup',                 [App\Http\Controllers\PosController::class, 'setupIndex'])->name('pos.setup');
Route::post('pos/categories',           [App\Http\Controllers\PosController::class, 'categoryStore'])->name('pos.category.store');
Route::put('pos/categories/{category}', [App\Http\Controllers\PosController::class, 'categoryUpdate'])->name('pos.category.update');
Route::delete('pos/categories/{category}',[App\Http\Controllers\PosController::class, 'categoryDestroy'])->name('pos.category.destroy');
Route::post('pos/brands',               [App\Http\Controllers\PosController::class, 'brandStore'])->name('pos.brand.store');
Route::delete('pos/brands/{brand}',     [App\Http\Controllers\PosController::class, 'brandDestroy'])->name('pos.brand.destroy');
Route::post('pos/models',               [App\Http\Controllers\PosController::class, 'modelStore'])->name('pos.model.store');
Route::delete('pos/models/{model}',     [App\Http\Controllers\PosController::class, 'modelDestroy'])->name('pos.model.destroy');
Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('permission:dashboard.view');

// ── Customers ─────────────────────────────────────────────────────
Route::middleware('permission:customers.view')->group(function () {
    Route::get('customers',                     [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}',          [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{customer}/json',     [CustomerController::class, 'json'])->name('customers.json');
});
Route::middleware('permission:customers.create')->group(function () {
    Route::get('customers/create',              [CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers',                    [CustomerController::class, 'store'])->name('customers.store');
});
Route::middleware('permission:customers.edit')->group(function () {
    Route::get('customers/{customer}/edit',     [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}',          [CustomerController::class, 'update'])->name('customers.update');
    Route::patch('customers/{customer}',        [CustomerController::class, 'update']);
});
Route::delete('customers/{customer}',           [CustomerController::class, 'destroy'])
    ->name('customers.destroy')->middleware('permission:customers.delete');

// ── Jobs ──────────────────────────────────────────────────────────
Route::middleware('permission:jobs.view')->group(function () {
    Route::get('jobs',                          [JobController::class, 'index'])->name('jobs.index');
    Route::get('jobs/{job}',                    [JobController::class, 'show'])->name('jobs.show');
    Route::get('jobs/{job}/receipt',            [JobController::class, 'receipt'])->name('jobs.receipt');
});
Route::middleware('permission:jobs.create')->group(function () {
    Route::get('jobs/create',                   [JobController::class, 'create'])->name('jobs.create');
    Route::post('jobs',                         [JobController::class, 'store'])->name('jobs.store');
    Route::post('jobs/check-voucher',           [JobController::class, 'checkVoucher'])->name('jobs.check-voucher');
    Route::post('jobs/{job}/apply-voucher',     [JobController::class, 'applyVoucher'])->name('jobs.apply-voucher');
});
Route::middleware('permission:jobs.edit')->group(function () {
    Route::get('jobs/{job}/edit',               [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('jobs/{job}',                    [JobController::class, 'update'])->name('jobs.update');
    Route::patch('jobs/{job}',                  [JobController::class, 'update']);
    Route::post('jobs/{job}/update-discount',   [JobController::class, 'updateDiscount'])->name('jobs.update-discount');
    Route::post('jobs/{job}/remove-voucher',    [JobController::class, 'removeVoucher'])->name('jobs.remove-voucher');
    Route::post('jobs/{job}/check-voucher',     [JobController::class, 'checkVoucher'])->name('jobs.check-voucher-job');
});
Route::middleware('permission:jobs.update-status')->group(function () {
    Route::patch('jobs/{job}/status',           [JobController::class, 'updateStatus'])->name('jobs.update-status');
    Route::patch('devices/{device}/status',     [JobController::class, 'updateDeviceStatus'])->name('devices.update-status');
    Route::patch('repair-items/{repairItem}/status', [JobController::class, 'updateRepairItemStatus'])->name('repair-items.update-status');
});
Route::delete('jobs/{job}',                     [JobController::class, 'destroy'])
    ->name('jobs.destroy')->middleware('permission:jobs.delete');

// ── Payments ──────────────────────────────────────────────────────
Route::post('jobs/{job}/payments',              [PaymentController::class, 'store'])
    ->name('payments.store')->middleware('permission:payments.create');
Route::middleware('permission:payments.edit')->group(function () {
    Route::get('payments/{payment}/edit',       [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('payments/{payment}',            [PaymentController::class, 'update'])->name('payments.update');
});
Route::delete('payments/{payment}',             [PaymentController::class, 'destroy'])
    ->name('payments.destroy')->middleware('permission:payments.delete');

// ── Refunds ───────────────────────────────────────────────────────
Route::post('jobs/{job}/refund',    [App\Http\Controllers\RefundController::class, 'store'])
    ->name('refunds.store')->middleware('permission:refunds.create');
Route::delete('refunds/{refund}',   [App\Http\Controllers\RefundController::class, 'destroy'])
    ->name('refunds.destroy')->middleware('permission:refunds.delete');

// ── Parts / Stock ─────────────────────────────────────────────────
Route::middleware('permission:parts.view')->group(function () {
    Route::get('parts',                         [PartController::class, 'index'])->name('parts.index');
    Route::get('parts-report',                  [PartController::class, 'report'])->name('parts.report');
});
Route::middleware('permission:parts.create')->group(function () {
    Route::get('parts/create',                  [PartController::class, 'create'])->name('parts.create');
    Route::post('parts',                        [PartController::class, 'store'])->name('parts.store');
});
Route::middleware('permission:parts.edit')->group(function () {
    Route::get('parts/{part}/edit',             [PartController::class, 'edit'])->name('parts.edit');
    Route::put('parts/{part}',                  [PartController::class, 'update'])->name('parts.update');
    Route::patch('parts/{part}',                [PartController::class, 'update']);
});
Route::post('parts/{part}/topup',               [PartController::class, 'topup'])
    ->name('parts.topup')->middleware('permission:parts.topup');
Route::delete('parts/{part}',                   [PartController::class, 'destroy'])
    ->name('parts.destroy')->middleware('permission:parts.delete');

// ── Repair Types & Categories ─────────────────────────────────────
Route::middleware('permission:repair-types.manage')->group(function () {
    Route::resource('repair-types',       RepairTypeController::class)->except(['show','create','edit']);
    Route::resource('categories',         CategoryController::class)->except(['show','create','edit']);
    Route::resource('device-categories',  App\Http\Controllers\DeviceCategoryController::class)->except(['show','create','edit']);
});

// ── Catalogue (Product Types + Phone Models) ──────────────────────
Route::middleware('permission:repair-types.manage')->group(function () {
    Route::get('catalogue',                                     [App\Http\Controllers\CatalogueController::class, 'index'])->name('catalogue.index');
    Route::post('catalogue/types',                             [App\Http\Controllers\CatalogueController::class, 'storeType'])->name('catalogue.types.store');
    Route::put('catalogue/types/{type}',                       [App\Http\Controllers\CatalogueController::class, 'updateType'])->name('catalogue.types.update');
    Route::delete('catalogue/types/{type}',                    [App\Http\Controllers\CatalogueController::class, 'destroyType'])->name('catalogue.types.destroy');
    Route::post('catalogue/models',                            [App\Http\Controllers\CatalogueController::class, 'storeModel'])->name('catalogue.models.store');
    Route::put('catalogue/models/{model}',                     [App\Http\Controllers\CatalogueController::class, 'updateModel'])->name('catalogue.models.update');
    Route::delete('catalogue/models/{model}',                  [App\Http\Controllers\CatalogueController::class, 'destroyModel'])->name('catalogue.models.destroy');
});
Route::get('catalogue/types/json',  [App\Http\Controllers\CatalogueController::class, 'typesJson'])->name('catalogue.types.json');
Route::get('catalogue/models/json', [App\Http\Controllers\CatalogueController::class, 'modelsJson'])->name('catalogue.models.json');
Route::get('catalogue/brands/json', [App\Http\Controllers\CatalogueController::class, 'brandsJson'])->name('catalogue.brands.json');
Route::middleware('permission:phone-deals.view')->group(function () {
    Route::resource('inventory', App\Http\Controllers\InventoryController::class);
});

// ── Terms & Conditions ────────────────────────────────────────────
Route::middleware('permission:settings.view')->group(function () {
    Route::get('terms',  [App\Http\Controllers\TermsConditionController::class, 'index'])->name('terms.index');
    Route::post('terms', [App\Http\Controllers\TermsConditionController::class, 'update'])->name('terms.update');
});

// ── Vouchers ──────────────────────────────────────────────────────
Route::middleware('permission:vouchers.view')->group(function () {
    Route::get('vouchers',                      [VoucherController::class, 'index'])->name('vouchers.index');
    Route::get('vouchers/{voucher}/print',      [VoucherController::class, 'printView'])->name('vouchers.print');
});
Route::middleware('permission:vouchers.manage')->group(function () {
    Route::post('vouchers',                     [VoucherController::class, 'store'])->name('vouchers.store');
    Route::put('vouchers/{voucher}',            [VoucherController::class, 'update'])->name('vouchers.update');
    Route::patch('vouchers/{voucher}',          [VoucherController::class, 'update']);
    Route::delete('vouchers/{voucher}',         [VoucherController::class, 'destroy'])->name('vouchers.destroy');
    Route::post('vouchers/{voucher}/email',     [VoucherController::class, 'sendEmail'])->name('vouchers.email');
});

// ── Phone Deals ───────────────────────────────────────────────────
Route::middleware('permission:phone-deals.view')->group(function () {
    Route::get('phone-deals',                   [PhoneDealController::class, 'index'])->name('phone-deals.index');
    Route::get('phone-deals/{phoneDeal}',       [PhoneDealController::class, 'show'])->name('phone-deals.show');
    Route::get('phone-deals/{phoneDeal}/receipt',[PhoneDealController::class, 'receipt'])->name('phone-deals.receipt');
});
Route::middleware('permission:phone-deals.create')->group(function () {
    Route::get('phone-deals/create',            [PhoneDealController::class, 'create'])->name('phone-deals.create');
    Route::post('phone-deals',                  [PhoneDealController::class, 'store'])->name('phone-deals.store');
});
Route::middleware('permission:phone-deals.edit')->group(function () {
    Route::get('phone-deals/{phoneDeal}/edit',  [PhoneDealController::class, 'edit'])->name('phone-deals.edit');
    Route::put('phone-deals/{phoneDeal}',       [PhoneDealController::class, 'update'])->name('phone-deals.update');
    Route::patch('phone-deals/{phoneDeal}',     [PhoneDealController::class, 'update']);
    Route::post('phone-deals/{phoneDeal}/send-email', [PhoneDealController::class, 'sendEmail'])->name('phone-deals.send-email');
});
Route::delete('phone-deals/{phoneDeal}',        [PhoneDealController::class, 'destroy'])
    ->name('phone-deals.destroy')->middleware('permission:phone-deals.delete');

// ── Email ─────────────────────────────────────────────────────────
Route::get('jobs/{job}/email-preview',          [EmailController::class, 'preview'])->name('jobs.email-preview');
Route::post('jobs/{job}/send-email',            [EmailController::class, 'send'])->name('jobs.send-email');

// ── Settings ──────────────────────────────────────────────────────
Route::get('settings',  [SettingController::class, 'index'])
    ->name('settings.index')->middleware('permission:settings.view');
Route::put('settings',  [SettingController::class, 'update'])
    ->name('settings.update')->middleware('permission:settings.edit');

// ── Users ─────────────────────────────────────────────────────────
Route::middleware('permission:users.manage')->group(function () {
    Route::get('users',                         [App\Http\Controllers\AuthController::class, 'users'])->name('users.index');
    Route::post('users',                        [App\Http\Controllers\AuthController::class, 'storeUser'])->name('users.store');
    Route::put('users/{user}',                  [App\Http\Controllers\AuthController::class, 'updateUser'])->name('users.update');
    Route::delete('users/{user}',               [App\Http\Controllers\AuthController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('users/{user}/role',           [App\Http\Controllers\AuthController::class, 'updateRole'])->name('users.update-role');
    Route::post('users/{user}/permissions',     [App\Http\Controllers\AuthController::class, 'savePermissions'])->name('users.permissions');
});