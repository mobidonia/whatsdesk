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
    'middleware' => ['web'],
    'namespace' => 'Modules\Websupportwidget\Http\Controllers',
], function () {
    // Public widget routes
    Route::get('/websupport/widget', 'Main@widget')->name('websupportwidget.widget');
    Route::post('/websupport/send-email', 'Main@sendEmail')->name('websupportwidget.send-email');
    Route::get('/websupport/knowledge-articles', 'Main@getKnowledgeArticles')->name('websupportwidget.knowledge-articles');
    Route::get('/websupport/knowledge-categories', 'Main@getKnowledgeCategories')->name('websupportwidget.knowledge-categories');

    // Public debug route
    Route::get('/websupport/debug', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'Public route working',
            'timestamp' => now()->toDateTimeString(),
        ]);
    });

    Route::group(['middleware' => ['auth', 'XssSanitizer']], function () {
        // Admin routes
        Route::get('/websupport/configure', 'Main@edit')->name('websupportwidget.edit');
        Route::post('/websupport/configure', 'Main@store')->name('websupportwidget.store');
        Route::get('/websupport/demo', 'Main@demo')->name('websupportwidget.demo');

        // Debug route
        Route::get('/websupport/test', function () {
            return response()->json([
                'status' => 'success',
                'message' => 'Routes are working',
                'user' => auth()->check() ? auth()->user()->name : 'Not authenticated',
                'company_id' => auth()->check() ? auth()->user()->company_id : 'No company',
            ]);
        })->name('websupportwidget.test');
    });
});
