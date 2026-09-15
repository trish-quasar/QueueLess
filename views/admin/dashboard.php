<?php
$pageTitle = 'Admin Dashboard';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>Administrator Dashboard</h1>
        <p>Today’s queue activity, counters and service workload at a glance.</p>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <span>Total users</span>
        <b id="adminUsers"><?php echo $stats['users']; ?></b>
    </div>
    <div class="stat-card">
        <span>Waiting now</span>
        <b id="adminWaiting"><?php echo $stats['waiting']; ?></b>
    </div>
    <div class="stat-card">
        <span>Served today</span>
        <b id="adminServed"><?php echo $stats['served']; ?></b>
    </div>
    <div class="stat-card">
        <span>Open counters</span>
        <b id="adminOpenCounters"><?php echo $stats['open_counters']; ?></b>
    </div>
</div>

<div class="card table-wrap">
    <div class="card-head">
        <div>
            <h2>Live service overview</h2>
            <p>Updates automatically while this page is open.</p>
        </div>
        <span class="status-pill open">Live</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Waiting</th>
                <th>Currently serving</th>
            </tr>
        </thead>
        <tbody id="adminServiceRows">
            <?php while ($r = $live->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                    <td><?php echo $r['waiting']; ?></td>
                    <td><?php echo htmlspecialchars($r['current_token'] ?? '-'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="assets/js/admin.js"></script>
<?php require 'views/includes/footer.php'; ?>
