<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientControler;
use App\Http\Controllers\OrderController;

Route::apiResource('/clients', ClientControler::class);
Route::apiResource('/orders', OrderController::class);