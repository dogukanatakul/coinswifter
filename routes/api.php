<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'api.',
    'middleware' => [
        \App\Http\Middleware\LogActivity::class,
    ]
], function () {

    Route::group([
        'prefix' => 'form',
        'as' => 'form.',
    ], function () {
        Route::post('/coin-listing-request', [\App\Http\Controllers\Api\FormController::class, 'coinListingRequest']);
    });
    Route::get('/file/{uuid}', [\App\Http\Controllers\MediaController::class, 'getMedia'])->name('get_media');
    Route::post('/signup', [\App\Http\Controllers\Api\AuthController::class, 'signup']);
    Route::post('/signin', [\App\Http\Controllers\Api\AuthController::class, 'signin']);

    Route::group([
        'prefix' => 'forgot',
    ], function () {
        Route::post('/find', [\App\Http\Controllers\Api\AuthController::class, 'forgotFind']);
        Route::post('/verification', [\App\Http\Controllers\Api\AuthController::class, 'forgotVerification']);
        Route::post('/change', [\App\Http\Controllers\Api\AuthController::class, 'forgotChange']);
    });

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/phone-codes', [\App\Http\Controllers\Api\AuthController::class, 'phone_codes']);
    Route::post('/nationalities', [\App\Http\Controllers\Api\AuthController::class, 'nationalities']);
    Route::group([
        'prefix' => 'exchange',
        'as' => 'exchange.',
    ], function () {
        Route::post('/tokens', [\App\Http\Controllers\Api\CoinActions\Exchange::class, 'tokens']);
        Route::post('/parity/{source}-{coin}', [\App\Http\Controllers\Api\CoinActions\Exchange::class, 'setParity']);
        Route::post('/favorite', [\App\Http\Controllers\Api\CoinActions\Exchange::class, 'setFavorite'])->middleware([\App\Http\Middleware\CheckUser::class]);;
        Route::post('/bot', [\App\Http\Controllers\Api\CoinActions\Exchange::class, 'bot']);
        Route::post('/test', [\App\Http\Controllers\Api\CoinActions\Exchange::class, 'test']);
    });

    Route::middleware([\App\Http\Middleware\CheckUser::class])->group(function () {
        Route::post('/delete-withdrawal-wallet', [\App\Http\Controllers\Api\CoinActions\WalletController::class, 'deleteWithdrawalWallet']);
        Route::post('/withdrawal-wallet', [\App\Http\Controllers\Api\CoinActions\WalletController::class, 'withdrawalWallet']);
        Route::post('/get-adress', [\App\Http\Controllers\Api\AuthController::class, 'adress']);
        Route::post('/set-adress', [\App\Http\Controllers\Api\AuthController::class, 'adressUpdate']);
        Route::post('/banks', [\App\Http\Controllers\Api\AuthController::class, 'banks']);
        Route::post('/sessions', [\App\Http\Controllers\Api\AuthController::class, 'sessions']);
        Route::post('/bank-set', [\App\Http\Controllers\Api\AuthController::class, 'bankSet']);
        Route::post('/bank-set-primary', [\App\Http\Controllers\Api\AuthController::class, 'bankSetPrimary']);
        Route::post('/bank-delete', [\App\Http\Controllers\Api\AuthController::class, 'bankDelete']);
        Route::post('/password-reset', [\App\Http\Controllers\Api\AuthController::class, 'passwordReset']);
        Route::post('/withdrawal', [\App\Http\Controllers\Api\CoinActions\WalletController::class, 'withdrawal']);
        Route::post('/contact-get', [\App\Http\Controllers\Api\AuthController::class, 'getContact']);
        Route::post('/contact-update', [\App\Http\Controllers\Api\AuthController::class, 'updateContact']);
        Route::post('/kyc-get', [\App\Http\Controllers\Api\AuthController::class, 'getKYC']);
        Route::post('/kyc-set', [\App\Http\Controllers\Api\AuthController::class, 'setKYC']);
        Route::post('/referer', [\App\Http\Controllers\Api\AuthController::class, 'referer']);
        Route::post('/verification', [\App\Http\Controllers\Api\AuthController::class, 'verificationJob']);
        Route::post('/verification-control', [\App\Http\Controllers\Api\AuthController::class, 'verificationControl']);
        Route::post('/verification-info', [\App\Http\Controllers\Api\AuthController::class, 'verificationInfo']);
        Route::post('/contact-verify', [\App\Http\Controllers\Api\AuthController::class, 'contractVerify']);
        Route::post('/user', [\App\Http\Controllers\Api\AuthController::class, 'user']);

        Route::post('/order', [\App\Http\Controllers\Api\CoinActions\CoinsController::class, 'order'])
            ->middleware([
                \App\Http\Middleware\LockedUser::class
            ]);
        // coins
        Route::post('/delete-order/{uuid}', [\App\Http\Controllers\Api\CoinActions\CoinsController::class, 'deleteOrder'])
            ->middleware([
                \App\Http\Middleware\LockedUser::class
            ]);
        Route::post('/create-wallet/{token}', [\App\Http\Controllers\Api\CoinActions\WalletController::class, 'createWallet'])
            ->middleware([
                \App\Http\Middleware\LockedUser::class
            ]);
        Route::post('/my-wallets', [\App\Http\Controllers\Api\CoinActions\WalletController::class, 'myWallets']);
    });
    Route::group([
        'prefix' => 'network',
        'as' => 'network.',
        'middleware' => ['throttle:2000,1']
    ], function () {
        Route::post('/wallets/{key}/{network}', [\App\Http\Controllers\ToolsController::class, 'walletList']);
        Route::group([
            'prefix' => 'tron',
            'as' => 'tron.',
        ], function () {
            Route::post('/random-wallets/{type}', [\App\Http\Controllers\ToolsController::class, 'randomWallets']);
        });
        Route::post('/set-transactions/{network}', [\App\Http\Controllers\ToolsController::class, 'setTransactions']);
        Route::post('/get-transactions/{network}', [\App\Http\Controllers\ToolsController::class, 'getTransactions']);
        Route::post('/set-blocks', [\App\Http\Controllers\ToolsController::class, 'setBlocks']);
        Route::post('/get-blocks/{network}', [\App\Http\Controllers\ToolsController::class, 'getBlocks']);
    });
});




//Route::get('/backup-get', [\App\Http\Controllers\ToolsController::class, 'backUp']);
//Route::get('/backup-set', [\App\Http\Controllers\ToolsController::class, 'insertFromJson']);
//Route::get('/list-tokens', [\App\Http\Controllers\ToolsController::class, 'listTokens']);
