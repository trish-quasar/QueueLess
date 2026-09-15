<?php
$pageTitle = 'Admin Dashboard';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Administrator Dashboard
        </h1>
        <p>
            System overview for today.
        </p>
    </div>
</div>
<div class="stat-grid">
    <div class="stat-card">
        <span>
            Total users
        </span>
        <b>
            <?php echo $stats['users']; ?>
        </b>
    </div>
    <div class="stat-card">
        <span>
            Waiting now
        </span>
        <b>
            <?php echo $stats['waiting']; ?>
        </b>
    </div>
    <div class="stat-card">
        <span>
            Served today
        </span>
        <b>
            <?php echo $stats['served']; ?>
        </b>
    </div>
    <div class="stat-card">
        <span>
            Open counters
        </span>
        <b>
            <?php echo $stats['open_counters']; ?>
        </b>
    </div>
</div>
<div class="card table-wrap">
    <h2>
        Live service overview
    </h2>
    <table>
        <thead>
            <tr>
                <th>
                    Service
                </th>
                <th>
                    Waiting
                </th>
                <th>
                    Currently serving
                </th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $live->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($r['name']); ?>
                    </td>
                    <td>
                        <?php echo $r['waiting']; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($r['current_token']??'-'); ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require 'views/includes/footer.php'; ?>
