<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

$type = $_POST['type'] ?? '';
$otp  = $_POST['otp']  ?? '';

// Validate against the real OTP generated and sent to the email
$expected_otp = $_SESSION['current_otp'] ?? null;
$otp_time     = $_SESSION['otp_time'] ?? 0;

// Check if OTP is expired (5 minutes = 300 seconds)
if ((time() - $otp_time) > 300) {
    header("Location: verify_otp.php?type=$type&error=expired");
    exit();
}

if (!$expected_otp || $otp !== $expected_otp) {
    header("Location: verify_otp.php?type=$type&error=invalid");
    exit();
}

if ($type === 'register') {
    $data = $_SESSION['temp_reg_data'] ?? null;
    if (!$data) {
        header('Location: register.php');
        exit();
    }

    // Insert user — populate both fname/lname (legacy) and first_name/last_name (new)
    $stmt = $conn->prepare(
        "INSERT INTO users (fname, lname, first_name, last_name, username, email, password, branch, user_type, email_verified)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
    );
    $stmt->bind_param("sssssssss", 
        $data['first_name'], $data['last_name'], $data['first_name'], $data['last_name'],
        $data['username'], $data['email'], $data['hash'], $data['branch'], $data['user_type']
    );

    if ($stmt->execute()) {
        $new_id = $conn->insert_id;
        $_SESSION['user_id']   = $new_id;
        $_SESSION['username']  = $data['username'];
        $_SESSION['user_type'] = $data['user_type'];
        $_SESSION['first_name']= $data['first_name'];
        $_SESSION['role']      = 'user';
        unset($_SESSION['temp_reg_data']);
        unset($_SESSION['current_otp']);
        unset($_SESSION['otp_time']);

        header('Location: ../user/dashboard.php?welcome=1');
        exit();
    } else {
        header('Location: register.php?error=db');
        exit();
    }
} elseif ($type === 'reset') {
    if (!isset($_SESSION['reset_user_id'])) {
        header('Location: forgot_password.php');
        exit();
    }
    
    $_SESSION['otp_verified_for_reset'] = true;
    header('Location: reset_password.php');
    exit();
} else {
    header('Location: ../index.php');
    exit();
}
?>
