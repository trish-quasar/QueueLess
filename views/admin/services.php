<?php
$pageTitle = 'Manage Services';
require 'views/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Manage Services</h1>
        <p>Create, edit, activate or deactivate service definitions.</p>
    </div>
</div>

<div class="two-col admin">

    <section class="card">
        <h2>Add service</h2>

        <form method="POST" action="index.php?action=save_service">
            <label>Name</label>
            <input type="text" name="name">

            <label>Token prefix</label>
            <input type="text" name="token_prefix" maxlength="5">

            <label>Description</label>
            <textarea name="description" rows="4"></textarea>

            <button class="button">Add service</button>
        </form>
    </section>

    <section class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Prefix</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($service = $services->fetch_assoc()): ?>
                    <?php $form_id = 'serviceForm' . $service['service_id']; ?>

                    <tr>
                        <td>
                            <form method="POST" action="index.php?action=save_service" id="<?php echo $form_id; ?>">
                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">

                                <input class="table-input" name="name"
                                       value="<?php echo htmlspecialchars($service['name']); ?>">

                                <textarea class="table-textarea" name="description"><?php echo htmlspecialchars($service['description']); ?></textarea>
                            </form>
                        </td>

                        <td>
                            <input class="table-input small-input" name="token_prefix"
                                   value="<?php echo htmlspecialchars($service['token_prefix']); ?>"
                                   form="<?php echo $form_id; ?>">
                        </td>

                        <td>
                            <select name="status" form="<?php echo $form_id; ?>">
                                <option <?php echo $service['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                <option <?php echo $service['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </td>

                        <td>
                            <button class="text-button" form="<?php echo $form_id; ?>">Save</button>

                            <form method="POST" action="index.php?action=delete_service"
                                  onsubmit="return confirm('Remove this service?');">
                                <input type="hidden" name="service_id" value="<?php echo $service['service_id']; ?>">
                                <button class="text-button danger-text">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

</div>

<?php require 'views/includes/footer.php'; ?>
