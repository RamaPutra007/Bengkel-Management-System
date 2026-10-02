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

require __DIR__ . '/../public/index.php';
