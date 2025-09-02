<?php

use App\Http\Controllers\Backend\AllServiceController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\BusinessOwnerController;
use App\Http\Controllers\Backend\SubscriptionController;
use App\Http\Controllers\Backend\TimeslotController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\Web\backend\admin\FAQController;
use App\Http\Controllers\Web\backend\CategoryController;
use App\Http\Controllers\Web\backend\DashboardController;
use App\Http\Controllers\Web\backend\PremissionController;
use App\Http\Controllers\Web\backend\RoleController;
use App\Http\Controllers\Web\backend\SettingController;
use App\Http\Controllers\Web\backend\settings\DynamicPagesController;
use App\Http\Controllers\Web\backend\settings\ProfileSettingController;
use App\Http\Controllers\Web\backend\UserController;
use Illuminate\Support\Facades\Route;


// Dashboard
Route::controller(DashboardController::class)->group(function () {
     Route::get('/dashboard', 'index')->name('dashboard');
});

// ====================================================
// CMS Routes
// ====================================================

// FAQ Route
Route::controller(FAQController::class)->group(function () {
    Route::get('/faq', 'index')->name('faq.index');
    Route::get('/faq/get', 'get')->name('faq.get');
    Route::post('/faq/priorities', 'priority')->name('faq.priority');
    Route::get('/faq/status', 'status')->name('faq.status');
    Route::post('/faq/store', 'store')->name('faq.store');
    Route::post('/faq/update', 'update')->name('faq.update');
    Route::get('/faq/destroy/{id}', 'destroy')->name('faq.destroy');
});

// Dynamic Pages Route
Route::controller(DynamicPagesController::class)->group(function () {
    Route::get('/dynamicpages', 'index')->name('dynamicpages.index');
    Route::get('/dynamicpages/create', 'create')->name('dynamicpages.create');
    Route::get('/dynamicpages/edit/{id}', 'edit')->name('dynamicpages.edit');
    Route::post('/dynamicpages/store', 'store')->name('dynamicpages.store');
    Route::post('/dynamicpages/update/{id}', 'update')->name('dynamicpages.update');
    Route::delete('/dynamicpages/destroy/{id}', 'destroy')->name('dynamicpages.destroy');
    Route::post('/dynamicpages/status/{id}', 'changeStatus')->name('dynamicpages.status');
    Route::post('/dynamicpages/bulk-delete', 'bulkDelete')->name('dynamicpages.bulk-delete');
});

// Settings Route
Route::controller(SettingController::class)->group(function () {
    Route::get('/general/setting', 'create')->name('general.setting');
    Route::post('/system/update', 'update')->name('system.update');
    Route::get('/system/setting', 'systemSetting')->name('system.setting');
    Route::post('/system/setting/update', 'systemSettingUpdate')->name('system.settingupdate');
    Route::get('/setting', 'adminSetting')->name('admin.setting');
    Route::get('/stripe', 'stripe')->name('admin.setting.stripe');
    Route::post('/stripe', 'stripestore')->name('admin.setting.stripestore');
    Route::get('/paypal', 'paypal')->name('admin.setting.paypal');
    Route::post('/paypal', 'paypalstore')->name('admin.setting.paypalstore');
    Route::get('/mail', 'mail')->name('admin.setting.mail');
    Route::post('/mail', 'mailstore')->name('admin.setting.mailstore');
    Route::post('/setting/update', 'adminSettingUpdate')->name('admin.settingupdate');
});

// Permission Route
// Route::prefix('permissions')->controller(PremissionController::class)->group(function () {
//     Route::get('/list', 'index')->name('admin.permissions.list');
//     Route::get('/create', 'create')->name('admin.permissions.create');
// });

// Role Route
Route::prefix('role')->controller(RoleController::class)->group(function () {
    Route::get('/list', 'index')->name('admin.role.list');
    Route::get('/create', 'create')->name('admin.role.create');
    Route::post('/store', 'store')->name('admin.role.store');
    Route::get('/edit/{id}', 'edit')->name('admin.role.edit');
    Route::post('/update/{id}', 'update')->name('admin.role.update');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.role.destroy');
});

// User Route Comment
Route::controller(UserController::class)->group(function () {
    Route::get('/users/create', 'create')->name('user.create');
    Route::post('/users/store', 'store')->name('user.store');
    Route::get('/edit/users/{id}', 'edit')->name('user.edit');
    Route::post('/update/user', 'update')->name('user.update');
    Route::get('/users/list', 'index')->name('user.list');
    Route::get('/view/users/{id}', 'show')->name('show.user');
    Route::get('/status/users/{id}', 'status')->name('user.status');
    Route::post('/users/delete', 'destroy')->name('user.destroy');
});

// Promo Routes
Route::controller(PromoController::class)->group(function () {
    Route::get('/promo/get', 'get')->name('promo.get');
    Route::get('/promo/status', 'status')->name('promo.status');
    Route::get('/promo', 'index')->name('promo.index');
    Route::post('/promo/store', 'store')->name('promo.store');
    Route::post('/promo/update', 'update')->name('promo.update');
    Route::get('/promo/destroy/{id}', 'destroy')->name('promo.destroy');
    Route::get('/promo/menu', 'menu')->name('promo.menu');
});

// Category Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('/category/get', 'get')->name('category.get');
    Route::post('/category/priorities', 'priority')->name('category.priority');
    Route::get('/category/status', 'status')->name('category.status');
    Route::get('/category', 'index')->name('category.index');
    Route::post('/category/store', 'store')->name('category.store');
    Route::post('/category/update', 'update')->name('category.update');
    Route::get('/category/destroy/{id}', 'destroy')->name('category.destroy');
});

// Profile Settings Routes
Route::controller(ProfileSettingController::class)->group(function () {
    Route::get('/profile', 'index')->name('profile');
    Route::post('/profile/update', 'updateProfile')->name('profile.update');
    Route::post('/profile/update/password', 'updatePassword')->name('profile.update.password');
    Route::post('/profile/update/profile-picture', 'updateProfilePicture')->name('profile.update.profile.picture');
    Route::get('/checkusername', 'checkusername')->name('checkusername');
});

// Subcription
Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/subscription/plans', 'plans')->name('subscription.plans');
    Route::get('/subscription/edit/{id}', 'edit')->name('subscription.edit');
    Route::post('/subscription/update', 'update')->name('subscription.update');
    Route::get('/subscription/activeUsers', 'activeUsers')->name('subscription.activeUsers');
    Route::get('/subscription/freeTrial', 'freeTrial')->name('subscription.freeTrial');
    Route::get('/subscription/requests', 'requests')->name('subscription.requests');
    Route::get('/subscription/expiredUsers', 'expiredUsers')->name('subscription.expiredUsers');
    Route::get('/subscription/myPlans', 'myPlans')->name('subscription.myPlans');
    Route::get('/subscription/status', 'status')->name('subscription.status');
});

// All Services
// Route::controller(AllServiceController::class)->group(function () {
//     Route::get('/get/serviceinfo/{id}', 'get')->name('get.serviceinfo');
//     Route::get('/allservice', 'index')->name('allservice.index');
// });

// Bookings
Route::controller(BookingController::class)->group(function () {
    Route::get('/bookings', 'index')->name('booking.index');
    Route::get('/bookings/view/{id}', 'view')->name('booking.view');
});

// Business Owners
Route::controller(BusinessOwnerController::class)->group(function () {
    Route::get('/businessowners', 'index')->name('businessowners.index');
    Route::get('/businessowners/profile/{username}', 'profile')->name('businessowners.profile');
    Route::get('/businessowners/services/status/{id}', 'status')->name('businessowners.status');
});

