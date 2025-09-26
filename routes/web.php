<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\CourtRatesController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BaseBookingController;
use App\Http\Controllers\SingleBookingController;
use App\Http\Controllers\SubscriptionBookingController;
use App\Http\Controllers\CustomerBookingManagerController;
use App\Http\Controllers\TeammateFinderController;
use App\Http\Controllers\OwnerBookingManagerController;
use App\Http\Controllers\BookingCancelController;
use App\Http\Controllers\BookingUpdateController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\StatisticalController;
use App\Http\Controllers\VNPayController;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Đăng nhập & Đăng ký & Quên mật khẩu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login']);
    Route::get('/register', [AccountController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register']);
    Route::get('/forgot-password', [AccountController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AccountController::class, 'handleResetPassword'])->name('password.email');
});

// Đăng xuất & Cập nhật thông tin cá nhân
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
    Route::get('/profile', [AccountController::class, 'editProfile'])->name('profile');
    Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});

// API kiểm tra sân trống
Route::get('/booking/check-availability', [BaseBookingController::class, 'checkAvailability'])->name('booking.check-availability');
// Tính giá
Route::post('/booking/calculate-price', [BaseBookingController::class, 'calculatePrice'])->name('booking.calculate-price');
// Lấy giá theo ngày trong tuần
Route::get('/booking/get-rates-by-day/{day}', [BaseBookingController::class, 'getRatesByDay'])->name('booking.get-rates-by-day');
// API lấy khuyến mãi hợp lệ
Route::get('/booking/single/promotions', [SingleBookingController::class, 'getValidPromotions'])->name('booking.single.promotions');
Route::get('/booking/subscription/promotions', [SubscriptionBookingController::class, 'getValidPromotions'])->name('booking.subscription.promotions');

// Xem trạng thái đặt sân
Route::get('/booking', [BaseBookingController::class, 'index'])->name('booking.index');
// Trang đặt sân theo buổi
Route::get('/booking/single', [SingleBookingController::class, 'create'])->name('booking.single.create');
// Trang đặt sân định kỳ
Route::get('/booking/subscription', [SubscriptionBookingController::class, 'create'])->name('booking.subscription.create');

// Đặt sân
Route::middleware(['auth'])->group(function () {
    // Đặt sân theo buổi
    Route::post('/booking/single', [SingleBookingController::class, 'store'])->name('booking.single.store');
    Route::get('/booking/single/confirmation', [SingleBookingController::class, 'confirmation'])->name('booking.single.confirmation');
    Route::put('/booking/single/{booking}/cancel', [BookingCancelController::class, 'cancelSingleBooking'])->name('booking.single.cancel');
    // Đặt sân định kỳ
    Route::post('/booking/subscription', [SubscriptionBookingController::class, 'store'])->name('booking.subscription.store');
    Route::get('/booking/subscription/confirmation', [SubscriptionBookingController::class, 'confirmation'])->name('booking.subscription.confirmation');
    Route::put('/booking/subscription/{booking}/cancel', [BookingCancelController::class, 'cancelSubscriptionBooking'])->name('booking.subscription.cancel');
});

// Người dùng quản lý lịch đặt
Route::middleware(['auth'])->group(function () {
    Route::get('/booking-list', [CustomerBookingManagerController::class, 'index'])->name('booking.list');
    Route::get('/booking/{id}', [CustomerBookingManagerController::class, 'showDetail'])->name('booking.detail');
    Route::get('/subscription-booking/{id}', [CustomerBookingManagerController::class, 'showSubscriptionDetail'])->name('booking.subscription.detail');

    // Booking update routes - now use the new controller
    Route::get('/booking/{id}/change', [BookingUpdateController::class, 'showSingleBookingUpdateForm'])->name('booking.change');
    Route::post('/booking/{id}/change', [BookingUpdateController::class, 'updateSingleBooking'])->name('booking.change.submit');
    Route::get('/subscription-booking/{id}/change', [BookingUpdateController::class, 'showSubscriptionBookingUpdateForm'])->name('booking.subscription.change');
    Route::post('/subscription-booking/{id}/change', [BookingUpdateController::class, 'updateSubscriptionBooking'])->name('booking.subscription.change.submit');
});

// Tìm đồng đội
Route::middleware(['auth'])->group(function () {
    Route::get('/teammate-finder', [TeammateFinderController::class, 'index'])->name('teammate.index');
    Route::get('/teammate-finder/create', [TeammateFinderController::class, 'create'])->name('teammate.create');
    Route::post('/teammate-finder/store', [TeammateFinderController::class, 'store'])->name('teammate.store');
    Route::get('/teammate-finder/edit', [TeammateFinderController::class, 'edit'])->name('teammate.edit');
    Route::post('/teammate-finder/update', [TeammateFinderController::class, 'update'])->name('teammate.update');
    Route::post('/teammate-finder/delete', [TeammateFinderController::class, 'delete'])->name('teammate.delete');
    Route::get('/teammate-finder/{id}', [TeammateFinderController::class, 'show'])->name('teammate.show');
});

//-----------------------------------------------------------CHỦ SÂN-----------------------------------------------------------
// Quản lý đơn đặt sân (Owner)
Route::middleware(['auth'])->group(function () {
    Route::get('/owner/bookings', [OwnerBookingManagerController::class, 'ownerIndex'])->name('owner.bookings.index');
    // Single bookings
    Route::get('/owner/bookings/single/{id}', [OwnerBookingManagerController::class, 'showSingleBooking'])->name('owner.bookings.single');
    Route::post('/owner/bookings/single/{id}/cancel', [BookingCancelController::class, 'cancelSingleBooking'])->name('owner.bookings.single.cancel');
    Route::put('/owner/bookings/single/{id}/update', [BookingUpdateController::class, 'updateSingleBooking'])->name('owner.bookings.single.update');
    Route::get('/owner/bookings/single/{id}/print', [OwnerBookingManagerController::class, 'printSingleBooking'])->name('owner.bookings.single.print');
    // Subscription bookings
    Route::get('/owner/bookings/subscription/{id}', [OwnerBookingManagerController::class, 'showSubscriptionBooking'])->name('owner.bookings.subscription');
    Route::post('/owner/bookings/subscription/{id}/cancel', [BookingCancelController::class, 'cancelSubscriptionBooking'])->name('owner.bookings.subscription.cancel');
    Route::put('/owner/bookings/subscription/{id}/update', [BookingUpdateController::class, 'updateSubscriptionBooking'])->name('owner.bookings.subscription.update');
    Route::get('/owner/bookings/subscription/{id}/print', [OwnerBookingManagerController::class, 'printSubscriptionBooking'])->name('owner.bookings.subscription.print');
    // Complete bookings
    Route::post('/owner/bookings/{id}/complete', [OwnerBookingManagerController::class, 'completeBooking'])->name('owner.bookings.complete');
});

// Quản lý thống kê
Route::middleware(['auth'])->group(function () {
    Route::get('/owner/statistical', [StatisticalController::class, 'index'])->name('owner.statistical');
    Route::get('/owner/statistical/revenue-data', [StatisticalController::class, 'getRevenueData'])->name('owner.statistical.revenue');
    Route::get('/owner/statistical/booking-data', [StatisticalController::class, 'getBookingData'])->name('owner.statistical.bookings');
    Route::get('/owner/statistical/product-data', [StatisticalController::class, 'getProductData'])->name('owner.statistical.products');
});

// Quản lý sân
Route::middleware(['auth'])->group(function () {
    Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
    Route::get('/courts/create', [CourtController::class, 'create'])->name('courts.create');
    Route::post('/courts', [CourtController::class, 'store'])->name('courts.store');
    Route::get('/courts/{court}/edit', [CourtController::class, 'edit'])->name('courts.edit');
    Route::put('/courts/{court}', [CourtController::class, 'update'])->name('courts.update');
    Route::delete('/courts/{court}', [CourtController::class, 'destroy'])->name('courts.destroy');
});

// Quản lý sản phẩm
Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

// Quản lý cửa hàng
Route::middleware(['auth'])->group(function () {
    Route::get('/storage', [StorageController::class, 'index'])->name('storage.index');
    Route::get('/storage/create', [StorageController::class, 'create'])->name('storage.create');
    Route::post('/storage', [StorageController::class, 'store'])->name('storage.store');
    Route::get('/storage/{rental}', [StorageController::class, 'show'])->name('storage.show');
    Route::get('/storage/{rental}/edit', [StorageController::class, 'edit'])->name('storage.edit');
    Route::put('/storage/{rental}', [StorageController::class, 'update'])->name('storage.update');
    Route::put('/storage/{rental}/return', [StorageController::class, 'returnRental'])->name('storage.return');
    Route::get('/storage/{rental}/invoice', [StorageController::class, 'printInvoice'])->name('storage.invoice');
});

// Quản lý nhập hàng
Route::middleware(['auth'])->group(function () {
    Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('/imports/create', [ImportController::class, 'create'])->name('imports.create');
    Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
    Route::get('/imports/{import}', [ImportController::class, 'show'])->name('imports.show');
    Route::delete('/imports/{import}', [ImportController::class, 'destroy'])->name('imports.destroy');
    Route::get('/product-import-history/{product}', [ImportController::class, 'productHistory'])->name('product.import.history');
});

// Quản lý giá thuê
Route::middleware(['auth'])->group(function () {
    Route::get('/court-rates', [CourtRatesController::class, 'index'])->name('court-rates.index');
    Route::get('/court-rates/create', [CourtRatesController::class, 'create'])->name('court-rates.create');
    Route::post('/court-rates', [CourtRatesController::class, 'store'])->name('court-rates.store');
    Route::get('/court-rates/{courtRate}/edit', [CourtRatesController::class, 'edit'])->name('court-rates.edit');
    Route::put('/court-rates/{courtRate}', [CourtRatesController::class, 'update'])->name('court-rates.update');
    Route::delete('/court-rates/{courtRate}', [CourtRatesController::class, 'destroy'])->name('court-rates.destroy');
});

// Quản lý khuyến mãi
Route::middleware(['auth'])->group(function () {
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
});

// Quản lý khách hàng
Route::middleware(['auth'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
});

// VNPay payment return
Route::get('/payment/vnpay/return', [VNPayController::class, 'paymentReturn'])->name('payment.vnpay.return');
