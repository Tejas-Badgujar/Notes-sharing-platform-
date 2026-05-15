<?php
include '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['ok' => false, 'error' => 'unauthorized']);
    exit();
}

$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['ok' => false, 'error' => 'invalid_id']); exit(); }

$check = $conn->prepare("SELECT role FROM users WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$user = $check->get_result()->fetch_assoc();

if (!$user || $user['role'] === 'admin') {
    echo json_encode(['ok' => false, 'error' => 'cannot_delete_admin']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
$stmt->bind_param("i", $id);
$ok = $stmt->execute();
echo json_encode(['ok' => $ok]);
?>
