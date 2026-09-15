<?php
session_start();

require_once 'config/config.php';

require_once 'models/UserModel.php';
require_once 'models/ServiceModel.php';
require_once 'models/QueueModel.php';
require_once 'models/AppointmentModel.php';
require_once 'models/CounterModel.php';

require_once 'controllers/AuthController.php';
require_once 'controllers/ProfileController.php';
require_once 'controllers/CustomerController.php';
require_once 'controllers/StaffController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/AjaxController.php';

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit();
    }
}

function requireRole($role) {
    requireLogin();

    if (($_SESSION['role'] ?? '') !== $role) {
        header('Location: index.php?action=dashboard');
        exit();
    }
}

$action = $_GET['action'] ?? 'home';

$auth = new AuthController($conn);
$profile = new ProfileController($conn);
$customer = new CustomerController($conn);
$staff = new StaffController($conn);
$admin = new AdminController($conn);
$ajax = new AjaxController($conn);

if ($action === 'home') {
    require 'views/public/home.php';

} elseif ($action === 'login') {
    $auth->login();

} elseif ($action === 'register') {
    $auth->register();

} elseif ($action === 'logout') {
    $auth->logout();

} elseif ($action === 'forgot_password') {
    $auth->forgotPassword();

} elseif ($action === 'reset_password') {
    $auth->resetPassword();

} elseif ($action === 'profile') {
    $profile->index();

} elseif ($action === 'update_profile') {
    $profile->update();

} elseif ($action === 'change_password') {
    $profile->changePassword();

} elseif ($action === 'deactivate_account') {
    $profile->deactivate();

} elseif ($action === 'dashboard') {
    requireLogin();

    if ($_SESSION['role'] === 'Customer') {
        $customer->dashboard();
    } elseif ($_SESSION['role'] === 'Staff') {
        $staff->dashboard();
    } else {
        $admin->dashboard();
    }

} elseif ($action === 'services') {
    $customer->services();

} elseif ($action === 'join_queue') {
    $customer->joinQueue();

} elseif ($action === 'cancel_token') {
    $customer->cancelToken();

} elseif ($action === 'appointments') {
    $customer->appointments();

} elseif ($action === 'book_appointment') {
    $customer->bookAppointment();

} elseif ($action === 'cancel_appointment') {
    $customer->cancelAppointment();

} elseif ($action === 'check_in') {
    $customer->checkIn();

} elseif ($action === 'history') {
    requireLogin();

    if ($_SESSION['role'] === 'Customer') {
        $customer->history();
    } elseif ($_SESSION['role'] === 'Staff') {
        $staff->history();
    } else {
        header('Location: index.php?action=dashboard');
    }

} elseif ($action === 'counter_status') {
    $staff->counterStatus();

} elseif ($action === 'call_next') {
    $staff->queueAction('call');

} elseif ($action === 'start_serving') {
    $staff->queueAction('serve');

} elseif ($action === 'recall_token') {
    $staff->queueAction('recall');

} elseif ($action === 'skip_token') {
    $staff->queueAction('skip');

} elseif ($action === 'complete_token') {
    $staff->queueAction('complete');

} elseif ($action === 'admin_services') {
    $admin->services();

} elseif ($action === 'save_service') {
    $admin->saveService();

} elseif ($action === 'delete_service') {
    $admin->deleteService();

} elseif ($action === 'admin_users') {
    $admin->users();

} elseif ($action === 'create_staff') {
    $admin->createStaff();

} elseif ($action === 'user_status') {
    $admin->userStatus();

} elseif ($action === 'admin_counters') {
    $admin->counters();

} elseif ($action === 'save_counter') {
    $admin->saveCounter();

} elseif ($action === 'delete_counter') {
    $admin->deleteCounter();

} elseif ($action === 'admin_assignments') {
    $admin->assignments();

} elseif ($action === 'assign_staff') {
    $admin->assignStaff();

} elseif ($action === 'admin_reports') {
    $admin->reports();

} elseif ($action === 'ajax_check_email') {
    $ajax->checkEmail();

} elseif ($action === 'ajax_public_services') {
    $ajax->publicServices();

} elseif ($action === 'ajax_queue_status') {
    $ajax->queueStatus();

} elseif ($action === 'ajax_service_stats') {
    $ajax->serviceStats();

} elseif ($action === 'ajax_staff_queue') {
    $ajax->staffQueue();

} else {
    http_response_code(404);
    echo 'Page not found.';
}
?>
