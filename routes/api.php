<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CounterController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CountryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientControler;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\PriceController;



Route::apiResource('/clients', ClientControler::class);

Route::apiResource('/orders', OrderController::class);

Route::prefix('v1')->group(function(){

        Route::post('/setup/admin', [AuthController::class, 'setupAdmin'])
         ->middleware('throttle:5,1')
         ->name('setup.admin');

         Route::post('/auth/login', [AuthController::class, 'login'])
         ->middleware('throttle:10,1')
         ->name('auth.login');

         Route::middleware(['auth:sanctum', 'active.user'])->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me',     [AuthController::class, 'me'])->name('auth.me');


        
        // == So admin ==
        Route::middleware('role:admin')->group(function () {

            Route::apiResource('countries', CountryController::class);
            Route::apiResource('counters',  CounterController::class);
            
            Route::apiResource('users', UserController::class);
            Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
                 ->name('users.reset-password');
        
           
        }); 
    });

});

