<?php

return [
    /*
    |--------------------------------------------------------------------------
    | KiPay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | KiPay (https://kipay.id/) provides modern QRIS payment infrastructure.
    | You can configure your API Key and Webhook Secret here or via the Admin UI.
    |
    */

    'api_key' => env('KIPAY_API_KEY', 'qpg_pub_wECkxNn7oCDosZl_GPczDLTnQLB8JZAi'),
    'webhook_secret' => env('KIPAY_WEBHOOK_SECRET', ''),
    'base_url' => env('KIPAY_BASE_URL', 'https://api.kipay.id'),
    'mode' => env('KIPAY_MODE', 'sandbox'), // 'sandbox' or 'production'
];
