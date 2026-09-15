<?php
class CounterModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $sql = "SELECT c.*, s.name AS service_name
                FROM counters c
                JOIN services s ON c.service_id = s.service_id
                ORDER BY s.name, c.counter_name";

        return $this->conn->query($sql);
    }

    public function create($service_id, $name) {
        $sql = "INSERT INTO counters
                (service_id, counter_name, status)
                VALUES (?, ?, 'Closed')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $service_id, $name);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function update($id, $service_id, $name, $status) {
        $sql = "UPDATE counters
                SET service_id = ?, counter_name = ?, status = ?
                WHERE counter_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issi", $service_id, $name, $status, $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function delete($id) {
        $sql = "SELECT
                (SELECT COUNT(*) FROM queue_tokens WHERE counter_id = ?) +
                (SELECT COUNT(*) FROM staff_assignments WHERE counter_id = ?) AS total";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];
        $stmt->close();

        if ($total > 0) {
            return false;
        }

        $stmt = $this->conn->prepare(
            "DELETE FROM counters WHERE counter_id = ?"
        );
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getAssignmentForStaff($staff_id) {
        $sql = "SELECT sa.*,
                c.counter_name,
                c.status AS counter_status,
                c.service_id,
                s.name AS service_name
                FROM staff_assignments sa
                JOIN counters c ON sa.counter_id = c.counter_id
                JOIN services s ON c.service_id = s.service_id
                WHERE sa.staff_id = ?
                AND sa.status = 'Active'
                ORDER BY sa.assignment_id DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $assignment = $result->fetch_assoc();

        $stmt->close();

        return $assignment;
    }

    public function setCounterStatus($counter_id, $status) {
        $stmt = $this->conn->prepare(
            "UPDATE counters SET status = ? WHERE counter_id = ?"
        );
        $stmt->bind_param("si", $status, $counter_id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function assign($staff_id, $counter_id) {
        try {
            $this->conn->begin_transaction();

            $sql = "UPDATE staff_assignments
                    SET status = 'Inactive'
                    WHERE status = 'Active'
                    AND (staff_id = ? OR counter_id = ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $staff_id, $counter_id);
            $stmt->execute();
            $stmt->close();

            $sql = "INSERT INTO staff_assignments
                    (staff_id, counter_id, assigned_date, status)
                    VALUES (?, ?, CURDATE(), 'Active')";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $staff_id, $counter_id);
            $success = $stmt->execute();
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

    public function getAssignments() {
        $sql = "SELECT sa.*,
                u.full_name AS staff_name,
                c.counter_name,
                s.name AS service_name
                FROM staff_assignments sa
                JOIN users u ON sa.staff_id = u.user_id
                JOIN counters c ON sa.counter_id = c.counter_id
                JOIN services s ON c.service_id = s.service_id
                ORDER BY sa.assignment_id DESC";

        return $this->conn->query($sql);
    }

    public function getStaffUsers() {
        $sql = "SELECT user_id, full_name, email
                FROM users
                WHERE role = 'Staff'
                AND status = 'Active'
                ORDER BY full_name";

        return $this->conn->query($sql);
    }
}
?>
