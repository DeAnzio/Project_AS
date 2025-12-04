<?php
require_once '../../config/config.php';
require_once '../../includes/auth_check.php';

if (!hasRole('admin')) {
    redirect('../../dashboard/');
}

require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id'])) {
    redirect('admin-list.php');
}

$ticket_id = intval($_GET['id']);

// Ambil detail ticket
$query = "SELECT t.*, u.username, u.email, 
                 (SELECT GROUP_CONCAT(username) FROM users WHERE id = t.assigned_to) as assigned_name
          FROM tickets t
          JOIN users u ON t.user_id = u.id
          WHERE t.id = :id";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    redirect('admin-list.php');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if ($action === 'update_status') {
        $new_status = $_POST['status'] ?? '';
        $allowed_statuses = ['open', 'in_progress', 'resolved', 'closed'];
        if (in_array($new_status, $allowed_statuses)) {
            $resolved_at = ($new_status === 'resolved') ? date('Y-m-d H:i:s') : NULL;
            $upd = $db->prepare("UPDATE tickets SET status = :status, resolved_at = :resolved_at WHERE id = :id");
            $upd->execute([':status' => $new_status, ':resolved_at' => $resolved_at, ':id' => $ticket_id]);
            $message = 'Status ticket berhasil diubah.';
            addLog('Updated ticket ' . $ticket['ticket_number'] . ' status to ' . $new_status, $db);
            // Reload ticket
            $stmt->execute([':id' => $ticket_id]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } elseif ($action === 'add_comment' && !empty($comment)) {
        $ins = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (:ticket_id, :user_id, :comment)");
        $ins->execute([':ticket_id' => $ticket_id, ':user_id' => $_SESSION['user_id'], ':comment' => $comment]);
        $message = 'Feedback berhasil ditambahkan.';
        addLog('Added comment to ticket ' . $ticket['ticket_number'], $db);
    }
}

// Ambil comments
$comments_query = "SELECT tc.*, u.username FROM ticket_comments tc
                   JOIN users u ON tc.user_id = u.id
                   WHERE tc.ticket_id = :ticket_id
                   ORDER BY tc.created_at DESC";
$comments_stmt = $db->prepare($comments_query);
$comments_stmt->execute([':ticket_id' => $ticket_id]);
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Detail Ticket: ' . $ticket['ticket_number'];
include '../../includes/header.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-ticket-alt"></i> <?php echo htmlspecialchars($ticket['ticket_number']); ?></h1>

    <?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- Ticket Details -->
            <div class="card">
                <h2><?php echo htmlspecialchars($ticket['title']); ?></h2>
                <p><strong>From:</strong> <?php echo htmlspecialchars($ticket['username']); ?> (<?php echo htmlspecialchars($ticket['email']); ?>)</p>
                <p><strong>Created:</strong> <?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></p>
                <hr>
                <h3>Deskripsi</h3>
                <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
            </div>

            <!-- Status Update Form -->
            <div class="card" style="margin-top: 20px;">
                <h3>Update Status</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <div class="form-group">
                        <label for="status">Status Baru</label>
                        <select id="status" name="status" required>
                            <option value="open" <?php echo $ticket['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                            <option value="in_progress" <?php echo $ticket['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="resolved" <?php echo $ticket['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="closed" <?php echo $ticket['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>

            <!-- Comments -->
            <div class="card" style="margin-top: 20px;">
                <h3>Feedback & Comments</h3>
                
                <form method="POST" style="margin-bottom: 20px;">
                    <input type="hidden" name="action" value="add_comment">
                    <div class="form-group">
                        <label for="comment">Tambah Feedback</label>
                        <textarea id="comment" name="comment" rows="4" required placeholder="Tuliskan feedback atau update untuk user..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kirim Feedback</button>
                </form>

                <hr>

                <?php if (empty($comments)): ?>
                <p style="color: #999;">Belum ada feedback.</p>
                <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($comments as $cmt): ?>
                    <div class="comment-box">
                        <strong><?php echo htmlspecialchars($cmt['username']); ?></strong>
                        <span style="color: #999; font-size: 12px;">
                            <?php echo date('d/m/Y H:i', strtotime($cmt['created_at'])); ?>
                        </span>
                        <p><?php echo nl2br(htmlspecialchars($cmt['comment'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="card">
                <h3>Informasi Ticket</h3>
                <p><strong>Status:</strong><br>
                    <span class="badge badge-<?php echo $ticket['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                    </span>
                </p>
                <p><strong>Priority:</strong><br>
                    <span class="badge badge-<?php echo $ticket['priority']; ?>">
                        <?php echo ucfirst($ticket['priority']); ?>
                    </span>
                </p>
                <p><strong>Assigned To:</strong><br>
                    <?php echo $ticket['assigned_to'] ? htmlspecialchars($ticket['assigned_name']) : 'Unassigned'; ?>
                </p>
                <?php if ($ticket['resolved_at']): ?>
                <p><strong>Resolved:</strong><br>
                    <?php echo date('d/m/Y H:i', strtotime($ticket['resolved_at'])); ?>
                </p>
                <?php endif; ?>
                <a href="admin-list.php" class="btn btn-secondary" style="margin-top: 15px; width: 100%; text-align: center;">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card h2 {
    margin-top: 0;
    margin-bottom: 15px;
}

.card h3 {
    margin-top: 0;
    margin-bottom: 15px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
}

.form-group textarea {
    resize: vertical;
}

.comment-box {
    background: #f9f9f9;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 12px;
    border-left: 3px solid #007bff;
}

.comment-box p {
    margin: 8px 0 0 0;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.badge-open { background: #ffc107; color: #000; }
.badge-in_progress { background: #17a2b8; color: #fff; }
.badge-resolved { background: #28a745; color: #fff; }
.badge-closed { background: #6c757d; color: #fff; }
.badge-low { background: #28a745; color: #fff; }
.badge-medium { background: #ffc107; color: #000; }
.badge-high { background: #fd7e14; color: #fff; }
.badge-urgent { background: #dc3545; color: #fff; }

.btn {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}
</style>

<?php include '../../includes/footer.php'; ?>
