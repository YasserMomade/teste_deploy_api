<?php

use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CounterController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CountryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ClientControler;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ObservationController;
use App\Http\Controllers\Api\V1\PriceController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\StoreController;
use App\Http\Controllers\Api\V1\StatusController;
use App\Http\Controllers\Api\V1\CostumerController;
use App\Http\Controllers\Api\V1\CostumerFileController;
use App\Http\Controllers\Api\V1\OrdersRequestController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\WhatsAppController;



Route::prefix('v1')->group(function () {

    Route::post('/setup/admin', [AuthController::class, 'setupAdmin'])
        ->middleware('throttle:5,1')
        ->name('setup.admin');

    Route::apiResource('/files', FileController::class);

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('auth.login');

    Route::get('/order-statiscs', [OrderController::class, 'statisc']);

    Route::get('/tracking/{tracking}', [OrderController::class, 'tracking']);

        Route::apiResource('/costumer', CostumerController::class);
        Route::post('/customer-request',       [CostumerController::class, 'store']);
        Route::post('/customer-file',          [CostumerFileController::class, 'store']);
    
        Route::get('/orders/unsync', [OrderController::class, 'indexUnSync']);
        Route::apiResource('/orders', OrderController::class);

    Route::get('/orders/unsync', [OrderController::class, 'indexUnSync']);
    Route::apiResource('/orders', OrderController::class);

    Route::post('/webhook/stripe', [InvoiceController::class, 'handleWebhook'])
        ->name('stripe.webhook')
        ->withoutMiddleware('throttle');
    Route::middleware(['auth:sanctum', 'active.user', 'audit.context'])->group(function () {

        Route::post('/order-recivied', [WhatsAppController::class, 'orderRecivied']);


        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me',     [AuthController::class, 'me'])->name('auth.me');


        Route::apiResource('/clients', ClientControler::class);

        Route::apiResource('/categories', CategoryController::class);

        Route::apiResource('/prices', PriceController::class);

        Route::apiResource('/invoice', InvoiceController::class);

        Route::apiResource('/status', StatusController::class);

        Route::apiResource('/orderRequest', OrdersRequestController::class);

        Route::apiResource('countries', CountryController::class);

        Route::apiResource('stores', StoreController::class);

        Route::post('/invoice/{id}/payment-link', [InvoiceController::class, 'generatePaymentLink'])->name('invoice.payment-link');



        Route::prefix('orders/{orderID}/observations')->group(function () {
            Route::get('/', [ObservationController::class, 'index'])->name('observations.index');
            Route::post('/', [ObservationController::class, 'store'])->name('observations.store');
            Route::get('/{observation}', [ObservationController::class, 'show'])->name('observations.show');
            Route::put('/{observation}', [ObservationController::class, 'update'])->name('observations.update');
            Route::delete('/{observation}', [ObservationController::class, 'destroy'])->name('observations.destroy');
        });

        // == So admin ==
        Route::middleware('role:admin,Manager')->group(function () {


            Route::apiResource('counters',  CounterController::class);


            Route::apiResource('users', UserController::class);
            Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
                ->name('users.reset-password');

            Route::get('audit-logs',      [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        });

        // == Reports = admin and manager ==
        Route::middleware('role:admin,Manager')->prefix('reports')->group(function () {

            Route::get('/financial',   [ReportController::class, 'financial'])->name('reports.financial');
            Route::get('/operational',   [ReportController::class, 'operational'])->name('reports.operational');
            Route::get('/exception',   [ReportController::class, 'exception'])->name('reports.exception');

            Route::get('/financial/export',   [ReportController::class, 'exportFinancial'])->name('reports.financial.export');
            Route::get('/operational/export', [ReportController::class, 'exportOperational'])->name('reports.operational.export');
            Route::get('/exception/export', [ReportController::class, 'exportException'])->name('reports.exception.export');
        });
    });
});
