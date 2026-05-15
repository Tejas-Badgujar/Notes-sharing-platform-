<?php
/**
 * AJAX endpoint to toggle save/unsave a note
 * POST: note_id
 * Returns JSON: {ok, saved: true/false}
 */
include_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
    exit();
}

$note_id = intval($_POST['note_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

if (!$note_id) {
    echo json_encode(['ok' => false, 'error' => 'invalid_note']);
    exit();
}

// Check if note exists and is approved
$check = $conn->prepare("SELECT id FROM notes WHERE id = ? AND status = 'approved'");
$check->bind_param("i", $note_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode(['ok' => false, 'error' => 'note_not_found']);
    exit();
}

// Check if already saved
$exists = $conn->prepare("SELECT id FROM saved_notes WHERE user_id = ? AND note_id = ?");
$exists->bind_param("ii", $user_id, $note_id);
$exists->execute();
$already_saved = $exists->get_result()->num_rows > 0;

if ($already_saved) {
    // Unsave
    $del = $conn->prepare("DELETE FROM saved_notes WHERE user_id = ? AND note_id = ?");
    $del->bind_param("ii", $user_id, $note_id);
    $ok = $del->execute();
    echo json_encode(['ok' => $ok, 'saved' => false]);
} else {
    // Save
    $ins = $conn->prepare("INSERT INTO saved_notes (user_id, note_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $note_id);
    $ok = $ins->execute();
    echo json_encode(['ok' => $ok, 'saved' => true]);
}
?>
