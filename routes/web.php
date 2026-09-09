<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\PrivateBookingPage;
use App\Models\FestivalSetting;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $settings = FestivalSetting::findOrFail(1);
    config(['ifaf.tickets' => $settings->ticket_options]);

    return view('welcome', ['demoMode' => $settings->demo]);
})->name('home');
Route::view('/gallery', 'gallery')->name('gallery');

Route::middleware(PrivateBookingPage::class)->group(function () {
    Route::view('/my-tickets', 'booking.saved')->name('booking.saved');
    Route::get('/reserve', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/reserve', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
    Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/booking/{booking}/status', [BookingController::class, 'status'])->middleware('throttle:60,1')->name('booking.status');
    Route::post('/booking/{booking}/payment', [BookingController::class, 'payment'])->middleware('throttle:5,1')->name('booking.payment');
    Route::get('/booking/{booking}/tickets', [BookingController::class, 'tickets'])->name('booking.tickets');
    Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
    Route::post('/admin/login', [AdminController::class, 'authenticate'])->middleware('throttle:5,1')->name('admin.authenticate');
    Route::prefix('admin')->name('admin.')->middleware(AdminOnly::class)->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'saveSettings'])->name('settings.save');
        Route::get('/scan', [AdminController::class, 'scan'])->name('scan');
        Route::post('/scan', [AdminController::class, 'lookup'])->name('lookup');
        Route::get('/entry/{ticket}', [AdminController::class, 'ticket'])->name('ticket');
        Route::post('/entry/{ticket}', [AdminController::class, 'checkin'])->name('checkin');
        Route::get('/bookings/{booking}', [AdminController::class, 'show'])->name('booking');
        Route::get('/bookings/{booking}/receipt', [AdminController::class, 'receipt'])->name('receipt');
        Route::post('/bookings/{booking}/review', [AdminController::class, 'review'])->name('review');
        Route::post('/bookings/{booking}/resend', [AdminController::class, 'resend'])->middleware('throttle:3,1')->name('resend');
    });
});
