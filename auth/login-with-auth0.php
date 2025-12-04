<?php
// Redirect to Auth0 authorize endpoint
$config = require_once __DIR__ . '/../config/auth0.php';

$domain = $config['domain'];
$client_id = $config['client_id'];

// Build redirect_uri dynamically from current request (supports any host/IP)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$redirect = $scheme . '://' . $host . '/auth/auth0_callback.php';

$scope = urlencode('openid profile email');
$audience = isset($config['audience']) && $config['audience'] ? '&audience=' . urlencode($config['audience']) : '';

$authorize = "https://{$domain}/authorize?response_type=code&client_id={$client_id}&redirect_uri=" . urlencode($redirect) . "&scope={$scope}{$audience}";

header('Location: ' . $authorize);
exit();
