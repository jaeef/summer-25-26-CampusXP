<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Interview slots</h1>
        <p class="page-sub">Create time slots in campus rooms, then book applicants into them. A slot can only hold one person.</p>
    </div>
    <div class="row">
        <form class="row" method="GET" action="index.php">
            <input type="hidden" name="controller" value="executive">
            <input type="hidden" name="action" value="scheduler">
            <select name="drive_id" class="form-control toolbar-select" aria-label="Drive" onchange="this.form.submit()">
                <option value="0" <?= $selected_drive_id === 0 ? 'selected' : '' ?>>All drives</option>
                <?php foreach ($all_drives as $dr): ?>
                    <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dr['title']) ?> (<?= htmlspecialchars($dr['club_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <button class="btn btn-primary btn-sm" onclick="openModal('new-slot-modal')">New slot</button>
    </div>
</div>

<div class="grid-2" style="align-items: start;">
    <!-- Slot List Table -->
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">Slots <span class="count-badge"><?= count($slots) ?></span></h2>
        </div>

        <?php if (empty($slots)): ?>
            <div class="empty-state">No slots for this drive yet. Use <strong>New slot</strong> to add one.</div>
        <?php else: ?>
            <div class="table-wrap"><table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Status</th>
                        <th>Applicant</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $sl): ?>
                        <tr>
                            <td class="fw-600 nums" style="white-space: nowrap;"><?= date('M d, h:i A', strtotime($sl['slot_time'])) ?></td>
                            <td><?= htmlspecialchars($sl['venue_room']) ?></td>
                            <td>
                                <?php if ($sl['is_booked']): ?>
                                    <span class="badge-tag tag-info">Booked</span>
                                <?php else: ?>
                                    <span class="badge-tag">Open</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sl['is_booked']): ?>
                                    <strong><?= htmlspecialchars($sl['candidate_name'] ?? 'Booked') ?></strong>
                                    <div class="text-xs text-dim"><?= htmlspecialchars($sl['aiub_id'] ?? '') ?></div>
                                <?php else: ?>
                                    <span class="text-dim">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($sl['is_booked']): ?>
                                    <form action="index.php?controller=executive&action=clear_slot" method="POST">
                                        <input type="hidden" name="slot_id" value="<?= $sl['id'] ?>">
                                        <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline">Free slot</button>
                                    </form>
                                <?php else: ?>
                                    
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table></div>
        <?php endif; ?>
    </div>

    <!-- Candidate Slot Assign Card -->
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">Book an applicant</h2>
        </div>

        <form action="index.php?controller=executive&action=assign_slot" method="POST">
            <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">

            <div class="form-group">
                <label class="form-label" for="assign-app">Applicant</label>
                <select name="app_id" id="assign-app" class="form-control" required>
                    <option value="" disabled selected>Choose an applicant</option>
                    <?php foreach ($candidates as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            
                            <?= htmlspecialchars($c['full_name']) ?> · <?= htmlspecialchars($c['applied_role']) ?> · <?= htmlspecialchars($c['status']) ?><?= !empty($c['drive_title']) ? ' · ' . htmlspecialchars($c['drive_title']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="assign-slot">Slot</label>
                <select name="slot_id" id="assign-slot" class="form-control" required>
                    <option value="" disabled selected>Choose an open slot</option>
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
                    <div class="form-error">No open slots. Create a new slot first.</div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block" <?= $avail_count === 0 ? 'disabled' : '' ?>>
                Book slot
            </button>
        </form>
    </div>
</div>

<!-- Create New Slot Modal -->
<div class="modal-backdrop" id="new-slot-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">New interview slot</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-slot-modal')">Close</button>
        </div>

        <form action="index.php?controller=executive&action=create_slot" method="POST">
            <input type="hidden" name="drive_id" value="<?= $selected_drive_id ?>">

            <div class="form-group">
                <label class="form-label">Room</label>
                <input type="text" name="venue_room" class="form-control" value="Annex 2 - Room 2104" placeholder="e.g. Annex 1 - Lab 1102" required>
            </div>

            <div class="form-group">
                <label class="form-label">Date and time</label>
                <input type="datetime-local" name="slot_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+2 days 10:00')) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Create slot
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
