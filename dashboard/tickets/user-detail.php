<?php
require_once '../../config/config.php';
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Ambil ticket ID dari URL
$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$ticket_id) {
    redirect('../dashboard/');
}

// Ambil detail ticket user sendiri
$query = "SELECT id, ticket_number, title, description, status, priority, 
                 assigned_to, created_at, updated_at, resolved_at 
          FROM tickets 
          WHERE id = :id AND user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $ticket_id, ':user_id' => $_SESSION['user_id']]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    redirect('../dashboard/');
}

// Ambil comments/feedback dari admin
$query = "SELECT c.id, c.comment, c.created_at, u.username 
          FROM ticket_comments c 
          LEFT JOIN users u ON c.user_id = u.id 
          WHERE c.ticket_id = :ticket_id 
          ORDER BY c.created_at ASC";
$stmt = $db->prepare($query);
$stmt->execute([':ticket_id' => $ticket_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Ticket Detail - ' . $ticket['ticket_number'];
include '../../includes/header.php';
?>

<div class="dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 class="page-title"><i class="fas fa-ticket-alt"></i> <?php echo htmlspecialchars($ticket['ticket_number']); ?></h1>
        <a href="user-list.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Ticket Info -->
        <div class="info-card">
            <h3>Ticket Information</h3>
            <div style="margin-bottom: 15px;">
                <label>Title:</label>
                <p><?php echo htmlspecialchars($ticket['title']); ?></p>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Status:</label>
                <p>
                    <span class="badge badge-<?php echo $ticket['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                    </span>
                </p>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Priority:</label>
                <p>
                    <span class="badge badge-<?php echo $ticket['priority']; ?>">
                        <?php echo ucfirst($ticket['priority']); ?>
                    </span>
                </p>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Created:</label>
                <p><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></p>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Last Updated:</label>
                <p><?php echo date('d/m/Y H:i', strtotime($ticket['updated_at'])); ?></p>
            </div>
            <?php if ($ticket['resolved_at']): ?>
            <div style="margin-bottom: 15px;">
                <label>Resolved:</label>
                <p><?php echo date('d/m/Y H:i', strtotime($ticket['resolved_at'])); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Ticket Stats -->
        <div class="info-card">
            <h3>Summary</h3>
            <div style="margin-bottom: 15px;">
                <label>Assigned To:</label>
                <p>
                    <?php 
                    if ($ticket['assigned_to']) {
                        $adminQuery = "SELECT username FROM users WHERE id = :id";
                        $adminStmt = $db->prepare($adminQuery);
                        $adminStmt->execute([':id' => $ticket['assigned_to']]);
                        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
                        echo $admin ? htmlspecialchars($admin['username']) : 'Not assigned';
                    } else {
                        echo 'Not assigned';
                    }
                    ?>
                </p>
            </div>
            <div style="margin-bottom: 15px;">
                <label>Total Comments:</label>
                <p><?php echo count($comments); ?></p>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="info-card" style="margin-bottom: 30px;">
        <h3>Description</h3>
        <p style="white-space: pre-wrap; line-height: 1.6;">
            <?php echo htmlspecialchars($ticket['description']); ?>
        </p>
    </div>

    <!-- Comments/Feedback -->
    <div class="info-card">
        <h3><i class="fas fa-comments"></i> Comments & Feedback</h3>
        
        <?php if (empty($comments)): ?>
        <p style="color: #666; text-align: center; padding: 20px;">No comments yet. Please wait for admin feedback.</p>
        <?php else: ?>
        <div style="max-height: 500px; overflow-y: auto;">
            <?php foreach ($comments as $comment): ?>
            <div style="border-left: 3px solid #007bff; padding: 15px; margin-bottom: 15px; background: #f9f9f9; border-radius: 4px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong><?php echo htmlspecialchars($comment['username'] ?? 'Admin'); ?></strong>
                    <small style="color: #999;"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                </div>
                <p style="margin: 0; white-space: pre-wrap; line-height: 1.5;">
                    <?php echo htmlspecialchars($comment['comment']); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.badge-open {
    background: #ffc107;
    color: #000;
}

.badge-in_progress {
    background: #17a2b8;
    color: #fff;
}

.badge-resolved {
    background: #28a745;
    color: #fff;
}

.badge-closed {
    background: #6c757d;
    color: #fff;
}

.badge-low {
    background: #28a745;
    color: #fff;
}

.badge-medium {
    background: #ffc107;
    color: #000;
}

.badge-high {
    background: #fd7e14;
    color: #fff;
}

.badge-urgent {
    background: #dc3545;
    color: #fff;
}

.info-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.info-card h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
}

.info-card label {
    display: block;
    font-weight: bold;
    color: #555;
    font-size: 14px;
}

.info-card p {
    margin: 5px 0 0 0;
    color: #333;
}
</style>

<?php include '../../includes/footer.php'; ?>
