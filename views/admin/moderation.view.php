<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Campus Event Moderation Queue</h1>
        <p class="portal-sub">Approve or reject club recruitment campaigns and corporate tech seminars before publishing to student radar.</p>
    </div>
    <a href="index.php?controller=admin&action=dashboard" class="btn btn-outline btn-sm">← Back to Overview</a>
</div>

<div class="glass-card">
    <div class="card-header">
        <div class="card-title">Submitted Campaign Queue (<?= count($requests) ?>)</div>
    </div>

    <?php if (empty($requests)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No moderation requests found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Event / Drive Title</th>
                    <th>Category</th>
                    <th>Organizer / Submitter</th>
                    <th>Submission Date</th>
                    <th>Current Status</th>
                    <th>Moderator Feedback</th>
                    <th>Decision Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($r['title']) ?></td>
                        <td><span class="badge-tag badge-student"><?= htmlspecialchars($r['target_type']) ?></span></td>
                        <td><?= htmlspecialchars($r['submitted_by']) ?></td>
                        <td><?= date('M d, Y', strtotime($r['submitted_at'])) ?></td>
                        <td>
                            <?php if ($r['status'] === 'Approved'): ?>
                                <span class="badge-tag badge-exec">Approved</span>
                            <?php elseif ($r['status'] === 'Rejected'): ?>
                                <span class="badge-tag badge-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge-tag" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;">Pending Review</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-muted);">
                            <?= htmlspecialchars($r['admin_comments'] ?: 'No comments logged') ?>
                        </td>
                        <td>
                            <?php if ($r['status'] === 'Pending'): ?>
                                <div style="display: flex; gap: 4px;">
                                    <form action="index.php?controller=admin&action=moderate_action" method="POST" style="margin:0;">
                                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="decision" value="Approved">
                                        <input type="hidden" name="admin_comments" value="Approved for campus publication.">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="index.php?controller=admin&action=moderate_action" method="POST" style="margin:0;">
                                        <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="decision" value="Rejected">
                                        <input type="hidden" name="admin_comments" value="Requires revised eligibility criteria.">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 0.78rem; color: var(--text-dim);"><?= date('M d, Y', strtotime($r['reviewed_at'])) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
