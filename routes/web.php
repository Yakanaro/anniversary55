<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\HostSessionController;
use App\Http\Controllers\InvitationController;
use App\Http\Middleware\EnsureHostIsAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', InvitationController::class)->name('invitation');

Route::post('/guests', [GuestController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('guests.store');

Route::get('/host/login', [HostSessionController::class, 'create'])->name('host.login');
Route::post('/host/login', [HostSessionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('host.login.store');

Route::middleware(EnsureHostIsAuthenticated::class)->group(function () {
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
    Route::post('/host/logout', [HostSessionController::class, 'destroy'])->name('host.logout');
});
