<?php

// 1. Force HTTPS detection for Vercel serverless proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// 2. Ensure APP_KEY exists for encryption/sessions on Vercel
$defaultAppKey = 'base64:f0yGwnjHurwHkn9ci6msbqcwlFReY35XQrQdz+mmY1o=';
if (!getenv('APP_KEY') || trim(getenv('APP_KEY')) === '') {
    putenv("APP_KEY={$defaultAppKey}");
    $_ENV['APP_KEY'] = $defaultAppKey;
    $_SERVER['APP_KEY'] = $defaultAppKey;
}

if (!getenv('APP_ENV')) {
    putenv('APP_ENV=production');
    $_ENV['APP_ENV'] = 'production';
    $_SERVER['APP_ENV'] = 'production';
}

// 3. Dynamic APP_URL detection from Vercel domain
if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && (!getenv('APP_URL') || getenv('APP_URL') === 'http://127.0.0.1:8000' || getenv('APP_URL') === 'http://localhost')) {
    $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
    $detectedUrl = $proto . '://' . $_SERVER['HTTP_X_FORWARDED_HOST'];
    putenv("APP_URL={$detectedUrl}");
    $_ENV['APP_URL'] = $detectedUrl;
    $_SERVER['APP_URL'] = $detectedUrl;
}

// 4. Ensure writable storage directories in /tmp for Vercel serverless environment
putenv('LARAVEL_STORAGE_PATH=/tmp/storage');
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/storage/app/public/profiles',
    '/tmp/storage/app/public/qr-codes',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 5. Ensure SSL CA certificate exists for TiDB Cloud MySQL connection
$bundledCa = dirname(__DIR__) . '/database/certs/ca.pem';
if (file_exists($bundledCa)) {
    $currentCa = getenv('MYSQL_ATTR_SSL_CA');
    if (!$currentCa || !file_exists($currentCa)) {
        putenv("MYSQL_ATTR_SSL_CA={$bundledCa}");
        $_ENV['MYSQL_ATTR_SSL_CA'] = $bundledCa;
        $_SERVER['MYSQL_ATTR_SSL_CA'] = $bundledCa;
    }
}

// 6. Ensure SQLite database fallback exists and is writable in /tmp
if (getenv('DB_CONNECTION') === 'sqlite') {
    $tmpDb = '/tmp/database.sqlite';
    if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
        $bundledDb = dirname(__DIR__) . '/database/database.sqlite';
        if (file_exists($bundledDb) && filesize($bundledDb) > 0) {
            @copy($bundledDb, $tmpDb);
        } else {
            @touch($tmpDb);
        }
    }
    if (file_exists($tmpDb)) {
        @chmod($tmpDb, 0666);
    }
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// 7. Forward to public/index.php
require dirname(__DIR__) . '/public/index.php';

