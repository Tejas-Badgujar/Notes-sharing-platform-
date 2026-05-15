<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$remember = isset($_POST['remember']) ? true : false;

if (!$email || !$password) {
    header('Location: login.php?error=missing');
    exit();
}

// Support login by email OR username
$stmt = $conn->prepare("SELECT id, fname, first_name, username, password, user_type, role, email_verified, theme FROM users WHERE email = ? OR username = ?");
$stmt->bind_param("ss", $email, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: login.php?error=invalid');
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header('Location: login.php?error=invalid');
    exit();
}

// Check email verification (skip for admin)
if ($user['role'] !== 'admin' && isset($user['email_verified']) && $user['email_verified'] == 0) {
    header('Location: login.php?error=not_verified');
    exit();
}

// ── Set session with all needed fields ──
$_SESSION['user_id']    = $user['id'];
$_SESSION['username']   = $user['username'] ?: ($user['first_name'] ?: $user['fname']);
$_SESSION['first_name'] = $user['first_name'] ?: $user['fname'];
$_SESSION['user_type']  = $user['user_type'] ?? 'average';
$_SESSION['role']       = $user['role'] ?? 'user';
$_SESSION['theme']      = $user['theme'] ?? '';

// ── Remember Me Cookie ──
if ($remember) {
    $cookie_value = $email;
    setcookie('notes_remember_email', $cookie_value, time() + (30 * 24 * 60 * 60), '/', '', false, true);
} else {
    // Clear the cookie
    setcookie('notes_remember_email', '', time() - 3600, '/', '', false, true);
}

// Update last activity
$upd_act = $conn->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
$upd_act->bind_param("i", $user['id']);
$upd_act->execute();

// Route admin to admin dashboard
if ($user['role'] === 'admin') {
    header('Location: ../admin/dashboard.php');
} else {
    header('Location: ../user/dashboard.php');
}
exit();
?>
