<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';

// Hanya admin dan manager yang bisa akses
if (!hasRole('admin') && !hasRole('manager')) {
    redirect('../dashboard/');
}

$page_title = 'Monitoring System';
include '../includes/header.php';

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Tambahkan informasi container
$containers = [
    'app' => [
        'name' => 'PHP Application',
        'status' => 'running',
        'port' => '80',
        'image' => 'php:8.1-apache'
    ],
    'webserver' => [
        'name' => 'Nginx',
        'status' => 'running',
        'port' => '8080',
        'image' => 'nginx:alpine'
    ],
    'db' => [
        'name' => 'MySQL Database',
        'status' => 'running',
        'port' => '3306',
        'image' => 'mysql:8.0'
    ],
    'phpmyadmin' => [
        'name' => 'phpMyAdmin',
        'status' => 'optional',
        'port' => '8081',
        'image' => 'phpmyadmin/phpmyadmin'
    ]
];

?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-chart-bar"></i> System Monitoring</h1>
    
    <div class="monitoring-intro">
        <p>Halaman ini menampilkan monitoring sistem untuk skenario client-server.</p>
        <div class="scenario-info">
            <h3><i class="fas fa-project-diagram"></i> Skenario Client-Server</h3>
            <p>Arsitektur sistem menggunakan pendekatan client-server dimana:</p>
            <ul>
                <li><strong>Client:</strong> Browser web (interface user)</li>
                <li><strong>Server:</strong> Apache/PHP dengan MySQL database</li>
                <li><strong>Container:</strong> Docker untuk environment isolation</li>
            </ul>
        </div>
    </div>
    
    <div class="monitoring-grid">
        <div class="monitoring-card">
            <h3><i class="fas fa-server"></i> Server Status</h3>
            <div class="status-indicator status-online">
                <i class="fas fa-check-circle"></i> ONLINE
            </div>
            <div class="server-metrics">
                <div class="metric">
                    <span class="metric-label">PHP Version:</span>
                    <span class="metric-value"><?php echo phpversion(); ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Server Software:</span>
                    <span class="metric-value"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Current Time:</span>
                    <span class="metric-value"><?php echo date('Y-m-d H:i:s'); ?></span>
                </div>
            </div>
        </div>
        
        <div class="monitoring-card">
            <h3><i class="fas fa-database"></i> Database Status</h3>
            <div class="status-indicator status-online">
                <i class="fas fa-check-circle"></i> CONNECTED
            </div>
            <?php
            // Cek koneksi database
            try {
                $test_query = "SELECT 1";
                $db->query($test_query);
                $db_status = "Connected";
                $db_class = "status-online";
            } catch (PDOException $e) {
                $db_status = "Disconnected";
                $db_class = "status-offline";
            }
            ?>
            <div class="server-metrics">
                <div class="metric">
                    <span class="metric-label">Status:</span>
                    <span class="metric-value <?php echo $db_class; ?>"><?php echo $db_status; ?></span>
                </div>
                <div class="metric">
                    <span class="metric-label">Database:</span>
                    <span class="metric-value">project_akhir</span>
                </div>
            </div>
        </div>
        
        <div class="monitoring-card">
            <h3><i class="fas fa-users"></i> User Activity</h3>
            <div class="activity-chart">
                <div class="chart-bar" style="height: 80%;" data-value="80">
                    <span>Today</span>
                </div>
                <div class="chart-bar" style="height: 60%;" data-value="60">
                    <span>Yesterday</span>
                </div>
                <div class="chart-bar" style="height: 40%;" data-value="40">
                    <span>2 Days Ago</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-info">
        <h3><i class="fab fa-docker"></i> Docker Containers Status</h3>
        <div class="container-grid">
            <?php foreach ($containers as $key => $container): ?>
            <div class="container-card">
                <h4><i class="fas fa-box"></i> <?php echo $container['name']; ?></h4>
                <p><strong>Service:</strong> <?php echo $key; ?></p>
                <p><strong>Image:</strong> <?php echo $container['image']; ?></p>
                <p><strong>Port:</strong> <?php echo $container['port']; ?></p>
                <p><strong>Status:</strong> 
                    <span class="status-<?php echo $container['status']; ?>">
                        <?php echo ucfirst($container['status']); ?>
                    </span>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>