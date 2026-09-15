<?php
class AjaxController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function sendJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function checkEmail() {
        $email = trim($_GET['email'] ?? '');

        if ($email === '') {
            $this->sendJson([
                'exists' => false
            ]);
        }

        $userModel = new UserModel($this->conn);

        $this->sendJson([
            'exists' => $userModel->emailExists($email)
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

        $sql = "SELECT s.service_id, s.name,
                COUNT(CASE WHEN q.status = 'Waiting' THEN 1 END) AS waiting,
                MAX(CASE WHEN q.status IN ('Called', 'Serving') THEN q.token_number END) AS current_token
                FROM services s
                LEFT JOIN queue_tokens q
                ON s.service_id = q.service_id
                AND q.queue_date = CURDATE()
                WHERE s.status = 'Active'
                GROUP BY s.service_id
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
