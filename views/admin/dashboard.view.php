<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">University Administration Portal</h1>
        <p class="portal-sub">Oversee campus recruitment campaigns, approve club drives, assign campus booth numbers, and manage equipment logistics.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="index.php?controller=admin&action=moderation" class="btn btn-primary btn-sm">Moderation Queue (<?= $metrics['pending_moderations'] ?>)</a>
        <a href="index.php?controller=admin&action=booths" class="btn btn-outline btn-sm">Booth Allocator</a>
        <a href="index.php?controller=admin&action=logistics" class="btn btn-outline btn-sm">Logistics Manager</a>
    </div>
</div>

<!-- KPI Summary Metrics -->
<div class="grid-4" style="margin-bottom: 2rem;">
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-blue);"><?= $metrics['total_users'] ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Total Registered Users</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-green);"><?= $metrics['student_count'] ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Verified AIUB Students</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-purple);"><?= $metrics['active_drives'] ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Live Published Drives</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: #d97706;"><?= $metrics['pending_moderations'] ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Pending Moderations</div>
    </div>
</div>

<!-- Quick Moderation Queue Preview -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">Recent Event & Campaign Moderation Submissions</div>
        <a href="index.php?controller=admin&action=moderation" class="btn btn-outline btn-sm">View Full Queue →</a>
    </div>

    <?php if (empty($recent_moderations)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">All submitted campaigns have been reviewed. Queue is clear.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Submitted By</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_moderations as $req): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($req['title']) ?></td>
                        <td><span class="badge-tag badge-student"><?= htmlspecialchars($req['target_type']) ?></span></td>
                        <td><?= htmlspecialchars($req['submitted_by']) ?></td>
                        <td><?= date('M d, Y', strtotime($req['submitted_at'])) ?></td>
                        <td>
                            <?php if ($req['status'] === 'Approved'): ?>
                                <span class="badge-tag badge-exec">Approved</span>
                            <?php elseif ($req['status'] === 'Rejected'): ?>
                                <span class="badge-tag badge-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge-tag" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($req['status'] === 'Pending'): ?>
                                <a href="index.php?controller=admin&action=moderation" class="btn btn-primary btn-sm" style="padding: 3px 8px; font-size: 0.75rem;">Review</a>
                            <?php else: ?>
                                <span style="font-size: 0.78rem; color: var(--text-dim);"><?= date('M d', strtotime($req['reviewed_at'])) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Registered Users System Audit -->
<div class="glass-card">
    <div class="card-header">
        <div class="card-title">Registered Accounts Registry</div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>Assigned Role</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_users as $u): ?>
                <tr>
                    <td>#<?= $u['id'] ?></td>
                    <td style="font-weight: 700;"><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge-tag badge-<?= $u['role'] ?>"><?= ucfirst(str_replace('_', ' ', $u['role'])) ?></span></td>
                    <td><?= date('M d, Y - h:i A', strtotime($u['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
