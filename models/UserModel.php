<?php
class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        return $user;
    }

    public function findById($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE user_id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();

        return $user;
    }

    public function emailExists($email, $except_id = 0) {
        if ($except_id > 0) {
            $stmt = $this->conn->prepare(
                "SELECT user_id
                 FROM users
                 WHERE email = ?
                 AND user_id != ?
                 LIMIT 1"
            );
            $stmt->bind_param("si", $email, $except_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT user_id
                 FROM users
                 WHERE email = ?
                 LIMIT 1"
            );
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function create(
        $full_name,
        $email,
        $password,
        $role,
        $phone,
        $security_question,
        $security_answer,
        $photo = null
    ) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $answer_hash = password_hash(
            strtolower(trim($security_answer)),
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users
                (full_name, email, password_hash, role, phone, security_question, security_answer_hash, profile_photo, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssss",
            $full_name,
            $email,
            $password_hash,
            $role,
            $phone,
            $security_question,
            $answer_hash,
            $photo
        );

        $success = $stmt->execute();
        $user_id = $this->conn->insert_id;
        $stmt->close();

        if ($success) {
            return $user_id;
        }

        return false;
    }

    public function updateProfile($id, $full_name, $phone, $photo = null) {
        if ($photo !== null) {
            $sql = "UPDATE users
                    SET full_name = ?, phone = ?, profile_photo = ?
                    WHERE user_id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $full_name, $phone, $photo, $id);
        } else {
            $sql = "UPDATE users
                    SET full_name = ?, phone = ?
                    WHERE user_id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $full_name, $phone, $id);
        }

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function changePassword($id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "UPDATE users SET password_hash = ? WHERE user_id = ?"
        );
        $stmt->bind_param("si", $password_hash, $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function deactivate($id) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET status = 'Inactive' WHERE user_id = ?"
        );
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getAll() {
        $sql = "SELECT user_id, full_name, email, phone, role, status, profile_photo, created_at
                FROM users
                ORDER BY created_at DESC";

        return $this->conn->query($sql);
    }

    public function setStatus($id, $status) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET status = ? WHERE user_id = ?"
        );
        $stmt->bind_param("si", $status, $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function setRole($id, $role) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET role = ? WHERE user_id = ?"
        );
        $stmt->bind_param("si", $role, $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }
}
?>
