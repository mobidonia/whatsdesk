<?php

use Illuminate\Support\Facades\Route;
use Modules\Setup\Http\Controllers\SetupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web'])->prefix('install')->group(function () {
    // Step 1: Welcome
    Route::get('/', [SetupController::class, 'index'])->name('setup.index');

    // Step 2: Site Configuration
    Route::get('/site', [SetupController::class, 'site'])->name('setup.site');
    Route::post('/site', [SetupController::class, 'saveSite'])->name('setup.site.save');

    // Step 3: Database Configuration
    Route::get('/database', [SetupController::class, 'database'])->name('setup.database');
    Route::post('/database', [SetupController::class, 'saveDatabase'])->name('setup.database.save');

    // Step 4: Finalize
    Route::get('/finalize', [SetupController::class, 'finalize'])->name('setup.finalize');
    Route::post('/finalize', [SetupController::class, 'complete'])->name('setup.complete');
});
