<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Overview</h1>
        <p class="page-sub">Approvals waiting on you, live drives and everyone registered on CampusXP.</p>
    </div>
    <a href="index.php?controller=admin&action=moderation" class="btn btn-primary btn-sm">Review approvals</a>
</div>

<div class="stats">
    <a class="stat" href="index.php?controller=admin&action=moderation"><span class="stat-value"><?= $metrics['pending_moderations'] ?></span><span class="stat-label">Awaiting approval</span></a>
    <div class="stat"><span class="stat-value"><?= $metrics['active_drives'] ?></span><span class="stat-label">Live drives</span></div>
    <div class="stat"><span class="stat-value"><?= $metrics['student_count'] ?></span><span class="stat-label">Students</span></div>
    <div class="stat"><span class="stat-value"><?= $metrics['total_users'] ?></span><span class="stat-label">All accounts</span></div>
</div>

<div class="glass-card section">
    <div class="card-header">
        <h2 class="card-title">Recent submissions</h2>
        <a href="index.php?controller=admin&action=moderation" class="btn btn-outline btn-sm">View all</a>
    </div>

    <?php if (empty($recent_moderations)): ?>
        <div class="empty-state">Nothing waiting for approval.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Submitted by</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_moderations as $req): ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($req['title']) ?></td>
                        <td><?= htmlspecialchars($req['target_type']) ?></td>
                        <td><?= htmlspecialchars($req['submitted_by']) ?></td>
                        <td><?= date('M d, Y', strtotime($req['submitted_at'])) ?></td>
                        <td>
                            <?php if ($req['status'] === 'Approved'): ?>
                                <span class="badge-tag tag-success">Approved</span>
                            <?php elseif ($req['status'] === 'Rejected'): ?>
                                <span class="badge-tag tag-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge-tag tag-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($req['status'] === 'Pending'): ?>
                                <a href="index.php?controller=admin&action=moderation" class="btn btn-outline btn-sm">Review</a>
                            <?php else: ?>
                                <span class="text-xs text-dim"><?= date('M d', strtotime($req['reviewed_at'])) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">Accounts</h2>
    </div>

    <div class="table-wrap"><table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_users as $u): ?>
                <tr>
                    <td class="text-dim"><?= $u['id'] ?></td>
                    <td class="fw-600"><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $u['role'])) ?></td>
                    <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table></div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
