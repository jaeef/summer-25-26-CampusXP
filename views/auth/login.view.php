<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in – CampusXP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-box">
        <div class="auth-brand">
            <div class="logo-badge">C</div>
            <div class="brand-text">CampusXP</div>
        </div>

        <h1 class="auth-title">Sign in</h1>
        <p class="auth-sub">Opportunities and recruitment at AIUB.</p>

        <?php if ($flash): ?>
            <div class="flash-alert flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="alert">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <div class="glass-card" style="padding: 1.5rem;">
            <form action="index.php?controller=auth&action=login" method="POST">
                <div class="form-group">
                    <label class="form-label" for="login-email">Email</label>
                    <input type="email" name="email" id="login-email" class="form-control" autocomplete="email" placeholder="name@aiub.edu" value="<?= htmlspecialchars($remembered_email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="login-password">Password</label>
                    <input type="password" name="password" id="login-password" class="form-control" autocomplete="current-password" required>
                </div>

                <label class="check-label mb-2">
                    <input type="checkbox" name="remember_me" value="1" <?= !empty($remembered_email) ? 'checked' : '' ?>>
                    <span>Remember me</span>
                </label>

                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
            </form>
        </div>

        <div class="auth-footer">
            New to CampusXP? <a href="index.php?controller=auth&action=register" class="fw-600">Create an account</a>
        </div>
    </div>

</body>
</html>
