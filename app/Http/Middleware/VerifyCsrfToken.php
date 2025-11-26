<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'webhook/subscription/*',
        'webhooks/mollie',
        'paddle',
        'razorpaysubscribe/webhook',
        'paddlebilling',
        'webhook/wpbox/receive/*',
        'webhook/wpbox/receive',
        'webhook/whatsappcall/calling/*',
        'webhook/whatsappcall/calling',
        'api/wpbox/*',
        'api/*',
        'stripe/*',
    ];
}
