<?php
require 'includes/mailer.php';

$test_email = 'beingbokhya123@gmail.com';
$test_otp = '123456';

echo "Attempting to send OTP to $test_email...\n";
$result = send_otp_email($test_email, $test_otp);

if ($result) {
    echo "SUCCESS: Brevo accepted the email.\n";
} else {
    echo "FAILURE: Check error_log or Brevo response.\n";
}
?>
