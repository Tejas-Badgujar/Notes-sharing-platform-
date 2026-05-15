<?php
/**
 * Migration Script — Run once to update the database schema.
 * Visit: http://localhost/Notes%20Sharing/migrate.php
 */

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "notes_sharing";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$results = [];

// 1. Add email_verified to users
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'email_verified'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 1");
    $results[] = "✅ Added email_verified column to users";
} else {
    $results[] = "⏭️ email_verified column already exists";
}

// 2. Ensure note_ratings table exists
$check = $conn->query("SHOW TABLES LIKE 'note_ratings'");
if ($check->num_rows === 0) {
    $conn->query("CREATE TABLE note_ratings (
        id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        note_id INT(10) UNSIGNED NOT NULL,
        user_id INT(10) UNSIGNED NOT NULL,
        rating TINYINT(4) NOT NULL CHECK (rating BETWEEN 1 AND 5),
        rated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        UNIQUE KEY uq_rating (note_id, user_id),
        KEY user_id (user_id),
        FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    $results[] = "✅ Created note_ratings table";
} else {
    $results[] = "⏭️ note_ratings table already exists";
}

// 3. Ensure saved_notes table exists
$check = $conn->query("SHOW TABLES LIKE 'saved_notes'");
if ($check->num_rows === 0) {
    $conn->query("CREATE TABLE saved_notes (
        id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id INT(10) UNSIGNED NOT NULL,
        note_id INT(10) UNSIGNED NOT NULL,
        saved_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        UNIQUE KEY uq_saved (user_id, note_id),
        KEY note_id (note_id),
        FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    $results[] = "✅ Created saved_notes table";
} else {
    $results[] = "⏭️ saved_notes table already exists";
}

// 4. Ensure avg_rating column in notes
$check = $conn->query("SHOW COLUMNS FROM notes LIKE 'avg_rating'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN avg_rating DECIMAL(3,2) NOT NULL DEFAULT 0.00");
    $results[] = "✅ Added avg_rating column to notes";
} else {
    $results[] = "⏭️ avg_rating column already exists";
}

// 5. Ensure downloads column in notes
$check = $conn->query("SHOW COLUMNS FROM notes LIKE 'downloads'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN downloads INT(11) NOT NULL DEFAULT 0");
    $results[] = "✅ Added downloads column to notes";
} else {
    $results[] = "⏭️ downloads column already exists";
}

// 6. Ensure tags column in notes
$check = $conn->query("SHOW COLUMNS FROM notes LIKE 'tags'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN tags VARCHAR(255) DEFAULT NULL");
    $results[] = "✅ Added tags column to notes";
} else {
    $results[] = "⏭️ tags column already exists";
}

// 7. Ensure description column in notes
$check = $conn->query("SHOW COLUMNS FROM notes LIKE 'description'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN description TEXT DEFAULT NULL");
    $results[] = "✅ Added description column to notes";
} else {
    $results[] = "⏭️ description column already exists";
}

// 8. Ensure field column in notes
$check = $conn->query("SHOW COLUMNS FROM notes LIKE 'field'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN field VARCHAR(60) NOT NULL DEFAULT 'General'");
    $results[] = "✅ Added field column to notes";
} else {
    $results[] = "⏭️ field column already exists";
}

// 9. Ensure theme column in users
$check = $conn->query("SHOW COLUMNS FROM users LIKE 'theme'");
if ($check->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN theme VARCHAR(30) NOT NULL DEFAULT ''");
    $results[] = "✅ Added theme column to users";
} else {
    $results[] = "⏭️ theme column already exists";
}

// 10. Mark all existing users as email_verified
$conn->query("UPDATE users SET email_verified = 1 WHERE email_verified = 0 OR email_verified IS NULL");
$results[] = "✅ All existing users marked as email_verified";

// 11. Ensure default admin exists
$admin_email = "admin@notes.com";
$check = $conn->query("SELECT id FROM users WHERE email = '$admin_email'");
if ($check->num_rows === 0) {
    $admin_pass = password_hash("admin123", PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (fname, lname, first_name, last_name, email, password, role, email_verified, username) 
                  VALUES ('System', 'Admin', 'System', 'Admin', '$admin_email', '$admin_pass', 'admin', 1, 'admin')");
    $results[] = "✅ Default admin created (admin@notes.com / admin123)";
} else {
    $results[] = "⏭️ Default admin already exists";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head><title>Migration</title>
<style>
body { font-family: 'Outfit', sans-serif; background: #0a0b1a; color: #fff; padding: 3rem; }
.result { padding: 0.8rem 1.2rem; margin: 0.5rem 0; background: rgba(255,255,255,0.05); border-radius: 8px; border-left: 3px solid #00f3ff; }
h1 { color: #00f3ff; }
</style>
</head>
<body>
<h1>🔧 Database Migration</h1>
<?php foreach ($results as $r): ?>
<div class="result"><?= $r ?></div>
<?php endforeach; ?>
<br>
<p style="color: rgba(255,255,255,0.5);">Migration complete. You can delete this file now.</p>
<a href="index.php" style="color: #00f3ff;">← Go to Homepage</a>
</body>
</html>
