<?php
// router.php
// This router file enables routing /api/* requests to api/index.php
// when running the PHP built-in web server (php -S localhost:8000 router.php).

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Route API requests to api/index.php
if (strpos($uri, '/api/') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
    include __DIR__ . '/api/index.php';
    exit;
}

// 2. Serve existing files as-is
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// 3. Serve index.html or index.php in directories
if (is_dir(__DIR__ . $uri)) {
    if (file_exists(__DIR__ . $uri . '/index.html')) {
        return false;
    }
    if (file_exists(__DIR__ . $uri . '/index.php')) {
        return false;
    }
}

// Default to returning false (let PHP built-in server handle 404 or default routing)
return false;
