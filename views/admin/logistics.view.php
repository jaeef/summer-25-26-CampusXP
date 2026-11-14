<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Equipment</h1>
        <p class="page-sub">Equipment requests from clubs and recruiters. Approve, mark as dispatched, and record returns.</p>
    </div>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">Requests <span class="count-badge"><?= count($requests) ?></span></h2>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state">No equipment requests yet.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Requester</th>
                    <th>Role</th>
                    <th>Equipment</th>
                    <th>Qty</th>
                    <th>Needed</th>
                    <th>Return</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): 
                    $st = $r['dispatch_status'];
                    $badge = '';
                    if ($st === 'Approved') $badge = 'tag-success';
                    elseif ($st === 'Dispatched') $badge = 'tag-info';
                    elseif ($st === 'Rejected') $badge = 'tag-danger';
                ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($r['event_name']) ?></td>
                        <td><?= htmlspecialchars($r['requester_name']) ?></td>
                        <td class="text-muted"><?= ucfirst(str_replace('_', ' ', $r['requester_role'])) ?></td>
                        <td><?= htmlspecialchars($r['item_type']) ?></td>
                        <td><?= $r['quantity'] ?></td>
                        <td><?= !empty($r['needed_date']) ? date('M d, Y', strtotime($r['needed_date'])) : '—' ?></td>
                        <td><?= !empty($r['return_date']) ? date('M d, Y', strtotime($r['return_date'])) : '—' ?></td>
                        <td><span class="badge-tag <?= $badge ?>"><?= htmlspecialchars($st) ?></span></td>
                        <td>
                            <form action="index.php?controller=admin&action=logistics_action" method="POST" class="row" style="flex-wrap: nowrap; gap: 6px;">
                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                <select name="dispatch_status" class="form-control kc-select" style="margin-top: 0; width: auto;" aria-label="Status">
                                    <option value="Requested" <?= $st === 'Requested' ? 'selected' : '' ?>>Requested</option>
                                    <option value="Approved" <?= $st === 'Approved' ? 'selected' : '' ?>>Approve</option>
                                    <option value="Dispatched" <?= $st === 'Dispatched' ? 'selected' : '' ?>>Dispatch</option>
                                    <option value="Returned" <?= $st === 'Returned' ? 'selected' : '' ?>>Returned</option>
                                    <option value="Rejected" <?= $st === 'Rejected' ? 'selected' : '' ?>>Reject</option>
                                </select>
                                <button type="submit" class="btn btn-outline btn-sm">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
