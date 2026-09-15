<?php
$pageTitle = 'Daily History';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Daily Service History
        </h1>
        <p>
            Completed and skipped tokens handled by your assigned counter today.
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
                    Customer
                </th>
                <th>
                    Status
                </th>
                <th>
                    Called
                </th>
                <th>
                    Completed
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($history):while($h=$history->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($h['token_number']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($h['full_name']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($h['status']); ?>
                    </td>
                    <td>
                        <?php echo $h['called_at']?date('g:i A',strtotime($h['called_at'])):'-'; ?>
                    </td>
                    <td>
                        <?php echo $h['completed_at']?date('g:i A',strtotime($h['completed_at'])):'-'; ?>
                    </td>
                </tr>
            <?php endwhile;endif; ?>
        </tbody>
    </table>
</div>
<?php require 'views/includes/footer.php'; ?>
