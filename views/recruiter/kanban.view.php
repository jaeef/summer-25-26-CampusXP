<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Corporate Applicant Kanban Pipeline</h1>
        <p class="portal-sub">Track and progress candidate applications from student 1-click apply submissions across your job circulars.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <form method="GET" action="index.php" style="display: flex; gap: 8px; margin: 0;">
            <input type="hidden" name="controller" value="recruiter">
            <input type="hidden" name="action" value="kanban">
            <select name="drive_id" class="form-control" onchange="this.form.submit()" style="width: auto; min-width: 280px; font-weight: 600;">
                <option value="0" <?= $selected_drive_id === 0 ? 'selected' : '' ?>>★ All Published Job Circulars</option>
                <?php foreach ($drives as $dr): ?>
                    <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dr['job_title']) ?> (<?= htmlspecialchars($dr['company_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="index.php?controller=recruiter&action=create_drive" class="btn btn-outline btn-sm">+ Post Job</a>
    </div>
</div>

<!-- Kanban Columns -->
<div class="kanban-board">
    <?php 
    $stages = [
        'Applied' => ['label' => '1. Application Received', 'color' => '#1d4ed8', 'badge' => 'badge-student'],
        'Under Review' => ['label' => '2. Profile Screening', 'color' => '#b45309', 'badge' => 'badge-gold'],
        'Interview' => ['label' => '3. Technical Interview', 'color' => '#7e22ce', 'badge' => 'badge-admin'],
        'Selected' => ['label' => '4. Extended Offer', 'color' => '#15803d', 'badge' => 'badge-exec'],
        'Rejected' => ['label' => '5. Rejected / Closed', 'color' => '#dc2626', 'badge' => 'badge-danger']
    ];
    ?>

    <?php foreach ($stages as $stage_key => $st_info): 
        $candidates = $pipeline[$stage_key] ?? [];
    ?>
        <div class="kanban-column" style="border-top: 3px solid <?= $st_info['color'] ?>;">
            <div class="kanban-col-header">
                <span style="font-weight: 700; color: <?= $st_info['color'] ?>;"><?= $st_info['label'] ?></span>
                <span class="count-badge" style="background: <?= $st_info['color'] ?>; color: #fff; border-radius: 12px; padding: 2px 8px; font-size: 0.75rem; font-weight: 700;"><?= count($candidates) ?></span>
            </div>

            <div class="kanban-card-list" id="col-corp-<?= strtolower(str_replace(' ', '-', $stage_key)) ?>" style="min-height: 400px; display: flex; flex-direction: column; gap: 8px;">
                <?php if (empty($candidates)): ?>
                    <div style="text-align: center; color: var(--text-dim); font-size: 0.8rem; padding: 20px 0;">No candidates in this stage</div>
                <?php else: ?>
                    <?php foreach ($candidates as $cand): ?>
                        <div class="kanban-card" style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 12px; box-shadow: var(--shadow-sm);">
                            <div style="font-size: 0.72rem; color: var(--brand-purple); font-weight: 700; margin-bottom: 3px;">
                                <?= htmlspecialchars($cand['drive_title'] ?? 'Corporate Drive') ?>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                <strong style="font-size: 0.92rem; color: var(--text-main);"><?= htmlspecialchars($cand['full_name']) ?></strong>
                                <span class="badge-tag <?= $st_info['badge'] ?>" style="font-size: 0.7rem;"><?= htmlspecialchars($cand['department']) ?></span>
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-dim); margin-bottom: 4px;">
                                ID: <strong><?= htmlspecialchars($cand['aiub_id']) ?></strong> • CGPA: <strong style="color: var(--brand-blue);"><?= htmlspecialchars($cand['cgpa'] ? number_format((float)$cand['cgpa'], 2) : '0.00') ?></strong>
                            </div>
                            <div style="font-size: 0.8rem; color: #0f172a; font-weight: 600; margin-bottom: 6px;">
                                Applied Role: <span style="color: var(--brand-purple);"><?= htmlspecialchars($cand['applied_role']) ?></span>
                            </div>
                            <?php if (!empty($cand['cv_link'])): ?>
                                <div style="margin-bottom: 6px;">
                                    <a href="<?= htmlspecialchars($cand['cv_link']) ?>" target="_blank" style="font-size: 0.78rem; font-weight: 600; color: var(--brand-blue);">View Resume / CV</a>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($cand['statement_of_purpose'])): ?>
                                <details style="margin-bottom: 8px; font-size: 0.76rem; color: var(--text-muted);">
                                    <summary style="cursor: pointer; font-weight: 600;">SOP Statement</summary>
                                    <div style="background: #f8fafc; padding: 6px; border-radius: 4px; margin-top: 4px;">
                                        <?= htmlspecialchars($cand['statement_of_purpose']) ?>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <?php if (!empty($cand['custom_answers'])): 
                                $custom_ans = json_decode($cand['custom_answers'], true);
                                if (!empty($custom_ans) && is_array($custom_ans)):
                            ?>
                                <details style="margin-bottom: 8px; font-size: 0.76rem; color: var(--text-muted);">
                                    <summary style="cursor: pointer; font-weight: 600; color: var(--brand-purple);">📋 Custom Screening Responses</summary>
                                    <div style="background: #f8fafc; padding: 6px; border-radius: 4px; margin-top: 4px; display: flex; flex-direction: column; gap: 4px;">
                                        <?php foreach ($custom_ans as $q_lbl => $q_ans): ?>
                                            <div>
                                                <strong style="color: #0f172a;"><?= htmlspecialchars($q_lbl) ?>:</strong>
                                                <?php if (filter_var($q_ans, FILTER_VALIDATE_URL)): ?>
                                                    <a href="<?= htmlspecialchars($q_ans) ?>" target="_blank" style="color: var(--brand-blue); text-decoration: underline; word-break: break-all;"><?= htmlspecialchars($q_ans) ?></a>
                                                <?php else: ?>
                                                    <span style="color: var(--text-main); word-break: break-word;"><?= nl2br(htmlspecialchars($q_ans)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </details>
                            <?php endif; endif; ?>

                            <!-- Move Stage Form -->
                            <form action="index.php?controller=recruiter&action=update_status" method="POST" style="margin-top: 6px;">
                                <input type="hidden" name="app_id" value="<?= $cand['id'] ?>">
                                <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">
                                <select name="new_status" class="form-control" onchange="this.form.submit()" style="font-size: 0.78rem; padding: 4px 8px; height: 32px;">
                                    <option value="" disabled>Move Candidate Stage ▾</option>
                                    <?php foreach ($stages as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= $k === $stage_key ? 'selected' : '' ?>>
                                            → Move to <?= $k ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
