<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/artisan_schedule_run', function () {
    Artisan::call('schedule:run');
});

Route::get('/artisan_queue_work', function () {
    Artisan::call('queue:work --once');
});

// Route::post('/auth/apple/callback', [UserController::class, 'apple_sign_in_callback']);
