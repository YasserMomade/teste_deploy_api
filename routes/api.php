<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientControler;
use App\Http\Controllers\Api\V1\OrderController;

Route::apiResource('/clients', ClientControler::class);
Route::apiResource('/orders', OrderController::class);