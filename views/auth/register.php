<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account - QueueLess</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>
<body class="auth-body">
<div class="auth-shell">
    <aside class="auth-showcase">
        <a class="brand" href="index.php"><span class="brand-mark">Q</span><span>QueueLess</span></a>
        <div class="auth-showcase-copy">
            <span>Customer registration</span>
            <h2>Join the queue before you arrive.</h2>
            <p>Create your customer account once, then use it to join services, track tokens and manage appointments.</p>
        </div>
        <div class="auth-showcase-note">Staff and administrator accounts are created by an administrator.</div>
    </aside>

    <main class="auth-main">
        <div class="auth-card wide">
            <div class="auth-kicker">New customer</div>
            <h1>Create your account</h1>
            <p class="auth-intro">Choose a unique username and use accurate contact details for your account.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $e): ?>
                        <p><?php echo htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="registerForm">
                <div class="form-grid">
                    <div>
                        <label for="full_name">Full name</label>
                        <input type="text" name="full_name" id="full_name" autocomplete="name" value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>">
                        <span class="field-msg" id="name_msg"></span>
                    </div>

                    <div>
                        <label for="reg_username">Username</label>
                        <input type="text" name="username" id="reg_username" autocomplete="username" maxlength="30" placeholder="e.g. trishan_22" value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>">
                        <span class="field-msg" id="reg_username_msg"></span>
                    </div>

                    <div>
                        <label for="reg_email">Email</label>
                        <input type="email" name="email" id="reg_email" autocomplete="email" placeholder="e.g. trishan@example.com" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                        <span class="field-msg" id="reg_email_msg"></span>
                    </div>

                    <div>
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" maxlength="11" inputmode="numeric" autocomplete="tel" placeholder="e.g. 01712345678" value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
                        <span class="field-msg" id="phone_msg"></span>
                    </div>

                    <div>
                        <label for="reg_password">Password</label>
                        <input type="password" name="password" id="reg_password" autocomplete="new-password">
                        <div class="password-rules" id="password_rules" aria-live="polite" style="display: none;">
                            <span data-rule="length">8+ characters</span>
                            <span data-rule="uppercase">Uppercase letter</span>
                            <span data-rule="lowercase">Lowercase letter</span>
                            <span data-rule="number">Number</span>
                            <span data-rule="symbol">Symbol</span>
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password">
                        <span class="field-msg" id="confirm_password_msg"></span>
                    </div>

                    <div>
                        <label for="profile_photo">Profile photo <span class="muted">(optional)</span></label>
                        <input type="file" id="profile_photo" name="profile_photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                    </div>

                    <div>
                        <label for="security_question">Security question</label>
                        <select name="security_question" id="security_question">
                            <option value="">Select a question</option>
                            <option value="What is your first school?" <?php echo (($old['security_question'] ?? '') === 'What is your first school?') ? 'selected' : ''; ?>>What is your first school?</option>
                            <option value="What is your favorite book?" <?php echo (($old['security_question'] ?? '') === 'What is your favorite book?') ? 'selected' : ''; ?>>What is your favorite book?</option>
                            <option value="What city were you born in?" <?php echo (($old['security_question'] ?? '') === 'What city were you born in?') ? 'selected' : ''; ?>>What city were you born in?</option>
                        </select>
                    </div>

                    <div class="full-span">
                        <label for="security_answer">Security answer</label>
                        <input type="text" name="security_answer" id="security_answer" autocomplete="off">
                    </div>
                </div>

                <button class="button full" type="submit">Create account</button>
            </form>

            <div class="auth-links">
                <a href="index.php?action=login">Already have an account? Sign in</a>
            </div>
        </div>
    </main>
</div>
<script src="assets/js/validation.js?v=4"></script>
</body>
</html>
