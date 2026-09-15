<?php
$pageTitle = 'Reports';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Operational Reports
        </h1>
        <p>
            Basic queue performance for today.
        </p>
    </div>
</div>
<div class="stat-grid three">
    <div class="stat-card">
        <span>
            Customers served
        </span>
        <b>
            <?php echo $summary['served']??0; ?>
        </b>
    </div>
    <div class="stat-card">
        <span>
            Currently waiting
        </span>
        <b>
            <?php echo $summary['waiting']??0; ?>
        </b>
    </div>
    <div class="stat-card">
        <span>
            Average wait
        </span>
        <b>
            <?php echo ($summary['avg_wait']??0) . ' min'; ?>
        </b>
    </div>
</div>
<div class="card table-wrap">
    <h2>
        Service activity
    </h2>
    <table>
        <thead>
            <tr>
                <th>
                    Service
                </th>
                <th>
                    Total tokens
                </th>
                <th>
                    Completed
                </th>
                <th>
                    Average wait
                </th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $serviceReport->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($r['name']); ?>
                    </td>
                    <td>
                        <?php echo $r['total_tokens']; ?>
                    </td>
                    <td>
                        <?php echo $r['completed']; ?>
                    </td>
                    <td>
                        <?php echo ($r['avg_wait']??0).' min'; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require 'views/includes/footer.php'; ?>
