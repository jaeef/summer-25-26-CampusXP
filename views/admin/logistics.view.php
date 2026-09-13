<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">University Campus Logistics & Equipment Dispatch</h1>
        <p class="portal-sub">Review, approve, and record return status for university sound equipment, projectors, and event inventory.</p>
    </div>
    <a href="index.php?controller=admin&action=dashboard" class="btn btn-outline btn-sm">← Back to Overview</a>
</div>

<div class="glass-card">
    <div class="card-header">
        <div class="card-title">All Campus Equipment Requisitions (<?= count($requests) ?>)</div>
    </div>

    <?php if (empty($requests)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No equipment requisitions on file.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Requester</th>
                    <th>Role</th>
                    <th>Equipment Type</th>
                    <th>Qty</th>
                    <th>Needed Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Manage Dispatch Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): 
                    $st = $r['dispatch_status'];
                    $badge = 'badge-student';
                    if ($st === 'Approved') $badge = 'badge-exec';
                    elseif ($st === 'Dispatched') $badge = 'badge-purple';
                    elseif ($st === 'Rejected') $badge = 'badge-danger';
                ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($r['event_name']) ?></td>
                        <td><?= htmlspecialchars($r['requester_name']) ?></td>
                        <td><span class="badge-tag badge-<?= $r['requester_role'] ?>"><?= ucfirst($r['requester_role']) ?></span></td>
                        <td><?= htmlspecialchars($r['item_type']) ?></td>
                        <td><strong><?= $r['quantity'] ?> units</strong></td>
                        <td><?= date('M d, Y', strtotime($r['needed_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($r['return_date'])) ?></td>
                        <td><span class="badge-tag <?= $badge ?>"><?= htmlspecialchars($st) ?></span></td>
                        <td>
                            <form action="index.php?controller=admin&action=logistics_action" method="POST" style="display: flex; gap: 4px; margin: 0;">
                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                <select name="dispatch_status" class="form-control" style="font-size: 0.78rem; padding: 4px 6px; height: 32px; width: auto;">
                                    <option value="Requested" <?= $st === 'Requested' ? 'selected' : '' ?>>Requested</option>
                                    <option value="Approved" <?= $st === 'Approved' ? 'selected' : '' ?>>Approve</option>
                                    <option value="Dispatched" <?= $st === 'Dispatched' ? 'selected' : '' ?>>Dispatch</option>
                                    <option value="Returned" <?= $st === 'Returned' ? 'selected' : '' ?>>Returned</option>
                                    <option value="Rejected" <?= $st === 'Rejected' ? 'selected' : '' ?>>Reject</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
