<?php

// 1. Force HTTPS and Client IP detection for Vercel serverless proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

if (empty($_SERVER['REMOTE_ADDR'])) {
    $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? '127.0.0.1';
    if (str_contains($clientIp, ',')) {
        $clientIp = trim(explode(',', $clientIp)[0]);
    }
    $_SERVER['REMOTE_ADDR'] = !empty($clientIp) ? $clientIp : '127.0.0.1';
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

// 3. Dynamic APP_URL and ASSET_URL detection from Vercel domain
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? null;
if ($host && (!getenv('APP_URL') || getenv('APP_URL') === 'http://127.0.0.1:8000' || getenv('APP_URL') === 'http://localhost')) {
    if (str_contains($host, ',')) {
        $host = trim(explode(',', $host)[0]);
    }
    $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
             (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
    $detectedUrl = "{$proto}://{$host}";
    putenv("APP_URL={$detectedUrl}");
    $_ENV['APP_URL'] = $detectedUrl;
    $_SERVER['APP_URL'] = $detectedUrl;
    putenv("ASSET_URL={$detectedUrl}");
    $_ENV['ASSET_URL'] = $detectedUrl;
    $_SERVER['ASSET_URL'] = $detectedUrl;
}

// 4. Clean up Vite dev hot file if it accidentally exists
$hotFile = dirname(__DIR__) . '/public/hot';
if (file_exists($hotFile)) {
    @unlink($hotFile);
}

// 5. Ensure writable storage directories in /tmp for Vercel serverless environment
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

// 6. Ensure SSL CA certificate exists for TiDB Cloud MySQL connection
$bundledCa = dirname(__DIR__) . '/database/certs/ca.pem';
if (file_exists($bundledCa)) {
    $currentCa = getenv('MYSQL_ATTR_SSL_CA');
    if (!$currentCa || !file_exists($currentCa)) {
        putenv("MYSQL_ATTR_SSL_CA={$bundledCa}");
        $_ENV['MYSQL_ATTR_SSL_CA'] = $bundledCa;
        $_SERVER['MYSQL_ATTR_SSL_CA'] = $bundledCa;
    }
}

// 7. Configure Database Connection & Intelligent Fallback
// Set TiDB Cloud defaults
if (!getenv('DB_HOST')) {
    putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
    $_ENV['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
    $_SERVER['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
}
if (!getenv('DB_PORT')) {
    putenv('DB_PORT=4000');
    $_ENV['DB_PORT'] = '4000';
    $_SERVER['DB_PORT'] = '4000';
}
if (!getenv('DB_DATABASE')) {
    putenv('DB_DATABASE=qr_social');
    $_ENV['DB_DATABASE'] = 'qr_social';
    $_SERVER['DB_DATABASE'] = 'qr_social';
}
if (!getenv('DB_USERNAME')) {
    putenv('DB_USERNAME=9DnYhSCY9Rj7SGv.root');
    $_ENV['DB_USERNAME'] = '9DnYhSCY9Rj7SGv.root';
    $_SERVER['DB_USERNAME'] = '9DnYhSCY9Rj7SGv.root';
}

$dbPassword = getenv('DB_PASSWORD');
if (!empty($dbPassword) && trim($dbPassword) !== '') {
    // When DB_PASSWORD is provided on Vercel, connect to TiDB Cloud MySQL
    putenv('DB_CONNECTION=mysql');
    $_ENV['DB_CONNECTION'] = 'mysql';
    $_SERVER['DB_CONNECTION'] = 'mysql';

    putenv('SESSION_DRIVER=database');
    $_ENV['SESSION_DRIVER'] = 'database';
    $_SERVER['SESSION_DRIVER'] = 'database';

    putenv('CACHE_STORE=database');
    $_ENV['CACHE_STORE'] = 'database';
    $_SERVER['CACHE_STORE'] = 'database';
} else {
    // If DB_PASSWORD has not yet been configured in Vercel settings,
    // seamlessly fall back to SQLite in /tmp so the site works without 500 errors
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';

    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
    $_SERVER['CACHE_STORE'] = 'array';

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

// 8. Forward to public/index.php
require dirname(__DIR__) . '/public/index.php';

