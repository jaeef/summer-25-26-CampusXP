<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Publish Job Opening & Custom Screening Form</h1>
        <p class="portal-sub">Attach custom assessment questions and screening prompts to your corporate job opening/internship circular.</p>
    </div>
    <a href="index.php?controller=recruiter&action=dashboard" class="btn btn-outline btn-sm">← Back to Job Openings</a>
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <!-- Main Form Settings -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">1. Job Opening Core Details</div>
        </div>

        <form action="index.php?controller=recruiter&action=save_drive" method="POST" id="drive-builder-form" onsubmit="return validateDriveBuilderForm()">
            <div class="form-group">
                <label class="form-label">Hiring Company Name</label>
                <input type="text" name="company_name" class="form-control" value="Brain Station 23" required>
            </div>

            <div class="form-group">
                <label class="form-label">Position Title</label>
                <input type="text" name="job_title" id="job-title-input" class="form-control" placeholder="e.g. Associate Software Engineer" pattern="^[a-zA-Z\s\-]+$" minlength="2" maxlength="100" title="Position title must contain only letters and spaces (a-z, A-Z)" required oninput="validateJobTitleInput(this)">
                <div id="job-title-error" style="color: #dc2626; font-size: 0.75rem; margin-top: 2px; display: none;"></div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Opportunity / Event Category</label>
                    <select name="job_type" class="form-control" required>
                        <option value="Internship" selected>Internship (Undergraduate / Graduate)</option>
                        <option value="Full-Time">Full-Time Career Position</option>
                        <option value="Seminar">Tech Seminar / Industry Keynote</option>
                        <option value="Workshop">Technical Workshop / Hands-on Bootcamp</option>
                        <option value="Fest / Hackathon">Hackathon / Campus Fest Competition</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum Eligible CGPA</label>
                    <input type="number" step="0.01" min="0.00" max="4.00" name="min_cgpa" class="form-control" value="3.00" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Application Deadline</label>
                <input type="date" name="deadline" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Job Requirements & Technical Stack</label>
                <textarea name="requirements" class="form-control" rows="3" placeholder="Specify tech stack requirements (e.g. PHP, JavaScript, MySQL, OOP)..." required></textarea>
            </div>

            <!-- 2. Dynamic Custom Form Fields Section -->
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div>
                        <strong style="font-size: 0.95rem; color: #0f172a;">2. Dynamic Form Questions</strong>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">Custom fields students must answer when applying.</div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="addCustomField()">+ Add Question</button>
                </div>

                <div id="custom-fields-container" style="display: flex; flex-direction: column; gap: 8px;">
                    <!-- Default Field 1: GitHub / Portfolio -->
                    <div class="custom-field-row" id="custom-field-1" style="display: flex; gap: 8px; align-items: center; background: #f8fafc; padding: 8px; border: 1px solid var(--border-light); border-radius: var(--radius-sm);">
                        <input type="text" name="custom_labels[]" class="form-control" value="GitHub / Portfolio Profile Link" placeholder="New Question / Field Prompt" required style="flex: 2;" oninput="updateCustomFieldsPreview()">
                        <select name="custom_types[]" class="form-control" style="flex: 1;" onchange="updateCustomFieldsPreview()">
                            <option value="url" selected>URL Link</option>
                            <option value="text">Short Text</option>
                            <option value="textarea">Long Paragraph</option>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="removeCustomField('custom-field-1')" style="color: #dc2626; padding: 4px 10px;">✕</button>
                    </div>

                    <!-- Default Field 2: Long Paragraph -->
                    <div class="custom-field-row" id="custom-field-2" style="display: flex; gap: 8px; align-items: center; background: #f8fafc; padding: 8px; border: 1px solid var(--border-light); border-radius: var(--radius-sm);">
                        <input type="text" name="custom_labels[]" class="form-control" value="Why are you interested in joining our engineering team?" placeholder="New Question / Field Prompt" required style="flex: 2;" oninput="updateCustomFieldsPreview()">
                        <select name="custom_types[]" class="form-control" style="flex: 1;" onchange="updateCustomFieldsPreview()">
                            <option value="textarea" selected>Long Paragraph</option>
                            <option value="text">Short Text</option>
                            <option value="url">URL Link</option>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="removeCustomField('custom-field-2')" style="color: #dc2626; padding: 4px 10px;">✕</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 1.5rem;">
                Publish Opportunity / Circular
            </button>
        </form>
    </div>

    <!-- Live Candidate Form Preview -->
    <div class="glass-card" style="background: #ffffff;">
        <div class="card-header">
            <div class="card-title">Live Candidate Application Modal Preview</div>
            <span class="badge-tag badge-recruiter">Applicant View</span>
        </div>

        <p style="font-size: 0.84rem; color: var(--text-muted); margin-bottom: 1rem;">
            This is what AIUB students will see and fill out when they click "1-Click Apply" on your recruitment card:
        </p>

        <div style="background: #f8fafc; border: 1px solid var(--border-light); border-radius: var(--radius-sm); padding: 1rem;">
            <div class="form-group">
                <label class="form-label" style="color: var(--text-dim);">Auto-Filled Student Vault Data</label>
                <div style="background: #ffffff; border: 1px solid var(--border-strong); border-radius: 4px; padding: 8px; font-size: 0.85rem;">
                    <div>✓ AIUB Student ID & Verified CGPA</div>
                    <div>✓ Resume / CV URL Link</div>
                    <div>✓ Auto-Filled Statement of Purpose (SOP)</div>
                </div>
            </div>

            <div style="border-top: 1px dashed var(--border-strong); margin: 1rem 0;"></div>

            <div class="form-group">
                <label class="form-label" style="color: var(--brand-purple); font-weight: 700;">Recruiter Custom Assessment Questions:</label>
                <div id="preview-custom-list" style="display: flex; flex-direction: column; gap: 8px; font-size: 0.85rem; color: var(--text-main);">
                    <div>• GitHub / Portfolio Profile Link [URL Link]</div>
                    <div>• Why are you interested in joining our engineering team? [Paragraph]</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
