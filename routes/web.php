<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::prefix('lv-admin')->name('lv-admin.')->middleware('web')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
});
