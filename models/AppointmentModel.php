<?php
class AppointmentModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function book($customer_id, $service_id, $date, $time) {
        $sql = "SELECT appointment_id
                FROM appointments
                WHERE customer_id = ?
                AND status = 'Booked'
                AND appointment_date = ?
                AND slot_time = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $customer_id, $date, $time);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt->close();
            return false;
        }

        $stmt->close();

        $sql = "INSERT INTO appointments
                (customer_id, service_id, appointment_date, slot_time, status)
                VALUES (?, ?, ?, ?, 'Booked')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiss",
            $customer_id,
            $service_id,
            $date,
            $time
        );

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function forCustomer($customer_id) {
        $sql = "SELECT a.*, s.name AS service_name
                FROM appointments a
                JOIN services s ON a.service_id = s.service_id
                WHERE a.customer_id = ?
                ORDER BY a.appointment_date DESC, a.slot_time DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function cancel($id, $customer_id) {
        $sql = "UPDATE appointments
                SET status = 'Cancelled'
                WHERE appointment_id = ?
                AND customer_id = ?
                AND status = 'Booked'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $customer_id);
        $stmt->execute();

        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }

    public function findForCustomer($id, $customer_id) {
        $sql = "SELECT *
                FROM appointments
                WHERE appointment_id = ?
                AND customer_id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $id, $customer_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $appointment = $result->fetch_assoc();

        $stmt->close();

        return $appointment;
    }

    public function markCheckedIn($id) {
        $sql = "UPDATE appointments
                SET status = 'Checked-in'
                WHERE appointment_id = ?
                AND status = 'Booked'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}
?>
