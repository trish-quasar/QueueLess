<?php
$pageTitle = 'Customer Dashboard';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Customer Dashboard
        </h1>
        <p>
            Welcome back,
            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            .
        </p>
    </div>
    <a class="button" href="index.php?action=services">
        Join a queue
    </a>
</div>
<div class="dashboard-grid">
    <section class="card token-card">
        <h2>
            Your current token
        </h2>
        <div id="liveToken" data-has-token="<?php echo $live ? '1' : '0'; ?>">
            <?php if ($live): ?>
                <div class="big-token">
                    <?php echo htmlspecialchars($live['token_number']); ?>
                </div>
                <div class="status-pill" id="tokenStatus">
                    <?php echo htmlspecialchars($live['status']); ?>
                </div>
                <div class="mini-grid">
                    <div>
                        <span>
                            Service
                        </span>
                        <b>
                            <?php echo htmlspecialchars($live['service_name']); ?>
                        </b>
                    </div>
                    <div>
                        <span>
                            People ahead
                        </span>
                        <b id="peopleAhead">
                            <?php echo $live['people_ahead']; ?>
                        </b>
                    </div>
                    <div>
                        <span>
                            Currently serving
                        </span>
                        <b id="currentToken">
                            <?php echo htmlspecialchars($live['currently_serving']); ?>
                        </b>
                    </div>
                    <div>
                        <span>
                            Your counter
                        </span>
                        <b id="yourCounter">
                            <?php echo htmlspecialchars($live['counter_name']??'-'); ?>
                        </b>
                    </div>
                </div>
                <?php if ($live['status']==='Waiting'): ?>
                    <form method="POST" action="index.php?action=cancel_token" onsubmit="return confirm('Cancel this token?');">
                        <input type="hidden" name="token_id" value="<?php echo $live['token_id']; ?>">
                        <button class="button danger small">
                            Cancel token
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>
                        You do not have an active queue token.
                    </p>
                    <a href="index.php?action=services">
                        Browse services
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <section class="card">
        <h2>
            Available services
        </h2>
        <div class="list">
            <?php while ($s = $services->fetch_assoc()): ?>
                <div class="list-row">
                    <div>
                        <b>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </b>
                        <small>
                            <?php echo htmlspecialchars($s['description']); ?>
                        </small>
                    </div>
                    <span>
                        <?php echo $s['waiting_count']; ?>
                        waiting
                    </span>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</div>
<script src="assets/js/customer.js"></script>
<?php require 'views/includes/footer.php'; ?>
