<?php
$role = $_SESSION['role'] ?? '';
$full_name = $_SESSION['full_name'] ?? '';
$currentAction = $_GET['action'] ?? 'dashboard';
$display_name = $full_name;
if ($role === 'Staff' && strpos($display_name, 'Demo ') === 0) {
    $display_name = substr($display_name, 5);
}

function navActive($actions, $currentAction) {
    if (!is_array($actions)) {
        $actions = [$actions];
    }

    return in_array($currentAction, $actions, true) ? 'active' : '';
}

function navIcon($name) {
    $icons = [
        'dashboard' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>',
        'services' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16M4 12h16M4 18h10"/><circle cx="18" cy="18" r="2.5"/></svg>',
        'appointments' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>',
        'history' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/></svg>',
        'counter' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V9l8-5 8 5v11"/><path d="M8 20v-6h8v6M3 20h18"/></svg>',
        'counters' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="8" height="6" rx="2"/><rect x="15" y="14" width="6" height="6" rx="2"/></svg>',
        'assignments' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="3"/><path d="M3 20v-2a5 5 0 0 1 10 0v2M16 7h5M18.5 4.5v5M15 15h6"/></svg>',
        'users' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M16 5.5a3 3 0 0 1 0 5.8M18 15a5 5 0 0 1 3 4.6V20"/></svg>',
        'reports' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>'
    ];

    return $icons[$name] ?? $icons['dashboard'];
}

$initial = $display_name !== '' ? strtoupper(substr($display_name, 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - QueueLess' : 'QueueLess'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=10">
</head>
<body class="app-body">
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php?action=dashboard">
            <span class="brand-mark">Q</span>
            <span>QueueLess</span>
        </a>

        <div class="sidebar-role"><?php echo htmlspecialchars($role); ?> workspace</div>

        <nav class="side-nav">
            <?php if ($role === 'Customer'): ?>
                <a class="<?php echo navActive('dashboard', $currentAction); ?>" href="index.php?action=dashboard">
                    <?php echo navIcon('dashboard'); ?> <span>Dashboard</span>
                </a>
                <a class="<?php echo navActive(['services', 'join_queue'], $currentAction); ?>" href="index.php?action=services">
                    <?php echo navIcon('services'); ?> <span>Services</span>
                </a>
                <a class="<?php echo navActive(['appointments', 'book_appointment'], $currentAction); ?>" href="index.php?action=appointments">
                    <?php echo navIcon('appointments'); ?> <span>Appointments</span>
                </a>
                <a class="<?php echo navActive('history', $currentAction); ?>" href="index.php?action=history">
                    <?php echo navIcon('history'); ?> <span>Queue History</span>
                </a>

            <?php elseif ($role === 'Staff'): ?>
                <a class="<?php echo navActive(['dashboard', 'counter_status', 'call_next', 'start_serving', 'recall_token', 'skip_token', 'complete_token'], $currentAction); ?>" href="index.php?action=dashboard">
                    <?php echo navIcon('counter'); ?> <span>My Counter</span>
                </a>
                <a class="<?php echo navActive('history', $currentAction); ?>" href="index.php?action=history">
                    <?php echo navIcon('history'); ?> <span>Daily History</span>
                </a>

            <?php elseif ($role === 'Admin'): ?>
                <a class="<?php echo navActive('dashboard', $currentAction); ?>" href="index.php?action=dashboard">
                    <?php echo navIcon('dashboard'); ?> <span>Dashboard</span>
                </a>
                <a class="<?php echo navActive(['admin_services', 'save_service'], $currentAction); ?>" href="index.php?action=admin_services">
                    <?php echo navIcon('services'); ?> <span>Services</span>
                </a>
                <a class="<?php echo navActive(['admin_counters', 'save_counter'], $currentAction); ?>" href="index.php?action=admin_counters">
                    <?php echo navIcon('counters'); ?> <span>Counters</span>
                </a>
                <a class="<?php echo navActive(['admin_assignments', 'assign_staff'], $currentAction); ?>" href="index.php?action=admin_assignments">
                    <?php echo navIcon('assignments'); ?> <span>Assignments</span>
                </a>
                <a class="<?php echo navActive(['admin_users', 'create_staff'], $currentAction); ?>" href="index.php?action=admin_users">
                    <?php echo navIcon('users'); ?> <span>Users</span>
                </a>
                <a class="<?php echo navActive('admin_reports', $currentAction); ?>" href="index.php?action=admin_reports">
                    <?php echo navIcon('reports'); ?> <span>Reports</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-user">
            <div class="user-card">
                <div class="user-main">
                    <div class="user-avatar-mini"><?php echo htmlspecialchars($initial); ?></div>
                    <div class="user-meta">
                        <strong><?php echo htmlspecialchars($display_name); ?></strong>
                        <span><?php echo htmlspecialchars($role); ?></span>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="index.php?action=profile">Profile</a>
                    <a href="index.php?action=logout">Logout</a>
                </div>
            </div>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-panel">
        <header class="mobile-topbar">
            <button class="menu-button" id="menuButton" type="button" aria-label="Open navigation"><span></span></button>
            <a class="brand dark" href="index.php?action=dashboard"><span class="brand-mark">Q</span><span>QueueLess</span></a>
            <a href="index.php?action=profile" class="user-avatar-mini"><?php echo htmlspecialchars($initial); ?></a>
        </header>

        <main class="main-area">
            <div class="content-wrap">
                <?php require 'views/includes/messages.php'; ?>
