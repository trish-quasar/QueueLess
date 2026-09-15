<?php
class CustomerController {
    private $conn;
    private $serviceModel;
    private $queueModel;
    private $appointmentModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->serviceModel = new ServiceModel($conn);
        $this->queueModel = new QueueModel($conn);
        $this->appointmentModel = new AppointmentModel($conn);
    }

    public function dashboard() {
        requireRole('Customer');

        $live = $this->queueModel->getLiveStatus($_SESSION['user_id']);
        $services = $this->serviceModel->getActive();

        require 'views/customer/dashboard.php';
    }

    public function services() {
        requireRole('Customer');

        $services = $this->serviceModel->getActive();

        require 'views/customer/services.php';
    }

    public function joinQueue() {
        requireRole('Customer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=services');
            exit();
        }

        $service_id = (int)($_POST['service_id'] ?? 0);

        if ($service_id <= 0) {
            $_SESSION['errors'] = ['Please select a service.'];
            header('Location: index.php?action=services');
            exit();
        }

        $token_id = $this->queueModel->createToken(
            $_SESSION['user_id'],
            $service_id
        );

        if (!$token_id) {
            $_SESSION['errors'] = [
                'You already have an active token or the service is unavailable.'
            ];
        } else {
            setcookie(
                'last_service',
                (string)$service_id,
                time() + (30 * 24 * 60 * 60),
                '/'
            );

            $_SESSION['success'] = 'You joined the queue successfully.';
        }

        header('Location: index.php?action=dashboard');
        exit();
    }

    public function cancelToken() {
        requireRole('Customer');

        $token_id = (int)($_POST['token_id'] ?? 0);

        if (!$this->queueModel->cancelToken($token_id, $_SESSION['user_id'])) {
            $_SESSION['errors'] = ['Only a waiting token can be cancelled.'];
        } else {
            $_SESSION['success'] = 'Token cancelled.';
        }

        header('Location: index.php?action=dashboard');
        exit();
    }

    public function appointments() {
        requireRole('Customer');

        $services = $this->serviceModel->getActive();
        $appointments = $this->appointmentModel->forCustomer($_SESSION['user_id']);

        require 'views/customer/appointments.php';
    }

    public function bookAppointment() {
        requireRole('Customer');

        $service_id = (int)($_POST['service_id'] ?? 0);
        $date = trim($_POST['appointment_date'] ?? '');
        $time = trim($_POST['slot_time'] ?? '');

        $errors = [];

        if ($service_id <= 0) {
            $errors[] = 'Please select a service.';
        }

        if ($date === '' || strtotime($date) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Choose today or a future appointment date.';
        }

        if ($time === '') {
            $errors[] = 'Please select a time slot.';
        }

        if ($date !== '' && $time !== '') {
            $appointment_time = strtotime($date . ' ' . $time);

            if ($appointment_time === false || $appointment_time <= time()) {
                $errors[] = 'Choose an appointment time in the future.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;

        } elseif ($this->appointmentModel->book(
            $_SESSION['user_id'],
            $service_id,
            $date,
            $time
        )) {
            $_SESSION['success'] = 'Appointment booked.';

        } else {
            $_SESSION['errors'] = ['Appointment could not be booked.'];
        }

        header('Location: index.php?action=appointments');
        exit();
    }

    public function cancelAppointment() {
        requireRole('Customer');

        $appointment_id = (int)($_POST['appointment_id'] ?? 0);

        if ($this->appointmentModel->cancel(
            $appointment_id,
            $_SESSION['user_id']
        )) {
            $_SESSION['success'] = 'Appointment cancelled.';
        } else {
            $_SESSION['errors'] = ['Appointment cannot be cancelled.'];
        }

        header('Location: index.php?action=appointments');
        exit();
    }

    public function checkIn() {
        requireRole('Customer');

        $appointment_id = (int)($_POST['appointment_id'] ?? 0);

        $appointment = $this->appointmentModel->findForCustomer(
            $appointment_id,
            $_SESSION['user_id']
        );

        if (!$appointment || $appointment['status'] !== 'Booked') {
            $_SESSION['errors'] = ['Appointment is not available for check-in.'];

        } else {
            $appointment_time = strtotime(
                $appointment['appointment_date'] . ' ' . $appointment['slot_time']
            );
            $check_in_start = $appointment_time - (30 * 60);
            $check_in_end = $appointment_time + (60 * 60);

            if (time() < $check_in_start || time() > $check_in_end) {
                $_SESSION['errors'] = [
                    'Check-in opens 30 minutes before the appointment and closes 60 minutes after the scheduled time.'
                ];

                header('Location: index.php?action=appointments');
                exit();
            }

            try {
                $this->conn->begin_transaction();

                $token_id = $this->queueModel->createToken(
                    $_SESSION['user_id'],
                    $appointment['service_id'],
                    $appointment_id
                );

                $checked_in = false;

                if ($token_id) {
                    $checked_in = $this->appointmentModel->markCheckedIn(
                        $appointment_id
                    );
                }

                if ($token_id && $checked_in) {
                    $this->conn->commit();
                    $_SESSION['success'] = 'Checked in. Your queue token is ready.';
                } else {
                    $this->conn->rollback();
                    $_SESSION['errors'] = [
                        'Could not check in. You may already have an active token.'
                    ];
                }

            } catch (Throwable $e) {
                $this->conn->rollback();
                $_SESSION['errors'] = ['Could not complete check-in. Please try again.'];
            }
        }

        header('Location: index.php?action=appointments');
        exit();
    }

    public function history() {
        requireRole('Customer');

        $history = $this->queueModel->history($_SESSION['user_id']);

        require 'views/customer/history.php';
    }
}
?>
