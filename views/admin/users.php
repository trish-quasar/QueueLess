<?php
$pageTitle = 'Manage Users';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Manage Users
        </h1>
        <p>
            Create staff/admin accounts and control account status.
        </p>
    </div>
</div>
<div class="two-col admin">
    <section class="card">
        <h2>
            Create staff or admin
        </h2>
        <form method="POST" action="index.php?action=create_staff">
            <label>
                Full name
            </label>
            <input type="text" name="full_name">
            <label>
                Email
            </label>
            <input type="text" name="email">
            <label>
                Phone
            </label>
            <input type="text" name="phone" maxlength="11">
            <label>
                Temporary password
            </label>
            <input type="password" name="password">
            <label>
                Role
            </label>
            <select name="role">
                <option value="Staff">
                    Service Staff
                </option>
                <option value="Admin">
                    Administrator
                </option>
            </select>
            <button class="button">
                Create account
            </button>
        </form>
        <p class="help">
            Default reset answer is
            <b>
                change-me
            </b>
            . The user should update their password and profile after first login.
        </p>
    </section>
    <section class="card table-wrap">
        <table>
            <thead>
                <tr>
                    <th>
                        User
                    </th>
                    <th>
                        Role
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
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <b>
                                <?php echo htmlspecialchars($u['full_name']); ?>
                            </b>
                            <small>
                                <?php echo htmlspecialchars($u['email']); ?>
                            </small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($u['role']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($u['status']); ?>
                        </td>
                        <td>
                            <?php if ($u['user_id']!=$_SESSION['user_id']): ?>
                                <form method="POST" action="index.php?action=user_status">
                                    <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $u['status']==='Active'?'Inactive':'Active'; ?>">
                                    <button class="text-button">
                                        <?php echo $u['status']==='Active'?'Deactivate':'Activate'; ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="muted">
                                    Current admin
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>
<?php require 'views/includes/footer.php'; ?>
