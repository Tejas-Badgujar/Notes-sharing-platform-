<?php
$host = "localhost";
$user = "root";
$pass = "";

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS notes_sharing";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("notes_sharing");

// sql to create table users
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    branch VARCHAR(50),
    role ENUM('admin', 'user') DEFAULT 'user',
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Table users created successfully<br>";
} else {
    echo "Error creating table users: " . $conn->error . "<br>";
}

// sql to create table notes
$sql_notes = "CREATE TABLE IF NOT EXISTS notes (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED,
    title VARCHAR(255) NOT NULL,
    subject VARCHAR(100),
    file_path VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql_notes) === TRUE) {
    echo "Table notes created successfully<br>";
} else {
    echo "Error creating table notes: " . $conn->error . "<br>";
}

// Insert a default admin if not exists
$admin_email = "admin@notes.com";
$admin_pass = password_hash("admin123", PASSWORD_DEFAULT);
$check_admin = "SELECT * FROM users WHERE email = '$admin_email'";
$result = $conn->query($check_admin);
if ($result->num_rows == 0) {
    $insert_admin = "INSERT INTO users (fname, lname, email, password, role) VALUES ('System', 'Admin', '$admin_email', '$admin_pass', 'admin')";
    if ($conn->query($insert_admin) === TRUE) {
        echo "Default admin created successfully<br>";
    }
}

$conn->close();
?>
