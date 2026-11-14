<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Talent search</h1>
        <p class="page-sub">Find AIUB students by department, CGPA and skills, then invite them to apply.</p>
    </div>
    <button class="btn btn-outline btn-sm" onclick="openModal('new-bucket-modal')">New shortlist</button>
</div>

<div class="glass-card section">
    <form method="GET" action="index.php">
        <input type="hidden" name="controller" value="recruiter">
        <input type="hidden" name="action" value="talent_search">

        <div class="grid-3" style="align-items: end;">
            <div class="form-group mb-0">
                <label class="form-label" for="f-dept">Department</label>
                <select name="dept" id="f-dept" class="form-control">
                    <option value="">All departments</option>
                    <option value="CSE" <?= $filter_dept === 'CSE' ? 'selected' : '' ?>>Computer Science (CSE)</option>
                    <option value="EEE" <?= $filter_dept === 'EEE' ? 'selected' : '' ?>>Electrical Engineering (EEE)</option>
                    <option value="BBA" <?= $filter_dept === 'BBA' ? 'selected' : '' ?>>Business Administration (BBA)</option>
                    <option value="Arch" <?= $filter_dept === 'Arch' ? 'selected' : '' ?>>Architecture</option>
                </select>
            </div>

            <div class="form-group mb-0">
                <label class="form-label" for="f-cgpa">Minimum CGPA</label>
                <input type="number" step="0.01" min="0.00" max="4.00" name="min_cgpa" id="f-cgpa" class="form-control" value="<?= htmlspecialchars($filter_cgpa ?: '') ?>" placeholder="e.g. 3.50">
            </div>

            <div class="form-group mb-0">
                <label class="form-label" for="f-skill">Skill</label>
                <input type="text" name="skill" id="f-skill" class="form-control" value="<?= htmlspecialchars($filter_skill ?: '') ?>" placeholder="e.g. PHP, React, Python, SQL">
            </div>
        </div>

        <div class="row mt-2" style="justify-content: flex-end;">
            <a href="index.php?controller=recruiter&action=talent_search" class="btn btn-outline btn-sm">Clear</a>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
        </div>
    </form>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">Results <span class="count-badge"><?= count($candidates) ?></span></h2>
    </div>

    <?php if (empty($candidates)): ?>
        <div class="empty-state">No students match these filters. Try a lower CGPA or a different department.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Department</th>
                    <th>CGPA</th>
                    <th>Skills</th>
                    <th>CV</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $cand): ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($cand['full_name']) ?></td>
                        <td><?= htmlspecialchars($cand['aiub_id']) ?></td>
                        <td><?= htmlspecialchars($cand['department']) ?></td>
                        <td><?= htmlspecialchars($cand['cgpa'] ? number_format((float)$cand['cgpa'], 2) : '0.00') ?></td>
                        <td class="wrap">
                            <?php 
                            $skills = !empty($cand['skills']) ? explode(',', $cand['skills']) : [];
                            foreach (array_slice($skills, 0, 3) as $sk): 
                                $sk = trim($sk);
                                if (empty($sk)) continue;
                            ?>
                                <span class="badge-tag"><?= htmlspecialchars($sk) ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php if (!empty($cand['cv_url'])): ?>
                                <a href="<?= htmlspecialchars($cand['cv_url']) ?>" target="_blank" rel="noopener" class="fw-600">View</a>
                            <?php else: ?>
                                <span class="text-dim">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline btn-sm" onclick="openOutreachModal(<?= $cand['id'] ?>, '<?= htmlspecialchars(addslashes($cand['full_name'])) ?>')">
                                Invite
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<!-- Send Candidate Invite Modal -->
<div class="modal-backdrop" id="outreach-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">Invite to apply</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('outreach-modal')">Close</button>
        </div>

        <form action="index.php?controller=recruiter&action=send_outreach" method="POST">
            <input type="hidden" name="student_id" id="modal-student-id">

            <div class="form-group">
                <label class="form-label" for="modal-student-name">To</label>
                <input type="text" id="modal-student-name" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label class="form-label" for="m-bucket">Shortlist</label>
                <select name="shortlist_id" id="m-bucket" class="form-control" required>
                    <?php if (empty($shortlists)): ?>
                        <option value="0" selected>No shortlist</option>
                    <?php else: ?>
                        <?php foreach ($shortlists as $sl): ?>
                            <option value="<?= $sl['id'] ?>"><?= htmlspecialchars($sl['bucket_name']) ?> (CGPA ≥ <?= $sl['filter_cgpa_min'] ?>)</option>
                        <?php endforeach; ?>
                        <option value="0">No shortlist</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="m-msg">Message</label>
                <textarea name="message" id="m-msg" class="form-control" rows="4" placeholder="Why you're reaching out and which role to apply for" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Send invitation
            </button>
        </form>
    </div>
</div>

<!-- Create New Bucket Modal -->
<div class="modal-backdrop" id="new-bucket-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">New shortlist</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-bucket-modal')">Close</button>
        </div>

        <form action="index.php?controller=recruiter&action=create_bucket" method="POST">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="bucket_name" class="form-control" placeholder="e.g. Fall 2026 SQA Engineering Talent" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select name="filter_dept" class="form-control">
                        <option value="CSE">Computer Science (CSE)</option>
                        <option value="EEE">Electrical Engineering (EEE)</option>
                        <option value="BBA">Business Administration (BBA)</option>
                        <option value="Arch">Architecture</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum CGPA</label>
                    <input type="number" step="0.01" min="0.00" max="4.00" name="min_cgpa" class="form-control" value="3.50" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Create shortlist
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
