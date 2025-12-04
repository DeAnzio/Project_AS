<?php
require_once '../../config/config.php';
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Ambil ticket user sendiri
$query = "SELECT id, ticket_number, title, status, priority, created_at, updated_at 
          FROM tickets 
          WHERE user_id = :user_id 
          ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Ticket Saya';
include '../../includes/header.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-list"></i> Ticket Saya</h1>

    <div style="margin-bottom: 20px;">
        <a href="user-submit.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Submit Ticket Baru
        </a>
    </div>

    <?php if (empty($tickets)): ?>
    <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; text-align: center;">
        <p>Anda belum memiliki ticket. <a href="user-submit.php">Submit ticket baru sekarang</a>.</p>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($ticket['ticket_number']); ?></strong></td>
                <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $ticket['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                    </span>
                </td>
                <td>
                    <span class="badge badge-<?php echo $ticket['priority']; ?>">
                        <?php echo ucfirst($ticket['priority']); ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
                <td>
                    <a href="user-detail.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
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

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}
</style>

<?php include '../../includes/footer.php'; ?>
