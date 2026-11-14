<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Applicants</h1>
        <p class="page-sub">Drag a card to another column, or use the menu on the card, to change an applicant's stage.</p>
    </div>
    <form method="GET" action="index.php" class="row">
        <input type="hidden" name="controller" value="executive">
        <input type="hidden" name="action" value="kanban">
        <label for="drive-filter" class="text-sm text-muted">Drive</label>
        <select name="drive_id" id="drive-filter" class="form-control toolbar-select" onchange="this.form.submit()">
            <option value="0" <?= $selected_drive_id === 0 ? 'selected' : '' ?>>All drives</option>
            <?php foreach ($all_drives as $dr): ?>
                <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dr['title']) ?> (<?= htmlspecialchars($dr['club_name']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="kanban-board">
    <?php
    $stages = [
        'Applied' => ['label' => 'Applied', 'dot' => ''],
        'Under Review' => ['label' => 'Under review', 'dot' => 'stage-review'],
        'Interview' => ['label' => 'Interview', 'dot' => 'stage-interview'],
        'Selected' => ['label' => 'Selected', 'dot' => 'stage-selected'],
        'Rejected' => ['label' => 'Rejected', 'dot' => 'stage-rejected']
    ];
    ?>

    <?php foreach ($stages as $stage_key => $st_info): 
        $candidates = $pipeline[$stage_key] ?? [];
    ?>
        <div class="kanban-column">
            <div class="kanban-col-header">
                <span><span class="stage-dot <?= $st_info['dot'] ?>"></span><?= $st_info['label'] ?></span>
                <span class="count-badge"><?= count($candidates) ?></span>
            </div>

            <div class="kanban-card-list" id="col-<?= strtolower(str_replace(' ', '-', $stage_key)) ?>" 
                 data-stage="<?= $stage_key ?>"
                 ondragover="kanbanAllowDrop(event)"
                 ondragleave="kanbanDragLeave(event)"
                 ondrop="kanbanDropCard(event, '<?= $stage_key ?>')">
                <?php if (empty($candidates)): ?>
                    <div class="empty-col-notice">No applicants</div>
                <?php else: ?>
                    <?php foreach ($candidates as $cand): ?>
                        <div class="kanban-card" id="exec-cand-card-<?= $cand['id'] ?>" 
                             draggable="true" 
                             ondragstart="kanbanDragStart(event, '<?= $cand['id'] ?>')">
                            <div class="kc-drive">
                                <?= htmlspecialchars($cand['drive_title'] ?? 'Club Recruitment Drive') ?>
                            </div>
                            <div class="applicant-name"><?= htmlspecialchars($cand['full_name']) ?></div>
                            <div class="kc-meta nums"><?= htmlspecialchars($cand['aiub_id']) ?> · <?= htmlspecialchars($cand['department']) ?> · CGPA <?= htmlspecialchars($cand['cgpa'] ? number_format((float)$cand['cgpa'], 2) : '0.00') ?></div>
                            <div class="kc-meta"><?= htmlspecialchars($cand['applied_role']) ?></div>
                            <?php if (!empty($cand['cv_link'])): ?>
                                <a href="<?= htmlspecialchars($cand['cv_link']) ?>" target="_blank" rel="noopener" class="text-xs fw-600" style="display: inline-block; margin-top: 4px;">View CV</a>
                            <?php endif; ?>

                            <form action="index.php?controller=executive&action=update_status" method="POST">
                                <input type="hidden" name="app_id" value="<?= $cand['id'] ?>">
                                <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">
                                <select name="new_status" class="form-control kc-select" aria-label="Move to stage" onchange="this.form.submit()">
                                    <option value="" disabled>Move to…</option>
                                    <?php foreach ($stages as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= $k === $stage_key ? 'selected' : '' ?>>
                                            <?= $k === $stage_key ? 'In ' : 'Move to ' ?><?= htmlspecialchars($v['label']) ?>
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
