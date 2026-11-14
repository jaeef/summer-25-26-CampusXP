<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Approvals</h1>
        <p class="page-sub">Club drives and corporate job posts only reach students after you approve them.</p>
    </div>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">Submissions <span class="count-badge"><?= count($requests) ?></span></h2>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state">No submissions yet.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Submitted by</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($r['title']) ?></td>
                        <td><?= htmlspecialchars($r['target_type']) ?></td>
                        <td><?= htmlspecialchars($r['submitted_by']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['submitted_at'])) ?></td>
                        <td>
                            <?php if ($r['status'] === 'Approved'): ?>
                                <span class="badge-tag tag-success">Approved</span>
                            <?php elseif ($r['status'] === 'Rejected'): ?>
                                <span class="badge-tag tag-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge-tag tag-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-xs text-muted wrap">
                            <?= htmlspecialchars($r['admin_comments'] ?: '—') ?>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'Pending'): ?>
                                <div class="row" style="gap: 6px; flex-wrap: nowrap;">
                                    <form action="index.php?controller=admin&action=moderate_action" method="POST">
                                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="decision" value="Approved">
                                        <input type="hidden" name="admin_comments" value="Approved for campus publication.">
                                        <button type="submit" class="btn btn-sm">Approve</button>
                                    </form>
                                    <form action="index.php?controller=admin&action=moderate_action" method="POST">
                                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="decision" value="Rejected">
                                        <input type="hidden" name="admin_comments" value="Requires revised eligibility criteria.">
                                        <button type="submit" class="btn btn-sm btn-outline">Reject</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="text-xs text-dim"><?= date('M d, Y', strtotime($r['reviewed_at'])) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
