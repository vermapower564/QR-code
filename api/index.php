<?php

// Ensure writable directories in /tmp for Vercel serverless environment
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Ensure SQLite database exists in /tmp if using sqlite
if (getenv('DB_CONNECTION') === 'sqlite' || !getenv('DB_CONNECTION')) {
    $tmpDb = '/tmp/database.sqlite';
    if (!file_exists($tmpDb)) {
        $bundledDb = __DIR__ . '/../database/database.sqlite';
        if (file_exists($bundledDb)) {
            @copy($bundledDb, $tmpDb);
        } else {
            @touch($tmpDb);
        }
    }
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// Forward to public/index.php
require __DIR__ . '/../public/index.php';
