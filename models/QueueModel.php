<?php
class QueueModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function activeTokenForCustomer($customer_id) {
        $sql = "SELECT q.*, s.name AS service_name, c.counter_name
                FROM queue_tokens q
                JOIN services s ON q.service_id = s.service_id
                LEFT JOIN counters c ON q.counter_id = c.counter_id
                WHERE q.customer_id = ?
                AND q.status IN ('Waiting', 'Called', 'Serving')
                ORDER BY q.token_id DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $token = $result->fetch_assoc();

        $stmt->close();

        return $token;
    }

    public function createToken($customer_id, $service_id, $appointment_id = null) {
        if ($this->activeTokenForCustomer($customer_id)) {
            return false;
        }

        $sql = "SELECT token_prefix
                FROM services
                WHERE service_id = ?
                AND status = 'Active'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $service_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $service = $result->fetch_assoc();
        $stmt->close();

        if (!$service) {
            return false;
        }

        $sql = "SELECT COUNT(*) AS total
                FROM queue_tokens
                WHERE service_id = ?
                AND queue_date = CURDATE()";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $service_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'] + 1;
        $stmt->close();

        $token_number = $service['token_prefix'] . '-' . str_pad(
            $total,
            3,
            '0',
            STR_PAD_LEFT
        );

        $sql = "INSERT INTO queue_tokens
                (customer_id, service_id, appointment_id, token_number, queue_date, status, joined_at)
                VALUES (?, ?, ?, ?, CURDATE(), 'Waiting', NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiis",
            $customer_id,
            $service_id,
            $appointment_id,
            $token_number
        );

        $success = $stmt->execute();
        $token_id = $this->conn->insert_id;
        $stmt->close();

        if ($success) {
            return $token_id;
        }

        return false;
    }

    public function getLiveStatus($customer_id) {
        $token = $this->activeTokenForCustomer($customer_id);

        if (!$token) {
            return null;
        }

        $sql = "SELECT COUNT(*) AS people_ahead
                FROM queue_tokens
                WHERE service_id = ?
                AND queue_date = CURDATE()
                AND status = 'Waiting'
                AND token_id < ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ii",
            $token['service_id'],
            $token['token_id']
        );
        $stmt->execute();

        $result = $stmt->get_result();
        $token['people_ahead'] = (int)$result->fetch_assoc()['people_ahead'];
        $stmt->close();

        $sql = "SELECT q.token_number, c.counter_name
                FROM queue_tokens q
                LEFT JOIN counters c ON q.counter_id = c.counter_id
                WHERE q.service_id = ?
                AND q.queue_date = CURDATE()
                AND q.status IN ('Called', 'Serving')
                ORDER BY q.called_at DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $token['service_id']);
        $stmt->execute();

        $result = $stmt->get_result();
        $current = $result->fetch_assoc();
        $stmt->close();

        if ($current) {
            $token['currently_serving'] = $current['token_number'];
            $token['current_counter'] = $current['counter_name'] ?? '-';
        } else {
            $token['currently_serving'] = '-';
            $token['current_counter'] = '-';
        }

        return $token;
    }

    public function cancelToken($token_id, $customer_id) {
        $sql = "UPDATE queue_tokens
                SET status = 'Cancelled', completed_at = NOW()
                WHERE token_id = ?
                AND customer_id = ?
                AND status = 'Waiting'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $token_id, $customer_id);
        $stmt->execute();

        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }

    public function history($customer_id) {
        $sql = "SELECT q.*, s.name AS service_name, c.counter_name
                FROM queue_tokens q
                JOIN services s ON q.service_id = s.service_id
                LEFT JOIN counters c ON q.counter_id = c.counter_id
                WHERE q.customer_id = ?
                ORDER BY q.token_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function waitingForCounter($counter_id) {
        $sql = "SELECT q.*, u.full_name
                FROM queue_tokens q
                JOIN users u ON q.customer_id = u.user_id
                JOIN counters c ON c.service_id = q.service_id
                WHERE c.counter_id = ?
                AND q.queue_date = CURDATE()
                AND q.status = 'Waiting'
                ORDER BY q.token_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function currentForCounter($counter_id) {
        $sql = "SELECT q.*, u.full_name
                FROM queue_tokens q
                JOIN users u ON q.customer_id = u.user_id
                WHERE q.counter_id = ?
                AND q.queue_date = CURDATE()
                AND q.status IN ('Called', 'Serving')
                ORDER BY q.called_at DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $token = $result->fetch_assoc();
        $stmt->close();

        return $token;
    }

    public function callNext($counter_id, $service_id) {
        if ($this->currentForCounter($counter_id)) {
            return false;
        }

        try {
            $this->conn->begin_transaction();

            $sql = "SELECT token_id
                    FROM queue_tokens
                    WHERE service_id = ?
                    AND queue_date = CURDATE()
                    AND status = 'Waiting'
                    ORDER BY token_id ASC
                    LIMIT 1
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $service_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $token = $result->fetch_assoc();
            $stmt->close();

            if (!$token) {
                $this->conn->rollback();
                return false;
            }

            $sql = "UPDATE queue_tokens
                    SET status = 'Called', counter_id = ?, called_at = NOW()
                    WHERE token_id = ?
                    AND status = 'Waiting'";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $counter_id, $token['token_id']);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            if ($success) {
                $this->conn->commit();
            } else {
                $this->conn->rollback();
            }

            return $success;

        } catch (Throwable $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function startServing($counter_id) {
        $sql = "UPDATE queue_tokens
                SET status = 'Serving', service_started_at = NOW()
                WHERE counter_id = ?
                AND queue_date = CURDATE()
                AND status = 'Called'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();

        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }

    public function recall($counter_id) {
        $sql = "UPDATE queue_tokens
                SET recall_count = recall_count + 1, called_at = NOW()
                WHERE counter_id = ?
                AND queue_date = CURDATE()
                AND status = 'Called'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();

        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }

    public function skip($counter_id) {
        $sql = "UPDATE queue_tokens
                SET status = 'Skipped', completed_at = NOW()
                WHERE counter_id = ?
                AND queue_date = CURDATE()
                AND status IN ('Called', 'Serving')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();

        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }

    public function complete($counter_id) {
        $sql = "UPDATE queue_tokens
                SET status = 'Completed', completed_at = NOW()
                WHERE counter_id = ?
                AND queue_date = CURDATE()
                AND status = 'Serving'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $counter_id);
        $stmt->execute();

        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }
}
?>
