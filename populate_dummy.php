<?php
include 'includes/config.php';

// Insert Dummy Users
$users = [
    ['John', 'Doe', 'john@gmail.com', 'CS'],
    ['Jane', 'Smith', 'jane@yahoo.com', 'IT'],
    ['Alice', 'Johnson', 'alice@gmail.com', 'EC'],
];

foreach ($users as $u) {
    $pass = password_hash('password123', PASSWORD_DEFAULT);
    $check = $conn->query("SELECT id FROM users WHERE email = '$u[2]'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO users (fname, lname, email, password, branch) VALUES ('$u[0]', '$u[1]', '$u[2]', '$pass', '$u[3]')");
    }
}

// Insert Dummy Notes for each user
$res = $conn->query("SELECT id FROM users WHERE role = 'user' LIMIT 3");
$user_ids = [];
while($r = $res->fetch_assoc()) $user_ids[] = $r['id'];

if (count($user_ids) > 0) {
    $notes = [
        [$user_ids[0], 'Data Structures Unit 1', 'Data Structures', 'uploads/sample1.pdf'],
        [$user_ids[1], 'Algorithm Design Ch 2', 'Algorithms', 'uploads/sample2.pdf'],
        [$user_ids[2] ?? $user_ids[0], 'Microprocessors Notes', 'EC', 'uploads/sample3.pdf'],
    ];

    foreach ($notes as $n) {
        $check = $conn->query("SELECT id FROM notes WHERE title = '$n[1]'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO notes (user_id, title, subject, file_path, status) VALUES ($n[0], '$n[1]', '$n[2]', '$n[3]', 'pending')");
        }
    }
}

echo "Dummy data populated successfully!";
?>
