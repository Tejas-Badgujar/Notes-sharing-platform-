<?php
/**
 * AJAX endpoint to resend OTP via Brevo mailer
 */
include_once '../includes/config.php';

header('Content-Type: application/json');

$type = $_POST['type'] ?? '';

if ($type === 'register' && isset($_SESSION['temp_reg_data'])) {
    $email = $_SESSION['temp_reg_data']['email'];
} elseif ($type === 'reset' && isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
} else {
    echo json_encode(['ok' => false, 'error' => 'no_session']);
    exit();
}

// Generate new OTP
$otp = (string)rand(100000, 999999);
$_SESSION['current_otp'] = $otp;
$_SESSION['otp_time']    = time();

// Send via Brevo
require_once '../includes/mailer.php';
$sent = send_otp_email($email, $otp);

echo json_encode(['ok' => $sent]);
?>
