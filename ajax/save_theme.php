<?php
include_once '../includes/config.php';

// Only for logged-in users
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
    exit();
}

header('Content-Type: application/json');

$allowed = ['', 'theme-gold', 'theme-pinky', 'theme-demon', 'theme-light'];
$theme   = $_POST['theme'] ?? '';

if (!in_array($theme, $allowed)) {
    echo json_encode(['ok' => false, 'error' => 'invalid_theme']);
    exit();
}

$uid  = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("UPDATE users SET theme = ? WHERE id = ?");
$stmt->bind_param("si", $theme, $uid);
$ok   = $stmt->execute();

echo json_encode(['ok' => $ok]);
?>
