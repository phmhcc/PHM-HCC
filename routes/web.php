<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardNotificationController;
use App\Http\Controllers\DashboardProfileController;
use App\Http\Controllers\DashboardResourceController;
use App\Http\Controllers\DashboardRolePermissionController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RoleDashboardController;

Route::get('/', [PublicPageController::class, 'home'])->name('home');

Route::view('/about', 'pages.about')->name('about');

Route::view('/leadership', 'pages.leadership')->name('leadership');

Route::view('/ministries', 'pages.ministries')->name('ministries');

Route::view('/gallery', 'pages.gallery')->name('gallery');

// Route::get('/events', [PublicPageController::class, 'events'])->name('events');

Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');

Route::view('/contact', 'pages.contact')->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::middleware(['auth', 'can:dashboard.view'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/deacon', [RoleDashboardController::class, 'show'])->defaults('role', 'deacon')->name('deacon');
        Route::get('/elder', [RoleDashboardController::class, 'show'])->defaults('role', 'elder')->name('elder');
        Route::get('/department-leader', [RoleDashboardController::class, 'show'])->defaults('role', 'department-leader')->name('department-leader');
        Route::get('/treasurer', [RoleDashboardController::class, 'show'])->defaults('role', 'treasurer')->name('treasurer');

        Route::get('/notifications', [DashboardNotificationController::class, 'index'])->name('notifications');
        Route::patch('/notifications/{notification}/read', [DashboardNotificationController::class, 'read'])->name('notifications.read');
        Route::get('/profile', [DashboardProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [DashboardProfileController::class, 'update'])->name('profile.update');
        Route::get('/roles', [DashboardRolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [DashboardRolePermissionController::class, 'create'])->name('roles.create');
        Route::post('/roles', [DashboardRolePermissionController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [DashboardRolePermissionController::class, 'show'])->whereNumber('role')->name('roles.show');
        Route::get('/roles/{role}/edit', [DashboardRolePermissionController::class, 'edit'])->whereNumber('role')->name('roles.edit');
        Route::put('/roles/{role}', [DashboardRolePermissionController::class, 'update'])->whereNumber('role')->name('roles.update');
        Route::delete('/roles/{role}', [DashboardRolePermissionController::class, 'destroy'])->whereNumber('role')->name('roles.destroy');
        Route::get('/reports/{record}/attachment', [DashboardResourceController::class, 'downloadReportAttachment'])
            ->whereNumber('record')
            ->name('reports.attachment');
        Route::get('/reports/{record}/print', [DashboardResourceController::class, 'printReport'])
            ->whereNumber('record')
            ->name('reports.print');

        Route::get('/{resource}', [DashboardResourceController::class, 'index'])->name('resource.index');
        Route::post('/{resource}', [DashboardResourceController::class, 'store'])->name('resource.store');
        Route::put('/{resource}/{record}', [DashboardResourceController::class, 'update'])->whereNumber('record')->name('resource.update');
        Route::delete('/{resource}/{record}', [DashboardResourceController::class, 'destroy'])->whereNumber('record')->name('resource.destroy');
    });
});

Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send');
