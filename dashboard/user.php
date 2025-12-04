<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';

$page_title = 'Dashboard User';
include '../includes/header.php';

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-user"></i> Dashboard User</h1>
    
    <div class="welcome-card">
        <h2>Selamat datang, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Role: <span class="badge badge-user"><?php echo $_SESSION['role']; ?></span></p>
        <p>Email: <?php echo $_SESSION['email']; ?></p>
    </div>
    
    <div class="dashboard-grid">
        <a href="tickets/user-submit.php" class="dashboard-card dashboard-link">
            <div class="card-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h3>Submit Ticket</h3>
            <p>Buat tiket baru untuk department IT</p>
        </a>
        
        <a href="tickets/user-list.php" class="dashboard-card dashboard-link">
            <div class="card-icon">
                <i class="fas fa-list"></i>
            </div>
            <h3>Ticket Saya</h3>
            <p>Lihat semua tiket Anda</p>
        </a>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <h3>Informasi Akun</h3>
            <p>Lihat dan kelola informasi akun Anda</p>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-history"></i>
            </div>
            <h3>Aktivitas Terakhir</h3>
            <p>Monitor aktivitas login terakhir</p>
        </div>
        
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-cog"></i>
            </div>
            <h3>Pengaturan</h3>
            <p>Ubah password dan pengaturan lain</p>
        </div>
    </div>
    
    <style>
    .dashboard-link {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
    }
    
    .dashboard-link:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    </style>
    
    <div class="quick-info">
        <h3><i class="fas fa-bullhorn"></i> Pengumuman</h3>
        <div class="announcement">
            <p><strong>Skenario Project:</strong> Sistem ini merupakan implementasi dari skenario client-server dengan container-based setup.</p>
            <p><strong>Fitur User:</strong> Anda dapat melihat dashboard, informasi akun, dan aktivitas login.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>