<?php
$pageTitle = 'Queue History';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Queue History
        </h1>
        <p>
            Your previous and current queue records.
        </p>
    </div>
</div>
<div class="card table-wrap">
    <table>
        <thead>
            <tr>
                <th>
                    Token
                </th>
                <th>
                    Service
                </th>
                <th>
                    Date
                </th>
                <th>
                    Status
                </th>
                <th>
                    Counter
                </th>
                <th>
                    Joined
                </th>
            </tr>
        </thead>
        <tbody>
            <?php while ($h = $history->fetch_assoc()): ?>
                <tr>
                    <td>
                        <b>
                            <?php echo htmlspecialchars($h['token_number']); ?>
                        </b>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($h['service_name']); ?>
                    </td>
                    <td>
                        <?php echo $h['queue_date']; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($h['status']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($h['counter_name']??'-'); ?>
                    </td>
                    <td>
                        <?php echo date('g:i A',strtotime($h['joined_at'])); ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require 'views/includes/footer.php'; ?>
