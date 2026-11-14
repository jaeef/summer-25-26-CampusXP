<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Post a job</h1>
        <p class="page-sub">Job posts are reviewed by OSA before students see them. Add screening questions if you need more than a CV and statement.</p>
    </div>
    <a href="index.php?controller=recruiter&action=dashboard" class="btn btn-outline btn-sm">Back to job posts</a>
</div>

<div class="grid-2 section" style="align-items: start;">
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">Job details</h2>
        </div>

        <form action="index.php?controller=recruiter&action=save_drive" method="POST" id="drive-builder-form" onsubmit="return validateDriveBuilderForm()">
            <div class="form-group">
                <label class="form-label" for="company-input">Company</label>
                <input type="text" name="company_name" id="company-input" class="form-control" value="Brain Station 23" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="job-title-input">Job title</label>
                <input type="text" name="job_title" id="job-title-input" class="form-control" placeholder="e.g. Associate Software Engineer" pattern="^[a-zA-Z\s\-]+$" minlength="2" maxlength="100" title="Position title must contain only letters and spaces (a-z, A-Z)" required oninput="validateJobTitleInput(this)">
                <div class="field-error" id="job-title-error" style="display: none;"></div>
            </div>

            <div class="grid-2" style="gap: 0 1rem;">
                <div class="form-group">
                    <label class="form-label" for="job-type-input">Type</label>
                    <select name="job_type" id="job-type-input" class="form-control" required>
                        <option value="Internship" selected>Internship</option>
                        <option value="Full-Time">Full-time job</option>
                        <option value="Seminar">Seminar / talk</option>
                        <option value="Workshop">Workshop / bootcamp</option>
                        <option value="Fest / Hackathon">Hackathon / competition</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="min-cgpa-input">Minimum CGPA</label>
                    <input type="number" step="0.01" min="0.00" max="4.00" name="min_cgpa" id="min-cgpa-input" class="form-control" value="3.00" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="deadline-input">Application deadline</label>
                <input type="date" name="deadline" id="deadline-input" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="requirements-input">Requirements</label>
                <textarea name="requirements" id="requirements-input" class="form-control" rows="3" placeholder="Skills and stack, e.g. PHP, JavaScript, MySQL, OOP" required></textarea>
            </div>

            <div class="form-section">
                <div class="row-between mb-2">
                    <div>
                        <h3>Screening questions</h3>
                        <div class="text-xs text-muted">Students answer these when they apply.</div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="addCustomField()">Add question</button>
                </div>

                <div id="custom-fields-container" class="stack">
                    <div class="custom-field-row" id="custom-field-1">
                        <input type="text" name="custom_labels[]" class="form-control" value="GitHub / Portfolio Profile Link" placeholder="Question" aria-label="Question" required oninput="updateCustomFieldsPreview()">
                        <select name="custom_types[]" class="form-control" aria-label="Answer type" onchange="updateCustomFieldsPreview()">
                            <option value="url" selected>Link</option>
                            <option value="text">Short answer</option>
                            <option value="textarea">Paragraph</option>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="removeCustomField('custom-field-1')">Remove</button>
                    </div>

                    <div class="custom-field-row" id="custom-field-2">
                        <input type="text" name="custom_labels[]" class="form-control" value="Why are you interested in joining our engineering team?" placeholder="Question" aria-label="Question" required oninput="updateCustomFieldsPreview()">
                        <select name="custom_types[]" class="form-control" aria-label="Answer type" onchange="updateCustomFieldsPreview()">
                            <option value="textarea" selected>Paragraph</option>
                            <option value="text">Short answer</option>
                            <option value="url">Link</option>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="removeCustomField('custom-field-2')">Remove</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-2" style="margin-top: 1.5rem;">Submit for approval</button>
        </form>
    </div>

    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">What applicants fill in</h2>
        </div>

        <div class="form-label">From their profile</div>
        <ul class="text-sm text-muted mb-2" style="padding-left: 1.1rem;">
            <li>Student ID and CGPA</li>
            <li>CV link</li>
            <li>Statement of purpose</li>
        </ul>

        <div class="form-label">Your questions</div>
        <div id="preview-custom-list" class="text-sm">
            <div class="row-between list-item"><span>GitHub / Portfolio Profile Link</span><span class="text-xs text-dim">Link</span></div>
            <div class="row-between list-item"><span>Why are you interested in joining our engineering team?</span><span class="text-xs text-dim">Paragraph</span></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
