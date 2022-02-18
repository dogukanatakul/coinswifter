<?php

use Illuminate\Support\Facades\Route;


Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
], function () {
    Route::post('/user-activity', [\App\Http\Controllers\Admin\UserActivity::class, 'log_activity']);
});


try {
    // composer
    Route::prefix('jobs')->group(function () {
        Route::queueMonitor();
    });

    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

} catch (\Exception $e) {
}

Route::get('/{any?}', function () {
    if (env('CARE_MODE') && request()->ip() !== env('CARE_IP')) {
        return view('care');
    } else {
        return view('vue3');
    }
})->where('any', '.*')->middleware(\App\Http\Middleware\GetParameters::class)->name('home');
