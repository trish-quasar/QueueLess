<?php
class ProfileController {
    private $userModel;

    public function __construct($conn) {
        $this->userModel = new UserModel($conn);
    }

    public function index() {
        requireLogin();

        $user = $this->userModel->findById($_SESSION['user_id']);

        require 'views/profile/index.php';
    }

    public function update() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=profile');
            exit();
        }

        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $errors = [];

        if ($full_name === '') {
            $errors[] = 'Full name is required.';
        }

        if (!ctype_digit($phone) || strlen($phone) != 11) {
            $errors[] = 'Phone must contain exactly 11 digits.';
        }

        $photo = null;

        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] != 4) {

            if ($_FILES['profile_photo']['error'] != 0) {
                $errors[] = 'Photo upload failed.';

            } else {
                $file_tmp = $_FILES['profile_photo']['tmp_name'];
                $file_size = $_FILES['profile_photo']['size'];
                $file_type = mime_content_type($file_tmp);

                $allowed_types = [
                    'image/jpeg',
                    'image/png'
                ];

                if (!in_array($file_type, $allowed_types)) {
                    $errors[] = 'Photo must be JPG or PNG.';
                }

                if ($file_size > 2 * 1024 * 1024) {
                    $errors[] = 'Photo must be less than 2 MB.';
                }

                if (empty($errors)) {
                    if ($file_type === 'image/png') {
                        $extension = '.png';
                    } else {
                        $extension = '.jpg';
                    }

                    $photo = time() . '_' . mt_rand(1000, 9999) . $extension;

                    $destination = __DIR__ . '/../uploads/profile/' . $photo;

                    if (!move_uploaded_file($file_tmp, $destination)) {
                        $errors[] = 'Photo could not be saved.';
                        $photo = null;
                    }
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=profile');
            exit();
        }

        $this->userModel->updateProfile(
            $_SESSION['user_id'],
            $full_name,
            $phone,
            $photo
        );

        $_SESSION['full_name'] = $full_name;
        $_SESSION['success'] = 'Profile updated.';

        header('Location: index.php?action=profile');
        exit();
    }

    public function changePassword() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=profile');
            exit();
        }

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $user = $this->userModel->findById($_SESSION['user_id']);
        $errors = [];

        if (!password_verify($current_password, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        }

        $password_error = $this->userModel->passwordError($new_password);
        if ($password_error !== '') {
            $errors[] = $password_error;
        }

        if ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?action=profile');
            exit();
        }

        $this->userModel->changePassword(
            $_SESSION['user_id'],
            $new_password
        );

        $_SESSION['success'] = 'Password changed successfully.';

        header('Location: index.php?action=profile');
        exit();
    }

    public function deactivate() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->userModel->deactivate($_SESSION['user_id']);

            $_SESSION = [];
            session_destroy();

            header('Location: index.php');
            exit();
        }

        header('Location: index.php?action=profile');
        exit();
    }
}
?>
