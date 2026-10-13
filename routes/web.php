<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::view('/', 'pages.index')->name('home');

Route::view('/about', 'pages.about')->name('about');

Route::view('/leadership', 'pages.leadership')->name('leadership');

Route::view('/ministries', 'pages.ministries')->name('ministries');

Route::view('/gallery', 'pages.gallery')->name('gallery');

Route::view('/contact', 'pages.contact')->name('contact');


Route::post('/contact', [ContactController::class, 'send'])
    ->name('contact.send');

