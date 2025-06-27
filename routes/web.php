<?php

use App\Events\BookingCreated;
use App\Http\Controllers\Dashboard\BarbermanScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Dashboard\BarbermanController;
use App\Http\Controllers\Dashboard\BookingController as DashboardBookingController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\Dashboard\HairstyleController;
use App\Http\Controllers\Dashboard\ReviewController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperationalHourController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WelcomeController;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::group([
    'prefix' => 'booking',
    'as' => 'booking.',
    'middleware' => ['auth', 'verified']
], function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::post('/store', [BookingController::class, 'store'])->name('store');

    Route::post('/get-schedule', [BookingController::class, 'getSchedule'])->name('get-schedule');
    Route::post('/by-face-shape', [BookingController::class, 'getByFaceShape'])->name('get-byFaceShape');
    Route::get('/{id}/snap-token', [BookingController::class, 'getSnapToken'])->name('getSnapToken');
    Route::post('/check-date', [BookingController::class, 'checkDate'])->name('checkDate');
});

Route::group([
    'prefix' => 'rekomendasi',
    'as' => 'rekomendasi.',
], function () {
    Route::get('/', [RekomendasiController::class, 'index'])->name('index');
    Route::get('/detail/{shape}', [RekomendasiController::class, 'detail'])->name('detail');
    Route::get('show/{hairstyle}', [RekomendasiController::class, 'show'])->name('show');
});

Route::group([
    'prefix' => 'review',
    'as' => 'review.',
], function () {
    Route::get('/barberman/{id}', [BarbermanController::class, 'reviews'])->name('reviews');
    Route::get('/hairstyle/{id}', [HairstyleController::class, 'reviews'])->name('reviews');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/queue', [QueueController::class, 'index'])->name('queue');

Route::get('/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');

Route::middleware('web')->group(function () {
    Route::get('/google/callback', [GoogleController::class, 'callback']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::group([
        'prefix' => 'dashboard',
        'as' => 'dashboard.',
    ], function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('index');
        Route::post('/process-daily-queue', [QueueController::class, 'processDailyQueue']);
        Route::get('/queue/check-missing', [QueueController::class, 'checkMissingQueue']);
        Route::post('/queue/update-status', [DashboardController::class, 'updateStatus'])->name('updateStatus');
        Route::group([
            'prefix' => 'notification',
            'as' => 'notification.'
        ], function() {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
            Route::delete('/destroy/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        });
        Route::group([
            'prefix' => 'review',
            'as' => 'review.'
        ], function() {
            Route::post('/store', [ReviewController::class, 'store'])->name('store');
        });
        Route::group([
            'prefix' => 'booking',
            'as' => 'booking.',
        ], function() {
            Route::get('/', [DashboardBookingController::class, 'index'])->name('index');
            Route::get('/getData', [DashboardBookingController::class, 'getData'])->name('getData');
            Route::get('/show/{id}', [DashboardBookingController::class, 'show'])->name('show');
            Route::post('/store', [DashboardBookingController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[DashboardBookingController::class, 'update'])->name('update');
            Route::post('/updateStatus/{id}' ,[DashboardBookingController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/destroy/{id}', [DashboardBookingController::class, 'destroy'])->name('destroy');
        });
        Route::group([
            'prefix' => 'hairstyle',
            'as' => 'hairstyle.'
        ], function () {
            Route::get('/', [HairstyleController::class, 'index'])->name('index');
            Route::get('/getData', [HairstyleController::class, 'getData'])->name('getData');
            Route::post('/store', [HairstyleController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[HairstyleController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [HairstyleController::class, 'destroy'])->name('destroy');
            Route::get('/reviews/{id}', [HairstyleController::class, 'reviews'])->name('reviews');
        });
        Route::group([
            'prefix' => 'gallery',
            'as' => 'gallery.'
        ], function () {
            Route::get('/', [GalleryController::class, 'index'])->name('index');
            Route::get('/getData', [GalleryController::class, 'getData'])->name('getData');
            Route::post('/store', [GalleryController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[GalleryController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [GalleryController::class, 'destroy'])->name('destroy');
            Route::post('/toggle-visible/{id}', [GalleryController::class, 'toggleVisible'])->name('toggleVisible');
        });
        Route::group([
            'prefix' => 'service',
            'as' => 'service.'
        ], function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/getData', [ServiceController::class, 'getData'])->name('getData');
            Route::post('/store', [ServiceController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[ServiceController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [ServiceController::class, 'destroy'])->name('destroy');
        });
        Route::group([
            'prefix' => 'operationalHour',
            'as' => 'operationalHour.'
        ], function () {
            Route::get('/', [OperationalHourController::class, 'index'])->name('index');
            Route::get('/getData', [OperationalHourController::class, 'getData'])->name('getData');
            Route::post('/store', [OperationalHourController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[OperationalHourController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [OperationalHourController::class, 'destroy'])->name('destroy');
            Route::post('/toggle-open/{id}', [OperationalHourController::class, 'toggleOpen'])->name('toggleOpen');
        });
        Route::group([
            'prefix' => 'holiday',
            'as' => 'holiday.'
        ], function () {
            Route::get('/', [HolidayController::class, 'index'])->name('index');
            Route::get('/getData', [HolidayController::class, 'getData'])->name('getData');
            Route::post('/store', [HolidayController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[HolidayController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [HolidayController::class, 'destroy'])->name('destroy');
        });
        Route::group([
            'prefix' => 'barberman',
            'as' => 'barberman.',
        ], function() {
            Route::group([
                'prefix' => 'schedule',
                'as' => 'schedule.'
            ], function() {
                Route::get('/', [BarbermanScheduleController::class, 'index'])->name('index');
                Route::get('/getData', [BarbermanScheduleController::class, 'getData'])->name('getData');
                Route::post('/store', [BarbermanScheduleController::class, 'store'])->name('store');
                Route::post('/update/{id}' ,[BarbermanScheduleController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [BarbermanScheduleController::class, 'destroy'])->name('destroy');
            });

            Route::group([
                'prefix' => 'data',
                'as' => 'data.'
            ], function() {
                Route::get('/', [BarbermanController::class, 'index'])->name('index');
                Route::get('/getData', [BarbermanController::class, 'getData'])->name('getData');
                Route::post('/store', [BarbermanController::class, 'store'])->name('store');
                Route::post('/update/{id}' ,[BarbermanController::class, 'update'])->name('update');
                Route::delete('/destroy/{id}', [BarbermanController::class, 'destroy'])->name('destroy');
                Route::get('/reviews/{id}', [BarbermanController::class, 'reviews'])->name('reviews');
            });
        });
        Route::group([
            'prefix' => 'user',
            'as' => 'user.'
        ], function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/getData', [UserController::class, 'getData'])->name('getData');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::post('/update/{id}' ,[UserController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
        });
    });
});