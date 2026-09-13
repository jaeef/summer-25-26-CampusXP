<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="portal-header">
    <div>
        <h1 class="portal-title">Applicant Demographics & Analytics</h1>
        <p class="portal-sub">Analyze applicant distribution across AIUB departments, CGPA tiers, and key technical skills.</p>
    </div>
    <form method="GET" action="index.php" style="display: flex; gap: 8px; margin: 0;">
        <input type="hidden" name="controller" value="executive">
        <input type="hidden" name="action" value="analytics">
        <select name="drive_id" class="form-control" onchange="this.form.submit()" style="width: auto; min-width: 250px;">
            <option value="0" <?= $selected_drive_id == 0 ? 'selected' : '' ?>>All Club Recruitment Drives</option>
            <?php foreach ($all_drives as $dr): ?>
                <option value="<?= $dr['id'] ?>" <?= $dr['id'] == $selected_drive_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dr['title']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Key Analytics Cards -->
<div class="grid-3" style="margin-bottom: 2rem;">
    <!-- Department Breakdown -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">Department Distribution</div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px;">
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
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 4px;">
                        <strong><?= htmlspecialchars($dept_name) ?></strong>
                        <span><?= $count ?> applicants (<?= $pct ?>%)</span>
                    </div>
                    <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?= $pct ?>%; background: var(--brand-blue);"></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($dept_counts)): ?>
                <p style="color: var(--text-dim); font-size: 0.85rem;">No candidate data available yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- CGPA Tier Breakdown -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">CGPA Quality Tiers</div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php
            $cgpa_tiers = ['3.75 - 4.00 (High Merit)' => 0, '3.50 - 3.74 (Merit)' => 0, '3.00 - 3.49 (Standard)' => 0, '< 3.00' => 0];
            foreach ($applicants as $ap) {
                $g = floatval($ap['cgpa'] ?? 0);
                if ($g >= 3.75) $cgpa_tiers['3.75 - 4.00 (High Merit)']++;
                elseif ($g >= 3.50) $cgpa_tiers['3.50 - 3.74 (Merit)']++;
                elseif ($g >= 3.00) $cgpa_tiers['3.00 - 3.49 (Standard)']++;
                else $cgpa_tiers['< 3.00']++;
            }
            foreach ($cgpa_tiers as $tier_name => $tcount):
                $tpct = round(($tcount / $total_cands) * 100);
            ?>
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 4px;">
                        <strong><?= htmlspecialchars($tier_name) ?></strong>
                        <span><?= $tcount ?> (<?= $tpct ?>%)</span>
                    </div>
                    <div style="height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?= $tpct ?>%; background: var(--brand-green);"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Skills Mentioned -->
    <div class="glass-card">
        <div class="card-header">
            <div class="card-title">Top Skills Declared</div>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
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
                <span class="badge-tag" style="background: #eff6ff; color: var(--brand-blue); border: 1px solid #bfdbfe; font-size: 0.8rem; padding: 4px 8px;">
                    <?= htmlspecialchars($sname) ?> (<?= $scount ?>)
                </span>
            <?php endforeach; ?>
            <?php if (empty($top_skills)): ?>
                <p style="color: var(--text-dim); font-size: 0.85rem;">No technical competencies logged yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
