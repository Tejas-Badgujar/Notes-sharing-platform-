<?php 
$level = '../'; 
$page_title = 'Login';
include '../includes/header.php'; 

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

// Read remember-me cookie
$remembered_email = '';
if (isset($_COOKIE['notes_remember_email'])) {
    $remembered_email = htmlspecialchars($_COOKIE['notes_remember_email']);
}
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="glass-panel animate-fade-in-up" style="display: flex; max-width: 1000px; width: 100%; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        
        <!-- Left Side: Branding -->
        <div class="auth-branding-panel" style="flex: 1; background: linear-gradient(135deg, rgba(138, 43, 226, 0.2), rgba(0, 243, 255, 0.2)); padding: 4rem; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden;">
            <!-- Decorative circle -->
            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: var(--accent-violet); filter: blur(80px); border-radius: 50%; opacity: 0.5;"></div>
            <div style="position: absolute; bottom: -50px; right: -50px; width: 200px; height: 200px; background: var(--accent-cyan); filter: blur(80px); border-radius: 50%; opacity: 0.5;"></div>
            
            <h1 style="font-size: 3rem; margin-bottom: 1rem; position: relative; z-index: 1;">Welcome Back.</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem; position: relative; z-index: 1;">Access your premium study materials and pick up where you left off.</p>
        </div>

        <!-- Right Side: Form -->
        <div style="flex: 1; padding: 4rem; background: rgba(10, 11, 26, 0.7); backdrop-filter: blur(20px);" class="auth-form-panel">
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <a href="../index.php" class="logo" style="justify-content: center; font-size: 2rem;">
                    <i class="fa-solid fa-layer-group"></i><span>Notes</span>Platform
                </a>
                <p style="color: var(--text-secondary); margin-top: 0.5rem;">Sign in to your account</p>
            </div>

            <?php if($success === 'reset'): ?>
            <div style="background:rgba(0,255,136,0.1);border:1px solid rgba(0,255,136,0.4);color:#00ff88;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
                <i class="fa-solid fa-circle-check"></i> Password reset successfully! Please sign in with your new password.
            </div>
            <?php endif; ?>

            <?php if($error === 'invalid'): ?>
            <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Invalid email/username or password. Please try again.
            </div>
            <?php elseif($error === 'missing'): ?>
            <div style="background:rgba(255,170,0,0.1);border:1px solid rgba(255,170,0,0.4);color:#ffaa00;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Please fill in all fields.
            </div>
            <?php elseif($error === 'not_verified'): ?>
            <div style="background:rgba(255,170,0,0.1);border:1px solid rgba(255,170,0,0.4);color:#ffaa00;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Please verify your email first. Check your inbox for the OTP.
            </div>
            <?php endif; ?>

            <form action="process_login.php" method="POST">
                <div class="form-group">
                    <input type="text" id="email" name="email" class="form-control" placeholder=" " required value="<?= $remembered_email ?>">
                    <label for="email" class="form-label">Email or Username</label>
                </div>
                
                <div class="form-group" style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control" placeholder=" " required style="padding-right: 3rem;">
                    <label for="password" class="form-label">Password</label>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('password', this)" title="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary); font-size: 0.9rem; cursor: pointer;">
                        <input type="checkbox" name="remember" value="1" <?= $remembered_email ? 'checked' : '' ?> style="accent-color: var(--accent-cyan);"> Remember me
                    </label>
                    <a href="forgot_password.php" style="color: var(--accent-cyan); text-decoration: none; font-size: 0.9rem;">Forgot?</a>
                </div>

                <button type="submit" class="btn btn-primary magnetic-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-bottom: 1.5rem;">
                    Sign In <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
                
                <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                    Don't have an account? <a href="register.php" style="color: var(--accent-cyan); text-decoration: none; font-weight: 500;">Create one</a>
                </p>
            </form>
        </div>
    </div>
</div>

<style>
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

/* Responsive: hide branding on mobile */
@media (max-width: 768px) {
    .auth-branding-panel { display: none !important; }
    .auth-form-panel { padding: 2rem !important; }
}
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
