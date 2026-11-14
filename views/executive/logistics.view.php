<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Equipment</h1>
        <p class="page-sub">Borrow sound systems, projectors, podiums and more from OSA for your events.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-logistics-modal')">Request equipment</button>
</div>

<!-- Logistics Requests Table -->
<div class="glass-card section">
    <div class="card-header">
        <h2 class="card-title">Your requests <span class="count-badge"><?= count($requests) ?></span></h2>
    </div>

    <?php if (empty($requests)): ?>
        <div class="empty-state">No equipment requests yet.</div>
    <?php else: ?>
        <div class="table-wrap"><table class="data-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Equipment</th>
                    <th>Qty</th>
                    <th>Needed</th>
                    <th>Return</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): 
                    $st = $r['dispatch_status'];
                    $badge = '';
                    if ($st === 'Approved') $badge = 'tag-success';
                    elseif ($st === 'Dispatched') $badge = 'tag-info';
                    elseif ($st === 'Rejected') $badge = 'tag-danger';
                ?>
                    <tr>
                        <td class="fw-600"><?= htmlspecialchars($r['event_name']) ?></td>
                        <td><?= htmlspecialchars($r['item_type']) ?></td>
                        <td><?= $r['quantity'] ?></td>
                        <td><?= !empty($r['needed_date']) ? date('M d, Y', strtotime($r['needed_date'])) : '—' ?></td>
                        <td><?= !empty($r['return_date']) ? date('M d, Y', strtotime($r['return_date'])) : '—' ?></td>
                        <td><span class="badge-tag <?= $badge ?>"><?= htmlspecialchars($st) ?></span></td>
                        <td class="text-xs text-muted wrap"><?= htmlspecialchars($r['notes'] ?: '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<!-- New Logistics Requisition Modal -->
<div class="modal-backdrop" id="new-logistics-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">Request equipment</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-logistics-modal')">Close</button>
        </div>

        <form action="index.php?controller=executive&action=request_logistics" method="POST">
            <div class="form-group">
                <label class="form-label">Event</label>
                <input type="text" name="event_name" class="form-control" placeholder="e.g. CS Fest 2026 Opening Ceremony" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Equipment</label>
                    <select name="item_type" class="form-control">
                        <option value="Sound System / Wireless Mics">Sound System / Wireless Mics</option>
                        <option value="HD Projector & Tripod Screen">HD Projector & Tripod Screen</option>
                        <option value="Branded Canopies & Registration Desks">Branded Canopies & Registration Desks</option>
                        <option value="Extension Power Cords & Strips">Extension Power Cords & Strips</option>
                        <option value="VIP Podiums & Standees">VIP Podiums & Standees</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" max="50" name="quantity" class="form-control" value="2" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Needed on</label>
                    <input type="date" name="needed_date" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Return by</label>
                    <input type="date" name="return_date" class="form-control" value="<?= date('Y-m-d', strtotime('+4 days')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Room setup, sound check time, anything OSA should know"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Send request
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
