<?php
class AjaxController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function sendJson($data) {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function checkEmail() {
        $email = trim($_GET['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendJson([
                'valid' => false,
                'exists' => false,
                'message' => $email === '' ? '' : 'Enter a valid email address.'
            ]);
        }

        $userModel = new UserModel($this->conn);
        $exists = $userModel->emailExists($email);

        $this->sendJson([
            'valid' => true,
            'exists' => $exists,
            'message' => $exists ? 'Email is already registered.' : 'Email is available.'
        ]);
    }


    public function checkUsername() {
        $username = strtolower(trim($_GET['username'] ?? ''));
        $userModel = new UserModel($this->conn);
        $message = $userModel->validateUsername($username);

        if ($message !== '') {
            $this->sendJson([
                'valid' => false,
                'available' => false,
                'message' => $message
            ]);
        }

        $exists = $userModel->usernameExists($username);

        $this->sendJson([
            'valid' => true,
            'available' => !$exists,
            'message' => $exists ? 'Username is already taken.' : 'Username is available.'
        ]);
    }

    public function validatePassword() {
        $password = $_POST['password'] ?? '';
        $userModel = new UserModel($this->conn);
        $checks = $userModel->passwordChecks($password);

        $this->sendJson([
            'valid' => $checks['valid'],
            'checks' => [
                'length' => $checks['length'],
                'uppercase' => $checks['uppercase'],
                'lowercase' => $checks['lowercase'],
                'number' => $checks['number'],
                'symbol' => $checks['symbol']
            ]
        ]);
    }

    public function queueStatus() {
        requireRole('Customer');

        $queueModel = new QueueModel($this->conn);
        $queue = $queueModel->getLiveStatus($_SESSION['user_id']);

        $this->sendJson([
            'success' => true,
            'queue' => $queue
        ]);
    }

    public function publicServices() {
        $sql = "SELECT s.service_id, s.name,
                (SELECT COUNT(*)
                 FROM queue_tokens q
                 WHERE q.service_id = s.service_id
                 AND q.queue_date = CURDATE()
                 AND q.status = 'Waiting') AS waiting,

                (SELECT q.token_number
                 FROM queue_tokens q
                 WHERE q.service_id = s.service_id
                 AND q.queue_date = CURDATE()
                 AND q.status IN ('Called', 'Serving')
                 ORDER BY q.called_at DESC
                 LIMIT 1) AS current_token,

                (SELECT c.counter_name
                 FROM queue_tokens q
                 LEFT JOIN counters c ON q.counter_id = c.counter_id
                 WHERE q.service_id = s.service_id
                 AND q.queue_date = CURDATE()
                 AND q.status IN ('Called', 'Serving')
                 ORDER BY q.called_at DESC
                 LIMIT 1) AS counter_name

                FROM services s
                WHERE s.status = 'Active'
                ORDER BY s.name";

        $result = $this->conn->query($sql);
        $services = [];

        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }

        $this->sendJson([
            'success' => true,
            'services' => $services
        ]);
    }

    public function serviceStats() {
        requireLogin();

        $summary = [];

        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        $summary['users'] = (int)$result->fetch_assoc()['total'];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM queue_tokens
             WHERE queue_date = CURDATE()
             AND status = 'Waiting'"
        );
        $summary['waiting'] = (int)$result->fetch_assoc()['total'];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM queue_tokens
             WHERE queue_date = CURDATE()
             AND status = 'Completed'"
        );
        $summary['served'] = (int)$result->fetch_assoc()['total'];

        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM counters
             WHERE status = 'Open'"
        );
        $summary['open_counters'] = (int)$result->fetch_assoc()['total'];

        $sql = "SELECT s.service_id, s.name,
                COUNT(CASE WHEN q.status = 'Waiting' THEN 1 END) AS waiting,
                MAX(CASE WHEN q.status IN ('Called', 'Serving') THEN q.token_number END) AS current_token
                FROM services s
                LEFT JOIN queue_tokens q
                ON s.service_id = q.service_id
                AND q.queue_date = CURDATE()
                WHERE s.status = 'Active'
                GROUP BY s.service_id, s.name
                ORDER BY s.name";

        $result = $this->conn->query($sql);
        $services = [];

        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }

        $this->sendJson([
            'success' => true,
            'summary' => $summary,
            'services' => $services
        ]);
    }

    public function staffQueue() {
        requireRole('Staff');

        $counterModel = new CounterModel($this->conn);
        $assignment = $counterModel->getAssignmentForStaff($_SESSION['user_id']);

        if (!$assignment) {
            $this->sendJson([
                'success' => false
            ]);
        }

        $queueModel = new QueueModel($this->conn);
        $current = $queueModel->currentForCounter($assignment['counter_id']);
        $result = $queueModel->waitingForCounter($assignment['counter_id']);
        $waiting = [];

        while ($row = $result->fetch_assoc()) {
            $waiting[] = $row;
        }

        $this->sendJson([
            'success' => true,
            'counter_status' => $assignment['counter_status'],
            'current' => $current,
            'waiting' => $waiting
        ]);
    }
}
?>
