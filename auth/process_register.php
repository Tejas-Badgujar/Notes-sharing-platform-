<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit();
}

// ── Slang / profanity blocklist ──
$blocked_words = ['fuck','shit','bitch','asshole','bastard','cunt','dick','pussy','nigger','faggot',
                  'slut','whore','retard','idiot','moron','dumb','stupid','loser','ugly'];

$first_name       = trim($_POST['first_name'] ?? '');
$last_name        = trim($_POST['last_name']  ?? '');
$username         = trim($_POST['username']   ?? '');
$email            = trim($_POST['email']      ?? '');
$password         = $_POST['password']        ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$branch           = trim($_POST['branch']     ?? '');

// Always standard user — no premium option
$user_type = 'average';

// ── Validate username for slang ──
$user_lower = strtolower($username);
foreach ($blocked_words as $word) {
    if (strpos($user_lower, $word) !== false) {
        header('Location: register.php?error=slang_username');
        exit();
    }
}

// ── Basic validation ──
if (!$first_name || !$email || !$password || !$username) {
    header('Location: register.php?error=missing_fields');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=invalid_email');
    exit();
}

if (strlen($password) < 6) {
    header('Location: register.php?error=weak_password');
    exit();
}

// ── Confirm password match ──
if ($password !== $confirm_password) {
    header('Location: register.php?error=password_mismatch');
    exit();
}

// ── Check duplicate email / username ──
$check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$check->bind_param("ss", $email, $username);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    header('Location: register.php?error=exists');
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// ── Save to session, generate real OTP, and send email ──
$otp = (string)rand(100000, 999999);
$_SESSION['current_otp'] = $otp;
$_SESSION['otp_time']    = time();

$_SESSION['temp_reg_data'] = [
    'first_name' => $first_name,
    'last_name'  => $last_name,
    'username'   => $username,
    'email'      => $email,
    'hash'       => $hash,
    'branch'     => $branch,
    'user_type'  => $user_type
];

require_once '../includes/mailer.php';
$mail_sent = send_otp_email($email, $otp);

if (!$mail_sent) {
    // If mail fails, still redirect but log the error
    error_log("Failed to send OTP email to: $email");
}

header('Location: verify_otp.php?type=register');
exit();
?>
