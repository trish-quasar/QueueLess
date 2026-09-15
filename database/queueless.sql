-- QueueLess fresh database setup
-- WARNING: importing this file deletes the existing queueless_db database and all of its data.

DROP DATABASE IF EXISTS queueless_db;
CREATE DATABASE queueless_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE queueless_db;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Customer','Staff','Admin') NOT NULL DEFAULT 'Customer',
    phone VARCHAR(20) NOT NULL,
    security_question VARCHAR(150) NOT NULL,
    security_answer_hash VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255) NULL,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    token_prefix VARCHAR(5) NOT NULL,
    description TEXT,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active'
);

CREATE TABLE counters (
    counter_id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    counter_name VARCHAR(80) NOT NULL,
    status ENUM('Open','Closed') NOT NULL DEFAULT 'Closed',
    FOREIGN KEY (service_id) REFERENCES services(service_id)
);

CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    status ENUM('Booked','Cancelled','Checked-in') NOT NULL DEFAULT 'Booked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(user_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id)
);

CREATE TABLE queue_tokens (
    token_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    counter_id INT NULL,
    appointment_id INT NULL,
    token_number VARCHAR(20) NOT NULL,
    queue_date DATE NOT NULL,
    status ENUM('Waiting','Called','Serving','Skipped','Cancelled','Completed') NOT NULL DEFAULT 'Waiting',
    recall_count INT NOT NULL DEFAULT 0,
    joined_at DATETIME NOT NULL,
    called_at DATETIME NULL,
    service_started_at DATETIME NULL,
    completed_at DATETIME NULL,
    INDEX idx_queue_service_date_status (service_id, queue_date, status),
    INDEX idx_queue_customer_status (customer_id, status),
    INDEX idx_queue_counter_date_status (counter_id, queue_date, status),
    FOREIGN KEY (customer_id) REFERENCES users(user_id),
    FOREIGN KEY (service_id) REFERENCES services(service_id),
    FOREIGN KEY (counter_id) REFERENCES counters(counter_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id)
);

CREATE TABLE staff_assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    counter_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    INDEX idx_assignment_staff_status (staff_id, status),
    INDEX idx_assignment_counter_status (counter_id, status),
    FOREIGN KEY (staff_id) REFERENCES users(user_id),
    FOREIGN KEY (counter_id) REFERENCES counters(counter_id)
);

INSERT INTO services (name, token_prefix, description, status) VALUES
('Accounts Office', 'A', 'Fees, payments, receipts and account-related support.', 'Active'),
('Registrar Office', 'R', 'Registration, academic records and document services.', 'Active'),
('IT Support', 'IT', 'Account, network and technical support.', 'Active'),
('Library Help Desk', 'L', 'Library membership, borrowing and general assistance.', 'Active');

INSERT INTO counters (service_id, counter_name, status) VALUES
(1, 'Accounts Counter 1', 'Closed'),
(1, 'Accounts Counter 2', 'Closed'),
(2, 'Registrar Counter 1', 'Closed'),
(3, 'IT Counter 1', 'Closed'),
(4, 'Library Counter 1', 'Closed');

-- Default administrator password: Admin123
-- Default staff password: Staff123
-- Security answer for both seed accounts: change-me
INSERT INTO users (full_name, username, email, password_hash, role, phone, security_question, security_answer_hash, status) VALUES
('System Administrator', 'admin', 'admin@queueless.local', '$2y$12$pGkMgOxHg/nA7.aesEiYa.tyhYBJTMuijgxCfsmFNsACGwBXf96I2', 'Admin', '01700000000', 'Default question: What is your first school?', '$2y$12$OO8K3ARZR9rOufNAl8na8O1Eu4xhPal6gXkzcZXtgEfY9FyzROwCu', 'Active'),
('Service Staff', 'staff', 'staff@queueless.local', '$2y$12$Mtenh9WrDPm1kNOA15xi9.1llvuhgc8DVilC/5fM1BkCMIZjzGLE2', 'Staff', '01800000000', 'Default question: What is your first school?', '$2y$12$OO8K3ARZR9rOufNAl8na8O1Eu4xhPal6gXkzcZXtgEfY9FyzROwCu', 'Active');

INSERT INTO staff_assignments (staff_id, counter_id, assigned_date, status) VALUES (2, 1, CURDATE(), 'Active');
