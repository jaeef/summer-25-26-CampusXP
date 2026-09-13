<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">AIUB Verified Student Talent Search</h1>
        <p class="portal-sub">Query student talent by academic criteria, CGPA threshold, and declared technical skill competencies.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <button class="btn btn-primary btn-sm" onclick="openModal('new-bucket-modal')">+ Create Shortlist Bucket</button>
        <a href="index.php?controller=recruiter&action=shortlists" class="btn btn-outline btn-sm">View Buckets</a>
    </div>
</div>

<!-- Search Filter Card -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <form method="GET" action="index.php">
        <input type="hidden" name="controller" value="recruiter">
        <input type="hidden" name="action" value="talent_search">

        <div class="grid-3" style="align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Department / Major</label>
                <select name="dept" class="form-control">
                    <option value="">All Departments</option>
                    <option value="CSE" <?= $filter_dept === 'CSE' ? 'selected' : '' ?>>Computer Science (CSE)</option>
                    <option value="EEE" <?= $filter_dept === 'EEE' ? 'selected' : '' ?>>Electrical Engineering (EEE)</option>
                    <option value="BBA" <?= $filter_dept === 'BBA' ? 'selected' : '' ?>>Business Administration (BBA)</option>
                    <option value="Arch" <?= $filter_dept === 'Arch' ? 'selected' : '' ?>>Architecture</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Minimum CGPA (0.00 - 4.00)</label>
                <input type="number" step="0.01" min="0.00" max="4.00" name="min_cgpa" class="form-control" value="<?= htmlspecialchars($filter_cgpa ?: '') ?>" placeholder="e.g. 3.50">
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Technical Skill Keyword</label>
                <input type="text" name="skill" class="form-control" value="<?= htmlspecialchars($filter_skill ?: '') ?>" placeholder="e.g. PHP, React, Python, SQL">
            </div>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 8px; justify-content: flex-end;">
            <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-outline btn-sm">Reset Filters</a>
            <button type="submit" class="btn btn-primary btn-sm">Apply Talent Filter</button>
        </div>
    </form>
</div>

<!-- Results Table -->
<div class="glass-card">
    <div class="card-header">
        <div class="card-title">Matching Candidate Pool (<?= count($candidates) ?> Found)</div>
    </div>

    <?php if (empty($candidates)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No student profiles match your filter criteria. Try broadening your CGPA or department filters.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>AIUB ID</th>
                    <th>Department</th>
                    <th>CGPA</th>
                    <th>Declared Skills</th>
                    <th>Resume Link</th>
                    <th>Outreach Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $cand): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($cand['full_name']) ?></td>
                        <td><?= htmlspecialchars($cand['aiub_id']) ?></td>
                        <td><span class="badge-tag badge-student"><?= htmlspecialchars($cand['department']) ?></span></td>
                        <td><strong style="color: var(--brand-blue);"><?= htmlspecialchars($cand['cgpa'] ? number_format((float)$cand['cgpa'], 2) : '0.00') ?></strong></td>
                        <td>
                            <?php 
                            $skills = !empty($cand['skills']) ? explode(',', $cand['skills']) : [];
                            foreach (array_slice($skills, 0, 3) as $sk): 
                                $sk = trim($sk);
                                if (empty($sk)) continue;
                            ?>
                                <span class="badge-tag" style="background: #eff6ff; color: var(--brand-blue); font-size: 0.72rem;"><?= htmlspecialchars($sk) ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php if (!empty($cand['cv_url'])): ?>
                                <a href="<?= htmlspecialchars($cand['cv_url']) ?>" target="_blank" style="font-size: 0.82rem; font-weight: 600;">View CV</a>
                            <?php else: ?>
                                <span style="color: var(--text-dim); font-size: 0.8rem;">Not available</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openOutreachModal(<?= $cand['id'] ?>, '<?= htmlspecialchars(addslashes($cand['full_name'])) ?>')">
                                ✉ Send Invite
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Send Candidate Invite Modal -->
<div class="modal-backdrop" id="outreach-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Send Opportunity Invitation</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('outreach-modal')">Close</button>
        </div>

        <form action="index.php?controller=recruiter&action=send_outreach" method="POST">
            <input type="hidden" name="student_id" id="modal-student-id">

            <div class="form-group">
                <label class="form-label">Recipient Candidate</label>
                <input type="text" id="modal-student-name" class="form-control" readonly style="background: #f8fafc; font-weight: 700;">
            </div>

            <div class="form-group">
                <label class="form-label">Select Target Shortlist Bucket</label>
                <select name="shortlist_id" class="form-control" required>
                    <?php if (empty($shortlists)): ?>
                        <option value="0" selected>General Direct Candidate Outreach Pool (Default)</option>
                    <?php else: ?>
                        <?php foreach ($shortlists as $sl): ?>
                            <option value="<?= $sl['id'] ?>"><?= htmlspecialchars($sl['bucket_name']) ?> (Min CGPA: <?= $sl['filter_cgpa_min'] ?>)</option>
                        <?php endforeach; ?>
                        <option value="0">+ General Direct Candidate Outreach Pool</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Invitation Message / Opportunity Summary</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Dear candidate, we were impressed by your profile and invite you to apply for our upcoming recruitment drive..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Dispatch Direct Invitation Alert
            </button>
        </form>
    </div>
</div>

<!-- Create New Bucket Modal -->
<div class="modal-backdrop" id="new-bucket-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Create Target Shortlist Bucket</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-bucket-modal')">Close</button>
        </div>

        <form action="index.php?controller=recruiter&action=create_bucket" method="POST">
            <div class="form-group">
                <label class="form-label">Bucket Name / Purpose</label>
                <input type="text" name="bucket_name" class="form-control" placeholder="e.g. Fall 2026 SQA Engineering Talent" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Target Department</label>
                    <select name="filter_dept" class="form-control">
                        <option value="CSE">Computer Science (CSE)</option>
                        <option value="EEE">Electrical Engineering (EEE)</option>
                        <option value="BBA">Business Administration (BBA)</option>
                        <option value="Arch">Architecture</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum CGPA Criterion</label>
                    <input type="number" step="0.01" min="0.00" max="4.00" name="min_cgpa" class="form-control" value="3.50" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Save Shortlist Bucket
            </button>
        </form>
    </div>
</div>

<script>
function openOutreachModal(studentId, studentName) {
    document.getElementById('modal-student-id').value = studentId;
    document.getElementById('modal-student-name').value = studentName;
    openModal('outreach-modal');
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
