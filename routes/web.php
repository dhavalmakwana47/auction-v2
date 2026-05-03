<?php

use App\Http\Controllers\RaAuthController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\NpvCategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [AuctionController::class, 'dashboard'])
    ->middleware(['auth', 'verified', 'permission:view dashboard'])->name('dashboard');

Route::middleware(['auth', 'role:admin|scrutinizer|bidder'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('npv-categories', NpvCategoryController::class)
        ->middleware([
            'index'   => 'permission:view npv-categories',
            'show'    => 'permission:view npv-categories',
            'create'  => 'permission:create npv-categories',
            'store'   => 'permission:create npv-categories',
            'edit'    => 'permission:edit npv-categories',
            'update'  => 'permission:edit npv-categories',
            'destroy' => 'permission:delete npv-categories',
        ]);
    Route::get('npv-categories-datatable', [NpvCategoryController::class, 'datatable'])
        ->middleware('permission:view npv-categories')
        ->name('npv-categories.datatable');

    Route::resource('auctions', AuctionController::class)
        ->middleware([
            'index'   => 'permission:view auctions',
            'show'    => 'permission:view auctions',
            'create'  => 'permission:create auctions',
            'store'   => 'permission:create auctions',
            'edit'    => 'permission:edit auctions',
            'update'  => 'permission:edit auctions',
            'destroy' => 'permission:delete auctions',
        ]);
    Route::get('auctions-datatable', [AuctionController::class, 'datatable'])
        ->middleware('permission:view auctions')
        ->name('auctions.datatable');

    Route::post('auctions/{auction}/start-challenge', [AuctionController::class, 'startChallenge'])
        ->middleware('permission:edit auctions')
        ->name('auctions.start-challenge');

    Route::post('auctions/{auction}/edit-values', [AuctionController::class, 'editValues'])
        ->middleware('permission:edit auctions')
        ->name('auctions.edit-values');

    Route::post('auctions/{auction}/end-challenge', [AuctionController::class, 'endChallenge'])
        ->middleware('permission:edit auctions')
        ->name('auctions.end-challenge');

    Route::get('auctions/{auction}/download-report', [AuctionController::class, 'downloadReport'])
        ->middleware('permission:view auctions')
        ->name('auctions.download-report');

    Route::resource('users', UserController::class)
        ->middleware([
            'index'   => 'permission:view users',
            'show'    => 'permission:view users',
            'create'  => 'permission:create users',
            'store'   => 'permission:create users',
            'edit'    => 'permission:edit users',
            'update'  => 'permission:edit users',
            'destroy' => 'permission:delete users',
        ]);
    Route::get('users-datatable', [UserController::class, 'datatable'])
        ->middleware('permission:view users')
        ->name('users.datatable');

    Route::resource('roles', RoleController::class)
        ->middleware([
            'index'   => 'permission:view roles',
            'show'    => 'permission:view roles',
            'create'  => 'permission:create roles',
            'store'   => 'permission:create roles',
            'edit'    => 'permission:edit roles',
            'update'  => 'permission:edit roles',
            'destroy' => 'permission:delete roles',
        ]);
    Route::get('roles-datatable', [RoleController::class, 'datatable'])
        ->middleware('permission:view roles')
        ->name('roles.datatable');
});

// ── RA OTP Login ──
Route::prefix('ra')->name('ra.')->group(function () {
    Route::get('login',       [RaAuthController::class, 'showLoginForm'])->name('login');
    Route::post('send-otp',   [RaAuthController::class, 'sendOtp'])->name('send-otp');
    Route::get('otp',         [RaAuthController::class, 'showOtpForm'])->name('otp.form');
    Route::post('verify-otp', [RaAuthController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('logout',     [RaAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard',   [RaAuthController::class, 'dashboard'])->middleware(['auth', 'permission:view ra-dashboard'])->name('dashboard');
    Route::get('auction/{auction}',      [RaAuthController::class, 'auctionPortal'])->middleware(['auth', 'permission:view ra-dashboard'])->name('auction.portal');
    Route::get('auction/{auction}/top-bids',  [RaAuthController::class, 'topBids'])->middleware(['auth', 'permission:view ra-dashboard'])->name('auction.top-bids');
    Route::get('auction/{auction}/my-bids',   [RaAuthController::class, 'myBids'])->middleware(['auth', 'permission:view ra-dashboard'])->name('auction.my-bids');
    Route::post('auction/{auction}/bid',  [RaAuthController::class, 'placeBid'])->middleware(['auth', 'permission:view ra-dashboard'])->name('auction.bid');
});

require __DIR__.'/auth.php';
