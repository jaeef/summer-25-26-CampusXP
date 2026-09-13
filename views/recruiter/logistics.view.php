<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Corporate Campus Event & Logistics Requisitions</h1>
        <p class="portal-sub">Request university technical equipment, networking booths, and presentation facilities for on-campus corporate hiring events.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-rec-logistics-modal')">+ Request Campus Logistics</button>
</div>

<div class="glass-card">
    <div class="card-header">
        <div class="card-title">My Corporate Logistics Requests (<?= count($requests) ?>)</div>
    </div>

    <?php if (empty($requests)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No logistics requisitions submitted yet. Click "+ Request Campus Logistics" to submit a request to the university administration.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Event / Drive Title</th>
                    <th>Equipment Type</th>
                    <th>Quantity</th>
                    <th>Required Date</th>
                    <th>Return Date</th>
                    <th>Approval Status</th>
                    <th>Admin Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): 
                    $st = $r['dispatch_status'];
                    $badge = 'badge-student';
                    if ($st === 'Approved') $badge = 'badge-exec';
                    elseif ($st === 'Dispatched') $badge = 'badge-purple';
                    elseif ($st === 'Rejected') $badge = 'badge-danger';
                ?>
                    <tr>
                        <td style="font-weight: 700;"><?= htmlspecialchars($r['event_name']) ?></td>
                        <td><?= htmlspecialchars($r['item_type']) ?></td>
                        <td><strong><?= $r['quantity'] ?> units</strong></td>
                        <td><?= date('M d, Y', strtotime($r['needed_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($r['return_date'])) ?></td>
                        <td><span class="badge-tag <?= $badge ?>"><?= htmlspecialchars($st) ?></span></td>
                        <td style="font-size: 0.82rem; color: var(--text-muted);"><?= htmlspecialchars($r['notes'] ?: 'Standard request') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="new-rec-logistics-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">Request Campus Logistics & Facilities</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-rec-logistics-modal')">Close</button>
        </div>

        <form action="index.php?controller=recruiter&action=request_logistics" method="POST">
            <div class="form-group">
                <label class="form-label">Recruitment Event / Drive Title</label>
                <input type="text" name="event_name" class="form-control" placeholder="e.g. Brain Station 23 On-Campus Tech Talk & Assessment" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Equipment Category</label>
                    <select name="item_type" class="form-control">
                        <option value="Auditorium Presentation Screen & PA System">Auditorium Presentation Screen & PA System</option>
                        <option value="On-Campus Interview Desk & High-Speed LAN">On-Campus Interview Desk & High-Speed LAN</option>
                        <option value="Branded Corporate Booth Canopy (Annex 1 Plaza)">Branded Corporate Booth Canopy (Annex 1 Plaza)</option>
                        <option value="Multi-Port Power Distribution Strips">Multi-Port Power Distribution Strips</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity Needed</label>
                    <input type="number" min="1" max="50" name="quantity" class="form-control" value="1" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Event Date</label>
                    <input type="date" name="needed_date" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Conclusion / Return Date</label>
                    <input type="date" name="return_date" class="form-control" value="<?= date('Y-m-d', strtotime('+8 days')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Special Setup Notes / Technical Requirements</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Specify technical requirements, presentation resolution, visitor pass counts..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Submit Request to University Admin
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
