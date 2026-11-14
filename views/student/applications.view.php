<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Applications</h1>
        <p class="page-sub">Where each of your club and corporate applications stands.</p>
    </div>
    <a href="index.php?controller=student&action=dashboard" class="btn btn-outline btn-sm">Browse opportunities</a>
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

<div class="stats">
    <div class="stat"><span class="stat-value"><?= $total_apps_count ?></span><span class="stat-label">Total</span></div>
    <div class="stat"><span class="stat-value"><?= $in_review_count ?></span><span class="stat-label">Under review</span></div>
    <div class="stat"><span class="stat-value"><?= $interview_count ?></span><span class="stat-label">Interview</span></div>
    <div class="stat"><span class="stat-value"><?= $selected_count ?></span><span class="stat-label">Selected</span></div>
</div>

<!-- Corporate Applications -->
<section class="section">
    <div class="card-header">
        <h2 class="card-title">Corporate applications <span class="count-badge"><?= count($corp_apps) ?></span></h2>
    </div>

    <?php if (empty($corp_apps)): ?>
        <div class="glass-card empty-state">No corporate applications yet. <a href="index.php?controller=student&action=dashboard">Browse opportunities</a></div>
    <?php else: ?>
        <div class="stack-lg">
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
                            <div class="row">
                                <strong style="font-size: 1rem;"><?= htmlspecialchars($capp['drive_title']) ?></strong>
                                <span class="text-sm text-muted">· <?= htmlspecialchars($capp['company_name']) ?></span>
                            </div>
                            <div class="tracker-meta-row">
                                <span>Role <strong><?= htmlspecialchars($capp['applied_role']) ?></strong></span>
                                <span class="text-dim">·</span>
                                <span>Submitted <?= date('M d, Y, h:i A', strtotime($capp['applied_at'])) ?></span>
                            </div>
                        </div>

                        <div>
                            <?php if ($cstatus === 'Selected'): ?>
                                <span class="badge-tag tag-success">Selected</span>
                            <?php elseif ($cstatus === 'Rejected'): ?>
                                <span class="badge-tag tag-danger">Not selected</span>
                            <?php elseif ($cstatus === 'Interview'): ?>
                                <span class="badge-tag tag-info">Interview</span>
                            <?php elseif ($cstatus === 'Under Review'): ?>
                                <span class="badge-tag tag-warning">Under review</span>
                            <?php else: ?>
                                <span class="badge-tag">Applied</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="linear-trackbar-wrapper" style="margin-bottom: 0;">
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
</section>

<!-- Club Applications -->
<section class="section">
    <div class="card-header">
        <h2 class="card-title">Club applications <span class="count-badge"><?= count($club_apps) ?></span></h2>
    </div>

    <?php if (empty($club_apps)): ?>
        <div class="glass-card empty-state">No club applications yet. <a href="index.php?controller=student&action=dashboard">Browse opportunities</a></div>
    <?php else: ?>
        <div class="stack-lg">
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
                            <div class="row">
                                <strong style="font-size: 1rem;"><?= htmlspecialchars($app['drive_title']) ?></strong>
                                <span class="text-sm text-muted">· <?= htmlspecialchars($app['club_name']) ?></span>
                            </div>
                            <div class="tracker-meta-row">
                                <span>Role <strong><?= htmlspecialchars($app['applied_role']) ?></strong></span>
                                <span class="text-dim">·</span>
                                <span>Submitted <?= date('M d, Y, h:i A', strtotime($app['applied_at'])) ?></span>
                            </div>
                        </div>

                        <div>
                            <?php if ($status === 'Selected'): ?>
                                <span class="badge-tag tag-success">Selected</span>
                            <?php elseif ($status === 'Rejected'): ?>
                                <span class="badge-tag tag-danger">Not selected</span>
                            <?php elseif ($status === 'Interview'): ?>
                                <span class="badge-tag tag-info">Interview</span>
                            <?php elseif ($status === 'Under Review'): ?>
                                <span class="badge-tag tag-warning">Under review</span>
                            <?php else: ?>
                                <span class="badge-tag">Applied</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="linear-trackbar-wrapper" style="margin-bottom: 0;">
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
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

