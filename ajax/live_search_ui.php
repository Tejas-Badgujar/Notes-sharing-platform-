<?php
/**
 * AJAX Live Search — queries notes DB in real-time
 * GET: q (search query)
 * Returns JSON: [{id, title, field, subject, avg_rating, downloads}]
 */
include_once '../includes/config.php';

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');

if (strlen($query) < 1) {
    echo json_encode([]);
    exit();
}

// Prepared statement — search title, subject, tags, field
$search_term = '%' . $query . '%';
$stmt = $conn->prepare("
    SELECT n.id, n.title, n.field, n.subject, n.avg_rating, n.downloads
    FROM notes n
    WHERE n.status = 'approved'
      AND (n.title LIKE ? OR n.subject LIKE ? OR n.tags LIKE ? OR n.field LIKE ?)
    ORDER BY 
        CASE WHEN n.title LIKE ? THEN 0 ELSE 1 END,
        n.downloads DESC
    LIMIT 6
");
$exact_term = $query . '%';
$stmt->bind_param("sssss", $search_term, $search_term, $search_term, $search_term, $exact_term);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        'id'        => (int)$row['id'],
        'title'     => $row['title'],
        'field'     => $row['field'],
        'subject'   => $row['subject'],
        'rating'    => number_format((float)$row['avg_rating'], 1),
        'downloads' => (int)$row['downloads']
    ];
}

echo json_encode($results);
?>
