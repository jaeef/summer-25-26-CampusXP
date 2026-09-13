<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Student Opportunity Radar</h1>
        <p class="portal-sub">Centralized opportunity discovery, 1-click application auto-filling, and live tracking.</p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <span class="badge-tag badge-student">ID: <?= htmlspecialchars($profile['aiub_id'] ?? 'Pending Setup') ?></span>
        <span class="badge-tag badge-admin">CGPA: <?= htmlspecialchars($profile['cgpa'] ? number_format((float)$profile['cgpa'], 2) : '0.00') ?></span>
    </div>
</div>

<!-- Quick Stats Bar -->
<div class="grid-4" style="margin-bottom: 1.5rem;">
    <div class="glass-card" style="text-align: center; cursor: pointer;" onclick="window.location.href='index.php?controller=student&action=applications'">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-blue);"><?= $total_apps ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Active Applications (Club & Corp)</div>
    </div>
    <div class="glass-card" style="text-align: center; cursor: pointer;" onclick="filterCategory('saved', document.getElementById('cat-pill-saved'))">
        <div id="saved-count-stat" style="font-size: 2rem; font-weight: 800; color: #d97706;"><?= $saved_count ?></div>
        <div style="font-size: 0.82rem; font-weight: 700; color: #b45309;">★ Saved Bookmarks (Click to Filter)</div>
    </div>
    <div class="glass-card" style="text-align: center; cursor: pointer;" onclick="filterCategory('recruitment', document.getElementById('cat-pill-rec'))">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-green);"><?= count($club_drives) ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Open Club Drives & Events</div>
    </div>
    <div class="glass-card" style="text-align: center; cursor: pointer;" onclick="filterCategory('corporate', document.getElementById('cat-pill-corp'))">
        <div style="font-size: 2rem; font-weight: 800; color: var(--brand-purple);"><?= count($corp_drives) ?></div>
        <div style="font-size: 0.82rem; color: var(--text-muted);">Corporate Drives</div>
    </div>
</div>

<!-- Corporate Direct Alerts (If Any) -->
<?php if (!empty($alerts)): ?>
<div class="glass-card" style="border-left: 4px solid var(--brand-purple); margin-bottom: 1.5rem; background: #ffffff;">
    <div class="card-header">
        <div class="card-title" style="color: var(--brand-purple);">Direct Recruiter Invitations</div>
        <span class="badge-tag badge-recruiter"><?= count($alerts) ?> New Inquiries</span>
    </div>
    <?php foreach ($alerts as $al): ?>
        <div style="padding: 10px; border-bottom: 1px solid var(--border-light);">
            <p style="font-size: 0.88rem; font-weight: 600; color: var(--brand-purple);">Invitation from <?= htmlspecialchars($al['recruiter_name']) ?> (Bucket: <?= htmlspecialchars($al['bucket_name']) ?>):</p>
            <p style="font-size: 0.85rem; color: var(--text-main); margin-top: 4px;">"<?= htmlspecialchars($al['message']) ?>"</p>
            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">Received on: <?= htmlspecialchars($al['sent_at']) ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Opportunity Radar Section -->
<div class="glass-card" id="radar-section" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">AIUB Live Opportunity Radar</div>
        <div style="display: flex; gap: 8px;">
            <a href="index.php?controller=student&action=profile" class="btn btn-outline btn-sm">View SOP Vault</a>
            <a href="index.php?controller=student&action=applications" class="btn btn-primary btn-sm">Track Applications</a>
        </div>
    </div>

    <!-- Search & Category Filters -->
    <div class="search-bar-container">
        <input type="text" class="search-input" id="radar-search" placeholder="Search opportunities by club, event, title, keywords..." onkeyup="filterRadarSearch()">
        <div class="category-pills">
            <button type="button" class="cat-pill active" id="cat-pill-all" onclick="filterCategory('all', this)">All Opportunities</button>
            <button type="button" class="cat-pill" id="cat-pill-rec" onclick="filterCategory('recruitment', this)">Recruitments & Jobs</button>
            <button type="button" class="cat-pill" id="cat-pill-intern" onclick="filterCategory('internship', this)">Internships</button>
            <button type="button" class="cat-pill" id="cat-pill-sem" onclick="filterCategory('seminar', this)">Seminars</button>
            <button type="button" class="cat-pill" id="cat-pill-ws" onclick="filterCategory('workshop', this)">Workshops</button>
            <button type="button" class="cat-pill" id="cat-pill-fest" onclick="filterCategory('fest', this)">Fests & Hackathons</button>
            <button type="button" class="cat-pill" id="cat-pill-corp" onclick="filterCategory('corporate', this)">Corporate Drives</button>
            <button type="button" class="cat-pill" id="cat-pill-saved" onclick="filterCategory('saved', this)" style="border-color: #f59e0b; color: #b45309; font-weight: 700;">★ Saved Bookmarks (<?= $saved_count ?>)</button>
        </div>
    </div>

    <!-- Opportunity Grid -->
    <div class="grid-3" id="radar-grid">
        
        <!-- Club Drives (Recruitment, Fest, Workshop, Seminar) -->
        <?php foreach ($club_drives as $drive): 
            $cat_class = 'badge-student';
            $club_cat = strtolower(trim($drive['category']));
            if ($club_cat === 'recruitment') $cat_class = 'badge-exec';
            elseif ($club_cat === 'fest') $cat_class = 'badge-gold';
            elseif ($club_cat === 'workshop') $cat_class = 'badge-purple';
            $is_saved = isset($bookmarked_map['club_drive_' . $drive['id']]);
        ?>
            <div class="radar-item" data-cat="<?= htmlspecialchars($club_cat) ?>" data-type="club" data-saved="<?= $is_saved ? '1' : '0' ?>">
                <div>
                    <div class="card-badge-row" style="display: flex; gap: 6px; align-items: center; margin-bottom: 6px;">
                        <span class="radar-category <?= $cat_class ?>"><?= htmlspecialchars(ucfirst($drive['category'])) ?></span>
                        <?php if ($is_saved): ?>
                            <span class="bookmarked-tag" style="font-size: 0.75rem; color: #b45309; font-weight: 700; background: #fef3c7; padding: 2px 6px; border-radius: 4px;">★ Bookmarked</span>
                        <?php endif; ?>
                    </div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 4px;"><?= htmlspecialchars($drive['title']) ?></h3>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--brand-blue); margin-bottom: 8px;">
                        <?= htmlspecialchars($drive['club_name']) ?>
                    </div>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5;">
                        <?= htmlspecialchars($drive['requirements']) ?>
                    </p>
                </div>

                <div>
                    <div style="font-size: 0.78rem; color: var(--text-dim); margin-bottom: 8px;">
                        <div>📍 <?= htmlspecialchars($drive['location'] ?: 'AIUB Campus') ?></div>
                        <div>⏳ Deadline: <?= date('M d, Y', strtotime($drive['deadline'])) ?></div>
                    </div>
                    <div class="radar-action-row" style="display: flex; gap: 8px;">
                        <?php 
                        $already_applied_club = !empty($applied_club_drive_ids) && in_array((int)$drive['id'], array_map('intval', $applied_club_drive_ids), true);
                        if ($already_applied_club): 
                        ?>
                            <button type="button" class="btn btn-sm btn-outline" disabled style="flex: 1; justify-content: center; background: #e0f2fe; color: #0369a1; border-color: #bae6fd; font-weight: 700; cursor: not-allowed;" title="You have already applied to this opportunity">
                                ✓ Applied
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm" style="flex: 1; justify-content: center;"
                                    data-drive-id="<?= (int)$drive['id'] ?>"
                                    data-drive-title="<?= htmlspecialchars($drive['title'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-organizer="<?= htmlspecialchars($drive['club_name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-drive-type="club"
                                    data-fixed-role=""
                                    data-custom-fields="[]"
                                    onclick="handleQuickApplyClick(this)">
                                1-Click Apply
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn <?= $is_saved ? 'btn-sm btn-bookmark-active' : 'btn-outline btn-sm' ?>"
                                onclick="toggleBookmarkAjax(event, this, 'club_drive', <?= $drive['id'] ?>)">
                            <?= $is_saved ? '★ Saved' : '☆ Save' ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Corporate Drives -->
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
            <div class="radar-item" data-cat="<?= htmlspecialchars($corp_cat) ?>" data-type="corporate" data-saved="<?= $is_saved ? '1' : '0' ?>" style="border-top: 3px solid var(--brand-purple);">
                <div>
                    <div class="card-badge-row" style="display: flex; gap: 6px; align-items: center; margin-bottom: 6px;">
                        <span class="radar-category badge-recruiter"><?= htmlspecialchars($drive['job_type']) ?></span>
                        <span class="badge-tag" style="background: #faf5ff; color: var(--brand-purple); border: 1px solid var(--brand-purple-border); font-size: 0.72rem;">Min CGPA: <?= $drive['min_cgpa'] ?></span>
                        <?php if ($is_saved): ?>
                            <span class="bookmarked-tag" style="font-size: 0.75rem; color: #b45309; font-weight: 700; background: #fef3c7; padding: 2px 6px; border-radius: 4px;">★ Bookmarked</span>
                        <?php endif; ?>
                    </div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 4px;"><?= htmlspecialchars($drive['job_title']) ?></h3>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--brand-purple); margin-bottom: 8px;">
                        🏢 <?= htmlspecialchars($drive['company_name']) ?>
                    </div>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 12px; line-height: 1.5;">
                        <?= htmlspecialchars($drive['requirements']) ?>
                    </p>
                </div>

                <div>
                    <div style="font-size: 0.78rem; color: var(--text-dim); margin-bottom: 8px;">
                        <div>⏳ Deadline: <?= date('M d, Y', strtotime($drive['deadline'])) ?></div>
                    </div>
                    <div class="radar-action-row" style="display: flex; gap: 8px;">
                        <?php if ($already_applied_corp): ?>
                            <button type="button" class="btn btn-sm btn-outline" disabled style="flex: 1; justify-content: center; background: #f3e8ff; color: #6b21a8; border-color: #e9d5ff; font-weight: 700; cursor: not-allowed;" title="You have already applied to this circular">
                                ✓ Applied
                            </button>
                        <?php else: 
                            $custom_fields_json = htmlspecialchars(json_encode($drive['custom_fields'] ?? []), ENT_QUOTES, 'UTF-8');
                        ?>
                            <button type="button" class="btn btn-sm" style="flex: 1; justify-content: center; background: var(--brand-purple);"
                                    data-drive-id="<?= (int)$drive['id'] ?>"
                                    data-drive-title="<?= htmlspecialchars($drive['job_title'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-organizer="<?= htmlspecialchars($drive['company_name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-drive-type="corporate"
                                    data-min-cgpa="<?= (float)($drive['min_cgpa'] ?? 0.00) ?>"
                                    data-fixed-role="<?= htmlspecialchars($drive['job_title'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-custom-fields='<?= $custom_fields_json ?>'
                                    onclick="handleQuickApplyClick(this)">
                                1-Click Apply
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn <?= $is_saved ? 'btn-sm btn-bookmark-active' : 'btn-outline btn-sm' ?>"
                                onclick="toggleBookmarkAjax(event, this, 'corporate_drive', <?= $drive['id'] ?>)">
                            <?= $is_saved ? '★ Saved' : '☆ Save' ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<!-- Hidden Vault Credentials Reference for Modal -->
<input type="hidden" id="student-cgpa" value="<?= (float)($profile['cgpa'] ?? 0.00) ?>">
<input type="hidden" id="vault-cv-url" value="<?= htmlspecialchars($profile['cv_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
<input type="hidden" id="vault-default-sop" value="<?= htmlspecialchars($profile['default_sop'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<!-- CGPA Ineligibility Pop-up Modal -->
<div class="modal-backdrop" id="cgpa-warning-modal">
    <div class="modal-box" style="max-width: 440px; text-align: center;">
        <div style="font-size: 2.8rem; margin-bottom: 6px;">⚠️</div>
        <h3 style="font-size: 1.2rem; font-weight: 800; color: #dc2626; margin-bottom: 8px;">Minimum CGPA Requirement Notice</h3>
        <p style="font-size: 0.88rem; color: var(--text-main); margin-bottom: 12px; line-height: 1.5;">
            This position requires a minimum CGPA of <strong id="cgpa-warn-required" style="color: #dc2626;"></strong>.
        </p>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-bottom: 1.25rem; font-size: 0.85rem; color: #991b1b; text-align: left;">
            • <strong>Your Current CGPA:</strong> <span id="cgpa-warn-current"></span><br>
            • <strong>Status:</strong> Ineligible to apply.<br>
            <span style="font-size: 0.8rem; color: #b91c1c; display: block; margin-top: 4px;">You cannot apply for this circular because your CGPA does not meet the recruiter's minimum threshold.</span>
        </div>
        <button type="button" class="btn btn-outline" style="width: 100%; justify-content: center;" onclick="closeModal('cgpa-warning-modal')">
            Understand & Close
        </button>
    </div>
</div>

<!-- 1-Click Vault Apply Modal -->
<div class="modal-backdrop" id="apply-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Apply with Unified Profile Vault</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('apply-modal')">Close</button>
        </div>

        <p style="font-size: 0.84rem; color: var(--text-muted); margin-bottom: 1rem;">
            Your saved CV link and Statement of Purpose have been auto-populated from your Profile Vault. You may edit them before submitting.
        </p>

        <form action="index.php?controller=student&action=apply" method="POST" id="quick-apply-form">
            <input type="hidden" name="drive_id" id="apply-drive-id">
            <input type="hidden" name="drive_type" id="apply-drive-type" value="club">

            <div style="background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 10px; margin-bottom: 1rem;">
                <div style="font-size: 0.78rem; color: var(--text-muted);">Target Opportunity & Organizer:</div>
                <strong id="apply-drive-title" style="font-size: 0.95rem; color: var(--text-main);"></strong>
                <div id="apply-club-name" style="font-size: 0.82rem; color: var(--brand-blue); font-weight: 600;"></div>
            </div>

            <div class="form-group" id="apply-role-group">
                <label class="form-label" id="apply-role-label">Position / Role Applied For</label>
                <input type="text" name="applied_role" id="apply-role-input" class="form-control" value="Candidate Member" minlength="2" maxlength="100" pattern="^[a-zA-Z0-9\s\.\/\-\,\(\)]+$" title="Please enter a valid position or role name (2-100 chars)" required>
                <small id="apply-role-hint" style="display: none; color: var(--brand-purple); font-size: 0.75rem; font-weight: 600; margin-top: 3px;">🔒 Position fixed by hiring recruiter and cannot be changed.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Auto-Filled Resume / CV Link</label>
                <input type="url" name="cv_link" id="apply-cv-link" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Auto-Filled Statement of Purpose (SOP)</label>
                <textarea name="statement_of_purpose" id="apply-sop" class="form-control" rows="4" required></textarea>
            </div>

            <!-- Dynamic Custom Screening Questions Container -->
            <div id="apply-custom-fields-container" style="display: none; margin-bottom: 1rem; border-top: 1px dashed var(--border-strong); padding-top: 12px;">
                <div style="font-size: 0.85rem; color: var(--brand-purple); font-weight: 700; margin-bottom: 8px;">
                    📋 Recruiter Custom Screening Questions:
                </div>
                <div id="apply-custom-fields-list" style="display: flex; flex-direction: column; gap: 10px;"></div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Confirm & Submit Application
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
