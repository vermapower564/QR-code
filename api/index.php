<?php

// 1. Ensure APP_KEY exists for encryption/sessions on Vercel
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

// 2. Ensure writable directories in /tmp for Vercel serverless environment
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 3. Ensure SQLite database exists and is writable in /tmp
if (getenv('DB_CONNECTION') === 'sqlite' || !getenv('DB_CONNECTION')) {
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

// 4. Forward to public/index.php
require dirname(__DIR__) . '/public/index.php';

