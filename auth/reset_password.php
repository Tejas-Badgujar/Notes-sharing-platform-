<?php
$level = '../';
$page_title = 'Reset Password';
include '../includes/config.php';

// Auth guard
if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['otp_verified_for_reset'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass1 = $_POST['pass1'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';
    
    if (strlen($pass1) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($pass1 !== $pass2) {
        $error = 'Passwords do not match.';
    } else {
        // Update password
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $_SESSION['reset_user_id']);
        
        if ($stmt->execute()) {
            $success = true;
            // Clean up session
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified_for_reset']);
        } else {
            $error = 'Failed to reset password. Please try again.';
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
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 0 20px rgba(0,255,136,0.2);">
                    <i class="fa-solid fa-key" style="font-size: 1.5rem; color: #00ff88;"></i>
                </div>
                <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem;">New Password</h2>
                <p style="color: var(--text-secondary); font-size: 0.95rem;">Secure your account with a strong new password.</p>
            </div>

            <?php if($success): ?>
                <div style="background:rgba(0,255,136,0.1);border:1px solid rgba(0,255,136,0.4);color:#00ff88;padding:1.5rem;border-radius:12px;margin-bottom:1.5rem;text-align:center;">
                    <i class="fa-solid fa-circle-check" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <strong>Password Reset Successfully!</strong><br>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; color: rgba(255,255,255,0.7);">You can now log in with your new password.</p>
                </div>
                <a href="login.php?success=reset" class="btn btn-primary magnetic-btn" style="display: block; text-align: center; width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 12px;">Go to Login</a>
            <?php else: ?>
                <?php if($error): ?>
                  <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
                  </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group" style="position: relative;">
                        <input type="password" name="pass1" class="form-control" id="pass1" placeholder=" " required minlength="6" style="padding-right: 3rem;">
                        <label for="pass1" class="form-label">New Password</label>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('pass1', this)" title="Show password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 2rem; position: relative;">
                        <input type="password" name="pass2" class="form-control" id="pass2" placeholder=" " required minlength="6" style="padding-right: 3rem;">
                        <label for="pass2" class="form-label">Confirm New Password</label>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('pass2', this)" title="Show password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    
                    <button type="submit" class="btn btn-primary magnetic-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        Reset Password <i class="fa-solid fa-check"></i>
                    </button>
                </form>
            <?php endif; ?>
        </div>

    </div>
</div>

<style>
body { overflow: hidden; }
.main-content { margin-top: 0 !important; }
.password-toggle-btn {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    font-size: 1rem;
    padding: 0.3rem;
    transition: color 0.3s;
    z-index: 2;
}
.password-toggle-btn:hover { color: var(--accent-cyan); }
</style>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
