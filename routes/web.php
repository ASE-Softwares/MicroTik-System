<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/test',[App\Helpers\Mpesa::class, 'getAccessToken']);
Route::get('/', [App\Http\Controllers\GuestController::class, 'welcome']);
Route::post('/customer/purchase',[App\Http\Controllers\GuestController::class, 'purchase'])->name('purchase');
Auth::routes(['register'=>false]);

Route::post('/mpesa_response',[App\Http\Controllers\GuestController::class, 'responseFromMpesa'])->name('mpesa_response');
Route::post('/validation',[App\Helpers\Mpesa::class, 'mpesaValidation'])->name('mpesa_validate');
Route::post('/confirmation',[App\Helpers\Mpesa::class, 'mpesaConfirmation'])->name('mpesa_confirm');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('router/auto_login/{microtik}',[App\Http\Controllers\HomeController::class, 'router_auto_login'])->name('router_auto_login');
	Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // profiles
	Route::get('/add_profiles', [App\Http\Controllers\AdminController::class, 'showForm'])->name('showForm');
	Route::post('/add_profiles', [App\Http\Controllers\AdminController::class, 'newProfile'])->name('newProfile');
	Route::get('/view_profiles', [App\Http\Controllers\AdminController::class, 'listProfiles'])->name('listProfiles');
    // delele profil
    // Route::get('/', [App\Http\Controllers\AdminController::class, 'deleteProfile'])->name("deleteProfiles");
    // edit profile
    // Route::get('', [App\Http\Controllers\AdminController::class, 'editProfile'])->name('editProfile');




    Route::get('/router_login', [App\Http\Controllers\HomeController::class, 'routerLogin'])->name('router_login');
    Route::post('addRouter', [App\Http\Controllers\HomeController::class, 'add_router'])->name('add_router');
    Route::post('/router_verify', [App\Http\Controllers\HomeController::class, 'init'])->name('router_verify');


    // Admin Manage registration of users
    Route::get('admins', [App\Http\Controllers\AdminAnyController::class, 'index'])->name('admin.index');
    Route::get('create', [App\Http\Controllers\AdminAnyController::class, 'create'])->name('admin.create');
    Route::post('store', [App\Http\Controllers\AdminAnyController::class, 'store'])->name('admin.store');
    Route::get('edit/{user}', [App\Http\Controllers\AdminAnyController::class, 'edit'])->name('admin.edit');
    Route::get('delete', [\App\Http\Controllers\AdminAnyController::class, 'destroy'])->name('admin.delete');
    // Route::get('show/{user}', [App\Http\Controllers\AdminAnyController::class, 'show'])->name('admin.show');

    // get all users
    Route::get('users', [App\Http\Controllers\AdminController::class, 'active_hotspot_users'])->name('admin.allUsers');

    Route::post('/change-router', [App\Http\Controllers\AdminAnyController::class, 'changeRouter'])->name('change-router');

    // get interfaces and their data
    Route::get('interfaces', [App\Http\Controllers\AdminController::class, 'interfaces']);
    Route::get('interfaces/{id}', [App\Http\Controllers\AdminController::class, 'getInterfaceData']);
    
});

