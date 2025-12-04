<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
$config = require_once __DIR__ . '/../config/auth0.php';

// Exchange authorization code for tokens
if (!isset($_GET['code'])) {
    redirect('../auth/login.php');
}

$code = $_GET['code'];
$token_url = "https://{$config['domain']}/oauth/token";

// Build redirect_uri dynamically (must match what was sent to Auth0)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$redirect_uri = $scheme . '://' . $host . '/auth/auth0_callback.php';

$post = [
    'grant_type' => 'authorization_code',
    'client_id' => $config['client_id'],
    'client_secret' => $config['client_secret'],
    'code' => $code,
    'redirect_uri' => $redirect_uri
];

$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
$resp = curl_exec($ch);
if ($resp === false) {
    error_log('Auth0 token request failed: ' . curl_error($ch));
    redirect('../auth/login.php');
}
curl_close($ch);

$tokenData = json_decode($resp, true);
if (!isset($tokenData['access_token'])) {
    error_log('Auth0 token response missing access_token: ' . $resp);
    redirect('../auth/login.php');
}

$access_token = $tokenData['access_token'];

// Get user info
$userinfo_url = "https://{$config['domain']}/userinfo";
$ch = curl_init($userinfo_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$access_token}"]);
$uresp = curl_exec($ch);
if ($uresp === false) {
    error_log('Auth0 userinfo request failed: ' . curl_error($ch));
    redirect('../auth/login.php');
}
curl_close($ch);

$user = json_decode($uresp, true);
if (!isset($user['email'])) {
    error_log('Auth0 userinfo missing email: ' . $uresp);
    redirect('../auth/login.php');
}

$email = $user['email'];
$name = isset($user['name']) ? $user['name'] : $email;
$sub = isset($user['sub']) ? $user['sub'] : null;

// Optional: require email to be verified by the IdP
if (isset($user['email_verified']) && $user['email_verified'] === false) {
    // prevent account creation if email not verified
    error_log('Auth0 user email not verified: ' . $email);
    // You can redirect to an error page or back to login with message
    redirect('../auth/login.php');
}

// Upsert user in local database
$database = new Database();
$db = $database->getConnection();

// Ensure required OAuth columns exist (safe migration)
try {
    $required = [
        'auth0_id' => "VARCHAR(255) DEFAULT NULL",
        'oauth_provider' => "VARCHAR(50) DEFAULT NULL",
        'oauth_token' => "TEXT DEFAULT NULL"
    ];

    foreach ($required as $col => $ddl) {
        $check = $db->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = :col");
        $check->execute([':col' => $col]);
        $res = $check->fetch(PDO::FETCH_ASSOC);
        if (isset($res['cnt']) && intval($res['cnt']) === 0) {
            $db->exec("ALTER TABLE users ADD COLUMN {$col} {$ddl}");
        }
    }
} catch (PDOException $e) {
    error_log('Migration check failed: ' . $e->getMessage());
    // continue; the later insert will show a clear error
}

$query = "SELECT * FROM users WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_id = $existing['id'];
    $role = $existing['role'];
    // Ensure auth0_id is stored for future logins
    if (empty($existing['auth0_id']) && $sub) {
        $upd = $db->prepare("UPDATE users SET auth0_id = :auth0_id WHERE id = :id");
        $upd->execute([':auth0_id' => $sub, ':id' => $user_id]);
    }
} else {
    // Create new user with 'user' role
    $insert = "INSERT INTO users (username, email, password, role, auth0_id, created_at) VALUES (:username, :email, :password, 'user', :auth0_id, NOW())";
    $ist = $db->prepare($insert);
    $pw_placeholder = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
    $ist->bindParam(':username', $name);
    $ist->bindParam(':email', $email);
    $ist->bindParam(':password', $pw_placeholder);
    $ist->bindParam(':auth0_id', $sub);
    $ist->execute();
    $user_id = $db->lastInsertId();
    $role = 'user';
}

// Set session and redirect
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $name;
$_SESSION['email'] = $email;
$_SESSION['role'] = $role;

// Log the login
if (function_exists('addLog')) {
    addLog('Login via Auth0', $db);
}

redirect('../dashboard/');
