<?php
include '../includes/config.php';

// ── Login guard ──
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=download');
    exit();
}

$note_id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

if (!$note_id) {
    http_response_code(400);
    echo "Invalid note.";
    exit();
}

// Fetch note (must be approved)
$stmt = $conn->prepare("SELECT title, file_path FROM notes WHERE id = ? AND status = 'approved'");
$stmt->bind_param("i", $note_id);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();

if (!$note) {
    http_response_code(404);
    echo "Note not found or not approved.";
    exit();
}

$file_path = '../' . ltrim($note['file_path'], '/');

if (!file_exists($file_path)) {
    http_response_code(404);
    echo "File not found on server.";
    exit();
}

// ── Increment download counter ──
$upd = $conn->prepare("UPDATE notes SET downloads = downloads + 1 WHERE id = ?");
$upd->bind_param("i", $note_id);
$upd->execute();

// ── Serve file ──
$filename = basename($file_path);
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache');
readfile($file_path);
exit();
?>
