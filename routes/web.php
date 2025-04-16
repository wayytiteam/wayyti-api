<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Mail\MonthlyDrawWinnerNotificationSent;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/artisan_schedule_run', function () {
    Artisan::call('schedule:run');
});

Route::get('/artisan_queue_work', function () {
    Artisan::call('queue:work --once');
});

Route::get('/notify-winner', function () {

    $username = 'John Doe';
    $subject = 'Congratulations, '.'John Doe'.'!'. ' You’re Wayyti’s Monthly Draw Winner!';

    $mail = new \App\Mail\MonthlyDrawWinnerNotificationSent($username, $subject);

    return $mail->render();
});

Route::get('/price-down-update', function () {

    // $username = 'John Doe';
    // $subject = 'Congratulations, '.'John Doe'.'!'. ' You’re Wayyti’s Monthly Draw Winner!';
    $new_price = '₱1490';
    $old_price = '₱1530';
    $item = 'Uniqlo Soft Puffy Shoulder Bag - Olive';
    $percentage = 30;
    $mail = new \App\Mail\PriceDownUpdate($old_price, $new_price, $item, $percentage);

    return $mail->render();
});

// Route::post('/auth/apple/callback', [UserController::class, 'apple_sign_in_callback']);
