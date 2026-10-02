<?php

/**
 * Vercel Serverless Function entry point.
 * This forwards all requests to the Laravel public/index.php.
 */

// Set storage path ke /tmp karena Vercel merupakan read-only filesystem
$_ENV['APP_STORAGE'] = '/tmp/storage';
if (!is_dir($_ENV['APP_STORAGE'])) {
    mkdir($_ENV['APP_STORAGE'], 0777, true);
    mkdir($_ENV['APP_STORAGE'].'/framework/views', 0777, true);
    mkdir($_ENV['APP_STORAGE'].'/framework/cache', 0777, true);
    mkdir($_ENV['APP_STORAGE'].'/framework/sessions', 0777, true);
}

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
