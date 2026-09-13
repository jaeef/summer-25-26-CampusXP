<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Corporate Recruiter Portal</h1>
        <p class="portal-sub">Post job circulars, screen applicants on Kanban pipeline, and search verified AIUB talent.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="index.php?controller=recruiter&action=create_drive" class="btn btn-primary btn-sm">+ Post New Job</a>
        <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-outline btn-sm">Talent Search</a>
        <a href="index.php?controller=recruiter&action=logistics" class="btn btn-outline btn-sm">Campus Logistics</a>
    </div>
</div>

<!-- KPI Metrics -->
<div class="grid-4" style="margin-bottom: 1.5rem;">
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-blue);"><?= $active_drives_count ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Active Job Openings & Circulars</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-purple);"><?= count($shortlists) ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Target Shortlist Buckets</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-green);"><?= $outreach_count ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Direct Candidate Alerts Sent</div>
    </div>
    <div class="glass-card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-gold);">100%</div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">AIUB Verified Pool</div>
    </div>
</div>

<!-- Active Drives Table -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">My Job Circulars & Dynamic Screening Forms</div>
        <a href="index.php?controller=recruiter&action=create_drive" class="btn btn-outline btn-sm">+ Post Job Opening</a>
    </div>

    <?php if (empty($drives)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No corporate job circulars published yet. Click "+ Post Job Opening" to publish a new circular.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Position Type</th>
                    <th>Min CGPA</th>
                    <th>Custom Fields Attached</th>
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
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($dr['job_title']) ?></td>
                        <td><?= htmlspecialchars($dr['company_name']) ?></td>
                        <td><span class="badge-tag badge-recruiter"><?= htmlspecialchars($dr['job_type']) ?></span></td>
                        <td><strong><?= htmlspecialchars($dr['min_cgpa']) ?></strong></td>
                        <td><strong><?= $dr['custom_field_count'] ?> Questions</strong></td>
                        <td><?= date('M d, Y', strtotime($dr['deadline'])) ?></td>
                        <td>
                            <?php if ($is_rejected): ?>
                                <span class="badge-tag" style="background: var(--brand-red-light); color: var(--brand-red); border: 1px solid var(--brand-red-border); font-weight: 700;">
                                    ✕ Rejected / Closed
                                </span>
                                <?php if (!empty($dr['admin_comments'])): ?>
                                    <div style="font-size: 0.72rem; color: #dc2626; margin-top: 3px;"><?= htmlspecialchars($dr['admin_comments']) ?></div>
                                <?php endif; ?>
                            <?php elseif ($is_pending): ?>
                                <span class="badge-tag" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-weight: 700;">
                                    ⏳ Pending Review
                                </span>
                            <?php else: ?>
                                <span class="badge-tag badge-exec">✓ Active</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
