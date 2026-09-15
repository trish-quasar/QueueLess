<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            New password
        </title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth-body">
        <div class="auth-card">
            <h1>
                Choose a new password
            </h1>
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
                    New password
                </label>
                <input type="password" name="password">
                <label>
                    Confirm new password
                </label>
                <input type="password" name="confirm_password">
                <button class="button full">
                    Reset password
                </button>
            </form>
        </div>
    </body>
</html>
