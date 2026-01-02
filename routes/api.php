<?php

use App\Http\Controllers\Api\TenantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::post('/tenants/switch', [TenantController::class, 'switch']);
});

