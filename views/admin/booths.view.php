<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Booths</h1>
        <p class="page-sub">Physical booth spaces on campus and the club or company each one is assigned to.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-booth-modal')">Assign booth</button>
</div>

<div class="glass-card">
    <div class="card-header">
        <h2 class="card-title">All booths <span class="count-badge"><?= count($booths) ?></span></h2>
    </div>

    <?php if (empty($booths)): ?>
        <div class="empty-state">No booths set up yet. Use <strong>Assign booth</strong> to add one.</div>
    <?php else: ?>
        <div class="booth-grid">
            <?php foreach ($booths as $b): $occupied = !empty($b['is_occupied']); ?>
                <div class="booth-slot<?= $occupied ? ' occupied' : '' ?>">
                    <div class="row-between">
                        <span class="booth-number"><?= htmlspecialchars($b['booth_number']) ?></span>
                        <span class="badge-tag<?= $occupied ? ' tag-info' : '' ?>"><?= $occupied ? 'Assigned' : 'Open' ?></span>
                    </div>
                    <div class="booth-status"><?= htmlspecialchars($b['campus_location']) ?></div>
                    <div class="text-sm fw-600 mt-1"><?= htmlspecialchars($b['assigned_club_name'] ?: 'Unassigned') ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal-backdrop" id="new-booth-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">Assign a booth</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-booth-modal')">Close</button>
        </div>

        <form action="index.php?controller=admin&action=booth_action" method="POST">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Booth number</label>
                    <input type="text" name="booth_number" class="form-control" placeholder="e.g. B-07" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <select name="campus_location" class="form-control">
                        <option value="Annex 1 Ground Plaza">Annex 1 Ground Plaza</option>
                        <option value="Annex 2 Student Lounge">Annex 2 Student Lounge</option>
                        <option value="Auditorium Foyer">Auditorium Foyer</option>
                        <option value="Building D Multi-Purpose Hall">Building D Multi-Purpose Hall</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Club or company</label>
                <input type="text" name="assigned_club_name" class="form-control" placeholder="e.g. AIUB Computer Club (ACC)" required>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Power sockets, canopy size"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Assign booth
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
