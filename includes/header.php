<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
    <?php if (isLoggedIn() && basename($_SERVER['PHP_SELF']) != 'index.php'): ?>
    <nav class="navbar">
        <div class="nav-brand"><?php echo SITE_NAME; ?></div>
        <div class="nav-menu">
            <a href="/dashboard/" class="nav-item"><i class="fas fa-home"></i> Dashboard</a>
            <?php if (hasRole('admin') || hasRole('manager')): ?>
            <a href="/monitoring/" class="nav-item"><i class="fas fa-chart-bar"></i> Monitoring</a>
            <?php endif; ?>
            <?php if (hasRole('admin')): ?>
            <a href="/logs/" class="nav-item"><i class="fas fa-clipboard-list"></i> Logs</a>
            <?php endif; ?>
            <a href="/auth/logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <span class="nav-item user-info">
                <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?> 
                (<?php echo $_SESSION['role']; ?>)
            </span>
        </div>
    </nav>
    <?php endif; ?>