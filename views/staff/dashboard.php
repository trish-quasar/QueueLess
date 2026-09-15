<?php
$pageTitle = 'Staff Dashboard';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Service Staff Dashboard
        </h1>
        <p>
            Operate your assigned queue and counter.
        </p>
    </div>
</div>
<?php if (!$assignment): ?>
    <div class="card empty-state">
        <h2>
            No active assignment
        </h2>
        <p>
            An administrator must assign you to a counter.
        </p>
    </div>
<?php else: ?>
    <div class="staff-layout">
        <section class="card">
            <div class="card-head">
                <div>
                    <h2>
                        <?php echo htmlspecialchars($assignment['counter_name']); ?>
                    </h2>
                    <p>
                        <?php echo htmlspecialchars($assignment['service_name']); ?>
                    </p>
                </div>
                <span class="status-pill <?php echo $assignment['counter_status']==='Open'?'open':'closed'; ?>" id="counterStatus">
                    <?php echo $assignment['counter_status']; ?>
                </span>
            </div>
            <form method="POST" action="index.php?action=counter_status" class="inline-actions">
                <input type="hidden" name="status" value="<?php echo $assignment['counter_status']==='Open'?'Closed':'Open'; ?>">
                <button class="button <?php echo $assignment['counter_status']==='Open'?'secondary':''; ?>">
                    <?php echo $assignment['counter_status']==='Open'?'Close Counter':'Open Counter'; ?>
                </button>
            </form>
            <hr>
            <h3>
                Current token
            </h3>
            <div id="staffCurrent">
                <?php if ($current): ?>
                    <div class="big-token">
                        <?php echo htmlspecialchars($current['token_number']); ?>
                    </div>
                    <p>
                        <?php echo htmlspecialchars($current['full_name']); ?>
                        &middot;
                        <?php echo htmlspecialchars($current['status']); ?>
                    </p>
                <?php else: ?>
                    <div class="empty-state compact">
                        No token is currently called.
                    </div>
                <?php endif; ?>
            </div>
            <div class="action-grid">
                <form method="POST" action="index.php?action=call_next">
                    <button class="button">
                        Call Next
                    </button>
                </form>
                <form method="POST" action="index.php?action=start_serving">
                    <button class="button secondary">
                        Start Serving
                    </button>
                </form>
                <form method="POST" action="index.php?action=recall_token">
                    <button class="button secondary">
                        Recall
                    </button>
                </form>
                <form method="POST" action="index.php?action=skip_token">
                    <button class="button warning">
                        Skip
                    </button>
                </form>
                <form method="POST" action="index.php?action=complete_token">
                    <button class="button success-btn">
                        Complete
                    </button>
                </form>
            </div>
        </section>
        <section class="card">
            <h2>
                Waiting queue
            </h2>
            <div id="staffWaiting" class="list">
                <?php if ($waiting): while($q=$waiting->fetch_assoc()): ?>
                    <div class="list-row">
                        <div>
                            <b>
                                <?php echo htmlspecialchars($q['token_number']); ?>
                            </b>
                            <small>
                                <?php echo htmlspecialchars($q['full_name']); ?>
                            </small>
                        </div>
                        <span>
                            Waiting
                        </span>
                    </div>
                <?php endwhile; endif; ?>
            </div>
        </section>
    </div>
    <script src="assets/js/staff.js"></script>
<?php endif; ?>
<?php require 'views/includes/footer.php'; ?>
