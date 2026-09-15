<?php
class StaffController {
    private $conn;
    private $counterModel;
    private $queueModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->counterModel = new CounterModel($conn);
        $this->queueModel = new QueueModel($conn);
    }

    public function dashboard() {
        requireRole('Staff');

        $assignment = $this->counterModel->getAssignmentForStaff(
            $_SESSION['user_id']
        );

        $current = null;
        $waiting = null;

        if ($assignment) {
            $current = $this->queueModel->currentForCounter(
                $assignment['counter_id']
            );

            $waiting = $this->queueModel->waitingForCounter(
                $assignment['counter_id']
            );
        }

        require 'views/staff/dashboard.php';
    }

    private function getAssignment() {
        $assignment = $this->counterModel->getAssignmentForStaff(
            $_SESSION['user_id']
        );

        if (!$assignment) {
            $_SESSION['errors'] = [
                'No active counter is assigned to you.'
            ];
            return null;
        }

        return $assignment;
    }

    public function counterStatus() {
        requireRole('Staff');

        $assignment = $this->getAssignment();

        if (!$assignment) {
            header('Location: index.php?action=dashboard');
            exit();
        }

        $status = ($_POST['status'] ?? '') === 'Open'
            ? 'Open'
            : 'Closed';

        $this->counterModel->setCounterStatus(
            $assignment['counter_id'],
            $status
        );

        $_SESSION['success'] = 'Counter status updated.';

        header('Location: index.php?action=dashboard');
        exit();
    }

    public function queueAction($type) {
        requireRole('Staff');

        $assignment = $this->getAssignment();

        if (!$assignment) {
            header('Location: index.php?action=dashboard');
            exit();
        }

        if ($assignment['counter_status'] !== 'Open' && $type === 'call') {
            $_SESSION['errors'] = [
                'Open your counter before calling a token.'
            ];

            header('Location: index.php?action=dashboard');
            exit();
        }

        $success = false;

        if ($type === 'call') {
            $success = $this->queueModel->callNext(
                $assignment['counter_id'],
                $assignment['service_id']
            );

        } elseif ($type === 'serve') {
            $success = $this->queueModel->startServing(
                $assignment['counter_id']
            );

        } elseif ($type === 'recall') {
            $success = $this->queueModel->recall(
                $assignment['counter_id']
            );

        } elseif ($type === 'skip') {
            $success = $this->queueModel->skip(
                $assignment['counter_id']
            );

        } elseif ($type === 'complete') {
            $success = $this->queueModel->complete(
                $assignment['counter_id']
            );
        }

        if ($success) {
            $_SESSION['success'] = 'Queue updated successfully.';
        } else {
            $_SESSION['errors'] = [
                'This action could not be completed.'
            ];
        }

        header('Location: index.php?action=dashboard');
        exit();
    }

    public function history() {
        requireRole('Staff');

        $assignment = $this->counterModel->getAssignmentForStaff(
            $_SESSION['user_id']
        );

        if ($assignment) {
            $sql = "SELECT q.*, u.full_name
                    FROM queue_tokens q
                    JOIN users u ON q.customer_id = u.user_id
                    WHERE q.counter_id = ?
                    AND q.queue_date = CURDATE()
                    AND q.status IN ('Completed', 'Skipped')
                    ORDER BY q.completed_at DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $assignment['counter_id']);
            $stmt->execute();
            $history = $stmt->get_result();
        } else {
            $history = null;
        }

        require 'views/staff/history.php';
    }
}
?>
