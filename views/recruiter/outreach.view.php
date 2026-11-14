<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Outreach</h1>
        <p class="page-sub">Invitations you've sent. Students see them on their Opportunities page.</p>
    </div>
    <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-outline btn-sm">Talent search</a>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">Sent <span class="count-badge"><?= count($logs) ?></span></h2>
    </div>

    <?php if (empty($logs)): ?>
        <div class="empty-state">No invitations sent yet. Find students in <a href="index.php?controller=recruiter&action=talent_search">Talent search</a>.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>ID</th>
                    <th>Dept</th>
                    <th>CGPA</th>
                    <th>Shortlist</th>
                    <th>Message</th>
                    <th>Sent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $lg): ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($lg['student_name']) ?></td>
                        <td><?= htmlspecialchars($lg['aiub_id']) ?></td>
                        <td><?= htmlspecialchars($lg['department']) ?></td>
                        <td><?= htmlspecialchars($lg['cgpa'] ? number_format((float)$lg['cgpa'], 2) : '0.00') ?></td>
                        <td><?= htmlspecialchars($lg['bucket_name']) ?></td>
                        <td class="text-muted wrap"><?= htmlspecialchars($lg['message']) ?></td>
                        <td><?= date('M d, Y', strtotime($lg['sent_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
