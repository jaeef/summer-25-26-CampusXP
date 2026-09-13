<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Application Progress Tracker</h1>
        <p class="portal-sub">Real-time status tracking for your submitted club and corporate applications.</p>
    </div>
    <div>
        <a href="index.php?controller=student&action=dashboard" class="btn btn-primary btn-sm">Browse Opportunities</a>
    </div>
</div>

<?php
$total_apps_count = count($corp_apps) + count($club_apps);
$in_review_count = 0;
$interview_count = 0;
$selected_count = 0;

foreach (array_merge($corp_apps, $club_apps) as $item) {
    $st = $item['status'] ?? 'Applied';
    if ($st === 'Under Review') $in_review_count++;
    elseif ($st === 'Interview') $interview_count++;
    elseif ($st === 'Selected') $selected_count++;
}
?>

<!-- Overview Metrics -->
<div class="grid-4" style="margin-bottom: 1.5rem;">
    <div class="glass-card" style="text-align: center; padding: 1rem;">
        <div style="font-size: 1.6rem; font-weight: 700; color: var(--brand-blue);"><?= $total_apps_count ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Total Applications</div>
    </div>
    <div class="glass-card" style="text-align: center; padding: 1rem;">
        <div style="font-size: 1.6rem; font-weight: 700; color: #b45309;"><?= $in_review_count ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Under Review</div>
    </div>
    <div class="glass-card" style="text-align: center; padding: 1rem;">
        <div style="font-size: 1.6rem; font-weight: 700; color: var(--brand-purple);"><?= $interview_count ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Interview Scheduled</div>
    </div>
    <div class="glass-card" style="text-align: center; padding: 1rem;">
        <div style="font-size: 1.6rem; font-weight: 700; color: var(--brand-green);"><?= $selected_count ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted);">Selected</div>
    </div>
</div>

<!-- Corporate Applications -->
<div class="glass-card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <div class="card-title">Corporate Applications (<?= count($corp_apps) ?>)</div>
    </div>

    <?php if (empty($corp_apps)): ?>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.5rem 0;">No corporate applications submitted yet.</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($corp_apps as $capp): 
                $cstatus = $capp['status'] ?? 'Applied';
                $cprogress = '0%';
                $c1 = 'pending'; $c2 = 'pending'; $c3 = 'pending'; $c4 = 'pending';

                if ($cstatus === 'Applied') { 
                    $cprogress = '0%'; 
                    $c1 = 'active'; 
                } elseif ($cstatus === 'Under Review') { 
                    $cprogress = '33.3%'; 
                    $c1 = 'completed'; 
                    $c2 = 'active'; 
                } elseif ($cstatus === 'Interview') { 
                    $cprogress = '66.6%'; 
                    $c1 = 'completed'; 
                    $c2 = 'completed'; 
                    $c3 = 'active'; 
                } elseif ($cstatus === 'Selected') { 
                    $cprogress = '100%'; 
                    $c1 = 'completed'; 
                    $c2 = 'completed'; 
                    $c3 = 'completed'; 
                    $c4 = 'completed'; 
                } elseif ($cstatus === 'Rejected') { 
                    $cprogress = '100%'; 
                    $c1 = 'completed'; 
                    $c4 = 'active rejected-step'; 
                }
            ?>
                <div class="tracker-card">
                    <div class="tracker-card-header">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <strong style="font-size: 1rem; color: var(--text-main);"><?= htmlspecialchars($capp['drive_title']) ?></strong>
                                <span class="badge-tag badge-recruiter"><?= htmlspecialchars($capp['company_name']) ?></span>
                            </div>
                            <div class="tracker-meta-row">
                                <span>Role: <strong><?= htmlspecialchars($capp['applied_role']) ?></strong></span>
                                <span>|</span>
                                <span>Submitted: <?= date('M d, Y - h:i A', strtotime($capp['applied_at'])) ?></span>
                            </div>
                        </div>

                        <div>
                            <?php if ($cstatus === 'Selected'): ?>
                                <span class="badge-tag badge-exec">Selected</span>
                            <?php elseif ($cstatus === 'Rejected'): ?>
                                <span class="badge-tag badge-danger">Not Selected</span>
                            <?php elseif ($cstatus === 'Interview'): ?>
                                <span class="badge-tag badge-admin">Interview</span>
                            <?php elseif ($cstatus === 'Under Review'): ?>
                                <span class="badge-tag badge-gold">Under Review</span>
                            <?php else: ?>
                                <span class="badge-tag badge-student">Applied</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Minimal Linear Progress Stepper -->
                    <div class="linear-trackbar-wrapper">
                        <div class="linear-stepper">
                            <div class="linear-stepper-line">
                                <div class="linear-stepper-progress" style="width: <?= $cprogress ?>;"></div>
                            </div>

                            <div class="step-node <?= $c1 ?>">
                                <div class="step-dot"><?= ($c1 === 'completed') ? '✓' : '1' ?></div>
                                <div class="step-title">Applied</div>
                            </div>

                            <div class="step-node <?= $c2 ?>">
                                <div class="step-dot"><?= ($c2 === 'completed') ? '✓' : '2' ?></div>
                                <div class="step-title">Review</div>
                            </div>

                            <div class="step-node <?= $c3 ?>">
                                <div class="step-dot"><?= ($c3 === 'completed') ? '✓' : '3' ?></div>
                                <div class="step-title">Interview</div>
                            </div>

                            <div class="step-node <?= $c4 ?>">
                                <div class="step-dot"><?= ($cstatus === 'Selected') ? '✓' : (($cstatus === 'Rejected') ? '✕' : '4') ?></div>
                                <div class="step-title"><?= ($cstatus === 'Rejected') ? 'Rejected' : (($cstatus === 'Selected') ? 'Selected' : 'Decision') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Club Applications -->
<div class="glass-card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <div class="card-title">Club Applications (<?= count($club_apps) ?>)</div>
    </div>

    <?php if (empty($club_apps)): ?>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.5rem 0;">No club applications submitted yet.</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($club_apps as $app): 
                $status = $app['status'] ?? 'Applied';
                $progress = '0%';
                $s1 = 'pending'; $s2 = 'pending'; $s3 = 'pending'; $s4 = 'pending';

                if ($status === 'Applied') { 
                    $progress = '0%'; 
                    $s1 = 'active'; 
                } elseif ($status === 'Under Review') { 
                    $progress = '33.3%'; 
                    $s1 = 'completed'; 
                    $s2 = 'active'; 
                } elseif ($status === 'Interview') { 
                    $progress = '66.6%'; 
                    $s1 = 'completed'; 
                    $s2 = 'completed'; 
                    $s3 = 'active'; 
                } elseif ($status === 'Selected') { 
                    $progress = '100%'; 
                    $s1 = 'completed'; 
                    $s2 = 'completed'; 
                    $s3 = 'completed'; 
                    $s4 = 'completed'; 
                } elseif ($status === 'Rejected') { 
                    $progress = '100%'; 
                    $s1 = 'completed'; 
                    $s4 = 'active rejected-step'; 
                }
            ?>
                <div class="tracker-card">
                    <div class="tracker-card-header">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <strong style="font-size: 1rem; color: var(--text-main);"><?= htmlspecialchars($app['drive_title']) ?></strong>
                                <span class="badge-tag badge-student"><?= htmlspecialchars($app['club_name']) ?></span>
                            </div>
                            <div class="tracker-meta-row">
                                <span>Role: <strong><?= htmlspecialchars($app['applied_role']) ?></strong></span>
                                <span>|</span>
                                <span>Submitted: <?= date('M d, Y - h:i A', strtotime($app['applied_at'])) ?></span>
                            </div>
                        </div>

                        <div>
                            <?php if ($status === 'Selected'): ?>
                                <span class="badge-tag badge-exec">Selected</span>
                            <?php elseif ($status === 'Rejected'): ?>
                                <span class="badge-tag badge-danger">Not Selected</span>
                            <?php elseif ($status === 'Interview'): ?>
                                <span class="badge-tag badge-admin">Interview</span>
                            <?php elseif ($status === 'Under Review'): ?>
                                <span class="badge-tag badge-gold">Under Review</span>
                            <?php else: ?>
                                <span class="badge-tag badge-student">Applied</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Minimal Linear Progress Stepper -->
                    <div class="linear-trackbar-wrapper">
                        <div class="linear-stepper">
                            <div class="linear-stepper-line">
                                <div class="linear-stepper-progress" style="width: <?= $progress ?>;"></div>
                            </div>

                            <div class="step-node <?= $s1 ?>">
                                <div class="step-dot"><?= ($s1 === 'completed') ? '✓' : '1' ?></div>
                                <div class="step-title">Applied</div>
                            </div>

                            <div class="step-node <?= $s2 ?>">
                                <div class="step-dot"><?= ($s2 === 'completed') ? '✓' : '2' ?></div>
                                <div class="step-title">Review</div>
                            </div>

                            <div class="step-node <?= $s3 ?>">
                                <div class="step-dot"><?= ($s3 === 'completed') ? '✓' : '3' ?></div>
                                <div class="step-title">Interview</div>
                            </div>

                            <div class="step-node <?= $s4 ?>">
                                <div class="step-dot"><?= ($status === 'Selected') ? '✓' : (($status === 'Rejected') ? '✕' : '4') ?></div>
                                <div class="step-title"><?= ($status === 'Rejected') ? 'Rejected' : (($status === 'Selected') ? 'Selected' : 'Decision') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

