<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Equipment</h1>
        <p class="page-sub">Request screens, booths, interview desks and power for on-campus hiring events. OSA approves each request.</p>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openModal('new-rec-logistics-modal')">Request equipment</button>
</div>

<div class="glass-card">
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

<div class="modal-backdrop" id="new-rec-logistics-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="card-header">
            <h3 class="card-title">Request equipment</h3>
            <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('new-rec-logistics-modal')">Close</button>
        </div>

        <form action="index.php?controller=recruiter&action=request_logistics" method="POST">
            <div class="form-group">
                <label class="form-label">Event</label>
                <input type="text" name="event_name" class="form-control" placeholder="e.g. Brain Station 23 On-Campus Tech Talk & Assessment" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Equipment</label>
                    <select name="item_type" class="form-control">
                        <option value="Auditorium Presentation Screen & PA System">Auditorium Presentation Screen & PA System</option>
                        <option value="On-Campus Interview Desk & High-Speed LAN">On-Campus Interview Desk & High-Speed LAN</option>
                        <option value="Branded Corporate Booth Canopy (Annex 1 Plaza)">Branded Corporate Booth Canopy (Annex 1 Plaza)</option>
                        <option value="Multi-Port Power Distribution Strips">Multi-Port Power Distribution Strips</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" max="50" name="quantity" class="form-control" value="1" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Event Date</label>
                    <input type="date" name="needed_date" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Return by</label>
                    <input type="date" name="return_date" class="form-control" value="<?= date('Y-m-d', strtotime('+8 days')) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Setup needs, screen resolution, visitor passes"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Send request
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
