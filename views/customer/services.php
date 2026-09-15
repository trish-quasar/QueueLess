<?php
$pageTitle = 'Services';
require 'views/includes/header.php';

$last_service = (int)($_COOKIE['last_service'] ?? 0);
?>

<div class="page-head">
    <div>
        <h1>Services</h1>
        <p>Choose one service to receive a digital token.</p>
    </div>
</div>

<div class="service-grid">
    <?php while ($service = $services->fetch_assoc()): ?>

        <article class="card service-card <?php echo $last_service === $service['service_id'] ? 'remembered' : ''; ?>">
            <div>
                <h2><?php echo htmlspecialchars($service['name']); ?></h2>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
            </div>

            <div class="service-meta">
                <span>
                    <b class="service-waiting" data-service-id="<?php echo $service['service_id']; ?>"><?php echo $service['waiting_count']; ?></b> waiting
                </span>

                <span>
                    Prefix <b><?php echo htmlspecialchars($service['token_prefix']); ?></b>
                </span>
            </div>

            <form method="POST" action="index.php?action=join_queue">
                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                <button class="button full">Join queue</button>
            </form>
        </article>

    <?php endwhile; ?>
</div>

<script src="assets/js/customer.js"></script>
<?php require 'views/includes/footer.php'; ?>
