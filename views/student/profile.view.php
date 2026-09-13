<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Unified Profile & SOP Vault</h1>
        <p class="portal-sub">Save your academic profile, verified CGPA, skills, resume link, and Statement of Purpose once. Auto-fills into every AIUB club and corporate drive.</p>
    </div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <span class="badge-tag badge-student" id="header-aiub-id">ID: <?= htmlspecialchars($profile['aiub_id'] ?? 'Pending Setup') ?></span>
        <span class="badge-tag badge-admin" id="header-cgpa">CGPA: <?= htmlspecialchars($profile['cgpa'] ? number_format((float)$profile['cgpa'], 2) : '0.00') ?></span>
    </div>
</div>

<div class="grid-2">
    <!-- Edit Form Card -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">Edit Profile & SOP Vault</div>
            <span style="font-size: 0.8rem; color: var(--brand-blue); font-weight: 600;">⚡ Real-Time Live Preview</span>
        </div>

        <form action="index.php?controller=student&action=update_profile" method="POST" id="profile-edit-form">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>AIUB Student ID</span>
                        <span style="font-size: 0.72rem; color: var(--text-dim); font-weight: normal;">🔒 Unique & Permanent</span>
                    </label>
                    <input type="text" name="aiub_id" id="input-aiub-id" class="form-control" value="<?= htmlspecialchars($profile['aiub_id'] ?? '') ?>" readonly style="background: #f1f5f9; color: #334155; cursor: not-allowed; font-weight: 600;" title="AIUB Student ID is unique and cannot be modified">
                </div>
                <div class="form-group">
                    <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Department / Major</span>
                        <span style="font-size: 0.72rem; color: var(--text-dim); font-weight: normal;">🔒 Unique & Permanent</span>
                    </label>
                    <input type="text" name="department" id="input-dept" class="form-control" value="<?= htmlspecialchars($profile['department'] ?? 'CSE') ?>" readonly style="background: #f1f5f9; color: #334155; cursor: not-allowed; font-weight: 600;" title="Department is permanent and cannot be modified">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Cumulative GPA (CGPA)</label>
                    <input type="number" step="0.01" min="0" max="4.00" name="cgpa" id="input-cgpa" class="form-control" value="<?= htmlspecialchars($profile['cgpa'] ?? '') ?>" placeholder="e.g. 3.75" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Key Technical Skills (Comma separated)</label>
                    <input type="text" name="skills" id="input-skills" class="form-control" placeholder="e.g. PHP, JavaScript, SQL, Python, UI/UX" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Cloud Resume / CV URL (Google Drive, GitHub, Portfolio)</label>
                <input type="url" name="cv_url" id="input-cv-url" class="form-control" value="<?= htmlspecialchars($profile['cv_url'] ?? '') ?>" placeholder="Paste your public Google Drive or GitHub resume link here...">
            </div>

            <div class="form-group">
                <label class="form-label">Default Statement of Purpose (SOP)</label>
                <textarea name="default_sop" id="input-sop" class="form-control" rows="4" placeholder="Describe your experience, technical passion, and what makes you an ideal candidate..."><?= htmlspecialchars($profile['default_sop'] ?? 'I am a motivated AIUB undergraduate looking to apply my academic foundation, build practical projects, and collaborate with inspiring teams.') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Save & Update Unified Vault
            </button>
        </form>
    </div>

    <!-- Live Preview Card -->
    <div class="glass-card" style="background: #ffffff;">
        <div class="card-header">
            <div class="card-title">Public Auto-Fill Card Preview</div>
            <span class="badge-tag badge-student">Live Student Vault</span>
        </div>

        <div style="text-align: center; margin-bottom: 1.25rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #eff6ff; color: var(--brand-blue); font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; border: 2px solid #bfdbfe;">
                <?= strtoupper(substr($auth_user['full_name'], 0, 1)) ?>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($auth_user['full_name']) ?></h3>
            <p style="font-size: 0.82rem; color: var(--text-muted);"><?= htmlspecialchars($auth_user['email']) ?></p>
        </div>

        <div style="background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85rem;">
                <div>
                    <span style="color: var(--text-dim);">AIUB ID:</span>
                    <strong id="preview-aiub-id"><?= htmlspecialchars($profile['aiub_id'] ?? 'Not Set') ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-dim);">Department:</span>
                    <strong id="preview-dept"><?= htmlspecialchars($profile['department'] ?? 'CSE') ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-dim);">CGPA:</span>
                    <strong style="color: var(--brand-blue);"><span id="preview-cgpa"><?= htmlspecialchars($profile['cgpa'] ? number_format((float)$profile['cgpa'], 2) : '0.00') ?></span> / 4.00</strong>
                </div>
                <div>
                    <span style="color: var(--text-dim);">CV Vault:</span>
                    <span id="preview-cv-container">
                        <?php if (!empty($profile['cv_url'])): ?>
                            <a href="<?= htmlspecialchars($profile['cv_url']) ?>" id="preview-cv-link" target="_blank" style="font-size: 0.85rem; font-weight: 600;">View Document</a>
                        <?php else: ?>
                            <span style="color: var(--text-dim); font-size: 0.85rem;">No URL Provided</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Skills Cloud -->
        <div style="margin-bottom: 1rem;">
            <label class="form-label" style="margin-bottom: 6px;">Technical Competencies:</label>
            <div id="preview-skills-container" style="display: flex; flex-wrap: wrap; gap: 6px;">
                <?php 
                $skills_arr = !empty($profile['skills']) ? explode(',', $profile['skills']) : [];
                if (!empty($skills_arr)):
                    foreach ($skills_arr as $sk): 
                        $sk = trim($sk);
                        if (empty($sk)) continue;
                ?>
                        <span class="badge-tag" style="background: #eff6ff; color: var(--brand-blue); border: 1px solid #bfdbfe; font-size: 0.75rem;"><?= htmlspecialchars($sk) ?></span>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <span style="color: var(--text-dim); font-size: 0.8rem; font-style: italic;">No skills specified yet</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statement of Purpose Preview -->
        <div>
            <label class="form-label" style="margin-bottom: 6px;">Default Application SOP:</label>
            <div id="preview-sop" style="font-size: 0.82rem; color: var(--text-muted); background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 10px; line-height: 1.5; font-style: italic;">
                "<?= nl2br(htmlspecialchars($profile['default_sop'] ?? 'No default Statement of Purpose saved yet.')) ?>"
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
