<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Opportunities</h1>
        <p class="page-sub">Club drives, events and corporate jobs open to AIUB students. Apply with your saved CV and statement.</p>
    </div>
    <div class="text-sm text-muted nums">
        ID <strong class="fw-600" style="color: var(--text);"><?= htmlspecialchars($profile['aiub_id'] ?? 'Not set') ?></strong>
        &nbsp;·&nbsp; CGPA <strong class="fw-600" style="color: var(--text);"><?= htmlspecialchars($profile['cgpa'] ? number_format((float)$profile['cgpa'], 2) : '0.00') ?></strong>
    </div>
</div>

<div class="stats">
    <a class="stat" href="index.php?controller=student&action=applications">
        <span class="stat-value"><?= $total_apps ?></span>
        <span class="stat-label">Your applications</span>
    </a>
    <button type="button" class="stat" onclick="filterCategory('saved', document.getElementById('cat-pill-saved'))">
        <span class="stat-value" id="saved-count-stat"><?= $saved_count ?></span>
        <span class="stat-label">Saved</span>
    </button>
    <button type="button" class="stat" onclick="filterCategory('recruitment', document.getElementById('cat-pill-rec'))">
        <span class="stat-value"><?= count($club_drives) ?></span>
        <span class="stat-label">Club drives &amp; events</span>
    </button>
    <button type="button" class="stat" onclick="filterCategory('corporate', document.getElementById('cat-pill-corp'))">
        <span class="stat-value"><?= count($corp_drives) ?></span>
        <span class="stat-label">Corporate jobs</span>
    </button>
</div>

<?php if (!empty($alerts)): ?>
<div class="glass-card section">
    <div class="card-header">
        <h2 class="card-title">Recruiter invitations</h2>
        <span class="badge-tag tag-info"><?= count($alerts) ?> new</span>
    </div>
    <?php foreach ($alerts as $al): ?>
        <div class="list-item">
            <p class="text-sm fw-600"><?= htmlspecialchars($al['recruiter_name']) ?> <span class="text-dim" style="font-weight: 400;">· <?= htmlspecialchars($al['bucket_name']) ?></span></p>
            <p class="text-sm mt-1" style="max-width: 75ch;"><?= htmlspecialchars($al['message']) ?></p>
            <div class="text-xs text-dim mt-1 nums"><?= htmlspecialchars($al['sent_at']) ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<section id="radar-section" class="section">
    <div class="card-header">
        <h2 class="card-title">All opportunities</h2>
    </div>

    <div class="search-bar-container">
        <input type="search" class="search-input" id="radar-search" placeholder="Search by title, club, company or keyword" aria-label="Search opportunities" onkeyup="filterRadarSearch()">
        <div class="category-pills">
            <button type="button" class="cat-pill active" id="cat-pill-all" onclick="filterCategory('all', this)">All</button>
            <button type="button" class="cat-pill" id="cat-pill-rec" onclick="filterCategory('recruitment', this)">Recruitment &amp; jobs</button>
            <button type="button" class="cat-pill" id="cat-pill-intern" onclick="filterCategory('internship', this)">Internships</button>
            <button type="button" class="cat-pill" id="cat-pill-sem" onclick="filterCategory('seminar', this)">Seminars</button>
            <button type="button" class="cat-pill" id="cat-pill-ws" onclick="filterCategory('workshop', this)">Workshops</button>
            <button type="button" class="cat-pill" id="cat-pill-fest" onclick="filterCategory('fest', this)">Fests &amp; hackathons</button>
            <button type="button" class="cat-pill" id="cat-pill-corp" onclick="filterCategory('corporate', this)">Corporate</button>
            <button type="button" class="cat-pill" id="cat-pill-saved" onclick="filterCategory('saved', this)">Saved (<?= $saved_count ?>)</button>
        </div>
    </div>

    <div class="grid-3" id="radar-grid">

        <?php foreach ($club_drives as $drive):
            $club_cat = strtolower(trim($drive['category']));
            $is_saved = isset($bookmarked_map['club_drive_' . $drive['id']]);
            $already_applied_club = !empty($applied_club_drive_ids) && in_array((int)$drive['id'], array_map('intval', $applied_club_drive_ids), true);
        ?>
            <div class="radar-item" data-cat="<?= htmlspecialchars($club_cat) ?>" data-type="club" data-saved="<?= $is_saved ? '1' : '0' ?>">
                <div>
                    <div class="card-badge-row">
                        <span class="radar-category"><?= htmlspecialchars(ucfirst($drive['category'])) ?></span>
                        <?php if ($is_saved): ?>
                            <span class="bookmarked-tag">Saved</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="radar-title"><?= htmlspecialchars($drive['title']) ?></h3>
                    <div class="radar-org"><?= htmlspecialchars($drive['club_name']) ?></div>
                    <p class="radar-desc"><?= htmlspecialchars($drive['requirements']) ?></p>
                </div>

                <div>
                    <div class="meta-list nums">
                        <?= htmlspecialchars($drive['location'] ?: 'AIUB Campus') ?> · Closes <?= date('M d, Y', strtotime($drive['deadline'])) ?>
                    </div>
                    <div class="radar-action-row">
                        <?php if ($already_applied_club): ?>
                            <button type="button" class="btn btn-sm btn-applied" disabled title="You have already applied">Applied</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm"
                                    data-drive-id="<?= (int)$drive['id'] ?>"
                                    data-drive-title="<?= htmlspecialchars($drive['title'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-organizer="<?= htmlspecialchars($drive['club_name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-drive-type="club"
                                    data-fixed-role=""
                                    data-custom-fields="[]"
                                    onclick="handleQuickApplyClick(this)">
                                Apply
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn <?= $is_saved ? 'btn-sm btn-bookmark-active' : 'btn-outline btn-sm' ?>"
                                onclick="toggleBookmarkAjax(event, this, 'club_drive', <?= $drive['id'] ?>)">
                            <?= $is_saved ? 'Saved' : 'Save' ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php foreach ($corp_drives as $drive):
            $is_saved = isset($bookmarked_map['corporate_drive_' . $drive['id']]);
            $corp_cat = 'corporate';
            $jtype = strtolower($drive['job_type']);
            if (strpos($jtype, 'intern') !== false) $corp_cat = 'internship';
            elseif (strpos($jtype, 'seminar') !== false) $corp_cat = 'seminar';
            elseif (strpos($jtype, 'workshop') !== false) $corp_cat = 'workshop';
            elseif (strpos($jtype, 'fest') !== false || strpos($jtype, 'hack') !== false) $corp_cat = 'fest';
            elseif (strpos($jtype, 'full') !== false) $corp_cat = 'recruitment';
            $already_applied_corp = !empty($applied_corp_drive_ids) && in_array((int)$drive['id'], array_map('intval', $applied_corp_drive_ids), true);
        ?>
            <div class="radar-item" data-cat="<?= htmlspecialchars($corp_cat) ?>" data-type="corporate" data-saved="<?= $is_saved ? '1' : '0' ?>">
                <div>
                    <div class="card-badge-row">
                        <span class="radar-category"><?= htmlspecialchars($drive['job_type']) ?></span>
                        <span class="radar-category nums">Min CGPA <?= $drive['min_cgpa'] ?></span>
                        <?php if ($is_saved): ?>
                            <span class="bookmarked-tag">Saved</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="radar-title"><?= htmlspecialchars($drive['job_title']) ?></h3>
                    <div class="radar-org"><?= htmlspecialchars($drive['company_name']) ?></div>
                    <p class="radar-desc"><?= htmlspecialchars($drive['requirements']) ?></p>
                </div>

                <div>
                    <div class="meta-list nums">Closes <?= date('M d, Y', strtotime($drive['deadline'])) ?></div>
                    <div class="radar-action-row">
                        <?php if ($already_applied_corp): ?>
                            <button type="button" class="btn btn-sm btn-applied" disabled title="You have already applied">Applied</button>
                        <?php else:
                            $custom_fields_json = htmlspecialchars(json_encode($drive['custom_fields'] ?? []), ENT_QUOTES, 'UTF-8');
                        ?>
                            <button type="button" class="btn btn-sm"
                                    data-drive-id="<?= (int)$drive['id'] ?>"
                                    data-drive-title="<?= htmlspecialchars($drive['job_title'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-organizer="<?= htmlspecialchars($drive['company_name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-drive-type="corporate"
                                    data-min-cgpa="<?= (float)($drive['min_cgpa'] ?? 0.00) ?>"
                                    data-fixed-role="<?= htmlspecialchars($drive['job_title'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-custom-fields='<?= $custom_fields_json ?>'
                                    onclick="handleQuickApplyClick(this)">
                                Apply
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn <?= $is_saved ? 'btn-sm btn-bookmark-active' : 'btn-outline btn-sm' ?>"
                                onclick="toggleBookmarkAjax(event, this, 'corporate_drive', <?= $drive['id'] ?>)">
                            <?= $is_saved ? 'Saved' : 'Save' ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</section>

<!-- Profile data used to pre-fill the apply form -->
<input type="hidden" id="student-cgpa" value="<?= (float)($profile['cgpa'] ?? 0.00) ?>">
<input type="hidden" id="vault-cv-url" value="<?= htmlspecialchars($profile['cv_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" id="vault-default-sop" value="<?= htmlspecialchars($profile['default_sop'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<!-- CGPA below minimum -->
<div class="modal-backdrop" id="cgpa-warning-modal" role="dialog" aria-modal="true" aria-labelledby="cgpa-warn-title">
    <div class="modal-box modal-sm">
        <h3 id="cgpa-warn-title" class="mb-1" style="font-size: 1.125rem;">You can't apply to this job yet</h3>
        <p class="text-sm text-muted mb-2">
            The recruiter requires a minimum CGPA of <strong id="cgpa-warn-required" class="text-danger"></strong>.
            Your CGPA is <strong id="cgpa-warn-current"></strong>.
        </p>
        <button type="button" class="btn btn-outline btn-block" onclick="closeModal('cgpa-warning-modal')">Close</button>
    </div>
</div>

<!-- Apply -->
<div class="modal-backdrop" id="apply-modal" role="dialog" aria-modal="true" aria-labelledby="apply-modal-title">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title" id="apply-modal-title">Apply</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('apply-modal')">Close</button>
        </div>

        <p class="text-sm text-muted mb-2">
            We've filled in the CV link and statement from your profile. You can edit them before submitting.
        </p>

        <form action="index.php?controller=student&action=apply" method="POST" id="quick-apply-form">
            <input type="hidden" name="drive_id" id="apply-drive-id">
            <input type="hidden" name="drive_type" id="apply-drive-type" value="club">

            <div class="panel-inset mb-2">
                <strong id="apply-drive-title" style="display: block;"></strong>
                <div id="apply-club-name" class="text-sm text-muted"></div>
            </div>

            <div class="form-group" id="apply-role-group">
                <label class="form-label" id="apply-role-label" for="apply-role-input">Role you are applying for</label>
                <input type="text" name="applied_role" id="apply-role-input" class="form-control" value="Candidate Member" minlength="2" maxlength="100" pattern="^[a-zA-Z0-9\s\.\/\-\,\(\)]+$" title="Please enter a valid position or role name (2-100 chars)" required>
                <small id="apply-role-hint" class="form-hint" style="display: none;">Set by the recruiter.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="apply-cv-link">CV link</label>
                <input type="url" name="cv_link" id="apply-cv-link" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="apply-sop">Statement of purpose</label>
                <textarea name="statement_of_purpose" id="apply-sop" class="form-control" rows="4" required></textarea>
            </div>

            <div id="apply-custom-fields-container" style="display: none; margin-bottom: 1rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <div class="form-label mb-1">Screening questions</div>
                <div id="apply-custom-fields-list" class="stack"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit application</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
