<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set new password - QueueLess</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>
<body class="auth-body">
<div class="auth-shell">
    <aside class="auth-showcase">
        <a class="brand" href="index.php"><span class="brand-mark">Q</span><span>QueueLess</span></a>
        <div class="auth-showcase-copy">
            <span>Final step</span>
            <h2>Choose a new password.</h2>
            <p>After the password is updated, you can return to sign in and continue using QueueLess.</p>
        </div>
        <div class="auth-showcase-note">Use a password you do not share with other accounts.</div>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <div class="auth-kicker">Password reset</div>
            <h1>Set a new password</h1>
            <p class="auth-intro">Enter the new password twice to confirm it.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $e): ?>
                        <p><?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label for="password">New password</label>
                <input type="password" id="password" name="password" autocomplete="new-password">
                <p class="help">Use at least 8 characters with uppercase, lowercase, a number and a symbol.</p>

                <label for="confirm_password">Confirm new password</label>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">

                <button class="button full" type="submit">Update password</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
