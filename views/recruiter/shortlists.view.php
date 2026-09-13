<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Candidate Shortlist Buckets</h1>
        <p class="portal-sub">Organize verified AIUB candidates into recruitment groups and targeted hiring buckets.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-primary btn-sm">+ Talent Filter Search</a>
        <a href="index.php?controller=recruiter&action=outreach" class="btn btn-outline btn-sm">Outreach History</a>
    </div>
</div>

<div class="glass-card">
    <div class="card-header">
        <div class="card-title">My Configured Shortlist Buckets (<?= count($shortlists) ?>)</div>
    </div>

    <?php if (empty($shortlists)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No shortlist buckets created yet. Use the Talent Search to define new hiring groups.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bucket Name</th>
                    <th>Target Department</th>
                    <th>Minimum CGPA</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shortlists as $sl): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($sl['bucket_name']) ?></td>
                        <td><span class="badge-tag badge-student"><?= htmlspecialchars($sl['filter_dept'] ?: 'Any') ?></span></td>
                        <td><strong style="color: var(--brand-blue);"><?= htmlspecialchars($sl['filter_cgpa_min']) ?></strong></td>
                        <td><?= date('M d, Y', strtotime($sl['created_at'])) ?></td>
                        <td>
                            <a href="index.php?controller=recruiter&action=talent_search&dept=<?= urlencode($sl['filter_dept']) ?>&min_cgpa=<?= urlencode($sl['filter_cgpa_min']) ?>" class="btn btn-outline btn-sm">
                                View Matching Pool
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
