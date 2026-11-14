<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Job posts</h1>
        <p class="page-sub">Your job posts at AIUB, their approval status and screening questions.</p>
    </div>
    <a href="index.php?controller=recruiter&action=create_drive" class="btn btn-primary btn-sm">Post a job</a>
</div>

<div class="stats">
    <div class="stat"><span class="stat-value"><?= $active_drives_count ?></span><span class="stat-label">Live job posts</span></div>
    <a class="stat" href="index.php?controller=recruiter&action=shortlists"><span class="stat-value"><?= count($shortlists) ?></span><span class="stat-label">Shortlists</span></a>
    <a class="stat" href="index.php?controller=recruiter&action=outreach"><span class="stat-value"><?= $outreach_count ?></span><span class="stat-label">Invitations sent</span></a>
</div>

<div class="glass-card section">
    <div class="card-header">
        <h2 class="card-title">All job posts</h2>
    </div>

    <?php if (empty($drives)): ?>
        <div class="empty-state">No job posts yet. <a href="index.php?controller=recruiter&action=create_drive">Post your first job</a></div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Job</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Min CGPA</th>
                    <th>Questions</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drives as $dr): 
                    $app_status = $dr['approval_status'] ?? ($dr['status'] === 'active' ? 'Approved' : 'Pending');
                    $is_rejected = ($app_status === 'Rejected' || $dr['status'] === 'closed');
                    $is_pending = ($app_status === 'Pending' || $dr['status'] === 'pending_approval');
                ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($dr['job_title']) ?></td>
                        <td><?= htmlspecialchars($dr['company_name']) ?></td>
                        <td><?= htmlspecialchars($dr['job_type']) ?></td>
                        <td><?= htmlspecialchars($dr['min_cgpa']) ?></td>
                        <td><?= $dr['custom_field_count'] ?></td>
                        <td><?= date('M d, Y', strtotime($dr['deadline'])) ?></td>
                        <td class="wrap">
                            <?php if ($is_rejected): ?>
                                <span class="badge-tag tag-danger">Rejected</span>
                                <?php if (!empty($dr['admin_comments'])): ?>
                                    <div class="text-xs text-danger mt-1"><?= htmlspecialchars($dr['admin_comments']) ?></div>
                                <?php endif; ?>
                            <?php elseif ($is_pending): ?>
                                <span class="badge-tag tag-warning">Awaiting approval</span>
                            <?php else: ?>
                                <span class="badge-tag tag-success">Live</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
