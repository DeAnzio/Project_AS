<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';

// hanya admin yang bisa akses
if (!hasRole('admin')) {
    redirect('user.php');
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    $allowed = ['user','manager','admin'];
    if (!in_array($role, $allowed)) {
        $message = 'Role tidak valid.';
    } else {
        $upd = $db->prepare("UPDATE users SET role = :role WHERE id = :id");
        $upd->execute([':role' => $role, ':id' => $user_id]);
        $message = 'Role berhasil diperbarui.';
        addLog('Updated role for user_id=' . $user_id . ' to ' . $role, $db);
    }
}

// ambil semua user
$stmt = $db->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Manage Users';
include '../includes/header.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-user-cog"></i> Manage Users</h1>

    <?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="users-table">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['username']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['role']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display:inline-block">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>" />
                            <select name="role">
                                <option value="user" <?php echo $u['role']=='user' ? 'selected' : ''; ?>>User</option>
                                <option value="manager" <?php echo $u['role']=='manager' ? 'selected' : ''; ?>>Manager</option>
                                <option value="admin" <?php echo $u['role']=='admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button type="submit" class="btn btn-sm">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
