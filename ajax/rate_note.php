<?php
/**
 * AJAX endpoint to rate a note (1-5 stars)
 * POST: note_id, rating
 * Returns JSON: {ok, avg_rating, total_ratings, user_rating}
 */
include_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'not_logged_in']);
    exit();
}

$note_id = intval($_POST['note_id'] ?? 0);
$rating  = intval($_POST['rating']  ?? 0);
$user_id = (int)$_SESSION['user_id'];

if (!$note_id || $rating < 1 || $rating > 5) {
    echo json_encode(['ok' => false, 'error' => 'invalid_params']);
    exit();
}

// Check note exists and is approved
$check = $conn->prepare("SELECT id FROM notes WHERE id = ? AND status = 'approved'");
$check->bind_param("i", $note_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode(['ok' => false, 'error' => 'note_not_found']);
    exit();
}

// Insert or update rating
$stmt = $conn->prepare("INSERT INTO note_ratings (note_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), rated_at = NOW()");
$stmt->bind_param("iii", $note_id, $user_id, $rating);
$ok = $stmt->execute();

if (!$ok) {
    echo json_encode(['ok' => false, 'error' => 'db_error']);
    exit();
}

// Manually update avg_rating in notes
$upd_avg = $conn->prepare("UPDATE notes SET avg_rating = (SELECT COALESCE(AVG(rating), 0) FROM note_ratings WHERE note_id = ?) WHERE id = ?");
$upd_avg->bind_param("ii", $note_id, $note_id);
$upd_avg->execute();

// Get updated stats
$stats_stmt = $conn->prepare("SELECT AVG(rating) as avg_r, COUNT(*) as total FROM note_ratings WHERE note_id = ?");
$stats_stmt->bind_param("i", $note_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

echo json_encode([
    'ok'            => true,
    'avg_rating'    => round((float)$stats['avg_r'], 1),
    'total_ratings' => (int)$stats['total'],
    'user_rating'   => $rating
]);
?>
