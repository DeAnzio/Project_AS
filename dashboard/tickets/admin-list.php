<?php
require_once '../../config/config.php';
require_once '../../includes/auth_check.php';

if (!hasRole('admin')) {
    redirect('../../dashboard/');
}

require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Ambil semua tickets
$query = "SELECT t.id, t.ticket_number, t.title, t.status, t.priority, u.username, t.created_at, t.updated_at
          FROM tickets t
          JOIN users u ON t.user_id = u.id
          ORDER BY 
            CASE t.status 
                WHEN 'open' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'resolved' THEN 3
                ELSE 4
            END,
            t.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Daftar Ticket';
include '../../includes/header.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-list"></i> Daftar Semua Ticket</h1>

    <div style="margin-bottom: 20px; display: flex; gap: 10px;">
        <span class="stat-badge">Total: <strong><?php echo count($tickets); ?></strong></span>
        <span class="stat-badge">Open: <strong><?php echo count(array_filter($tickets, fn($t) => $t['status'] === 'open')); ?></strong></span>
        <span class="stat-badge">In Progress: <strong><?php echo count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress')); ?></strong></span>
        <span class="stat-badge">Resolved: <strong><?php echo count(array_filter($tickets, fn($t) => $t['status'] === 'resolved')); ?></strong></span>
    </div>

    <?php if (empty($tickets)): ?>
    <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; text-align: center;">
        <p>Tidak ada ticket.</p>
    </div>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>From User</th>
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
                <td><?php echo htmlspecialchars($ticket['username']); ?></td>
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
                    <a href="admin-detail.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<style>
.stat-badge {
    display: inline-block;
    background: #e9ecef;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 14px;
}

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
