<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientControler;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\PriceController;



Route::apiResource('/clients', ClientControler::class);
Route::apiResource('/orders', OrderController::class);
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/prices', PriceController::class);