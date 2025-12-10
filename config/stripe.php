<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | These are your Stripe API keys. You can find them in your Stripe dashboard.
    | The publishable key is used in the frontend, while the secret key is used
    | in the backend.
    |
    */

    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhook Secret
    |--------------------------------------------------------------------------
    |
    | This is the webhook secret used to verify that webhook events are sent
    | from Stripe and not a third party. You can find this in your Stripe
    | dashboard under Developers > Webhooks.
    |
    */

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe API Version
    |--------------------------------------------------------------------------
    |
    | The Stripe API version to use. This is optional but recommended to ensure
    | consistent behavior across different Stripe API versions.
    |
    */

    'api_version' => '2023-10-16',

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for your Stripe payments.
    | Stripe uses lowercase ISO currency codes.
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'myr'), // Malaysian Ringgit

];
