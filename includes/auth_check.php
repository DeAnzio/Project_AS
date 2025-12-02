<?php
// Cek apakah user sudah login
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}
?>