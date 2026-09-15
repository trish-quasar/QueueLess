<?php
$role = $_SESSION['role'] ?? '';
$full_name = $_SESSION['full_name'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - QueueLess' : 'QueueLess'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="topbar">
    <div class="container topbar-inner">
        <a class="brand" href="index.php?action=dashboard">QueueLess</a>

        <nav>
            <?php if ($role === 'Customer'): ?>
                <a href="index.php?action=dashboard">Dashboard</a>
                <a href="index.php?action=services">Services</a>
                <a href="index.php?action=appointments">Appointments</a>
                <a href="index.php?action=history">History</a>

            <?php elseif ($role === 'Staff'): ?>
                <a href="index.php?action=dashboard">My Counter</a>
                <a href="index.php?action=history">Daily History</a>

            <?php elseif ($role === 'Admin'): ?>
                <a href="index.php?action=dashboard">Dashboard</a>
                <a href="index.php?action=admin_services">Services</a>
                <a href="index.php?action=admin_counters">Counters</a>
                <a href="index.php?action=admin_assignments">Assignments</a>
                <a href="index.php?action=admin_users">Users</a>
                <a href="index.php?action=admin_reports">Reports</a>
            <?php endif; ?>
        </nav>

        <div class="user-menu">
            <a href="index.php?action=profile"><?php echo htmlspecialchars($full_name); ?></a>
            <a class="logout" href="index.php?action=logout">Logout</a>
        </div>
    </div>
</header>

<main class="container main-area">
    <?php require 'views/includes/messages.php'; ?>
