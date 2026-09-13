<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Campus Venue & Recruitment Booth Allocator</h1>
        <p class="portal-sub">Designate physical AIUB booth spaces and tables to approved clubs and corporate partners.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-booth-modal')">+ Allocate Campus Booth</button>
</div>

<div class="grid-2">
    <!-- Booth Table -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">Active AIUB Campus Booth Allocations (<?= count($booths) ?>)</div>
        </div>

        <?php if (empty($booths)): ?>
            <p style="color: var(--text-muted); font-size: 0.88rem;">No physical booths allocated yet.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booth #</th>
                        <th>Location</th>
                        <th>Assigned Club / Company</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($booths as $b): ?>
                        <tr>
                            <td style="font-weight: 800; color: var(--brand-blue);"><?= htmlspecialchars($b['booth_number']) ?></td>
                            <td><?= htmlspecialchars($b['campus_location']) ?></td>
                            <td style="font-weight: 700;"><?= htmlspecialchars($b['assigned_club_name'] ?: 'Open Reserve') ?></td>
                            <td><span class="badge-tag <?= !empty($b['is_occupied']) ? 'badge-exec' : 'badge-student' ?>"><?= !empty($b['is_occupied']) ? 'Assigned' : 'Available' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Campus Map Visualizer -->
    <div class="glass-card" style="background: #ffffff;">
        <div class="card-header">
            <div class="card-title">AIUB Campus Venue Grid Layout</div>
            <span class="badge-tag badge-admin">Annex 1 & 2 Plazas</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 1rem;">
            <?php 
            $default_spots = ['B-01' => 'ACC Recruitment', 'B-02' => 'IEEE AIUB', 'B-03' => 'OSCAD Workshop', 'B-04' => 'Drama Club', 'B-05' => 'Brain Station 23', 'B-06' => 'Available'];
            foreach ($default_spots as $bcode => $bassign):
                $is_avail = ($bassign === 'Available');
            ?>
                <div style="background: <?= $is_avail ? '#f8fafc' : '#eff6ff' ?>; border: 1px solid <?= $is_avail ? '#cbd5e1' : '#bfdbfe' ?>; border-radius: var(--radius-sm); padding: 12px; text-align: center;">
                    <div style="font-weight: 800; font-size: 1.1rem; color: <?= $is_avail ? 'var(--text-muted)' : 'var(--brand-blue)' ?>;"><?= $bcode ?></div>
                    <div style="font-size: 0.78rem; font-weight: 600; margin-top: 4px; color: var(--text-main);"><?= $bassign ?></div>
                    <span class="badge-tag <?= $is_avail ? 'badge-student' : 'badge-exec' ?>" style="margin-top: 6px; font-size: 0.7rem;">
                        <?= $is_avail ? 'Vacant' : 'Assigned' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="new-booth-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Allocate AIUB Campus Booth Space</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-booth-modal')">Close</button>
        </div>

        <form action="index.php?controller=admin&action=booth_action" method="POST">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Booth Identifier Number</label>
                    <input type="text" name="booth_number" class="form-control" placeholder="e.g. B-07" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Campus Zone Location</label>
                    <select name="campus_location" class="form-control">
                        <option value="Annex 1 Ground Plaza">Annex 1 Ground Plaza</option>
                        <option value="Annex 2 Student Lounge">Annex 2 Student Lounge</option>
                        <option value="Auditorium Foyer">Auditorium Foyer</option>
                        <option value="Building D Multi-Purpose Hall">Building D Multi-Purpose Hall</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Assign to Club / Company Name</label>
                <input type="text" name="assigned_club_name" class="form-control" placeholder="e.g. AIUB Computer Club (ACC)" required>
            </div>

            <div class="form-group">
                <label class="form-label">Admin Allocation Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Notes on electrical socket, canopy dimensions..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Confirm Booth Assignment
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
