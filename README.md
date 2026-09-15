# QueueLess

QueueLess is a web-based virtual queue and appointment management system developed for the CSC 3215 Web Technologies course at American International University-Bangladesh.

The system is designed for service environments where customers would otherwise wait in physical lines. Customers can join a queue online, receive a digital token, follow their position, and book appointments. Service staff operate assigned counters, while administrators manage services, users, counters, assignments, and reports.

## Features

### Customer
- Register with a unique username and email address
- Sign in using either username or email
- Live username and email availability checking during registration
- Live password requirement checking
- View available services and current waiting counts
- Join a virtual queue and receive a digital token
- Track token status, people ahead, currently serving token, and assigned counter
- Cancel a waiting token
- Book, cancel, and check in to appointments
- View queue and appointment history
- Manage profile information and upload a profile photo
- Change or reset password
- Deactivate account

### Service Staff
- View assigned service and counter
- Open or close the assigned counter
- View the live waiting queue
- Call the next token
- Recall a called token
- Start serving a customer
- Skip a token
- Complete a service
- View daily service history

### Administrator
- View system and queue statistics
- Create, update, activate, deactivate, and remove services
- Manage service counters
- Create Staff and Administrator accounts
- Activate or deactivate users
- Assign staff members to counters
- Monitor active queues
- View service activity and average waiting-time reports

## Technologies Used

- HTML5
- CSS3
- JavaScript
- PHP
- MySQL / MySQLi
- AJAX using `XMLHttpRequest`
- JSON
- MVC-style project organization
- PHP sessions and cookies

## Project Structure

```text
GPTQueueLess/
├── assets/
│   ├── css/
│   └── js/
├── config/
│   └── config.php
├── controllers/
├── database/
│   └── queueless.sql
├── models/
├── uploads/
│   └── profile/
├── views/
│   ├── admin/
│   ├── auth/
│   ├── customer/
│   ├── includes/
│   ├── profile/
│   ├── public/
│   └── staff/
├── index.php
└── README.md
```

## Main Queue Flow

```text
Waiting → Called → Serving → Completed
             └────────────→ Skipped

Waiting → Cancelled
```

Customer and Staff queue information is refreshed in the background using AJAX and JSON, so important queue changes can appear without a full page reload.

## Registration Validation

Registration uses both client-side and server-side validation.

AJAX checks are used for:
- Email availability
- Username availability
- Password requirements

A password must contain:
- At least 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one symbol

The same rules are checked again by PHP before an account is created.

## Authentication and Security

- Users can sign in with either their username or email address.
- Passwords and security answers are stored as hashes.
- PHP sessions maintain authenticated state and user roles.
- The PHP session identifier is stored in the browser session cookie.
- A separate non-sensitive cookie remembers the customer's last selected service.
- Prepared statements are used for database operations involving user input.
- Role checks protect Customer, Staff, and Administrator functionality.

## File Upload

Customers can upload a JPG or PNG profile photo during registration or from the profile page.

Uploaded profile images are stored in:

```text
uploads/profile/
```

The maximum profile-photo size is 2 MB.

## Database Setup

The complete database setup is located at:

```text
database/queueless.sql
```

This is a **fresh database script**. Importing it will delete the existing `queueless_db` database and recreate the complete current schema from scratch.

The main tables are:

```text
users
services
counters
appointments
queue_tokens
staff_assignments
```

The script also inserts the initial services, counters, Administrator account, Service Staff account, and the default Staff assignment.

## Running with MAMP on macOS

1. Copy the project to:

   ```text
   /Applications/MAMP/htdocs/GPTQueueLess/
   ```

2. Start Apache and MySQL in MAMP.
3. Open phpMyAdmin.
4. Import:

   ```text
   database/queueless.sql
   ```

5. Open QueueLess in the browser:

   ```text
   http://localhost/GPTQueueLess/
   ```

   If MAMP uses Apache port 8888, use:

   ```text
   http://localhost:8888/GPTQueueLess/
   ```

## Running with XAMPP on Windows

1. Copy the project to:

   ```text
   C:\xampp\htdocs\GPTQueueLess\
   ```

2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open:

   ```text
   http://localhost/phpmyadmin/
   ```

4. Import:

   ```text
   database/queueless.sql
   ```

5. Open:

   ```text
   http://localhost/GPTQueueLess/
   ```

`config/config.php` supports the common local MAMP and XAMPP MySQL defaults. The database settings can also be overridden with environment variables if required.

## Default Local Accounts

### Administrator

```text
Username: admin
Email: admin@queueless.local
Password: Admin123
```

### Service Staff

```text
Username: staff
Email: staff@queueless.local
Password: Staff123
```

Customers create their own accounts from the registration page.

The seeded accounts are provided for local development and academic demonstration.

## Course Information

**Course:** CSC 3215: Web Technologies  
**Institution:** American International University-Bangladesh  
**Semester:** Summer 2025-26  
**Section:** N

## Group Members

- Nur E Alam Siddike
- Faradiva Haque Tithi
- Trishan Talukder
- Tamim Hasan

## Purpose

QueueLess was developed as an academic project to demonstrate PHP, MySQL, JavaScript, AJAX, JSON, MVC organization, authentication, role-based authorization, form validation, sessions, cookies, file upload, relational database design, and CRUD operations in one multi-user web application.
