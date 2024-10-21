<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['api-signature']], function () {
    Route::get('order/{order:id}', [\App\Http\Controllers\API\OrderController::class, 'show']);
    Route::post('order', [\App\Http\Controllers\API\OrderController::class, 'store'])->name('api.order');
    Route::patch('order/{order}/cancel', [\App\Http\Controllers\API\OrderController::class, 'cancel']);

    Route::post('order/{order}/dispute', [\App\Http\Controllers\API\DisputeController::class, 'store'])->name('api.dispute');
    Route::get('order/{order}/dispute', [\App\Http\Controllers\API\DisputeController::class, 'show']);
});

Route::get('payment-gateways', [\App\Http\Controllers\API\PaymentGatewayController::class, 'index']);
Route::get('currencies', [\App\Http\Controllers\API\CurrencyController::class, 'index']);

Route::post('sms', [\App\Http\Controllers\API\SmsController::class, 'store'])->middleware(['idempotency']);
