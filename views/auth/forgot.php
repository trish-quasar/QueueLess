<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover account - QueueLess</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>
<body class="auth-body">
<div class="auth-shell">
    <aside class="auth-showcase">
        <a class="brand" href="index.php"><span class="brand-mark">Q</span><span>QueueLess</span></a>
        <div class="auth-showcase-copy">
            <span>Account recovery</span>
            <h2>Get back into your account.</h2>
            <p>Use the email and security answer saved during account creation to continue to password reset.</p>
        </div>
        <div class="auth-showcase-note">Your password is never stored in plain text.</div>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            <div class="auth-kicker">Password recovery</div>
            <h1>Verify your account</h1>
            <p class="auth-intro">Enter the details associated with your account.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $e): ?>
                        <p><?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" autocomplete="email">

                <label for="security_answer">Security answer</label>
                <input type="text" id="security_answer" name="security_answer" autocomplete="off">

                <button class="button full" type="submit">Continue</button>
            </form>

            <div class="auth-links">
                <a href="index.php?action=login">Back to sign in</a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
