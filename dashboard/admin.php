<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';

if (!hasRole('admin')) {
    redirect('user.php');
}

$page_title = 'Dashboard Admin';
include '../includes/header.php';

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Ambil statistik
$query = "SELECT COUNT(*) as total_users FROM users";
$stmt = $db->prepare($query);
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$query = "SELECT COUNT(*) as total_logs FROM logs";
$stmt = $db->prepare($query);
$stmt->execute();
$total_logs = $stmt->fetch(PDO::FETCH_ASSOC)['total_logs'];

// Ambil user terbaru
$query = "SELECT username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$docker_info = getDockerInfo();

?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-user-shield"></i> Dashboard Admin</h1>
    
    <div class="welcome-card admin-welcome">
        <h2>Halo, <?php echo $_SESSION['username']; ?> (Administrator)</h2>
        <p>Anda memiliki akses penuh ke semua fitur sistem.</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Total Users</h3>
                <p class="stat-number"><?php echo $total_users; ?></p>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3>Total Logs</h3>
                <p class="stat-number"><?php echo $total_logs; ?></p>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-info">
                <h3>Role Admin</h3>
                <p class="stat-number">Full Access</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h3><i class="fas fa-user-plus"></i> User Terbaru</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="badge badge-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="dashboard-section">
            <h3><i class="fas fa-cogs"></i> Quick Actions</h3>
            <div class="action-grid">
                <a href="tickets/admin-list.php" class="action-card">
                    <i class="fas fa-list"></i>
                    <span>Tickets</span>
                </a>
                <a href="../monitoring/" class="action-card">
                    <i class="fas fa-chart-bar"></i>
                    <span>Monitoring</span>
                </a>
                <a href="../logs/" class="action-card">
                    <i class="fas fa-clipboard-list"></i>
                    <span>View Logs</span>
                </a>
                <a href="manage_users.php" class="action-card">
                    <i class="fas fa-user-cog"></i>
                    <span>Manage Users</span>
                </a>
            </div>
            
            <div class="system-info">
                <h4><i class="fas fa-info-circle"></i> System Information</h4>
                <ul>
                    <li><strong>Skenario:</strong> Client-Server Architecture</li>
                    <li><strong>Server Setup:</strong> Container-based (Docker)</li>
                    <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
                    <li><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="system-info">
    <h4><i class="fas fa-docker"></i> Docker Information</h4>
    <ul>
        <li><strong>Container ID:</strong> <?php echo $docker_info['container_id']; ?></li>
        <li><strong>Environment:</strong> <?php echo $docker_info['environment']; ?></li>
        <li><strong>PHP Version:</strong> <?php echo $docker_info['php_version']; ?></li>
        <li><strong>Server:</strong> <?php echo $docker_info['server_software']; ?></li>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>