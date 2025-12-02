<?php
require_once '../config/config.php';

if (isLoggedIn()) {
    redirect('../dashboard/');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role
    
    // Validasi
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek username dan email
        $query = "SELECT id FROM users WHERE username = :username OR email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = 'Username atau email sudah terdaftar!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (username, email, password, role) 
                      VALUES (:username, :email, :password, :role)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":role", $role);
            
            if ($stmt->execute()) {
                $success = 'Pendaftaran berhasil! Silakan login.';
                addLog('New user registered: ' . $username, $db);
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php $page_title = 'Register'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title"><i class="fas fa-user-plus"></i> Daftar Akun Baru</h2>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Masukkan username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required 
                       placeholder="Masukkan email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Minimal 6 karakter">
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="Ulangi password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>
        
        <div class="auth-links">
            <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
            <p><a href="../">Kembali ke halaman utama</a></p>
        </div>
    </div>
    
    <div class="auth-info">
        <h3>Informasi Project:</h3>
        <ul class="info-list">
            <li><i class="fas fa-server"></i> <strong>Setup Server:</strong> Container-based</li>
            <li><i class="fas fa-id-card"></i> <strong>IDP:</strong> User, Admin, Manager</li>
            <li><i class="fas fa-chart-line"></i> <strong>Monitoring:</strong> Real-time system monitoring</li>
            <li><i class="fas fa-clipboard-list"></i> <strong>Log:</strong> Activity logging system</li>
            <li><i class="fas fa-laptop"></i> <strong>Skenario:</strong> Client-Server architecture</li>
        </ul>
        
        <div class="tech-info">
            <h4>Teknologi yang digunakan:</h4>
            <div class="tech-tags">
                <span class="tech-tag">PHP</span>
                <span class="tech-tag">MySQL</span>
                <span class="tech-tag">HTML5</span>
                <span class="tech-tag">CSS3</span>
                <span class="tech-tag">Docker</span>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>