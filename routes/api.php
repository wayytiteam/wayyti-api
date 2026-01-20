<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\MonthlyDrawController as AdminMonthlyDrawController;
use App\Http\Controllers\Admin\MonthlyDrawWinnerController as AdminMonthlyDrawWinnerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\BadgeUserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\GoogleProductController;
use App\Http\Controllers\MonthlyDrawController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PersonaUserController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecentSearchController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrackedProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('users/email', [UserController::class, 'email_sign_in']);
Route::post('auth/facebook', [UserController::class, 'facebook_mobile_sign_in']);
Route::post('auth/google', [UserController::class, 'google_mobile_sign_in']);
Route::post('auth/apple', [UserController::class, 'apple_sign_in']);
Route::get('auth/apple/callback', [UserController::class, 'apple_sign_in_callback']);
Route::post('users/request-verification', [UserController::class, 'request_verification_code']);
Route::post('users/verify', [UserController::class, 'verify_otp']);
Route::get('users/check-email', [UserController::class, 'check_email']);
Route::post('users/reset-password', [UserController::class, 'reset_password']);
Route::get('products/scrape', [ProductController::class, 'scrape']);
Route::get('google-products/scrape', [GoogleProductController::class, 'scrape']);
Route::get('google-products/browser-instruction', [GoogleProductController::class, 'browser_instruction']);
Route::post('tasks/mail-incomplete-profiles', [TaskController::class, 'mail_incomplete_profiles']);
Route::post('google-products/test-price-update', [GoogleProductController::class, 'test_price_update']);
Route::post('notifications/test', [NotificationController::class, 'test']);
Route::post('admin/users/authenticate', [AdminUserController::class, 'admin_auth']);
Route::resource('personas', PersonaController::class);
Route::post('users', [UserController::class, 'store']);
// subscription routes
Route::post('subscriptions/verify-google-subscription', [SubscriptionController::class, 'verify_google_subscription']);
Route::post('subscriptions/verify-apple-subscription', [SubscriptionController::class, 'verify_apple_subscription']);
// Route::post('subscriptions/verify-subscription', [SubscriptionController::class, 'verify_subscription']);
// Route::get('subscriptions/subscription-check', [SubscriptionController::class, 'subscription_check']);

Route::middleware('auth:api')->group(function () {
    Route::post('users/check-password', [UserController::class, 'check_password']);
    Route::post('users/welcome-email', [UserController::class, 'welcome_email']);
    Route::get('tracked-products/google-product-details', [TrackedProductController::class, 'google_product_details']);
    Route::post('tracked-products/batch-update', [TrackedProductController::class, 'batch_update']);
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResources([
        'google-products' => GoogleProductController::class,
        'persona-user' => PersonaUserController::class,
        'folders' => FolderController::class,
        'tracked-products' => TrackedProductController::class,
        'attendance' => AttendanceController::class,
        'monthly-draws' => MonthlyDrawController::class,
        'badges' => BadgeController::class,
        'points' => PointController::class,
        'recent-searches' => RecentSearchController::class,
        'user-badges' => BadgeUserController::class,
        'shares' => ShareController::class,
        'referrals' => ReferralController::class,
        'notifications' => NotificationController::class,
        'products' => ProductController::class,
        'banners' => BannerController::class,
        'subscriptions' => SubscriptionController::class,
    ]);
});

Route::middleware(['auth:api', 'scope:admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('admin/users/report', [AdminUserController::class, 'report']);
        Route::get('admin/subscriptions', [SubscriptionController::class, 'index']);
        Route::apiResources([
            'admin/monthly-draw-winners' => AdminMonthlyDrawWinnerController::class,
            'admin/users' => AdminUserController::class,
            'admin/monthly-draws' => AdminMonthlyDrawController::class,
            'admin/banners' => AdminBannerController::class,
        ]);
    });
