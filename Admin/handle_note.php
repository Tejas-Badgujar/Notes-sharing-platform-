<?php
include '../includes/config.php';

// Admin guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    if ($status === 'delete') {
        // Delete the note and its file
        $note = $conn->query("SELECT file_path FROM notes WHERE id = $id")->fetch_assoc();
        if ($note && $note['file_path']) {
            $file = '../' . ltrim($note['file_path'], '/');
            if (file_exists($file)) unlink($file);
        }
        $stmt = $conn->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? "success" : "error: " . $conn->error;
    } elseif (in_array($status, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE notes SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        echo $stmt->execute() ? "success" : "error: " . $conn->error;
    } else {
        echo 'Invalid status';
    }
}
?>
