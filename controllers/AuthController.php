<?php
class AuthController {
    private $conn;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new UserModel($conn);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $errors = [];

            if ($email === '') {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email address.';
            }

            if ($password === '') {
                $errors[] = 'Password is required.';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $errors[] = 'Invalid email or password.';

                } elseif ($user['status'] !== 'Active') {
                    $errors[] = 'Your account is inactive. Contact an administrator.';

                } else {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];

                    header('Location: index.php?action=dashboard');
                    exit();
                }
            }

            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = [
                'email' => $email
            ];

            header('Location: index.php?action=login');
            exit();
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old']);

        require 'views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $security_question = trim($_POST['security_question'] ?? '');
            $security_answer = trim($_POST['security_answer'] ?? '');

            $errors = [];

            if ($full_name === '') {
                $errors[] = 'Full name is required.';
            } elseif (!preg_match('/^[a-zA-Z .-]+$/', $full_name)) {
                $errors[] = 'Name may contain letters, spaces, dot and hyphen only.';
            }

            if ($email === '') {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email address.';
            } elseif ($this->userModel->emailExists($email)) {
                $errors[] = 'Email is already registered.';
            }

            if ($phone === '') {
                $errors[] = 'Phone is required.';
            } elseif (!ctype_digit($phone) || strlen($phone) != 11) {
                $errors[] = 'Phone must contain exactly 11 digits.';
            }

            if (strlen($password) < 6) {
                $errors[] = 'Password must contain at least 6 characters.';
            }

            if ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match.';
            }

            if ($security_question === '') {
                $errors[] = 'Security question is required.';
            }

            if ($security_answer === '') {
                $errors[] = 'Security answer is required.';
            }

            $photo = $this->uploadPhoto($errors);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = [
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone' => $phone,
                    'security_question' => $security_question
                ];

                header('Location: index.php?action=register');
                exit();
            }

            $user_id = $this->userModel->create(
                $full_name,
                $email,
                $password,
                'Customer',
                $phone,
                $security_question,
                $security_answer,
                $photo
            );

            if ($user_id) {
                $_SESSION['success'] = 'Registration successful. Please login.';

                header('Location: index.php?action=login');
                exit();
            }

            $_SESSION['errors'] = [
                'Registration failed. Please try again.'
            ];

            header('Location: index.php?action=register');
            exit();
        }

        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old']);

        require 'views/auth/register.php';
    }

    private function uploadPhoto(&$errors) {
        if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] == 4) {
            return null;
        }

        if ($_FILES['profile_photo']['error'] != 0) {
            $errors[] = 'Profile photo upload failed.';
            return null;
        }

        $file_size = $_FILES['profile_photo']['size'];
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_type = mime_content_type($file_tmp);

        $allowed_types = [
            'image/jpeg',
            'image/png'
        ];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Profile photo must be JPG or PNG.';
        }

        if ($file_size > 2 * 1024 * 1024) {
            $errors[] = 'Profile photo must be less than 2 MB.';
        }

        if (!empty($errors)) {
            return null;
        }

        if ($file_type === 'image/png') {
            $extension = '.png';
        } else {
            $extension = '.jpg';
        }

        $new_file_name = time() . '_' . mt_rand(1000, 9999) . $extension;

        move_uploaded_file(
            $file_tmp,
            __DIR__ . '/../uploads/profile/' . $new_file_name
        );

        return $new_file_name;
    }

    public function logout() {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: index.php?action=login');
        exit();
    }

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email'] ?? '');
            $security_answer = trim($_POST['security_answer'] ?? '');

            $user = $this->userModel->findByEmail($email);

            if (
                $user &&
                $user['status'] === 'Active' &&
                password_verify(
                    strtolower($security_answer),
                    $user['security_answer_hash']
                )
            ) {
                $_SESSION['reset_user_id'] = $user['user_id'];

                header('Location: index.php?action=reset_password');
                exit();
            }

            $_SESSION['errors'] = [
                'Email or security answer is incorrect.'
            ];

            header('Location: index.php?action=forgot_password');
            exit();
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        require 'views/auth/forgot.php';
    }

    public function resetPassword() {
        if (!isset($_SESSION['reset_user_id'])) {
            header('Location: index.php?action=forgot_password');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $errors = [];

            if (strlen($password) < 6) {
                $errors[] = 'Password must contain at least 6 characters.';
            }

            if ($password !== $confirm_password) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $this->userModel->changePassword(
                    $_SESSION['reset_user_id'],
                    $password
                );

                unset($_SESSION['reset_user_id']);

                $_SESSION['success'] = 'Password reset successful. Please login.';

                header('Location: index.php?action=login');
                exit();
            }

            $_SESSION['errors'] = $errors;

            header('Location: index.php?action=reset_password');
            exit();
        }

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        require 'views/auth/reset.php';
    }
}
?>
