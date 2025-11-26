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

Route::prefix('contacts')->group(function() {
    Route::get('/', 'ContactsController@index');
});


Route::group([
    'middleware' =>[ 'web','impersonate','XssSanitizer','auth'],
    'namespace' => 'Modules\Contacts\Http\Controllers'
], function () {
    Route::prefix('contacts')->group(function() {

        //Contacts
        Route::get('contacts', 'Main@index')->name('contacts.index');
        Route::get('contacts/{contact}/edit', 'Main@edit')->name('contacts.edit');
        Route::get('contacts/create', 'Main@create')->name('contacts.create');
        Route::post('contacts', 'Main@store')->name('contacts.store');
        Route::put('contacts/{contact}', 'Main@update')->name('contacts.update');
        Route::get('contacts/del/{contact}', 'Main@destroy')->name('contacts.delete');
        Route::get('contacts/bulkremove/{contacts}', 'Main@bulkremove')->name('contacts.bulkremove');
        Route::get('contacts/subscribe/{contacts}', 'Main@subscribe')->name('contacts.subscribe');
        Route::get('contacts/unsubscribe/{contacts}', 'Main@unsubscribe')->name('contacts.unsubscribe');
        Route::get('contacts/assigntogroup/{contacts}', 'Main@assigntogroup')->name('contacts.assigntogroup');
        Route::get('contacts/removefromgroup/{contacts}', 'Main@removefromgroup')->name('contacts.removefromgroup');

        //Group
        Route::get('groups', 'GroupsController@index')->name('contacts.groups.index');
        Route::get('groups/{group}/edit', 'GroupsController@edit')->name('contacts.groups.edit');
        Route::get('groups/create', 'GroupsController@create')->name('contacts.groups.create');
        Route::post('groups', 'GroupsController@store')->name('contacts.groups.store');
        Route::put('groups/{group}', 'GroupsController@update')->name('contacts.groups.update');
        Route::get('groups/del/{group}', 'GroupsController@destroy')->name('contacts.groups.delete');

        //Field
        Route::get('fields', 'FieldsController@index')->name('contacts.fields.index');
        Route::get('fields/{field}/edit', 'FieldsController@edit')->name('contacts.fields.edit');
        Route::get('fields/create', 'FieldsController@create')->name('contacts.fields.create');
        Route::post('fields', 'FieldsController@store')->name('contacts.fields.store');
        Route::put('fields/{field}', 'FieldsController@update')->name('contacts.fields.update');
        Route::get('fields/del/{field}', 'FieldsController@destroy')->name('contacts.fields.delete');


        //Import
        Route::get('import', 'Main@importindex')->name('contacts.import.index');
        Route::post('import', 'Main@import')->name('contacts.import.store');
    });
});