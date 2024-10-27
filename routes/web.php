<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['middleware' => ['auth', 'banned']], function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => ['auth', 'banned', 'role:Trader|Super Admin']], function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::resource('/payment-details', \App\Http\Controllers\PaymentDetailController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    //orders
    Route::resource('/orders', \App\Http\Controllers\OrderController::class)->only(['index']);
    Route::patch('/orders/{order}/accept', [\App\Http\Controllers\OrderController::class, 'acceptOrder'])->name('orders.accept');

    //disputes
    Route::get('/disputes', [\App\Http\Controllers\DisputeController::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{dispute}/receipt', [\App\Http\Controllers\DisputeController::class, 'receipt'])->name('disputes.receipt');
    Route::patch('/disputes/{dispute}/accept', [\App\Http\Controllers\DisputeController::class, 'accept'])->name('disputes.accept');
    Route::patch('/disputes/{dispute}/cancel', [\App\Http\Controllers\DisputeController::class, 'cancel'])->name('disputes.cancel');
    Route::patch('/disputes/{dispute}/rollback', [\App\Http\Controllers\DisputeController::class, 'rollback'])->name('disputes.rollback');

    //app
    Route::get('/apk', [\App\Http\Controllers\ApkController::class, 'index'])->name('apk.index');
    Route::get('/sms.apk', [\App\Http\Controllers\ApkController::class, 'download'])->name('app.download');

    Route::get('/wallet', [\App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');

    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');

    Route::get('/sms-logs', [\App\Http\Controllers\SmsLogController::class, 'index'])->name('sms-logs.index');

    Route::any('auth/telegram/callback', [\App\Http\Controllers\Auth\SocialController::class, 'callback']);
});

//common
Route::group(['middleware' => ['auth', 'banned', 'role:Trader|Super Admin']], function () {
    Route::get('/modal/sms-logs/{user}', [\App\Http\Controllers\ModalController::class, 'smsLogs'])->name('modal.user.sms-logs');
});

Route::group(['middleware' => ['auth', 'banned', 'role:Merchant|Super Admin']], function () {
    Route::resource('/merchants', \App\Http\Controllers\MerchantController::class)->only(['index', 'show', 'create', 'store']);
    Route::patch('/merchants/{merchant}/callback', [\App\Http\Controllers\MerchantController::class, 'updateCallbackURL'])->name('merchants.callback.update');
    Route::patch('/merchants/{merchant}/commission', [\App\Http\Controllers\MerchantController::class, 'updateCommissionSettings'])->name('merchants.commission.update');
});

Route::group(['prefix' => 'admin', 'as'=>'admin.', 'middleware' => ['auth', 'banned', 'role:Super Admin']], function () {
    Route::resource('/users', \App\Http\Controllers\Admin\UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('/payment-gateways', \App\Http\Controllers\Admin\PaymentGatewayController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');

    Route::resource('/currencies', \App\Http\Controllers\Admin\CurrencyController::class)->only(['index']);
    Route::get('currencies/{currency}/price-parsers', [\App\Http\Controllers\Admin\PriceParserController::class, 'edit'])->name('currencies.price-parsers.edit');
    Route::patch('currencies/{currency}/price-parsers', [\App\Http\Controllers\Admin\PriceParserController::class, 'update'])->name('currencies.price-parsers.update');

    Route::resource('/sms-parsers', \App\Http\Controllers\Admin\SmsParserController::class)->except(['show']);
    Route::get('/sms-logs', [\App\Http\Controllers\Admin\SmsLogController::class, 'index'])->name('sms-logs.index');
    Route::get('/payment-details', [\App\Http\Controllers\Admin\PaymentDetailController::class, 'index'])->name('payment-details.index');
    Route::get('/payment-details/{paymentDetail}/edit', [\App\Http\Controllers\PaymentDetailController::class, 'edit'])->name('payment-details.edit');

    Route::get('/disputes', [\App\Http\Controllers\Admin\DisputeController::class, 'index'])->name('disputes.index');

    Route::group(['as'=>'api.'], function () {
        Route::resource('/api/orders', \App\Http\Controllers\Admin\API\OrderController::class)
            ->only('index', 'store');
        Route::resource('/api/disputes', \App\Http\Controllers\Admin\API\DisputeController::class)
            ->only('index', 'store');
    });

    Route::get('/users/{user}/wallet', [\App\Http\Controllers\Admin\UserWalletController::class, 'index'])->name('users.wallet.index');
    Route::post('/users/{user}/wallet/deposit', [\App\Http\Controllers\Admin\UserWalletController::class, 'deposit'])->name('users.wallet.deposit');
    Route::post('/users/{user}/wallet/withdraw', [\App\Http\Controllers\Admin\UserWalletController::class, 'withdraw'])->name('users.wallet.withdraw');

    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/update/prime-time-bonus', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePrimeTimeBonus'])->name('settings.update.prime-time-bonus');

    Route::resource('/notifications', \App\Http\Controllers\Admin\NotificationController::class)->only('index', 'store');

    Route::get('/merchants', [\App\Http\Controllers\Admin\MerchantController::class, 'index'])->name('merchants.index');
    Route::get('/merchants/{merchant}', [\App\Http\Controllers\MerchantController::class, 'show'])->name('merchants.show');
    Route::patch('/merchants/{merchant}/ban', [\App\Http\Controllers\Admin\MerchantController::class, 'ban'])->name('merchants.ban');
    Route::patch('/merchants/{merchant}/unban', [\App\Http\Controllers\Admin\MerchantController::class, 'unban'])->name('merchants.unban');
    Route::patch('/merchants/{merchant}/validated', [\App\Http\Controllers\Admin\MerchantController::class, 'validated'])->name('merchants.validated');
});

Route::any('/telegram-bot/{token}/webhook', [\App\Http\Controllers\TelegramBotWebhookController::class, 'store'])->name('telegram-bot.webhook');

require __DIR__.'/auth.php';
