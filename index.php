<?php
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect('dashboard/');
} else {
    redirect('auth/login.php');
}
?>