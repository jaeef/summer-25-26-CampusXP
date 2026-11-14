<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Analytics</h1>
        <p class="page-sub">Who is applying: departments, CGPA ranges and the skills they list.</p>
    </div>
    <form class="row" method="GET" action="index.php">
        <input type="hidden" name="controller" value="executive">
        <input type="hidden" name="action" value="analytics">
        <select name="drive_id" class="form-control toolbar-select" aria-label="Drive" onchange="this.form.submit()">
            <option value="0" <?= $selected_drive_id == 0 ? 'selected' : '' ?>>All drives</option>
            <?php foreach ($all_drives as $dr): ?>
                <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dr['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Key Analytics Cards -->
<div class="grid-3 section">
    <!-- Department Breakdown -->
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">By department</h2>
        </div>
        <div class="stack">
            <?php 
            $dept_counts = [];
            foreach ($applicants as $ap) {
                $d = $ap['department'] ?? 'Other';
                $dept_counts[$d] = ($dept_counts[$d] ?? 0) + 1;
            }
            $total_cands = count($applicants) ?: 1;
            foreach ($dept_counts as $dept_name => $count): 
                $pct = round(($count / $total_cands) * 100);
            ?>
                <div>
                    <div class="stat-bar-label"><span><?= htmlspecialchars($dept_name) ?></span>
                        <span class="text-muted"><?= $count ?> · <?= $pct ?>%</span>
                    </div>
                    <div class="stat-bar-bg">
                        <div class="stat-bar-fill" style="width: <?= $pct ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($dept_counts)): ?>
                <p class="text-sm text-dim">No applicants yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- CGPA Tier Breakdown -->
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">By CGPA</h2>
        </div>
        <div class="stack">
            <?php
            $cgpa_tiers = ['3.75 – 4.00' => 0, '3.50 – 3.74' => 0, '3.00 – 3.49' => 0, '< 3.00' => 0];
            foreach ($applicants as $ap) {
                $g = floatval($ap['cgpa'] ?? 0);
                if ($g >= 3.75) $cgpa_tiers['3.75 – 4.00']++;
                elseif ($g >= 3.50) $cgpa_tiers['3.50 – 3.74']++;
                elseif ($g >= 3.00) $cgpa_tiers['3.00 – 3.49']++;
                else $cgpa_tiers['< 3.00']++;
            }
            foreach ($cgpa_tiers as $tier_name => $tcount):
                $tpct = round(($tcount / $total_cands) * 100);
            ?>
                <div>
                    <div class="stat-bar-label"><span><?= htmlspecialchars($tier_name) ?></span>
                        <span class="text-muted"><?= $tcount ?> · <?= $tpct ?>%</span>
                    </div>
                    <div class="stat-bar-bg">
                        <div class="stat-bar-fill" style="width: <?= $tpct ?>%;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Skills Mentioned -->
    <div class="glass-card">
        <div class="card-header">
            <h2 class="card-title">Top skills</h2>
        </div>
        <div class="row">
            <?php
            $skills_freq = [];
            foreach ($applicants as $ap) {
                if (!empty($ap['skills'])) {
                    $parts = explode(',', $ap['skills']);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if (!empty($p)) {
                            $skills_freq[$p] = ($skills_freq[$p] ?? 0) + 1;
                        }
                    }
                }
            }
            arsort($skills_freq);
            $top_skills = array_slice($skills_freq, 0, 12, true);
            foreach ($top_skills as $sname => $scount):
            ?>
                <span class="badge-tag"><?= htmlspecialchars($sname) ?> (<?= $scount ?>)</span>
            <?php endforeach; ?>
            <?php if (empty($top_skills)): ?>
                <p class="text-sm text-dim">No skills listed yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
