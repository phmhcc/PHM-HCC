<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardNotificationController;
use App\Http\Controllers\DashboardProfileController;
use App\Http\Controllers\DashboardResourceController;

Route::view('/', 'pages.index')->name('home');

Route::view('/about', 'pages.about')->name('about');

Route::view('/leadership', 'pages.leadership')->name('leadership');

Route::view('/ministries', 'pages.ministries')->name('ministries');

Route::view('/gallery', 'pages.gallery')->name('gallery');

Route::view('/events', 'pages.events')->name('events');

Route::view('/announcements', 'pages.announcements')->name('announcements');

Route::view('/contact', 'pages.contact')->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::view('/pastor', 'dashboards.pastor')->name('pastor');
        Route::view('/deacon', 'dashboards.deacon')->name('deacon');
        Route::view('/elder', 'dashboards.elder')->name('elder');
        Route::view('/department-leader', 'dashboards.department-leader')->name('department-leader');
        Route::view('/treasurer', 'dashboards.treasurer')->name('treasurer');

        Route::get('/notifications', [DashboardNotificationController::class, 'index'])->name('notifications');
        Route::patch('/notifications/{notification}/read', [DashboardNotificationController::class, 'read'])->name('notifications.read');
        Route::get('/profile', [DashboardProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [DashboardProfileController::class, 'update'])->name('profile.update');

        Route::get('/{resource}', [DashboardResourceController::class, 'index'])->name('resource.index');
        Route::post('/{resource}', [DashboardResourceController::class, 'store'])->name('resource.store');
        Route::put('/{resource}/{record}', [DashboardResourceController::class, 'update'])->whereNumber('record')->name('resource.update');
        Route::delete('/{resource}/{record}', [DashboardResourceController::class, 'destroy'])->whereNumber('record')->name('resource.destroy');
    });
});

Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send');
