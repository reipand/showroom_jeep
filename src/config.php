<?php
/**
 * Configuration file for Showroom Web Application
 * Handles environment-specific settings for local development and production
 */

// Detect environment
$isDocker = getenv('DB_HOST') !== false;
$isProduction = getenv('APP_ENV') === 'production';

// Debug mode
$debugMode = $isDocker ? true : true; // Enable debug for development

// Database configuration
if ($isDocker) {
    // Docker environment
    $host = getenv('DB_HOST') ?: 'db';
    $user = getenv('DB_USER') ?: 'jeep_user';
    $pass = getenv('DB_PASS') ?: 'bcst2526';
    $db = getenv('DB_NAME') ?: 'jeep_db';
} else {
    // Local development environment
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'jeep_db';
}

// Application configuration
$config = [
    'app' => [
        'name' => 'Showroom Jeep',
        'version' => '1.0.0',
        'debug' => $debugMode,
        'timezone' => 'Asia/Jakarta'
    ],
    'database' => [
        'host' => $host,
        'user' => $user,
        'pass' => $pass,
        'name' => $db,
        'charset' => 'utf8mb4'
    ],
    'session' => [
        'lifetime' => 3600, // 1 hour
        'secure' => $isProduction,
        'httponly' => true,
        'samesite' => 'Strict'
    ],
    'recaptcha' => [
        'site_key' => getenv('RECAPTCHA_SITE_KEY') ?: '6LemAOMrAAAAAJuwYNQaO2ko92Q8xFG3DgUFlKij', // Test key
        'secret_key' => getenv('RECAPTCHA_SECRET_KEY') ?: '6LemAOMrAAAAAOdVtlobP92EOTkvPLKUl19q7Dpi', // Test key
        'enabled' => true
    ],
    'upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'path' => '/var/www/html/assets/images/'
    ]
];

// Error reporting based on environment
if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Session configuration (only if no session is active)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', $config['session']['lifetime']);
    ini_set('session.cookie_secure', $config['session']['secure']);
    ini_set('session.cookie_httponly', $config['session']['httponly']);
    ini_set('session.cookie_samesite', $config['session']['samesite']);
}

// Return the configuration array
return $config;
?>
