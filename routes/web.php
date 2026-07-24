<?php

use App\Http\Controllers\Admin\ApiLogController;
use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\CryptoController;
use App\Http\Controllers\Admin\RankingController as AdminRankingController;
use App\Http\Controllers\Admin\RefreshController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CompareController;
use App\Http\Controllers\User\RankingController as UserRankingController;
use App\Http\Controllers\User\SawMethodController;
use App\Http\Controllers\User\WatchlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CryptoChartController;
use App\Http\Controllers\User\ComparisonController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/data-crypto', [CryptoController::class, 'index'])->name('crypto.index');

        Route::get('/ranking-saw', [AdminRankingController::class, 'index'])->name('ranking.index');
        Route::post('/ranking-saw/recalculate', [AdminRankingController::class, 'recalculate'])->name('ranking.recalculate');
        Route::get('/kriteria-bobot', [CriterionController::class, 'index'])->name('criteria.index');
        Route::put('/kriteria-bobot', [CriterionController::class, 'update'])->name('criteria.update');

        Route::get('/refresh-api', [RefreshController::class, 'index'])->name('refresh.index');
        Route::post('/refresh-api', [RefreshController::class, 'refresh'])->middleware('throttle:5,1')->name('refresh.run');

        Route::get('/log-api', [ApiLogController::class, 'index'])->name('logs.index');

        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/export-csv', [ReportController::class, 'exportCsv'])->name('reports.exportCsv');
        
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/ranking-crypto', fn () => redirect()->route('user.ranking.index'))->name('ranking.legacy');
        Route::get('/ranking-saya', [UserRankingController::class, 'index'])->name('ranking.index');
        Route::post('/ranking-saya/coins', [UserRankingController::class, 'store'])->middleware('throttle:20,1')->name('ranking.coins.store');
        Route::delete('/ranking-saya/coins/{cryptoCoin}', [UserRankingController::class, 'destroy'])->name('ranking.coins.destroy');
        Route::post('/ranking-saya/recalculate', [UserRankingController::class, 'recalculate'])->middleware('throttle:5,1')->name('ranking.recalculate');

        Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
        Route::post('/watchlist', [WatchlistController::class, 'store'])->middleware('throttle:20,1')->name('watchlist.store');
        Route::delete('/watchlist/{watchlist}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

        Route::get('/bandingkan-coin', [CompareController::class, 'index'])->name('compare.index');
        Route::get('/crypto/{cryptoCoin}/chart', [CryptoChartController::class, 'show'])->middleware('throttle:60,1')->name('crypto.chart');
        Route::post('/comparison/charts', [CryptoChartController::class, 'compare'])->name('comparison.charts');
        Route::get('/metode-saw', SawMethodController::class)->name('saw.method');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
