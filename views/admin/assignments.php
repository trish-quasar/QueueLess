<?php
$pageTitle = 'Staff Assignments';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Staff Assignments
        </h1>
        <p>
            Assign one active counter to each service staff member.
        </p>
    </div>
</div>
<div class="two-col admin">
    <section class="card">
        <h2>
            Assign staff
        </h2>
        <form method="POST" action="index.php?action=assign_staff">
            <label>
                Staff member
            </label>
            <select name="staff_id">
                <option value="">
                    Select staff
                </option>
                <?php while ($u = $staff->fetch_assoc()): ?>
                    <option value="<?php echo $u['user_id']; ?>">
                        <?php echo htmlspecialchars($u['full_name']); ?>
                        (
                        <?php echo htmlspecialchars($u['email']); ?>
                        )
                    </option>
                <?php endwhile; ?>
            </select>
            <label>
                Counter
            </label>
            <select name="counter_id">
                <option value="">
                    Select counter
                </option>
                <?php while ($c = $counters->fetch_assoc()): ?>
                    <option value="<?php echo $c['counter_id']; ?>">
                        <?php echo htmlspecialchars($c['service_name'].' - '.$c['counter_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button class="button">
                Assign
            </button>
        </form>
    </section>
    <section class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>
                        Staff
                    </th>
                    <th>
                        Service
                    </th>
                    <th>
                        Counter
                    </th>
                    <th>
                        Status
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php while ($a = $assignments->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($a['staff_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($a['service_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($a['counter_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($a['status']); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>
<?php require 'views/includes/footer.php'; ?>
