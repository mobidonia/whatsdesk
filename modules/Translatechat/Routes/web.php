<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvidesr within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'namespace' => 'Modules\Translatechat\Http\Controllers'
], function() {
    Route::post('/api/translate/convert-style', 'Main@convertStyle');
    Route::get('/api/translate/summarize-chat/{chat_id}', 'Main@summarizeChat');
    Route::post('/api/translate/ask-ai', 'Main@askAI');
});
