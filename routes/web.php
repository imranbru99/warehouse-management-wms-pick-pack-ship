<?php

declare(strict_types=1);

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\TrackShipmentController;
use App\Http\Middleware\SetMarketingLocale;
use Illuminate\Support\Facades\Route;

Route::middleware([SetMarketingLocale::class])->group(function () {
    Route::get('/locale/{locale}', fn () => redirect()->back())->name('locale.switch');

    Route::controller(MarketingController::class)->group(function () {
        Route::get('/', 'home')->name('marketing.home');
        Route::get('/features', 'features')->name('marketing.features');
        Route::get('/pricing', 'pricing')->name('marketing.pricing');
        Route::get('/demo', 'demo')->name('marketing.demo');
        Route::get('/docs', 'docs')->name('marketing.docs');
        Route::get('/about', 'about')->name('marketing.about');
        Route::get('/contact', 'contact')->name('marketing.contact');
        Route::get('/legal/terms', 'terms')->name('marketing.terms');
        Route::get('/legal/privacy', 'privacy')->name('marketing.privacy');
    });

    Route::get('/track/{number}', [TrackShipmentController::class, 'show'])->name('track.show');
});
