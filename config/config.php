<?php
session_start();

// Konfigurasi untuk Docker
define('SITE_NAME', 'Project Akhir - Docker');
define('SITE_URL', getenv('SITE_URL') ?: 'http://localhost:8080');
define('DOCKER_ENV', getenv('DOCKER_ENV') ?: 'development');

// Fungsi untuk mendapatkan environment Docker
function getDockerInfo() {
    return [
        'environment' => DOCKER_ENV,
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'container_id' => gethostname(),
    ];
}

// Fungsi helper lainnya tetap sama
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function redirect($url) {
    // Jika URL sudah lengkap (http://), gunakan langsung
    if (strpos($url, 'http') === 0) {
        header("Location: " . $url);
    } else {
        // Relatif path, gunakan header Location biasa
        header("Location: " . $url);
    }
    exit();
}

function addLog($action, $db) {
    $query = "INSERT INTO logs (user_id, action, ip_address, user_agent) 
              VALUES (:user_id, :action, :ip_address, :user_agent)";
    $stmt = $db->prepare($query);
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":action", $action);
    $stmt->bindParam(":ip_address", $_SERVER['REMOTE_ADDR']);
    $stmt->bindParam(":user_agent", $_SERVER['HTTP_USER_AGENT']);
    
    return $stmt->execute();
}
?>