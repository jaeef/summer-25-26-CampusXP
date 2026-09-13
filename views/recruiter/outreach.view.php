<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Direct Candidate Outreach Dispatch Logs</h1>
        <p class="portal-sub">Track direct student notification alerts sent to shortlisted candidates.</p>
    </div>
    <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-primary btn-sm">+ Talent Filter Search</a>
</div>

<div class="glass-card">
    <div class="card-header">
        <div class="card-title">Sent Invitations (<?= count($logs) ?>)</div>
    </div>

    <?php if (empty($logs)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No outreach invitations sent yet. Use the Talent Search to invite students directly.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>AIUB ID</th>
                    <th>Department</th>
                    <th>CGPA</th>
                    <th>Shortlist Bucket</th>
                    <th>Message Delivered</th>
                    <th>Date Sent</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $lg): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($lg['student_name']) ?></td>
                        <td><?= htmlspecialchars($lg['aiub_id']) ?></td>
                        <td><span class="badge-tag badge-student"><?= htmlspecialchars($lg['department']) ?></span></td>
                        <td><strong style="color: var(--brand-blue);"><?= htmlspecialchars($lg['cgpa'] ? number_format((float)$lg['cgpa'], 2) : '0.00') ?></strong></td>
                        <td><?= htmlspecialchars($lg['bucket_name']) ?></td>
                        <td style="font-size: 0.82rem; max-width: 250px;"><?= htmlspecialchars($lg['message']) ?></td>
                        <td><?= date('M d, Y - h:i A', strtotime($lg['sent_at'])) ?></td>
                        <td><span class="badge-tag badge-exec">✓ Delivered</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
