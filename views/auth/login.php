<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in - QueueLess</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>
<body class="auth-body">
<div class="auth-shell">
    <aside class="auth-showcase">
        <a class="brand" href="index.php"><span class="brand-mark">Q</span><span>QueueLess</span></a>
        <div class="auth-showcase-copy">
            <span>Welcome back</span>
            <h2>Your queue moves with you.</h2>
            <p>Sign in to track live tokens, manage appointments, or operate your assigned service counter.</p>
        </div>
        <div class="auth-showcase-note">QueueLess · Virtual Queue &amp; Appointment Management</div>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <div class="auth-kicker">Account access</div>
            <h1>Sign in</h1>
            <p class="auth-intro">Use your email address or username with your password.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $e): ?>
                        <p><?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($s = $_SESSION['success'] ?? ''): unset($_SESSION['success']); ?>
                <div class="alert success"><p><?php echo htmlspecialchars($s); ?></p></div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <label for="login">Email or username</label>
                <input type="text" name="login" id="login" autocomplete="username" placeholder="Email or username" value="<?php echo htmlspecialchars($old['login'] ?? ''); ?>">

                <label for="password">Password</label>
                <input type="password" name="password" id="password" autocomplete="current-password" placeholder="Enter your password">

                <button class="button full" type="submit">Sign in</button>
            </form>

            <div class="auth-links">
                <a href="index.php?action=forgot_password">Forgot password?</a>
                <a href="index.php?action=register">Create customer account</a>
            </div>
        </div>
    </main>
</div>
<script src="assets/js/validation.js"></script>
</body>
</html>
