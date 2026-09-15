<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            Register - QueueLess
        </title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body class="auth-body">
        <div class="auth-card wide">
            <a class="brand dark" href="index.php">
                QueueLess
            </a>
            <h1>
                Create customer account
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
            <form method="POST" enctype="multipart/form-data" id="registerForm">
                <div class="form-grid">
                    <div>
                        <label>
                            Full name
                        </label>
                        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($old['full_name']??''); ?>">
                        <span class="field-msg" id="name_msg">
                        </span>
                    </div>
                    <div>
                        <label>
                            Email
                        </label>
                        <input type="text" name="email" id="reg_email" value="<?php echo htmlspecialchars($old['email']??''); ?>">
                        <span class="field-msg" id="reg_email_msg">
                        </span>
                    </div>
                    <div>
                        <label>
                            Phone
                        </label>
                        <input type="text" name="phone" id="phone" maxlength="11" value="<?php echo htmlspecialchars($old['phone']??''); ?>">
                        <span class="field-msg" id="phone_msg">
                        </span>
                    </div>
                    <div>
                        <label>
                            Profile photo (JPG/PNG, optional)
                        </label>
                        <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png">
                    </div>
                    <div>
                        <label>
                            Password
                        </label>
                        <input type="password" name="password" id="reg_password">
                    </div>
                    <div>
                        <label>
                            Confirm password
                        </label>
                        <input type="password" name="confirm_password" id="confirm_password">
                    </div>
                    <div class="full-span">
                        <label>
                            Security question
                        </label>
                        <select name="security_question">
                            <option value="">
                                Select a question
                            </option>
                            <option <?php echo (($old['security_question']??'')==='What is your first school?')?'selected':''; ?>>
                                What is your first school?
                            </option>
                            <option <?php echo (($old['security_question']??'')==='What is your favorite book?')?'selected':''; ?>>
                                What is your favorite book?
                            </option>
                            <option <?php echo (($old['security_question']??'')==='What city were you born in?')?'selected':''; ?>>
                                What city were you born in?
                            </option>
                        </select>
                    </div>
                    <div class="full-span">
                        <label>
                            Security answer
                        </label>
                        <input type="text" name="security_answer">
                    </div>
                </div>
                <button class="button full" type="submit">
                    Register
                </button>
            </form>
            <div class="auth-links">
                <a href="index.php?action=login">
                    Already have an account? Login
                </a>
            </div>
        </div>
        <script src="assets/js/validation.js"></script>
    </body>
</html>
