<?php
// ==============================================================================
// CampusXP - Views Layout Header (sidebar app shell)
// ==============================================================================
$auth_user = get_auth_user();
$flash = get_flash();
$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';
if ($action === '') $action = 'dashboard';

// Role => [controller, role label, [action => nav label]]
$nav_map = [
    'student' => ['student', 'Student', [
        'dashboard'    => 'Opportunities',
        'applications' => 'Applications',
        'profile'      => 'Profile',
    ]],
    'club_exec' => ['executive', 'Club executive', [
        'dashboard' => 'Overview',
        'kanban'    => 'Applicants',
        'scheduler' => 'Interview slots',
        'logistics' => 'Equipment',
        'analytics' => 'Analytics',
    ]],
    'recruiter' => ['recruiter', 'Recruiter', [
        'dashboard'     => 'Job posts',
        'create_drive'  => 'Post a job',
        'kanban'        => 'Applicants',
        'talent_search' => 'Talent search',
        'shortlists'    => 'Shortlists',
        'outreach'      => 'Outreach',
        'logistics'     => 'Equipment',
    ]],
    'admin' => ['admin', 'Admin', [
        'dashboard'  => 'Overview',
        'moderation' => 'Approvals',
        'booths'     => 'Booths',
        'logistics'  => 'Equipment',
    ]],
];

$nav = $auth_user ? ($nav_map[$auth_user['role']] ?? null) : null;
if (!isset($page_title) && $nav && $controller === $nav[0]) {
    $page_title = $nav[2][$action] ?? null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' – ' : '' ?>CampusXP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php if ($nav): ?>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a href="index.php" class="brand-logo">
            <div class="logo-badge">C</div>
            <div class="brand-text">CampusXP</div>
        </a>

        <div class="nav-group-label"><?= $nav[1] ?></div>
        <nav class="side-nav" aria-label="Main">
            <?php foreach ($nav[2] as $nav_action => $nav_label): ?>
                <?php $is_active = $controller === $nav[0] && $action === $nav_action; ?>
                <a href="index.php?controller=<?= $nav[0] ?>&action=<?= $nav_action ?>"
                   class="side-link<?= $is_active ? ' active' : '' ?>"<?= $is_active ? ' aria-current="page"' : '' ?>><?= $nav_label ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-user">
            <div class="user-role-avatar"><?= strtoupper(substr($auth_user['full_name'], 0, 1)) ?></div>
            <div style="min-width: 0;">
                <div class="sidebar-user-name"><?= htmlspecialchars($auth_user['full_name']) ?></div>
                <div class="sidebar-user-role"><?= $nav[1] ?></div>
            </div>
            <a href="index.php?controller=auth&action=logout" class="sidebar-signout">Sign out</a>
        </div>
    </aside>
    <div class="sidebar-backdrop" onclick="document.body.classList.remove('nav-open')"></div>

    <div class="app-main">
        <div class="topbar">
            <a href="index.php" class="brand-logo">
                <div class="logo-badge">C</div>
                <div class="brand-text">CampusXP</div>
            </a>
            <button type="button" class="nav-toggle" aria-controls="sidebar" onclick="document.body.classList.toggle('nav-open')">Menu</button>
        </div>
<?php endif; ?>

    <main class="main-container">
        <?php if ($flash): ?>
            <div class="flash-alert flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="status">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>
