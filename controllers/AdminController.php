<?php
class AdminController {
    private $conn;
    private $users;
    private $services;
    private $counters;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->users = new UserModel($conn);
        $this->services = new ServiceModel($conn);
        $this->counters = new CounterModel($conn);
    }

    public function dashboard() {
        requireRole('Admin');

        $stats = [];

        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        $stats['users'] = $result->fetch_assoc()['total'];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM queue_tokens
             WHERE queue_date = CURDATE()
             AND status = 'Waiting'"
        );
        $stats['waiting'] = $result->fetch_assoc()['total'];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM queue_tokens
             WHERE queue_date = CURDATE()
             AND status = 'Completed'"
        );
        $stats['served'] = $result->fetch_assoc()['total'];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM counters
             WHERE status = 'Open'"
        );
        $stats['open_counters'] = $result->fetch_assoc()['total'];

        $sql = "SELECT s.name,
                COUNT(CASE WHEN q.status = 'Waiting' THEN 1 END) AS waiting,
                MAX(CASE WHEN q.status IN ('Called', 'Serving') THEN q.token_number END) AS current_token
                FROM services s
                LEFT JOIN queue_tokens q
                ON s.service_id = q.service_id
                AND q.queue_date = CURDATE()
                GROUP BY s.service_id
                ORDER BY s.name";

        $live = $this->conn->query($sql);

        require 'views/admin/dashboard.php';
    }

    public function services() {
        requireRole('Admin');

        $services = $this->services->getAll();

        require 'views/admin/services.php';
    }

    public function saveService() {
        requireRole('Admin');

        $service_id = (int)($_POST['service_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $token_prefix = strtoupper(trim($_POST['token_prefix'] ?? ''));
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'Active';

        if (!in_array($status, ['Active', 'Inactive'], true)) {
            $status = 'Active';
        }

        $errors = [];

        if ($name === '') {
            $errors[] = 'Service name is required.';
        }

        if ($token_prefix === '' || !preg_match('/^[A-Z0-9]{1,5}$/', $token_prefix)) {
            $errors[] = 'Token prefix must be 1-5 letters/numbers.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        } else {
            if ($service_id > 0) {
                $success = $this->services->update(
                    $service_id,
                    $name,
                    $token_prefix,
                    $description,
                    $status
                );
            } else {
                $success = $this->services->create(
                    $name,
                    $token_prefix,
                    $description
                );
            }

            if ($success) {
                $_SESSION['success'] = 'Service saved.';
            } else {
                $_SESSION['errors'] = ['Could not save service.'];
            }
        }

        header('Location: index.php?action=admin_services');
        exit();
    }

    public function deleteService() {
        requireRole('Admin');

        $service_id = (int)($_POST['service_id'] ?? 0);

        if ($this->services->delete($service_id)) {
            $_SESSION['success'] = 'Service removed.';
        } else {
            $_SESSION['errors'] = [
                'Service has related records. Deactivate it instead.'
            ];
        }

        header('Location: index.php?action=admin_services');
        exit();
    }

    public function users() {
        requireRole('Admin');

        $users = $this->users->getAll();

        require 'views/admin/users.php';
    }

    public function createStaff() {
        requireRole('Admin');

        $name = trim($_POST['full_name'] ?? '');
        $username = strtolower(trim($_POST['username'] ?? ''));
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'Staff';

        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        $username_error = $this->users->validateUsername($username);
        if ($username_error !== '') {
            $errors[] = $username_error;
        } elseif ($this->users->usernameExists($username)) {
            $errors[] = 'Username already exists.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        } elseif ($this->users->emailExists($email)) {
            $errors[] = 'Email already exists.';
        }

        if (!ctype_digit($phone) || strlen($phone) != 11) {
            $errors[] = 'Phone must be 11 digits.';
        }

        $password_error = $this->users->passwordError($password);
        if ($password_error !== '') {
            $errors[] = $password_error;
        }

        if (!in_array($role, ['Staff', 'Admin'])) {
            $role = 'Staff';
        }

        if (empty($errors)) {
            $success = $this->users->create(
                $name,
                $username,
                $email,
                $password,
                $role,
                $phone,
                'Default question: What is your first school?',
                'change-me'
            );

            if ($success) {
                $_SESSION['success'] = 'Account created. User should change password and profile details.';
            } else {
                $_SESSION['errors'] = ['Account creation failed.'];
            }
        } else {
            $_SESSION['errors'] = $errors;
        }

        header('Location: index.php?action=admin_users');
        exit();
    }

    public function userStatus() {
        requireRole('Admin');

        $user_id = (int)($_POST['user_id'] ?? 0);
        $status = ($_POST['status'] ?? '') === 'Active'
            ? 'Active'
            : 'Inactive';

        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['errors'] = [
                'You cannot deactivate your own logged-in admin account here.'
            ];
        } else {
            $this->users->setStatus($user_id, $status);
            $_SESSION['success'] = 'User status updated.';
        }

        header('Location: index.php?action=admin_users');
        exit();
    }

    public function counters() {
        requireRole('Admin');

        $counters = $this->counters->getAll();
        $services = $this->services->getActive();

        require 'views/admin/counters.php';
    }

    public function saveCounter() {
        requireRole('Admin');

        $counter_id = (int)($_POST['counter_id'] ?? 0);
        $service_id = (int)($_POST['service_id'] ?? 0);
        $counter_name = trim($_POST['counter_name'] ?? '');
        $status = $_POST['status'] ?? 'Closed';

        if (!in_array($status, ['Open', 'Closed'], true)) {
            $status = 'Closed';
        }

        if ($service_id <= 0 || $counter_name === '') {
            $_SESSION['errors'] = [
                'Service and counter name are required.'
            ];
        } else {
            if ($counter_id > 0) {
                $success = $this->counters->update(
                    $counter_id,
                    $service_id,
                    $counter_name,
                    $status
                );
            } else {
                $success = $this->counters->create(
                    $service_id,
                    $counter_name
                );
            }

            if ($success) {
                $_SESSION['success'] = 'Counter saved.';
            } else {
                $_SESSION['errors'] = ['Could not save counter.'];
            }
        }

        header('Location: index.php?action=admin_counters');
        exit();
    }

    public function deleteCounter() {
        requireRole('Admin');

        $counter_id = (int)($_POST['counter_id'] ?? 0);

        if ($this->counters->delete($counter_id)) {
            $_SESSION['success'] = 'Counter removed.';
        } else {
            $_SESSION['errors'] = [
                'Counter has related records. Keep it closed instead.'
            ];
        }

        header('Location: index.php?action=admin_counters');
        exit();
    }

    public function assignments() {
        requireRole('Admin');

        $assignments = $this->counters->getAssignments();
        $staff = $this->counters->getStaffUsers();
        $counters = $this->counters->getAll();

        require 'views/admin/assignments.php';
    }

    public function assignStaff() {
        requireRole('Admin');

        $staff_id = (int)($_POST['staff_id'] ?? 0);
        $counter_id = (int)($_POST['counter_id'] ?? 0);

        if (
            $staff_id > 0 &&
            $counter_id > 0 &&
            $this->counters->assign($staff_id, $counter_id)
        ) {
            $_SESSION['success'] = 'Staff assigned to counter.';
        } else {
            $_SESSION['errors'] = [
                'Please select a valid staff member and counter.'
            ];
        }

        header('Location: index.php?action=admin_assignments');
        exit();
    }

    public function reports() {
        requireRole('Admin');

        $sql = "SELECT
                COUNT(CASE WHEN status = 'Completed' THEN 1 END) AS served,
                COUNT(CASE WHEN status = 'Waiting' THEN 1 END) AS waiting,
                ROUND(
                    AVG(
                        CASE
                        WHEN called_at IS NOT NULL
                        THEN TIMESTAMPDIFF(MINUTE, joined_at, called_at)
                        END
                    ),
                    1
                ) AS avg_wait
                FROM queue_tokens
                WHERE queue_date = CURDATE()";

        $summary = $this->conn->query($sql)->fetch_assoc();

        $sql = "SELECT s.name,
                COUNT(q.token_id) AS total_tokens,
                COUNT(CASE WHEN q.status = 'Completed' THEN 1 END) AS completed,
                ROUND(
                    AVG(
                        CASE
                        WHEN q.called_at IS NOT NULL
                        THEN TIMESTAMPDIFF(MINUTE, q.joined_at, q.called_at)
                        END
                    ),
                    1
                ) AS avg_wait
                FROM services s
                LEFT JOIN queue_tokens q
                ON s.service_id = q.service_id
                AND q.queue_date = CURDATE()
                GROUP BY s.service_id
                ORDER BY total_tokens DESC";

        $serviceReport = $this->conn->query($sql);

        require 'views/admin/reports.php';
    }
}
?>
