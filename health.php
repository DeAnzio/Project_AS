<?php
/**
 * Health Check Endpoint
 * Endpoint untuk monitoring kesehatan aplikasi
 * Gunakan: http://localhost:8080/health.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check PHP
$health['checks']['php'] = [
    'version' => phpversion(),
    'status' => 'ok'
];

// Check Database Connection
require_once 'config/database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    $health['checks']['database'] = [
        'status' => 'ok',
        'connection' => 'mysql'
    ];
} catch (Exception $e) {
    $health['checks']['database'] = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    $health['status'] = 'unhealthy';
}

// Check File Permissions
$health['checks']['files'] = [
    'writable' => is_writable('.'),
    'status' => is_writable('.') ? 'ok' : 'warning'
];

// Check Required Extensions
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'gd'];
$all_loaded = true;
foreach ($extensions as $ext) {
    if (!extension_loaded($ext)) {
        $all_loaded = false;
        break;
    }
}
$health['checks']['extensions'] = [
    'required' => $extensions,
    'all_loaded' => $all_loaded,
    'status' => $all_loaded ? 'ok' : 'error'
];

// Server Info
$health['server'] = [
    'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'hostname' => gethostname(),
    'environment' => getenv('DOCKER_ENV') ?: 'unknown'
];

// Set HTTP Status Code
http_response_code($health['status'] === 'healthy' ? 200 : 503);

echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
