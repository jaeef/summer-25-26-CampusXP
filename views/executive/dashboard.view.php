<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Overview</h1>
        <p class="page-sub">Your club's drives, applicants and where each one stands.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-drive-modal')">New drive</button>
</div>

<div class="stats">
    <div class="stat"><span class="stat-value"><?= $stats['total_apps'] ?? 0 ?></span><span class="stat-label">Applicants</span></div>
    <div class="stat"><span class="stat-value"><?= $stats['review_apps'] ?? 0 ?></span><span class="stat-label">Under review</span></div>
    <div class="stat"><span class="stat-value"><?= $stats['interview_apps'] ?? 0 ?></span><span class="stat-label">Interview</span></div>
    <div class="stat"><span class="stat-value"><?= $stats['selected_apps'] ?? 0 ?></span><span class="stat-label">Selected</span></div>
</div>

<!-- Active Recruitment Drives Table -->
<div class="glass-card section">
    <div class="card-header">
        <h2 class="card-title">Your drives</h2>
    </div>

    <?php if (empty($drives)): ?>
        <div class="empty-state">You haven't posted a drive yet. <a href="#" onclick="openModal('new-drive-modal'); return false;">Create your first drive</a></div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Drive</th>
                    <th>Club</th>
                    <th>Category</th>
                    <th>Deadline</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Applicants</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drives as $d): 
                    $app_status = $d['approval_status'] ?? ($d['status'] === 'active' ? 'Approved' : 'Pending');
                    $is_rejected = ($app_status === 'Rejected' || $d['status'] === 'closed');
                    $is_pending = ($app_status === 'Pending' || $d['status'] === 'pending_approval');
                ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($d['title']) ?></td>
                        <td><?= htmlspecialchars($d['club_name']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($d['category'])) ?></td>
                        <td><?= date('M d, Y', strtotime($d['deadline'])) ?></td>
                        <td><?= htmlspecialchars($d['location']) ?></td>
                        <td class="wrap">
                            <?php if ($is_rejected): ?>
                                <span class="badge-tag tag-danger">Rejected</span>
                                <?php if (!empty($d['admin_comments'])): ?>
                                    <div class="text-xs text-danger mt-1"><?= htmlspecialchars($d['admin_comments']) ?></div>
                                <?php endif; ?>
                            <?php elseif ($is_pending): ?>
                                <span class="badge-tag tag-warning">Awaiting approval</span>
                            <?php else: ?>
                                <span class="badge-tag tag-success">Live</span>
                            <?php endif; ?>
                        </td>
                        <td class="nums"><?= $d['app_count'] ?></td>
                        <td style="white-space: nowrap;">
                            <a href="index.php?controller=executive&action=kanban&drive_id=<?= $d['id'] ?>" class="btn btn-outline btn-sm">Applicants</a>
                            <a href="index.php?controller=executive&action=scheduler&drive_id=<?= $d['id'] ?>" class="btn btn-outline btn-sm">Interviews</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<!-- Create New Drive Modal -->
<div class="modal-backdrop" id="new-drive-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">New drive</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-drive-modal')">Close</button>
        </div>

        <form action="index.php?controller=executive&action=create_drive" method="POST">
            <div class="form-group">
                <label class="form-label">Club</label>
                <input type="text" name="club_name" class="form-control" value="AIUB Computer Club (ACC)" required>
            </div>

            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Fall 2026 Executive Recruitment" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="recruitment">Recruitment drive</option>
                        <option value="workshop">Workshop</option>
                        <option value="seminar">Seminar</option>
                        <option value="fest">Fest / audition</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Application deadline</label>
                    <input type="date" name="deadline" class="form-control" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Venue</label>
                <input type="text" name="location" class="form-control" value="Annex 1 Ground Plaza - Booth B-01" required>
            </div>

            <div class="form-group">
                <label class="form-label">Eligibility and roles</label>
                <textarea name="requirements" class="form-control" rows="3" placeholder="Open roles, minimum CGPA, skills you need" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit for approval</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
