<?php

use Illuminate\Support\Facades\Route;




try {
    // composer
    Route::prefix('jobs')->group(function () {
        Route::queueMonitor();
    });

    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

} catch (\Exception $e) {
}
Route::get('/fire', function () {
    event(new \App\Events\MessagePushed());
    return 'ok';
});

Route::get('/{any?}', function () {
    if (env('CARE_MODE') && request()->ip() !== env('CARE_IP')) {
        return view('care');
    } else {
        return view('vue3');
    }
})->where('any', '.*')->middleware(\App\Http\Middleware\GetParameters::class)->name('home');
