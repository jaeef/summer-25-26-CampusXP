<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Event Equipment & Logistics Requisitions</h1>
        <p class="portal-sub">Request university sound systems, projectors, podiums, and track approval dispatch status from OSA Admin.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-logistics-modal')">+ Request Equipment</button>
</div>

<!-- Logistics Requests Table -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">My Equipment Requisitions (<?= count($requests) ?>)</div>
    </div>

    <?php if (empty($requests)): ?>
        <p style="color: var(--text-muted); font-size: 0.88rem;">No logistics requisitions submitted yet. Click "+ Request Equipment" to create one.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Equipment Type</th>
                    <th>Quantity</th>
                    <th>Required Date</th>
                    <th>Return Date</th>
                    <th>Dispatch Status</th>
                    <th>Notes & Feedback</th>
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
                        <td style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($r['notes'] ?: 'Standard request') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- New Logistics Requisition Modal -->
<div class="modal-backdrop" id="new-logistics-modal">
    <div class="modal-box">
        <div class="card-header">
            <div class="card-title">New Campus Equipment Requisition</div>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-logistics-modal')">Close</button>
        </div>

        <form action="index.php?controller=executive&action=request_logistics" method="POST">
            <div class="form-group">
                <label class="form-label">Event / Campaign Title</label>
                <input type="text" name="event_name" class="form-control" placeholder="e.g. CS Fest 2026 Opening Ceremony" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Equipment Category</label>
                    <select name="item_type" class="form-control">
                        <option value="Sound System / Wireless Mics">Sound System / Wireless Mics</option>
                        <option value="HD Projector & Tripod Screen">HD Projector & Tripod Screen</option>
                        <option value="Branded Canopies & Registration Desks">Branded Canopies & Registration Desks</option>
                        <option value="Extension Power Cords & Strips">Extension Power Cords & Strips</option>
                        <option value="VIP Podiums & Standees">VIP Podiums & Standees</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity Needed</label>
                    <input type="number" min="1" max="50" name="quantity" class="form-control" value="2" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Date Required</label>
                    <input type="date" name="needed_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Return Date</label>
                    <input type="date" name="return_date" class="form-control" value="<?= date('Y-m-d', strtotime('+4 days')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Usage Notes & Special Requirements</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Specify room setup, sound check time..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px;">
                Submit Requisition to OSA
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
