<?php
require_once '../config/config.php';

if (isLoggedIn()) {
    redirect('../dashboard/');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username = :username OR email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $username);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            addLog('Login successful', $db);
            
            redirect('../dashboard/');
        } else {
            $error = 'Password salah!';
            addLog('Failed login attempt: wrong password', $db);
        }
    } else {
        $error = 'Username/email tidak ditemukan!';
        addLog('Failed login attempt: user not found', $db);
    }
}
?>
<?php include '../includes/header.php'; ?>
<?php $page_title = 'Login'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title"><i class="fas fa-sign-in-alt"></i> Login</h2>
        
        <?php if ($error): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username atau Email</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Masukkan username atau email">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div style="margin-top:12px; text-align:center;">
            <a href="login-with-auth0.php" class="btn btn-secondary btn-block">
                <i class="fab fa-auth0"></i> Login with Auth0
            </a>
        </div>
        
        <div class="auth-links">
            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
            <p><a href="../">Kembali ke halaman utama</a></p>
        </div>
    </div>
    
    <div class="auth-info">
        <h3>Skenario Login:</h3>
        <div class="role-info">
            <div class="role-card">
                <h4><i class="fas fa-user-tie"></i> Admin</h4>
                <p>Akses penuh: dashboard, monitoring, logs</p>
            </div>
            <div class="role-card">
                <h4><i class="fas fa-user-cog"></i> Manager</h4>
                <p>Akses: dashboard dan monitoring</p>
            </div>
            <div class="role-card">
                <h4><i class="fas fa-user"></i> User</h4>
                <p>Akses: dashboard saja</p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>