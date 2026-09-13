<?php
// ==============================================================================
// CampusXP - Views Layout Header (MVC Presentation Layout)
// ==============================================================================
$auth_user = get_auth_user();
$flash = get_flash();
$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' – ' : '' ?>CampusXP</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Top Sticky Header -->
    <header>
        <div class="nav-container">
            <a href="index.php" class="brand-logo">
                <div class="logo-badge">C</div>
                <div class="brand-text">CampusXP</div>
            </a>

            <?php if ($auth_user): ?>
                <!-- Role-Specific MVC Navigation Menu -->
                <nav class="nav-links">
                    <?php if ($auth_user['role'] === 'student'): ?>
                        <a href="index.php?controller=student&action=dashboard" class="nav-link <?= ($controller === 'student' && $action === 'dashboard') ? 'active' : '' ?>">Radar Feed</a>
                        <a href="index.php?controller=student&action=profile" class="nav-link <?= ($controller === 'student' && $action === 'profile') ? 'active' : '' ?>">Profile & SOP Vault</a>
                        <a href="index.php?controller=student&action=applications" class="nav-link <?= ($controller === 'student' && $action === 'applications') ? 'active' : '' ?>">My Applications</a>
                    <?php elseif ($auth_user['role'] === 'club_exec'): ?>
                        <a href="index.php?controller=executive&action=dashboard" class="nav-link <?= ($controller === 'executive' && $action === 'dashboard') ? 'active' : '' ?>">Overview</a>
                        <a href="index.php?controller=executive&action=kanban" class="nav-link <?= ($controller === 'executive' && $action === 'kanban') ? 'active' : '' ?>">Kanban Pipeline</a>
                        <a href="index.php?controller=executive&action=scheduler" class="nav-link <?= ($controller === 'executive' && $action === 'scheduler') ? 'active' : '' ?>">Slot Scheduler</a>
                        <a href="index.php?controller=executive&action=logistics" class="nav-link <?= ($controller === 'executive' && $action === 'logistics') ? 'active' : '' ?>">Logistics</a>
                        <a href="index.php?controller=executive&action=analytics" class="nav-link <?= ($controller === 'executive' && $action === 'analytics') ? 'active' : '' ?>">Analytics</a>
                    <?php elseif ($auth_user['role'] === 'recruiter'): ?>
                        <a href="index.php?controller=recruiter&action=dashboard" class="nav-link <?= ($controller === 'recruiter' && $action === 'dashboard') ? 'active' : '' ?>">Job Circulars</a>
                        <a href="index.php?controller=recruiter&action=kanban" class="nav-link <?= ($controller === 'recruiter' && $action === 'kanban') ? 'active' : '' ?>">Applicant Kanban</a>
                        <a href="index.php?controller=recruiter&action=create_drive" class="nav-link <?= ($controller === 'recruiter' && $action === 'create_drive') ? 'active' : '' ?>">Post Job</a>
                        <a href="index.php?controller=recruiter&action=talent_search" class="nav-link <?= ($controller === 'recruiter' && $action === 'talent_search') ? 'active' : '' ?>">Talent Search</a>
                        <a href="index.php?controller=recruiter&action=shortlists" class="nav-link <?= ($controller === 'recruiter' && $action === 'shortlists') ? 'active' : '' ?>">Candidate Buckets</a>
                        <a href="index.php?controller=recruiter&action=outreach" class="nav-link <?= ($controller === 'recruiter' && $action === 'outreach') ? 'active' : '' ?>">Outreach Logs</a>
                        <a href="index.php?controller=recruiter&action=logistics" class="nav-link <?= ($controller === 'recruiter' && $action === 'logistics') ? 'active' : '' ?>">Campus Logistics</a>
                    <?php elseif ($auth_user['role'] === 'admin'): ?>
                        <a href="index.php?controller=admin&action=dashboard" class="nav-link <?= ($controller === 'admin' && $action === 'dashboard') ? 'active' : '' ?>">Admin Panel</a>
                        <a href="index.php?controller=admin&action=moderation" class="nav-link <?= ($controller === 'admin' && $action === 'moderation') ? 'active' : '' ?>">Event Moderation</a>
                        <a href="index.php?controller=admin&action=booths" class="nav-link <?= ($controller === 'admin' && $action === 'booths') ? 'active' : '' ?>">Venue Booths</a>
                        <a href="index.php?controller=admin&action=logistics" class="nav-link <?= ($controller === 'admin' && $action === 'logistics') ? 'active' : '' ?>">Logistics Dispatch</a>
                    <?php endif; ?>
                </nav>

                <!-- Profile Badge & Logout -->
                <div class="user-profile-badge">
                    <div class="user-role-avatar avatar-<?= $auth_user['role'] ?>">
                        <?= strtoupper(substr($auth_user['full_name'], 0, 1)) ?>
                    </div>
                    <span class="badge-tag badge-<?= $auth_user['role'] ?>"><?= ucfirst(str_replace('_', ' ', $auth_user['role'])) ?></span>
                    <span style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);"><?= htmlspecialchars($auth_user['full_name']) ?></span>
                    <a href="index.php?controller=auth&action=logout" class="btn btn-outline btn-sm">Sign Out</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="index.php?controller=auth&action=login" class="btn btn-outline btn-sm">Sign In</a>
                    <a href="index.php?controller=auth&action=register" class="btn btn-primary btn-sm">Join CampusXP</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Container -->
    <main class="main-container">
        <?php if ($flash): ?>
            <div class="flash-alert flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>
