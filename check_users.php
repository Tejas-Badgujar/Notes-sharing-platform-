<?php
$conn = new mysqli('localhost', 'root', '', 'notes_sharing');
$res = $conn->query('SELECT id, fname, email FROM users');
while($row = $res->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['fname'] . " | Email: " . $row['email'] . "\n";
}
?>
