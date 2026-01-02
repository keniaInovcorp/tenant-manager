<?php

use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return redirect()->route('tenants.index');
    })->name('home');
    
    Route::resource('tenants', TenantController::class)->except(['destroy']);
    Route::resource('tenants.users', TenantUserController::class)->only(['index', 'create', 'store', 'destroy']);
});
