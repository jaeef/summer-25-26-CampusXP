<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Applicant Kanban Pipeline</h1>
        <p class="portal-sub">Drag and drop candidates across stages or use fast stage selectors to progress applicants in real-time.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <form method="GET" action="index.php" style="display: flex; gap: 8px; margin: 0;">
            <input type="hidden" name="controller" value="executive">
            <input type="hidden" name="action" value="kanban">
            <select name="drive_id" class="form-control" onchange="this.form.submit()" style="width: auto; min-width: 280px; font-weight: 600;">
                <option value="0" <?= $selected_drive_id === 0 ? 'selected' : '' ?>>★ All Club Drives & Campaigns (Combined View)</option>
                <?php foreach ($all_drives as $dr): ?>
                    <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dr['title']) ?> (<?= htmlspecialchars($dr['club_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<!-- Kanban Columns -->
<div class="kanban-board">
    <?php 
    $stages = [
        'Applied' => ['label' => '1. Applied', 'color' => '#1d4ed8', 'badge' => 'badge-student'],
        'Under Review' => ['label' => '2. Under Review', 'color' => '#b45309', 'badge' => 'badge-gold'],
        'Interview' => ['label' => '3. Interview', 'color' => '#7e22ce', 'badge' => 'badge-admin'],
        'Selected' => ['label' => '4. Selected', 'color' => '#15803d', 'badge' => 'badge-exec'],
        'Rejected' => ['label' => '5. Rejected', 'color' => '#dc2626', 'badge' => 'badge-danger']
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

            <div class="kanban-card-list" id="col-<?= strtolower(str_replace(' ', '-', $stage_key)) ?>" 
                 data-stage="<?= $stage_key ?>"
                 ondragover="kanbanAllowDrop(event)"
                 ondragleave="kanbanDragLeave(event)"
                 ondrop="kanbanDropCard(event, '<?= $stage_key ?>')"
                 style="min-height: 420px; display: flex; flex-direction: column; gap: 8px; padding: 6px; border-radius: 6px; transition: background 0.2s ease;">
                <?php if (empty($candidates)): ?>
                    <div class="empty-col-notice" style="text-align: center; color: var(--text-dim); font-size: 0.8rem; padding: 20px 0; border: 1px dashed var(--border-light); border-radius: 6px;">
                        Drop candidates here
                    </div>
                <?php else: ?>
                    <?php foreach ($candidates as $cand): ?>
                        <div class="kanban-card" id="exec-cand-card-<?= $cand['id'] ?>" 
                             draggable="true" 
                             ondragstart="kanbanDragStart(event, '<?= $cand['id'] ?>')"
                             style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 12px; box-shadow: var(--shadow-sm); cursor: grab;">
                            <div style="font-size: 0.72rem; color: var(--brand-blue); font-weight: 700; margin-bottom: 3px;">
                                <?= htmlspecialchars($cand['drive_title'] ?? 'Club Recruitment Drive') ?>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                <strong style="font-size: 0.92rem; color: var(--text-main);"><?= htmlspecialchars($cand['full_name']) ?></strong>
                                <span class="badge-tag <?= $st_info['badge'] ?>" style="font-size: 0.7rem;"><?= htmlspecialchars($cand['department']) ?></span>
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-dim); margin-bottom: 4px;">
                                ID: <strong><?= htmlspecialchars($cand['aiub_id']) ?></strong> • CGPA: <strong style="color: var(--brand-blue);"><?= htmlspecialchars($cand['cgpa'] ? number_format((float)$cand['cgpa'], 2) : '0.00') ?></strong>
                            </div>
                            <div style="font-size: 0.8rem; color: #0f172a; font-weight: 600; margin-bottom: 6px;">
                                Applied Role: <span style="color: var(--brand-blue);"><?= htmlspecialchars($cand['applied_role']) ?></span>
                            </div>
                            <?php if (!empty($cand['cv_link'])): ?>
                                <div style="margin-bottom: 8px;">
                                    <a href="<?= htmlspecialchars($cand['cv_link']) ?>" target="_blank" style="font-size: 0.78rem; font-weight: 600;">View Candidate CV</a>
                                </div>
                            <?php endif; ?>

                            <!-- Fast Move Stage Dropdown -->
                            <form action="index.php?controller=executive&action=update_status" method="POST" style="margin-top: 6px;">
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

<!-- Hidden Form for Kanban Drag-and-Drop Form Submission -->
<form id="exec-kanban-status-form" action="index.php?controller=executive&action=update_status" method="POST" style="display:none;">
    <input type="hidden" name="app_id" id="exec-drag-app-id">
    <input type="hidden" name="new_status" id="exec-drag-new-status">
    <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
