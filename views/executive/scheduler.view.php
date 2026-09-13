<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Interview Slot Auto-Scheduler</h1>
        <p class="portal-sub">Allocate campus interview rooms and assign shortlisted candidates to prevent scheduling conflicts.</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <form method="GET" action="index.php" style="display: flex; gap: 8px; margin: 0;">
            <input type="hidden" name="controller" value="executive">
            <input type="hidden" name="action" value="scheduler">
            <select name="drive_id" class="form-control" onchange="this.form.submit()" style="width: auto; min-width: 280px; font-weight: 600;">
                <option value="0" <?= $selected_drive_id === 0 ? 'selected' : '' ?>>★ All Club Drives & Campaigns</option>
                <?php foreach ($all_drives as $dr): ?>
                    <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dr['title']) ?> (<?= htmlspecialchars($dr['club_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <button class="btn btn-primary btn-sm" onclick="openModal('new-slot-modal')">+ Create New Slot</button>
    </div>
</div>

<div class="grid-2">
    <!-- Slot List Table -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">Configured Interview Slots (<?= count($slots) ?>)</div>
        </div>

        <?php if (empty($slots)): ?>
            <p style="color: var(--text-muted); font-size: 0.88rem;">No slots generated for this drive. Click "+ Create New Slot" to create interview timeslots.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Slot Time</th>
                        <th>Venue / Room</th>
                        <th>Status</th>
                        <th>Assigned Candidate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $sl): ?>
                        <tr>
                            <td style="font-weight: 700;"><?= date('M d, Y - h:i A', strtotime($sl['slot_time'])) ?></td>
                            <td><?= htmlspecialchars($sl['venue_room']) ?></td>
                            <td>
                                <?php if ($sl['is_booked']): ?>
                                    <span class="badge-tag badge-exec">Booked</span>
                                <?php else: ?>
                                    <span class="badge-tag badge-student">Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sl['is_booked']): ?>
                                    <strong><?= htmlspecialchars($sl['candidate_name'] ?? 'Assigned Candidate') ?></strong>
                                    <div style="font-size: 0.75rem; color: var(--text-dim);">ID: <?= htmlspecialchars($sl['aiub_id'] ?? '') ?></div>
                                <?php else: ?>
                                    <span style="color: var(--text-dim); font-size: 0.8rem;">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sl['is_booked']): ?>
                                    <form action="index.php?controller=executive&action=clear_slot" method="POST" style="margin:0;">
                                        <input type="hidden" name="slot_id" value="<?= $sl['id'] ?>">
                                        <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 3px 8px; font-size: 0.75rem;">Release</button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.78rem; color: var(--brand-green); font-weight: 600;">Open</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Candidate Slot Assign Card -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">Assign Candidate to Interview Slot</div>
        </div>

        <form action="index.php?controller=executive&action=assign_slot" method="POST">
            <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">

            <div class="form-group">
                <label class="form-label">Select Candidate (Interview/Review Stage)</label>
                <select name="app_id" class="form-control" required>
                    <option value="" disabled selected>Select Candidate from Pipeline...</option>
                    <?php foreach ($candidates as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= ($c['status'] === 'Interview' ? '★ [Interview Call] ' : '[' . htmlspecialchars($c['status']) . '] ') ?>
                            <?= htmlspecialchars($c['full_name']) ?> (ID: <?= htmlspecialchars($c['aiub_id']) ?> - Role: <?= htmlspecialchars($c['applied_role']) ?><?= !empty($c['drive_title']) ? ' • ' . htmlspecialchars($c['drive_title']) : '' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Select Available Timeslot & Room</label>
                <select name="slot_id" class="form-control" required>
                    <option value="" disabled selected>Select Available Slot...</option>
                    <?php 
                    $avail_count = 0;
                    foreach ($slots as $sl): 
                        if (!$sl['is_booked']):
                            $avail_count++;
                    ?>
                        <option value="<?= $sl['id'] ?>">
                            <?= date('M d, Y - h:i A', strtotime($sl['slot_time'])) ?> (<?= htmlspecialchars($sl['venue_room']) ?>)
                        </option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
                <?php if ($avail_count === 0): ?>
                    <div style="font-size: 0.78rem; color: #dc2626; margin-top: 4px;">No unbooked slots available. Create a new timeslot first.</div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;" <?= $avail_count === 0 ? 'disabled' : '' ?>>
                Confirm Candidate Interview Slot
            </button>
        </form>
    </div>
</div>

<!-- Create New Slot Modal -->
<div class="modal-backdrop" id="new-slot-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Create Interview Venue Slot</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-slot-modal')">Close</button>
        </div>

        <form action="index.php?controller=executive&action=create_slot" method="POST">
            <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">

            <div class="form-group">
                <label class="form-label">Campus Room / Venue</label>
                <input type="text" name="venue_room" class="form-control" value="Annex 2 - Room 2104" placeholder="e.g. Annex 1 - Lab 1102" required>
            </div>

            <div class="form-group">
                <label class="form-label">Slot Date & Time</label>
                <input type="datetime-local" name="slot_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+2 days 10:00')) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Create Timeslot
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
