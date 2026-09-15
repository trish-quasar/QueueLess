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

    public function findByLogin($identifier) {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM users
             WHERE email = ? OR username = ?
             LIMIT 1"
        );
        $stmt->bind_param("ss", $identifier, $identifier);
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
                 WHERE email = ? AND user_id != ?
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

    public function usernameExists($username, $except_id = 0) {
        if ($except_id > 0) {
            $stmt = $this->conn->prepare(
                "SELECT user_id
                 FROM users
                 WHERE username = ? AND user_id != ?
                 LIMIT 1"
            );
            $stmt->bind_param("si", $username, $except_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT user_id
                 FROM users
                 WHERE username = ?
                 LIMIT 1"
            );
            $stmt->bind_param("s", $username);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function validateUsername($username) {
        if ($username === '') {
            return 'Username is required.';
        }

        if (!preg_match('/^[A-Za-z0-9_.]{4,30}$/', $username)) {
            return 'Use 4-30 letters, numbers, dots or underscores only.';
        }

        return '';
    }

    public function passwordChecks($password) {
        $checks = [
            'length' => strlen($password) >= 8,
            'uppercase' => preg_match('/[A-Z]/', $password) === 1,
            'lowercase' => preg_match('/[a-z]/', $password) === 1,
            'number' => preg_match('/[0-9]/', $password) === 1,
            'symbol' => preg_match('/[^A-Za-z0-9]/', $password) === 1
        ];

        $checks['valid'] = !in_array(false, $checks, true);

        return $checks;
    }

    public function passwordError($password) {
        $checks = $this->passwordChecks($password);

        if (!$checks['length']) {
            return 'Password must contain at least 8 characters.';
        }
        if (!$checks['uppercase']) {
            return 'Password must include an uppercase letter.';
        }
        if (!$checks['lowercase']) {
            return 'Password must include a lowercase letter.';
        }
        if (!$checks['number']) {
            return 'Password must include a number.';
        }
        if (!$checks['symbol']) {
            return 'Password must include a symbol.';
        }

        return '';
    }

    public function create(
        $full_name,
        $username,
        $email,
        $password,
        $role,
        $phone,
        $security_question,
        $security_answer,
        $photo = null
    ) {
        $username = strtolower(trim($username));
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $answer_hash = password_hash(
            strtolower(trim($security_answer)),
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO users
                (full_name, username, email, password_hash, role, phone, security_question, security_answer_hash, profile_photo, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssssssss",
            $full_name,
            $username,
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

        return $success ? $user_id : false;
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
        $sql = "SELECT user_id, full_name, username, email, phone, role, status, profile_photo, created_at
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
