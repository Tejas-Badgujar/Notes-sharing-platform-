<?php
include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: upload.php');
    exit();
}

$user_id     = $_SESSION['user_id'];
$title       = trim($_POST['title']       ?? '');
$field       = trim($_POST['field']       ?? 'General');
$subject     = trim($_POST['subject']     ?? '');
$description = trim($_POST['description'] ?? '');
$tags        = trim($_POST['tags']        ?? '');

if (!$title || !$field || !$subject) {
    header('Location: upload.php?error=1');
    exit();
}

// ── File validation ──
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: upload.php?error=1');
    exit();
}

$file_type = mime_content_type($_FILES['file']['tmp_name']);
if ($file_type !== 'application/pdf') {
    header('Location: upload.php?error=type');
    exit();
}

$max_size = 500 * 1024 * 1024; // 500 MB
if ($_FILES['file']['size'] > $max_size) {
    header('Location: upload.php?error=size');
    exit();
}

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$safe_name   = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['file']['name']));
$file_name   = time() . '_' . $user_id . '_' . $safe_name;
$target_file = $upload_dir . $file_name;
$db_path     = 'uploads/' . $file_name;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
    header('Location: upload.php?error=1');
    exit();
}

// ── Insert into DB (no semester) ──
$semester = 0; // Default, semester is removed from UI
$stmt = $conn->prepare(
    "INSERT INTO notes (user_id, title, field, semester, subject, description, tags, file_path, status, downloads, avg_rating)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 0, 0.00)"
);
$stmt->bind_param("ississss", $user_id, $title, $field, $semester, $subject, $description, $tags, $db_path);

if (!$stmt->execute()) {
    // Retry with simpler param count (in case some columns don't exist yet)
    $stmt2 = $conn->prepare("INSERT INTO notes (user_id, title, subject, file_path, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt2->bind_param("isss", $user_id, $title, $subject, $db_path);
    $stmt2->execute();
}

// ── Streak logic ──
$today     = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$streak_stmt = $conn->prepare("SELECT last_upload_date, current_streak, max_streak FROM users WHERE id = ?");
$streak_stmt->bind_param("i", $user_id);
$streak_stmt->execute();
$user_row  = $streak_stmt->get_result()->fetch_assoc();
$streak    = (int)($user_row['current_streak'] ?? 0);
$max       = (int)($user_row['max_streak']     ?? 0);
$last      = $user_row['last_upload_date']     ?? '';

if ($last !== $today) {
    $streak = ($last === $yesterday) ? $streak + 1 : 1;
    if ($streak > $max) $max = $streak;
    $upd = $conn->prepare("UPDATE users SET current_streak=?, max_streak=?, last_upload_date=? WHERE id=?");
    $upd->bind_param("iisi", $streak, $max, $today, $user_id);
    $upd->execute();
}

header('Location: my_notes.php?success=1');
exit();
?>
