<?php

use App\Http\Controllers\API\BookingControlller;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\FavouriteController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OverviewController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PaymentMethodInfo;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\BookingDueController;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Backend\CarDetailController;
use App\Http\Controllers\API\Backend\CharComfortController;
use App\Http\Controllers\API\Backend\CharExternalController;
use App\Http\Controllers\API\Backend\CharInternalController;
use App\Http\Controllers\API\Backend\CharSafetyController;
use App\Http\Controllers\API\Backend\QuickSpecController;

// =====================
// Site Info
// =====================

Route::get('/site/info', function () {
    $query = SystemSetting::query();

    $data = $query->first();

    return response()->json(['success' => true, 'data' => $data], 200);
});



// Category
Route::controller(CategoryController::class)->group(function () {
    Route::get('/category/get', 'get');
});

//Live Chat Routes
Route::middleware('auth:api')->controller(ChatController::class)->group(function () {
    Route::post('/chat/send', 'sendMessage');
    Route::get('/chat/get/{id}', 'fetchMessages');
    Route::get('/chat/top-users', 'topChatUsers');
    Route::get('/chat/contacts', 'chatContacts');
});

// Notification
Route::middleware('auth:api')->controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'notification');
});

// Payments
Route::middleware('auth:api')->controller(PaymentController::class)->group(function () {
    Route::post('/stripe', 'index');
    Route::get('/stripe/success', 'paymentSuccess')->name('stripe.api.success');
    Route::get('/stripe/cancel', 'cancel')->name('stripe.api.cancel');
});

// ============================================================
// Business Owner Routes
// ============================================================

// Payment Methods
Route::middleware('auth:api')->controller(PaymentMethodInfo::class)->group(function () {
    Route::get('/payment/methods/info/get', 'get');
    Route::post('/payment/methods/info', 'store');
    Route::get('/remove/payment/methods/info/{id}', 'remove');
});

// Overviews
Route::middleware('auth:api')->controller(OverviewController::class)->group(function () {
    Route::get('/overviews', 'overviews');
});

// Service
Route::middleware('auth:api')->controller(ServiceController::class)->group(function () {
    Route::get('service/getOwnerServices', 'getOwnerServices');
    Route::post('service/create', 'create');
    Route::post('service/addTimeSlot', 'addTimeSlot');
    Route::post('service/unavailableTimeSlot', 'unavailableTimeSlot');
});
// Booking
Route::middleware('auth:api')->controller(BookingControlller::class)->group(function () {
    Route::get('get/owner/booking', 'getOwnerBookings');
    Route::get('get/booking/change/requests', 'changeRequest');
    Route::post('change/booking/status', 'ChangeRequestStatus');
    Route::post('change/booking/update', 'updateChangeRequest');
    Route::post('booking/status', 'updateBookingStatus');
    Route::post('custom/booking', 'customBooking');
    Route::get('booking-details/{id}', 'bookingDetails');
});

// Item
Route::middleware('auth:api')->controller(ItemController::class)->group(function () {
    Route::get('item/get', 'get');
    Route::post('item/create', 'create');
    Route::get('item/remove/{id}', 'remove');
});

// Review Routes
Route::middleware('auth:api')->controller(ReviewController::class)->group(function () {
    Route::get('/review/getOwnerReviews', 'getOwnerReviews');
});

// Subcription Plan
Route::middleware('auth:api')->controller(SubscriptionController::class)->group(function () {
    Route::get('/subscription/plans', 'plans');
    Route::post('/subscription/user/purchase/plans', 'userPurchasePlans');
});

Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/subscription/user/purchase/plans/success', 'userPurchasePlansSuccess')->name('subscription.purchase.success');
    Route::get('/subscription/user/purchase/plans/cancel', 'userPurchasePlansCancel')->name('subscription.purchase.cancel');
});

// Get Due List
Route::middleware('auth:api')->controller(BookingDueController::class)->group(function () {
    Route::get('/get/due/list', 'getDueList');
    Route::post('/due/payment/request', 'duePaymentRequest');
    Route::get('/get/due/payment/request/list', 'getDuePaymentRequestList');
    Route::get('/due/payment/request/again/{id}', 'duePaymentRequestAgain');
    Route::get('/cancel/due/payment/request/{id}', 'cancelDuePaymentRequest');

    Route::post('/due/payment/custom', 'duePaymentCustom');
});

// ============================================================
// Business Owner Routes
// ============================================================



// =============================================================
// Customer Routes
// =============================================================

// Like / Dislike Reviews
Route::middleware('auth:api')->controller(ReviewController::class)->group(function () {
    Route::post('/like/dislike/review', 'likeDislikeReview');
});

// Favorite Service
Route::middleware('auth:api')->controller(FavouriteController::class)->group(function () {
    Route::post('add-favourite', 'add');
    Route::get('get-favourite', 'get');
    Route::get('remove-favourite/{id}', 'remove');
});

// Service
Route::middleware('auth:api')->controller(ServiceController::class)->group(function () {
    Route::get('service/getAllServices', 'getAllServices');
    Route::get('service_details/{id}', 'details');
    Route::post('available/service/time-slots', 'availableTimeSlots');
});

// Booking
Route::middleware('auth:api')->controller(BookingControlller::class)->group(function () {
    Route::get('get/customer/booking', 'getCustomerBookings');
    Route::post('book/service', 'booking');
    Route::post('cancel/booking/request', 'cancelBooking');
    Route::post('reschedule/booking/request', 'rescheduleBooking');
    Route::get('upcoming-booking', 'upcomingBooking');
    Route::get('booking-history', 'bookingHistory');
});
Route::controller(BookingControlller::class)->group(function () {
    Route::get('bookingSuccessfull', 'bookingSuccessfull')->name('stripe.booking.success');
    Route::get('bookingCancelled', 'bookingCancelled')->name('stripe.booking.cancel');
});

// Review Routes
Route::middleware('auth:api')->controller(ReviewController::class)->group(function () {
    Route::get('/review/getCustomerReviews', 'getCustomerReviews');
    Route::post('/review/store', 'store');
});

Route::middleware('auth:api')->controller(BookingDueController::class)->group(function () {
    Route::get('get/payment/requests', 'getPaymentRequests');
    Route::get('payment/requests/details/{id}', 'paymentRequestDetails');
    Route::post('pay/due/amount', 'payBooking');
});


Route::controller(BookingDueController::class)->group(function () {
    Route::get('pay/bookingSuccessfull', 'payBookingSuccessfull')->name('stripe.duePayment.success');
    Route::get('pay/bookingCancelled', 'payBookingCancelled')->name('stripe.duePayment.cancel');
});



// =============================================================
// Customer Routes
// =============================================================

// Login & Register
Route::controller(UserAuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('customer/register', 'customerRegister');
    Route::post('owner/register', 'ownerRegister');
    Route::get('user/logout', 'logout');

    Route::post('verify-otp-password', 'varifyOtpWithOutAuth');
    Route::post('resend-otp', 'resendOtp');

    Route::post('forget-password', 'forgetPassword');
    Route::post('reset-password', 'resetPassword');

    // Google / Apple Login
    Route::post('social-login/google', 'googleLogin');
    Route::post('social-login/apple', 'appleLogin');
});

// Update Profile
Route::middleware('auth:api')->controller(ProfileController::class)->group(function () {
    Route::post('update/user/image', 'updateUserImage');

    Route::get('profile', 'index');
    Route::post('profile/update', 'update');
    Route::post('profile/remove/coverImage', 'coverImageRemove');
    Route::post('profile/update/contact-info', 'updateContactInfo');
});




//carDetils
Route::middleware('auth:api') ->controller(CarDetailController::class)->group(function () {

//carDetils
Route::get('carDetails/get', 'index');
Route::post('carDetails/store',  'store');
Route::get('carDetails/edit/{id}', 'edit');
Route::post('carDetails/update/{id}', 'update');
Route::delete('carDetails/delete/{id}', 'destroy');

});


// quickSpec
Route::middleware('auth:api') ->controller(QuickSpecController::class)->group(function () {
Route::get('quickSpec/get', 'index');
Route::post('quickSpec/store',  'store');
Route::get('quickSpec/edit/{id}', 'edit');
Route::post('quickSpec/update/{id}', 'update');
Route::delete('quickSpec/delete/{id}', 'destroy');
});


// charComfort
Route::middleware('auth:api') ->controller(CharComfortController::class)->group(function () {
Route::get('charComfort/get', 'index');
Route::post('charComfort/store',  'store');
Route::get('charComfort/edit/{id}', 'edit');
Route::post('charComfort/update/{id}', 'update');
Route::delete('charComfort/delete/{id}', 'destroy');
});


// charExternal
Route::middleware('auth:api') ->controller(CharExternalController::class)->group(function () {
Route::get('charExternal/get', 'index');
Route::post('charExternal/store',  'store');
Route::get('charExternal/edit/{id}', 'edit');
Route::post('charExternal/update/{id}', 'update');
Route::delete('charExternal/delete/{id}', 'destroy');
});


// charInternal
Route::middleware('auth:api')->controller(CharInternalController::class)->group(function () {
Route::get('charInternal/get', 'index');
Route::post('charInternal/store',  'store');
Route::post('charInternal/update/{id}', 'update');
Route::delete('charInternal/delete/{id}', 'destroy');
});


//
Route::middleware('auth:api')->controller(CharSafetyController::class)->group(function () {
Route::get('charSafety/get', 'index');
Route::post('charSafety/store',  'store');
Route::get('charSafety/edit/{id}', 'edit');
Route::post('charSafety/update/{id}', 'update');
Route::delete('charSafety/delete/{id}', 'destroy');
});






