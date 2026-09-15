<?php
$pageTitle = 'Profile';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            My Profile
        </h1>
        <p>
            View and update your account information.
        </p>
    </div>
</div>
<div class="two-col">
    <section class="card">
        <div class="profile-head">
            <?php if ($user['profile_photo']): ?>
                <img src="uploads/profile/<?php echo htmlspecialchars($user['profile_photo']); ?>" class="avatar" alt="Profile photo">
            <?php else: ?>
                <div class="avatar placeholder">
                    <?php echo strtoupper(substr($user['full_name'],0,1)); ?>
                </div>
            <?php endif; ?>
            <div>
                <h2>
                    <?php echo htmlspecialchars($user['full_name']); ?>
                </h2>
                <p>
                    @<?php echo htmlspecialchars($user['username']); ?>
                    &middot;
                    <?php echo htmlspecialchars($user['role']); ?>
                    &middot;
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>
            </div>
        </div>
        <form method="POST" action="index.php?action=update_profile" enctype="multipart/form-data">
            <label>
                Full name
            </label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            <label>
                Phone
            </label>
            <input type="text" name="phone" maxlength="11" value="<?php echo htmlspecialchars($user['phone']); ?>">
            <label>
                New profile photo (optional)
            </label>
            <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png">
            <button class="button">
                Save profile
            </button>
        </form>
    </section>
    <section class="card">
        <h2>
            Change password
        </h2>
        <form method="POST" action="index.php?action=change_password">
            <label>
                Current password
            </label>
            <input type="password" name="current_password">
            <label>
                New password
            </label>
            <input type="password" name="new_password">
            <p class="help">Use at least 8 characters with uppercase, lowercase, a number and a symbol.</p>
            <label>
                Confirm new password
            </label>
            <input type="password" name="confirm_password">
            <button class="button">
                Change password
            </button>
        </form>
        <hr>
        <h2>
            Deactivate account
        </h2>
        <p class="muted">
            Your account will become inactive and you will be logged out. Your previous queue records will remain available.
        </p>
        <form method="POST" action="index.php?action=deactivate_account" onsubmit="return confirm('Deactivate your account?');">
            <button class="button danger">
                Deactivate account
            </button>
        </form>
    </section>
</div>
<?php require 'views/includes/footer.php'; ?>
