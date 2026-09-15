<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            Login - QueueLess
        </title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth-body">
        <div class="auth-card">
            <a class="brand dark" href="index.php">
                QueueLess
            </a>
            <h1>
                Sign in
            </h1>
            <p class="muted">
                Use your registered account.
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
            <?php if ($s=$_SESSION['success']??''):unset($_SESSION['success']); ?>
            <div class="alert success">
                <p>
                    <?php echo htmlspecialchars($s); ?>
                </p>
            </div>
        <?php endif; ?>
        <form method="POST" id="loginForm">
            <label>
                Email
            </label>
            <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($old['email']??''); ?>">
            <span class="field-msg" id="email_msg">
            </span>
            <label>
                Password
            </label>
            <input type="password" name="password" id="password">
            <button class="button full" type="submit">
                Login
            </button>
        </form>
        <div class="auth-links">
            <a href="index.php?action=forgot_password">
                Forgot password?
            </a>
            <a href="index.php?action=register">
                Create customer account
            </a>
        </div>
    </div>
    <script src="assets/js/validation.js"></script>
</body>
</html>
