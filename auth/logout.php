<?php
require_once '../config/config.php';

// Log logout
if (isset($_SESSION['user_id'])) {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    addLog('Logout', $db);
}

// Hapus semua session
session_destroy();

// Redirect ke login
header("Location: /auth/login.php");
exit();
?>