<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – CampusXP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; background: var(--bg-page);">

    <div style="max-width: 480px; width: 100%;">
        <!-- Brand Header -->
        <div style="text-align: center; margin-bottom: 1.75rem;">
            <div class="logo-badge" style="margin: 0 auto 10px; width: 44px; height: 44px; font-size: 1.4rem;">C</div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: #1e3a8a;">Campus<span style="color: var(--brand-gold);">XP</span></h1>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 2px;">Campus Opportunity & Recruitment Platform</p>
        </div>

        <?php if ($flash): ?>
            <div class="flash-alert flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <!-- Login Card -->
        <div class="glass-card" style="padding: 1.75rem; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem; color: #0f172a;">Account Sign In</h2>

            <form action="index.php?controller=auth&action=login" method="POST">
                <div class="form-group">
                    <label class="form-label">AIUB / Corporate Email Address</label>
                    <input type="email" name="email" id="login-email" class="form-control" placeholder="name@aiub.edu or recruiter@company.com" value="<?= htmlspecialchars($remembered_email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" id="login-password" class="form-control" placeholder="••••••••" required>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: var(--text-muted); cursor: pointer;">
                        <input type="checkbox" name="remember_me" value="1" <?= !empty($remembered_email) ? 'checked' : '' ?>>
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px; margin-top: 0.25rem;">
                    Sign In to Dashboard
                </button>
            </form>

            <div style="margin-top: 1.25rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                Don't have an account yet? <a href="index.php?controller=auth&action=register" style="font-weight: 700;">Create an Account</a>
            </div>
        </div>
    </div>

</body>
</html>
