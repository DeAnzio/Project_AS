<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';

// Redirect berdasarkan role
switch ($_SESSION['role']) {
    case 'admin':
        redirect('admin.php');
        break;
    case 'manager':
        redirect('manager.php');
        break;
    case 'user':
        redirect('user.php');
        break;
    default:
        redirect('../auth/login.php');
}
?>