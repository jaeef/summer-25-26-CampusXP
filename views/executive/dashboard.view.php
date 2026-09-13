<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Club Executive Dashboard</h1>
        <p class="portal-sub">Manage applicant pipelines, generate interview venue slots, and view candidate demographics.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button class="btn btn-primary btn-sm" onclick="openModal('new-drive-modal')">+ Create New Drive</button>
        <a href="index.php?controller=executive&action=logistics" class="btn btn-outline btn-sm">Equipment Logistics</a>
        <a href="index.php?controller=executive&action=kanban" class="btn btn-outline btn-sm">Open Kanban Board</a>
    </div>
</div>

<!-- Key Performance Metrics -->
<div class="grid-4" style="margin-bottom: 1.5rem;">
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-blue);"><?= $stats['total_apps'] ?? 0 ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Total Candidates</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-gold);"><?= $stats['review_apps'] ?? 0 ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Under Review</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-purple);"><?= $stats['interview_apps'] ?? 0 ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Interview Scheduled</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-green);"><?= $stats['selected_apps'] ?? 0 ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Selected Members</div>
    </div>
</div>

<!-- Active Recruitment Drives Table -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">My Club Recruitment Drives & Campaigns</div>
        <button class="btn btn-outline btn-sm" onclick="openModal('new-drive-modal')">+ New Drive Post</button>
    </div>

    <?php if (empty($drives)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No club drives posted yet. Click "+ New Drive Post" to create one.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Drive Title</th>
                    <th>Club Name</th>
                    <th>Category</th>
                    <th>Deadline</th>
                    <th>Location / Booth</th>
                    <th>Status</th>
                    <th>Applicants</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($drives as $d): 
                    $app_status = $d['approval_status'] ?? ($d['status'] === 'active' ? 'Approved' : 'Pending');
                    $is_rejected = ($app_status === 'Rejected' || $d['status'] === 'closed');
                    $is_pending = ($app_status === 'Pending' || $d['status'] === 'pending_approval');
                ?>
                    <tr>
                        <td style="font-weight: 700;"><?= htmlspecialchars($d['title']) ?></td>
                        <td><?= htmlspecialchars($d['club_name']) ?></td>
                        <td><span class="badge-tag badge-student"><?= htmlspecialchars(ucfirst($d['category'])) ?></span></td>
                        <td><?= date('M d, Y', strtotime($d['deadline'])) ?></td>
                        <td><?= htmlspecialchars($d['location']) ?></td>
                        <td>
                            <?php if ($is_rejected): ?>
                                <span class="badge-tag" style="background: var(--brand-red-light); color: var(--brand-red); border: 1px solid var(--brand-red-border); font-weight: 700;">
                                    ✕ Rejected / Closed
                                </span>
                                <?php if (!empty($d['admin_comments'])): ?>
                                    <div style="font-size: 0.72rem; color: #dc2626; margin-top: 3px;"><?= htmlspecialchars($d['admin_comments']) ?></div>
                                <?php endif; ?>
                            <?php elseif ($is_pending): ?>
                                <span class="badge-tag" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 700;">
                                    ⏳ Pending Review
                                </span>
                            <?php else: ?>
                                <span class="badge-tag badge-exec">✓ Active</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= $d['app_count'] ?> Candidates</strong></td>
                        <td>
                            <a href="index.php?controller=executive&action=kanban&drive_id=<?= $d['id'] ?>" class="btn btn-sm">Kanban</a>
                            <a href="index.php?controller=executive&action=scheduler&drive_id=<?= $d['id'] ?>" class="btn btn-outline btn-sm">Slots</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Create New Drive Modal -->
<div class="modal-backdrop" id="new-drive-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Post New Club Opportunity Drive</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-drive-modal')">Close</button>
        </div>

        <form action="index.php?controller=executive&action=create_drive" method="POST">
            <div class="form-group">
                <label class="form-label">Club / Organization Name</label>
                <input type="text" name="club_name" class="form-control" value="AIUB Computer Club (ACC)" required>
            </div>

            <div class="form-group">
                <label class="form-label">Recruitment Drive / Event Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Fall 2026 Executive Recruitment" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="recruitment">Recruitment Drive</option>
                        <option value="workshop">Technical Workshop</option>
                        <option value="seminar">Seminar</option>
                        <option value="fest">Fest / Audition</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Application Cutoff Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">AIUB Venue / Physical Location</label>
                <input type="text" name="location" class="form-control" value="Annex 1 Ground Plaza - Booth B-01" required>
            </div>

            <div class="form-group">
                <label class="form-label">Eligibility Criteria & Roles</label>
                <textarea name="requirements" class="form-control" rows="3" placeholder="Specify CGPA requirements, open positions, and required skills..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Publish Drive to Radar
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
