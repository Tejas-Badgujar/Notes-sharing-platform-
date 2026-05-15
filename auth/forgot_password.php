<?php
$level = '../';
$page_title = 'Forgot Password';
include '../includes/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    if (!$identifier) {
        $error = 'Please enter your email or username.';
    } else {
        $stmt = $conn->prepare("SELECT id, email, username FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $u = $res->fetch_assoc();
            
            $otp = (string)rand(100000, 999999);
            $_SESSION['current_otp']   = $otp;
            $_SESSION['otp_time']      = time();
            $_SESSION['reset_user_id'] = $u['id'];
            $_SESSION['reset_email']   = $u['email'];
            
            require_once '../includes/mailer.php';
            send_otp_email($u['email'], $otp);
            
            header("Location: verify_otp.php?type=reset");
            exit();
        } else {
            $error = 'No account found with that email or username.';
        }
    }
}

include '../includes/header.php';
?>

<div class="video-bg-container">
    <video autoplay muted loop playsinline id="bg-video">
        <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-technology-lines-flowing-21110-large.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
</div>

<div class="main-content" style="display: flex; align-items: center; justify-content: center; min-height: 100vh; margin-top: 0;">
    <div class="container" style="max-width: 450px; padding: 0 1rem;">
        
        <div class="glass-panel animate-fade-in-up" style="padding: 3rem; border-radius: 30px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(0,243,255,0.1); border: 1px solid rgba(0,243,255,0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                    <i class="fa-solid fa-lock" style="font-size: 1.5rem; color: var(--accent-cyan);"></i>
                </div>
                <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Forgot Password</h2>
                <p style="color: var(--text-secondary); font-size: 0.95rem;">Enter your email or username and we'll send a verification code to reset your password.</p>
            </div>

            <?php if($error): ?>
              <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group" style="margin-bottom: 2rem;">
                    <input type="text" name="identifier" class="form-control" id="identifier" placeholder=" " required>
                    <label for="identifier" class="form-label">Email Address or Username</label>
                </div>
                
                <button type="submit" class="btn btn-primary magnetic-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    Send Verification Code <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

            <div style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                Remember your password? 
                <a href="login.php" style="color: var(--accent-cyan); text-decoration: none; font-weight: 600;">Back to Login</a>
            </div>
        </div>

    </div>
</div>

<style>
body { overflow: hidden; }
.main-content { margin-top: 0 !important; }
</style>

<?php include '../includes/footer.php'; ?>
