<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::prefix('lv-admin')->name('lv-admin.')->middleware('web')->group(function () {
	Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

	// group posts
	Route::prefix('posts')->name('posts.')->group(function () {
		Route::post('post/get-data', [PostController::class, 'getdata'])->name('getdata');
		Route::resource('post', PostController::class);
	});

	// group users
	Route::prefix('users')->name('users.')->group(function () {
		Route::post('user/get-data', [UserController::class, 'getdata'])->name('getdata');
		Route::resource('user', UserController::class)->only('index', 'store', 'destroy', 'edit');
	});

	Route::prefix('roles')->name('roles.')->group(function () {
		Route::post('role/assign', [RoleController::class, 'assignRole'])->name('assign');
		Route::post('role/remove', [RoleController::class, 'removeRole'])->name('remove');

		Route::post('role/get-data', [RoleController::class, 'getdata'])->name('getdata');
		Route::post('role/get-data/user', [RoleController::class, 'getdatauser'])->name('getdata.user');
		Route::post('roles/getassigned/users', [RoleController::class, 'getAssignedUsers'])->name('getassigned.users');

		Route::resource('role', RoleController::class)->only('index', 'store', 'destroy', 'edit');
	});
});
