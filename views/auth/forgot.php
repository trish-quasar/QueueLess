<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            Reset password
        </title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth-body">
        <div class="auth-card">
            <a class="brand dark" href="index.php">
                QueueLess
            </a>
            <h1>
                Reset password
            </h1>
            <p class="muted">
                Enter your email and the security answer saved on your account.
            </p>
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $e): ?>
                        <p>
                            <?php echo htmlspecialchars($e); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <label>
                    Email
                </label>
                <input type="text" name="email">
                <label>
                    Security answer
                </label>
                <input type="text" name="security_answer">
                <button class="button full">
                    Continue
                </button>
            </form>
            <div class="auth-links">
                <a href="index.php?action=login">
                    Back to login
                </a>
            </div>
        </div>
    </body>
</html>
