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
    'middleware' => ['web', 'impersonate'],
    'namespace' => 'Modules\Wpbox\Http\Controllers',
], function () {
    Route::group([
        'middleware' => ['verified', 'web', 'auth', 'impersonate', 'XssSanitizer', 'isOwnerOnPro', 'Modules\Wpbox\Http\Middleware\CheckPlan'],
    ], function () {
        //Chat
        Route::get('chat', 'ChatController@index')->name('chat.index');

        //Setup
        Route::get('whatsapp/setup', 'DashboardController@setup')->name('whatsapp.setup');
        Route::post('whatsapp/setup', 'DashboardController@savesetup')->name('whatsapp.store');

        //Campaigns
        Route::get('campaigns', 'CampaignsController@index')->name('campaigns.index');
        Route::get('campaigns/{campaign}/show', 'CampaignsController@show')->name('campaigns.show');
        Route::get('campaigns/create', 'CampaignsController@create')->name('campaigns.create');
        Route::post('campaigns', 'CampaignsController@store')->name('campaigns.store');
        Route::put('campaigns/{campaign}', 'CampaignsController@update')->name('campaigns.update');
        Route::get('campaigns/del/{campaign}', 'CampaignsController@destroy')->name('campaigns.delete');

        //Templates
        Route::get('templates', 'TemplatesController@index')->name('templates.index');
        Route::get('templates/create', 'TemplatesController@create')->name('templates.create');
        Route::post('templates/store', 'TemplatesController@store')->name('templates.store');
        Route::get('templates/load', 'TemplatesController@loadTemplates')->name('templates.load');
        Route::post('templates/submit', 'TemplatesController@submit')->name('templates.submit');
        Route::delete('templates/del/{template}', 'TemplatesController@destroy')->name('templates.destroy');
        Route::post('templates/upload-image', 'TemplatesController@uploadImage')->name('templates.upload-image');
        Route::post('templates/upload-video', 'TemplatesController@uploadVideo')->name('templates.upload-video');
        Route::post('templates/upload-pdf', 'TemplatesController@uploadPdf')->name('templates.upload-pdf');

        //Replies
        Route::get('replies', 'RepliesController@index')->name('replies.index');
        Route::get('replies/{reply}/edit', 'RepliesController@edit')->name('replies.edit');
        Route::get('replies/create', 'RepliesController@create')->name('replies.create');
        Route::post('replies', 'RepliesController@store')->name('replies.store');
        Route::put('replies/{reply}', 'RepliesController@update')->name('replies.update');
        Route::get('replies/del/{reply}', 'RepliesController@destroy')->name('replies.delete');

        //Deactivate and activate bot
        Route::get('campaigns/deactivatebot/{campaign}', 'CampaignsController@deactivateBot')->name('campaigns.deactivatebot');
        Route::get('campaigns/activatebot/{campaign}', 'CampaignsController@activateBot')->name('campaigns.activatebot');

        //Pause and resume campaign
        Route::get('campaigns/pause/{campaign}', 'CampaignsController@pause')->name('campaigns.pause');
        Route::get('campaigns/resume/{campaign}', 'CampaignsController@resume')->name('campaigns.resume');

        //Report
        Route::get('campaigns/report/{campaign}', 'CampaignsController@report')->name('campaigns.report');

        //API
        Route::prefix('api/wpbox')->group(function () {

            Route::get('campaings/apis', 'APIController@index')->name('wpbox.api.index');
            Route::get('info', 'APIController@info')->name('api.info');
            Route::get('chats/{lastmessagetime}/{page?}/{search_query?}', 'ChatController@chatlist');
            Route::get('chat/{contact}', 'ChatController@chatmessages');
            Route::post('send/{contact}', 'ChatController@sendMessageToContact');
            Route::post('sendnote/{contact}', 'ChatController@sendNoteToContact');
            Route::post('sendimage/{contact}', 'ChatController@sendImageMessageToContact');
            Route::post('sendfile/{contact}', 'ChatController@sendDocumentMessageToContact');
            Route::post('assign/{contact}', 'ChatController@assignContact');
            Route::post('setlanguage/{contact}', 'ChatController@setLanguage');
            Route::post('updateContact', 'APIController@updateContact');
            Route::post('updateAIBot', 'APIController@updateAIBot');
            Route::post('updateChatStatus/{contact}', 'ChatController@updateChatStatus');
            Route::post('reopenChat/{contact}', 'ChatController@reopenChat');
            Route::get('contact-groups-and-custom-fields/{contact}', 'APIController@getContactGroupsAndCustomFields');
            Route::get('notes/{contact}', 'APIController@getNotes');

            // Reports API endpoints
            Route::prefix('reports')->group(function () {
                Route::get('open-conversations', 'ReportsController@openConversations');
                Route::get('conversation-traffic', 'ReportsController@conversationTraffic');
                Route::get('conversation-by-agent', 'ReportsController@conversationByAgent');
            });

        });
    });

    //Webhook
    Route::prefix('webhook/wpbox')->group(function () {
        Route::post('receive/{token}', 'ChatController@receiveMessage');
        Route::get('receive/{tokenViaURL}', 'ChatController@verifyWebhook');
        Route::get('sendschuduledmessages', 'CampaignsController@sendSchuduledMessages');
    });

    Route::group([
        'middleware' => ['Modules\Wpbox\Http\Middleware\CheckAPIPlan'],
    ], function () {
        //PUBLIC API
        Route::prefix('api/wpbox')->group(function () {
            Route::get('me', 'APIController@me')->name('wpbox.api.me');
            Route::post('sendtemplatemessage', 'APIController@sendTemplateMessageToPhoneNumber');
            Route::post('sendmessage', 'APIController@sendMessageToPhoneNumber');
            Route::post('sendlistmessage', 'APIController@sendListMessageToPhoneNumber');
            Route::get('getTemplates', 'APIController@getTemplates');
            Route::get('getGroups', 'APIController@getGroups');

            //getCampaigns
            Route::get('getCampaigns', 'APIController@getCampaigns');
            Route::get('getContacts', 'APIController@getContacts');
            Route::post('makeContact', 'APIController@contactApiMake');
            Route::post('sendcampaigns', 'APIController@sendCampaignMessageToPhoneNumber');

            //getSingleContact
            Route::get('getSingleContact', 'APIController@getSingleContact');

            //Mobile App
            Route::post('getConversations/{lastmessagetime}', 'APIController@getConversations');
            Route::post('getMessages', 'APIController@getMessages');
        });
    });

});
