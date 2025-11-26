<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|s
*/


Route::group([
    'middleware' =>[ 'web'],
    'namespace' => 'Modules\Embedwhatsapp\Http\Controllers'
], function () {
    Route::get('/popup/whatsapp', 'Main@index')->name('embedwhatsapp.chat');
        Route::group(['middleware' => ['auth','XssSanitizer']], function () {
            Route::get('/whatsapp/widget/edit', 'Main@edit')->name('embedwhatsapp.edit');
            Route::post('/whatsapp/widget/save', 'Main@store')->name('embedwhatsapp.store');
        });
    });
    
    