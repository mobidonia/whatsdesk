<?php

use Illuminate\Support\Facades\Route;
use Modules\Whatsappcall\Http\Controllers\CallController;
use Modules\Whatsappcall\Http\Controllers\SetupController;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    // Calls dashboard
    Route::get('/whatsappcall/calls', [CallController::class, 'index'])->name('whatsappcall.calls.index');
    // Settings / setup UI (embedded in Wpbox setup page via include or direct route)
    Route::get('/whatsappcall/settings', [SetupController::class, 'index'])->name('whatsappcall.settings');
    Route::post('/whatsappcall/settings', [SetupController::class, 'store'])->name('whatsappcall.settings.store');

    // Business-initiated call permission and start call
    Route::get('/whatsappcall/bic/permission-status', [CallController::class, 'getPermissionStatus'])->name('whatsappcall.bic.permission_status');
    Route::post('/whatsappcall/bic/request-permission', [CallController::class, 'requestPermission'])->name('whatsappcall.bic.request');
    Route::post('/whatsappcall/bic/start', [CallController::class, 'startBusinessCall'])->name('whatsappcall.bic.start');

    // Chat polling for UIC calls
    Route::get('/whatsappcall/chat/active-calls', [CallController::class, 'activeForChat'])->name('whatsappcall.chat.active');

    // UIC: pre-accept and accept
    Route::post('/whatsappcall/uic/pre-accept', [CallController::class, 'preAcceptWhatsAppCall'])->name('whatsappcall.uic.pre_accept');
    Route::post('/whatsappcall/uic/accept', [CallController::class, 'acceptWhatsAppCall'])->name('whatsappcall.uic.accept');
    Route::post('/whatsappcall/uic/terminate', [CallController::class, 'terminateWhatsAppCall'])->name('whatsappcall.uic.terminate');

    // Reports API endpoints
    Route::prefix('api/whatsappcall/reports')->group(function () {
        Route::get('call-statistics', [\Modules\Whatsappcall\Http\Controllers\ReportsController::class, 'callStatistics']);
        Route::get('call-performance', [\Modules\Whatsappcall\Http\Controllers\ReportsController::class, 'callPerformance']);
    });
});

// Webhooks - WhatsApp Calling events
Route::post('/webhook/whatsappcall/calling/{token?}', [CallController::class, 'receiveCallingWebhook'])->name('whatsappcall.webhook');
