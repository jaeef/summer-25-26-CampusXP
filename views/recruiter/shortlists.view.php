<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Shortlists</h1>
        <p class="page-sub">Saved searches for the kinds of students you hire. Open one to see who currently matches.</p>
    </div>
    <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-outline btn-sm">Talent search</a>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">Your shortlists <span class="count-badge"><?= count($shortlists) ?></span></h2>
    </div>

    <?php if (empty($shortlists)): ?>
        <div class="empty-state">No shortlists yet. Create one from <a href="index.php?controller=recruiter&action=talent_search">Talent search</a>.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Minimum CGPA</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shortlists as $sl): ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($sl['bucket_name']) ?></td>
                        <td><?= htmlspecialchars($sl['filter_dept'] ?: 'Any') ?></td>
                        <td><?= htmlspecialchars($sl['filter_cgpa_min']) ?></td>
                        <td><?= date('M d, Y', strtotime($sl['created_at'])) ?></td>
                        <td>
                            <a href="index.php?controller=recruiter&action=talent_search&dept=<?= urlencode($sl['filter_dept']) ?>&min_cgpa=<?= urlencode($sl['filter_cgpa_min']) ?>" class="btn btn-outline btn-sm">
                                See matches
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
