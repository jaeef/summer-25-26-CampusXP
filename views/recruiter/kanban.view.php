<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Applicants</h1>
        <p class="page-sub">Everyone who applied to your job posts, by hiring stage.</p>
    </div>
    <div class="row">
        <form method="GET" action="index.php" class="row">
            <input type="hidden" name="controller" value="recruiter">
            <input type="hidden" name="action" value="kanban">
            <label for="drive-filter" class="text-sm text-muted">Job</label>
            <select name="drive_id" id="drive-filter" class="form-control toolbar-select" onchange="this.form.submit()">
                <option value="0" <?= $selected_drive_id === 0 ? 'selected' : '' ?>>All job posts</option>
                <?php foreach ($drives as $dr): ?>
                    <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dr['job_title']) ?> (<?= htmlspecialchars($dr['company_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <a href="index.php?controller=recruiter&action=create_drive" class="btn btn-outline btn-sm">Post a job</a>
    </div>
</div>

<div class="kanban-board">
    <?php
    $stages = [
        'Applied' => ['label' => 'Received', 'dot' => ''],
        'Under Review' => ['label' => 'Screening', 'dot' => 'stage-review'],
        'Interview' => ['label' => 'Interview', 'dot' => 'stage-interview'],
        'Selected' => ['label' => 'Offer', 'dot' => 'stage-selected'],
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

            <div class="kanban-card-list" id="col-corp-<?= strtolower(str_replace(' ', '-', $stage_key)) ?>">
                <?php if (empty($candidates)): ?>
                    <div class="empty-col-notice">No applicants</div>
                <?php else: ?>
                    <?php foreach ($candidates as $cand): ?>
                        <div class="kanban-card">
                            <div class="kc-drive">
                                <?= htmlspecialchars($cand['drive_title'] ?? 'Corporate Drive') ?>
                            </div>
                            <div class="applicant-name"><?= htmlspecialchars($cand['full_name']) ?></div>
                            <div class="kc-meta nums"><?= htmlspecialchars($cand['aiub_id']) ?> · <?= htmlspecialchars($cand['department']) ?> · CGPA <?= htmlspecialchars($cand['cgpa'] ? number_format((float)$cand['cgpa'], 2) : '0.00') ?></div>
                            <div class="kc-meta"><?= htmlspecialchars($cand['applied_role']) ?></div>
                            <?php if (!empty($cand['cv_link'])): ?>
                                <a href="<?= htmlspecialchars($cand['cv_link']) ?>" target="_blank" rel="noopener" class="text-xs fw-600" style="display: inline-block; margin-top: 4px;">View CV</a>
                            <?php endif; ?>
                            <?php if (!empty($cand['statement_of_purpose'])): ?>
                                <details class="kc-more">
                                    <summary>Statement of purpose</summary>
                                    <div>
                                        <?= htmlspecialchars($cand['statement_of_purpose']) ?>
                                    </div>
                                </details>
                            <?php endif; ?>

                            <?php if (!empty($cand['custom_answers'])): 
                                $custom_ans = json_decode($cand['custom_answers'], true);
                                if (!empty($custom_ans) && is_array($custom_ans)):
                            ?>
                                <details class="kc-more">
                                    <summary>Screening answers</summary>
                                    <div>
                                        <?php foreach ($custom_ans as $q_lbl => $q_ans): ?>
                                            <div>
                                                <strong style="color: var(--text);"><?= htmlspecialchars($q_lbl) ?>:</strong>
                                                <?php if (filter_var($q_ans, FILTER_VALIDATE_URL)): ?>
                                                    <a href="<?= htmlspecialchars($q_ans) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($q_ans) ?></a>
                                                <?php else: ?>
                                                    <span style="color: var(--text);"><?= nl2br(htmlspecialchars($q_ans)) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </details>
                            <?php endif; endif; ?>

                            <form action="index.php?controller=recruiter&action=update_status" method="POST">
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
