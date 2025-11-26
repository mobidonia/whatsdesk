<?php

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
    'middleware' =>[ 'web','impersonate'],
    'namespace' => 'Modules\Agents\Http\Controllers'
], function () {
    Route::prefix('agent')->group(function() {

   
            Route::get('/list', 'Main@index')->name('agent.index');
            Route::get('/{agent}/edit', 'Main@edit')->name('agent.edit');
            Route::get('/create', 'Main@create')->name('agent.create');
            Route::post('/', 'Main@store')->name('agent.store');
            Route::put('/{agent}', 'Main@update')->name('agent.update');
            Route::get('/del/{agent}', 'Main@destroy')->name('agent.delete');
            Route::get('/loginas/{agent}', 'Main@loginas')->name('agent.loginas');
            
        


    });
});