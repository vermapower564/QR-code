<?php

// 0. Resolve base directory across Vercel environments (/var/task or /var/task/user)
$baseDir = __DIR__;
if (is_dir(__DIR__ . '/../user/app')) {
    $baseDir = realpath(__DIR__ . '/../user');
} elseif (is_dir(dirname(__DIR__) . '/app')) {
    $baseDir = dirname(__DIR__);
} elseif (is_dir('/var/task/user/app')) {
    $baseDir = '/var/task/user';
} elseif (is_dir('/var/task/app')) {
    $baseDir = '/var/task';
} else {
    $baseDir = dirname(__DIR__);
}

// 1. Directly serve public static assets if requested through the serverless function
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestPath = urldecode($requestPath);
if ($requestPath !== '/' && !empty($requestPath) && !str_ends_with(strtolower($requestPath), '.php')) {
    $candidates = [
        $baseDir . '/public' . $requestPath,
        dirname(__DIR__) . '/public' . $requestPath,
        dirname(__DIR__) . '/user/public' . $requestPath,
        '/var/task/user/public' . $requestPath,
        '/var/task/public' . $requestPath,
    ];
    foreach ($candidates as $staticFile) {
        if (file_exists($staticFile) && is_file($staticFile)) {
            $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
            $mimes = [
                'js' => 'application/javascript; charset=utf-8',
                'mjs' => 'application/javascript; charset=utf-8',
                'css' => 'text/css; charset=utf-8',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'json' => 'application/json',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'txt' => 'text/plain',
                'webp' => 'image/webp',
                'pdf' => 'application/pdf',
            ];
            $mime = $mimes[$ext] ?? 'application/octet-stream';
            header("Content-Type: {$mime}");
            header('Content-Length: ' . filesize($staticFile));
            header('Cache-Control: public, max-age=31536000, immutable');
            readfile($staticFile);
            exit;
        }
    }
}

// 2. Force HTTPS and Client IP detection for Vercel serverless proxy
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

// 3. Ensure APP_KEY exists for encryption/sessions on Vercel
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

// 4. Dynamic APP_URL and ASSET_URL detection from Vercel domain
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

// 5. Clean up Vite dev hot file if it accidentally exists
foreach ([$baseDir . '/public/hot', dirname(__DIR__) . '/public/hot'] as $hotFile) {
    if (file_exists($hotFile)) {
        @unlink($hotFile);
    }
}

// 6. Ensure writable storage directories in /tmp for Vercel serverless environment
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

// 7. Ensure SSL CA certificate exists for TiDB Cloud MySQL connection
$caCandidates = [
    $baseDir . '/database/certs/ca.pem',
    dirname(__DIR__) . '/database/certs/ca.pem',
    '/var/task/user/database/certs/ca.pem',
    '/var/task/database/certs/ca.pem',
];
$bundledCa = null;
foreach ($caCandidates as $caPath) {
    if (file_exists($caPath) && is_file($caPath)) {
        $bundledCa = $caPath;
        break;
    }
}
if ($bundledCa) {
    $currentCa = getenv('MYSQL_ATTR_SSL_CA');
    if (!$currentCa || !file_exists($currentCa)) {
        putenv("MYSQL_ATTR_SSL_CA={$bundledCa}");
        $_ENV['MYSQL_ATTR_SSL_CA'] = $bundledCa;
        $_SERVER['MYSQL_ATTR_SSL_CA'] = $bundledCa;
    }
}

// 8. Configure Database Connection & Intelligent Fallback
// TiDB Cloud defaults
$dbHost = getenv('DB_HOST');
if (empty($dbHost) || in_array($dbHost, ['localhost', '127.0.0.1', '::1'])) {
    putenv('DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com');
    $_ENV['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
    $_SERVER['DB_HOST'] = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
}
$dbPort = getenv('DB_PORT');
if (empty($dbPort) || $dbPort == '3306') {
    putenv('DB_PORT=4000');
    $_ENV['DB_PORT'] = '4000';
    $_SERVER['DB_PORT'] = '4000';
}
$dbName = getenv('DB_DATABASE');
if (empty($dbName) || in_array($dbName, ['laravel', 'sys'])) {
    putenv('DB_DATABASE=qr_social');
    $_ENV['DB_DATABASE'] = 'qr_social';
    $_SERVER['DB_DATABASE'] = 'qr_social';
}
$dbUser = getenv('DB_USERNAME');
if (empty($dbUser) || $dbUser == 'root') {
    putenv('DB_USERNAME=9DnYhSCY9Rj7SGv.root');
    $_ENV['DB_USERNAME'] = '9DnYhSCY9Rj7SGv.root';
    $_SERVER['DB_USERNAME'] = '9DnYhSCY9Rj7SGv.root';
}

$dbPassword = getenv('DB_PASSWORD');
$useMysql = false;

if (!empty($dbPassword) && trim($dbPassword) !== '') {
    try {
        $dsn = "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_DATABASE');
        $opts = [
            PDO::ATTR_TIMEOUT => 3,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        if ($bundledCa && file_exists($bundledCa)) {
            $opts[PDO::MYSQL_ATTR_SSL_CA] = $bundledCa;
            $opts[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
        $pdoTest = new PDO($dsn, getenv('DB_USERNAME'), $dbPassword, $opts);
        $useMysql = true;
    } catch (\Throwable $e) {
        $useMysql = false;
    }
}

if ($useMysql) {
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
    // If TiDB is unavailable or credentials not yet verified, fall back to SQLite
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';

    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
    $_SERVER['CACHE_STORE'] = 'array';

    $dbCandidates = [
        $baseDir . '/database/database.sqlite',
        dirname(__DIR__) . '/database/database.sqlite',
        '/var/task/user/database/database.sqlite',
        '/var/task/database/database.sqlite',
    ];
    $bundledDb = null;
    foreach ($dbCandidates as $dbPath) {
        if (file_exists($dbPath) && is_file($dbPath)) {
            $bundledDb = $dbPath;
            break;
        }
    }

    $tmpDb = '/tmp/database.sqlite';
    if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
        if ($bundledDb && file_exists($bundledDb) && filesize($bundledDb) > 0) {
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

// 9. Forward to public/index.php
$publicIndexCandidates = [
    $baseDir . '/public/index.php',
    dirname(__DIR__) . '/public/index.php',
    '/var/task/user/public/index.php',
    '/var/task/public/index.php',
];
foreach ($publicIndexCandidates as $indexPath) {
    if (file_exists($indexPath) && is_file($indexPath)) {
        require $indexPath;
        exit;
    }
}
require dirname(__DIR__) . '/public/index.php';

