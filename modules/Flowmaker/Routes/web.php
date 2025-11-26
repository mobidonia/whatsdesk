<?php

use Illuminate\Support\Facades\Route;
use Modules\Flowmaker\Http\Controllers\Main;
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


Route::group([
    'middleware' => ['web', 'impersonate'],
    'namespace' => 'Modules\Flowmaker\Http\Controllers',
], function () {
    Route::group([
        'middleware' => ['verified', 'web', 'auth', 'impersonate'],
    ], function () {
        Route::prefix('flowmaker')->group(function() {
            Route::get('/flows_maker_not_installed', [Main::class, 'index'])->name('flowmaker.not_installed');
        });
    });
});
