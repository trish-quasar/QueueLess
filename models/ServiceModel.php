<?php
class ServiceModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getActive() {
        $sql = "SELECT s.*,
                (SELECT COUNT(*)
                 FROM queue_tokens q
                 WHERE q.service_id = s.service_id
                 AND q.queue_date = CURDATE()
                 AND q.status = 'Waiting') AS waiting_count
                FROM services s
                WHERE s.status = 'Active'
                ORDER BY s.name";

        return $this->conn->query($sql);
    }

    public function getAll() {
        return $this->conn->query(
            "SELECT * FROM services ORDER BY name"
        );
    }

    public function find($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM services WHERE service_id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $service = $result->fetch_assoc();

        $stmt->close();

        return $service;
    }

    public function create($name, $prefix, $description) {
        $sql = "INSERT INTO services
                (name, token_prefix, description, status)
                VALUES (?, ?, ?, 'Active')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $name, $prefix, $description);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function update($id, $name, $prefix, $description, $status) {
        $sql = "UPDATE services
                SET name = ?, token_prefix = ?, description = ?, status = ?
                WHERE service_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssi",
            $name,
            $prefix,
            $description,
            $status,
            $id
        );

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function delete($id) {
        $sql = "SELECT
                (SELECT COUNT(*) FROM queue_tokens WHERE service_id = ?) +
                (SELECT COUNT(*) FROM appointments WHERE service_id = ?) +
                (SELECT COUNT(*) FROM counters WHERE service_id = ?) AS total";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $id, $id, $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];
        $stmt->close();

        if ($total > 0) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "DELETE FROM services WHERE service_id = ?"
        );
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}
?>
