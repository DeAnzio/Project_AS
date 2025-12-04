<?php
require_once '../../config/config.php';
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'medium';

    if (empty($title) || empty($description)) {
        $message = 'Title dan description harus diisi.';
        $message_type = 'error';
    } else {
        // Generate ticket number (TKT-YYYYMMDD-XXXX)
        $ticket_number = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $insert = $db->prepare("INSERT INTO tickets (ticket_number, user_id, title, description, priority, status) 
                               VALUES (:ticket_number, :user_id, :title, :description, :priority, 'open')");
        $insert->execute([
            ':ticket_number' => $ticket_number,
            ':user_id' => $_SESSION['user_id'],
            ':title' => $title,
            ':description' => $description,
            ':priority' => $priority
        ]);

        $message = 'Ticket berhasil dibuat: ' . $ticket_number;
        $message_type = 'success';
        addLog('Created ticket: ' . $ticket_number, $db);

        // Clear form
        $_POST = [];
    }
}

$page_title = 'Submit Ticket';
include '../../includes/header.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Submit Ticket Baru</h1>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 600px;">
        <form method="POST" action="">
            <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Judul Ticket</label>
                <input type="text" id="title" name="title" required 
                       placeholder="Deskripsi singkat masalah Anda"
                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Deskripsi Detail</label>
                <textarea id="description" name="description" rows="8" required 
                          placeholder="Jelaskan masalah Anda secara detail..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="priority"><i class="fas fa-exclamation-triangle"></i> Prioritas</label>
                <select id="priority" name="priority">
                    <option value="low" <?php echo ($_POST['priority'] ?? '') === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo ($_POST['priority'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo ($_POST['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo ($_POST['priority'] ?? '') === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Submit Ticket
            </button>
            <a href="user-list.php" class="btn btn-secondary">
                <i class="fas fa-list"></i> Lihat Ticket Saya
            </a>
        </form>
    </div>
</div>

<style>
.form-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.btn {
    padding: 10px 15px;
    margin-right: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}
</style>

<?php include '../../includes/footer.php'; ?>
