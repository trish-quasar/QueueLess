<?php
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';

unset($_SESSION['errors'], $_SESSION['success']);
?>

<?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert success">
        <p><?php echo htmlspecialchars($success); ?></p>
    </div>
<?php endif; ?>
