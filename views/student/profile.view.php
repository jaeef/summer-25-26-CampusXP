<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Profile</h1>
        <p class="page-sub">Fill this in once. Your CV link and statement are pre-filled every time you apply.</p>
    </div>
</div>

<div class="grid-2" style="align-items: start;">
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">Your details</h2>
        </div>

        <form action="index.php?controller=student&action=update_profile" method="POST" id="profile-edit-form">
            <div class="grid-2" style="gap: 0 1rem;">
                <div class="form-group">
                    <label class="form-label" for="input-aiub-id">AIUB student ID</label>
                    <input type="text" name="aiub_id" id="input-aiub-id" class="form-control" value="<?= htmlspecialchars($profile['aiub_id'] ?? '') ?>" readonly title="Your student ID can't be changed">
                </div>
                <div class="form-group">
                    <label class="form-label" for="input-dept">Department</label>
                    <input type="text" name="department" id="input-dept" class="form-control" value="<?= htmlspecialchars($profile['department'] ?? 'CSE') ?>" readonly title="Your department can't be changed">
                </div>
                <div class="form-group">
                    <label class="form-label" for="input-cgpa">CGPA</label>
                    <input type="number" step="0.01" min="0" max="4.00" name="cgpa" id="input-cgpa" class="form-control" value="<?= htmlspecialchars($profile['cgpa'] ?? '') ?>" placeholder="e.g. 3.75" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="input-skills">Skills</label>
                    <input type="text" name="skills" id="input-skills" class="form-control" placeholder="PHP, SQL, Python" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>">
                </div>
            </div>
            <p class="form-hint" style="margin: -0.5rem 0 1rem;">Student ID and department are set at registration. Separate skills with commas.</p>

            <div class="form-group">
                <label class="form-label" for="input-cv-url">CV link</label>
                <input type="url" name="cv_url" id="input-cv-url" class="form-control" value="<?= htmlspecialchars($profile['cv_url'] ?? '') ?>" placeholder="https://drive.google.com/…">
                <span class="form-hint">A public Google Drive, GitHub or portfolio link.</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="input-sop">Statement of purpose</label>
                <textarea name="default_sop" id="input-sop" class="form-control" rows="5" placeholder="Your experience, interests and what you'd bring to the team"><?= htmlspecialchars($profile['default_sop'] ?? 'I am a motivated AIUB undergraduate looking to apply my academic foundation, build practical projects, and collaborate with inspiring teams.') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save profile</button>
        </form>
    </div>

    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">What recruiters see</h2>
            <span class="text-xs text-dim">Updates as you type</span>
        </div>

        <div class="row mb-2" style="gap: 12px;">
            <div class="user-role-avatar" style="width: 44px; height: 44px; font-size: 1.125rem;">
                <?= strtoupper(substr($auth_user['full_name'], 0, 1)) ?>
            </div>
            <div>
                <h3><?= htmlspecialchars($auth_user['full_name']) ?></h3>
                <p class="text-sm text-muted"><?= htmlspecialchars($auth_user['email']) ?></p>
            </div>
        </div>

        <dl class="panel-inset mb-2 text-sm nums" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div>
                <dt class="text-dim text-xs">Student ID</dt>
                <dd class="fw-600" id="preview-aiub-id"><?= htmlspecialchars($profile['aiub_id'] ?? 'Not set') ?></dd>
            </div>
            <div>
                <dt class="text-dim text-xs">Department</dt>
                <dd class="fw-600" id="preview-dept"><?= htmlspecialchars($profile['department'] ?? 'CSE') ?></dd>
            </div>
            <div>
                <dt class="text-dim text-xs">CGPA</dt>
                <dd class="fw-600"><span id="preview-cgpa"><?= htmlspecialchars($profile['cgpa'] ? number_format((float)$profile['cgpa'], 2) : '0.00') ?></span> / 4.00</dd>
            </div>
            <div>
                <dt class="text-dim text-xs">CV</dt>
                <dd id="preview-cv-container">
                    <?php if (!empty($profile['cv_url'])): ?>
                        <a href="<?= htmlspecialchars($profile['cv_url']) ?>" id="preview-cv-link" target="_blank" rel="noopener" class="fw-600">View CV</a>
                    <?php else: ?>
                        <span class="text-dim">No CV link yet</span>
                    <?php endif; ?>
                </dd>
            </div>
        </dl>

        <div class="mb-2">
            <div class="form-label">Skills</div>
            <div id="preview-skills-container" class="row" style="gap: 6px;">
                <?php
                $skills_arr = !empty($profile['skills']) ? explode(',', $profile['skills']) : [];
                if (!empty($skills_arr)):
                    foreach ($skills_arr as $sk):
                        $sk = trim($sk);
                        if (empty($sk)) continue;
                ?>
                        <span class="badge-tag"><?= htmlspecialchars($sk) ?></span>
                <?php
                    endforeach;
                else:
                ?>
                    <span class="text-dim text-sm">No skills added yet</span>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div class="form-label">Statement of purpose</div>
            <div id="preview-sop" class="text-sm text-muted" style="line-height: 1.6;">
                "<?= nl2br(htmlspecialchars($profile['default_sop'] ?? 'No statement saved yet.')) ?>"
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
