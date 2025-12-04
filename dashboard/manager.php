<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';

if (!hasRole('manager')) {
    redirect('user.php');
}

$page_title = 'Dashboard Manager';
include '../includes/header.php';

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-user-tie"></i> Dashboard Manager</h1>
    
    <div class="welcome-card manager-welcome">
        <h2>Selamat datang, <?php echo $_SESSION['username']; ?> (Manager)</h2>
        <p>Role: <span class="badge badge-manager"><?php echo $_SESSION['role']; ?></span></p>
        <p>Anda memiliki akses ke monitoring dan laporan sistem.</p>
    </div>
    
    <div class="dashboard-grid">
        <a href="tickets/manager-log.php" class="dashboard-card dashboard-link">
            <div class="card-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h3>Ticketing Monitor</h3>
            <p>Lihat progress semua tiket</p>
        </a>

        <a href="../monitoring/" class="dashboard-card dashboard-link">
            <div class="card-icon">
                <i class="fas fa-server"></i>
            </div>
            <h3>System Monitoring</h3>
            <p>Monitor sistem dan container</p>
        </a>

        <div class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <h3>Informasi Akun</h3>
            <p>Lihat dan kelola informasi akun Anda</p>
        </div>
    </div>

    <div class="quick-info">
        <h3><i class="fas fa-bullhorn"></i> Akses Manager</h3>
        <div class="announcement">
            <p><strong>Fitur Utama:</strong> Anda dapat melihat monitoring sistem dan log ticketing untuk memastikan semua proses berjalan dengan baik.</p>
            <p><strong>Tanggung Jawab:</strong> Pantau progress tiket dan status sistem, berikan laporan kepada management.</p>
        </div>
    </div>
</div>

<style>
.manager-welcome {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

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

.badge-manager {
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}
</style>

<?php include '../includes/footer.php'; ?>
