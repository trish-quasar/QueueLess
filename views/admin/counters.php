<?php
$pageTitle = 'Manage Counters';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Manage Counters
        </h1>
        <p>
            Create service counters and control their configuration.
        </p>
    </div>
</div>
<div class="two-col admin">
    <section class="card">
        <h2>
            Add counter
        </h2>
        <form method="POST" action="index.php?action=save_counter">
            <label>
                Service
            </label>
            <select name="service_id">
                <option value="">
                    Select service
                </option>
                <?php while ($s = $services->fetch_assoc()): ?>
                    <option value="<?php echo $s['service_id']; ?>">
                        <?php echo htmlspecialchars($s['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label>
                Counter name
            </label>
            <input type="text" name="counter_name">
            <button class="button">
                Add counter
            </button>
        </form>
    </section>
    <section class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>
                        Counter
                    </th>
                    <th>
                        Service
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $counters->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($c['counter_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($c['service_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($c['status']); ?>
                        </td>
                        <td>
                            <form method="POST" action="index.php?action=save_counter" class="inline-actions">
                                <input type="hidden" name="counter_id" value="<?php echo $c['counter_id']; ?>">
                                <input type="hidden" name="service_id" value="<?php echo $c['service_id']; ?>">
                                <input type="hidden" name="counter_name" value="<?php echo htmlspecialchars($c['counter_name']); ?>">
                                <select name="status">
                                    <option <?php echo $c['status']==='Open'?'selected':''; ?>>
                                        Open
                                    </option>
                                    <option <?php echo $c['status']==='Closed'?'selected':''; ?>>
                                        Closed
                                    </option>
                                </select>
                                <button class="text-button">
                                    Save
                                </button>
                            </form>
                            <form method="POST" action="index.php?action=delete_counter" onsubmit="return confirm('Remove this counter?');">
                                <input type="hidden" name="counter_id" value="<?php echo $c['counter_id']; ?>">
                                <button class="text-button danger-text">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>
<?php require 'views/includes/footer.php'; ?>
