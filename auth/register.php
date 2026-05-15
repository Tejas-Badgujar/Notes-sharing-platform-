<?php 
$level = '../'; 
$page_title = 'Register';
include '../includes/header.php'; 

$error = $_GET['error'] ?? '';
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="glass-panel animate-fade-in-up" style="display: flex; max-width: 1000px; width: 100%; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.5); flex-direction: row-reverse;">
        
        <!-- Right Side: Branding -->
        <div class="auth-branding-panel" style="flex: 1; background: linear-gradient(135deg, rgba(0, 243, 255, 0.2), rgba(138, 43, 226, 0.2)); padding: 4rem; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: var(--accent-cyan); filter: blur(80px); border-radius: 50%; opacity: 0.5;"></div>
            <div style="position: absolute; bottom: -50px; left: -50px; width: 200px; height: 200px; background: var(--accent-violet); filter: blur(80px); border-radius: 50%; opacity: 0.5;"></div>
            
            <h1 style="font-size: 3rem; margin-bottom: 1rem; position: relative; z-index: 1;">Join the Elite.</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem; position: relative; z-index: 1;">Create an account to share your notes and collaborate with top students.</p>
        </div>

        <!-- Left Side: Form -->
        <div style="flex: 1; padding: 3rem 3.5rem; background: rgba(10, 11, 26, 0.7); backdrop-filter: blur(20px);" class="auth-form-panel">
            <div style="text-align: center; margin-bottom: 2rem;">
                <a href="../index.php" class="logo" style="justify-content: center; font-size: 2rem;">
                    <i class="fa-solid fa-layer-group"></i><span>Notes</span>Platform
                </a>
                <p style="color: var(--text-secondary); margin-top: 0.5rem;">Create your account</p>
            </div>

            <?php if($error === 'exists'): ?>
            <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Email or username already exists.
            </div>
            <?php elseif($error === 'slang_username'): ?>
            <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Username contains inappropriate words. Please choose another.
            </div>
            <?php elseif($error === 'missing_fields'): ?>
            <div style="background:rgba(255,170,0,0.1);border:1px solid rgba(255,170,0,0.4);color:#ffaa00;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Please fill in all required fields.
            </div>
            <?php elseif($error === 'weak_password'): ?>
            <div style="background:rgba(255,170,0,0.1);border:1px solid rgba(255,170,0,0.4);color:#ffaa00;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Password must be at least 6 characters.
            </div>
            <?php elseif($error === 'password_mismatch'): ?>
            <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Passwords do not match. Please try again.
            </div>
            <?php elseif($error === 'db'): ?>
            <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Registration failed. Please try again.
            </div>
            <?php endif; ?>

            <form id="register-form" action="process_register.php" method="POST" onsubmit="return validateRegister()">
                <div style="display: flex; gap: 1rem;" class="auth-name-row">
                    <div class="form-group" style="flex: 1;">
                        <input type="text" id="fname" name="first_name" class="form-control" placeholder=" " required>
                        <label for="fname" class="form-label">First Name</label>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <input type="text" id="lname" name="last_name" class="form-control" placeholder=" " required>
                        <label for="lname" class="form-label">Last Name</label>
                    </div>
                </div>

                <div class="form-group">
                    <input type="text" id="username" name="username" class="form-control" placeholder=" " required minlength="3" maxlength="30">
                    <label for="username" class="form-label">Username</label>
                </div>

                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-control" placeholder=" " required>
                    <label for="email" class="form-label">Email Address</label>
                </div>
                
                <div class="form-group" style="position: relative;">
                    <input type="password" id="reg-password" name="password" class="form-control" placeholder=" " required minlength="6" style="padding-right: 3rem;">
                    <label for="reg-password" class="form-label">Password (min 6 chars)</label>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('reg-password', this)" title="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <div class="form-group" style="position: relative;">
                    <input type="password" id="reg-confirm-password" name="confirm_password" class="form-control" placeholder=" " required minlength="6" style="padding-right: 3rem;">
                    <label for="reg-confirm-password" class="form-label">Confirm Password</label>
                    <button type="button" class="password-toggle-btn" onclick="togglePassword('reg-confirm-password', this)" title="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <div id="password-match-msg" style="display:none;font-size:0.85rem;margin-top:-1rem;margin-bottom:1rem;padding:0.4rem 0.8rem;border-radius:8px;"></div>

                <div class="form-group">
                    <select name="branch" class="form-control" style="color: var(--text-secondary); appearance: none;" required>
                        <option value="" disabled selected>Select Field / Subject Area</option>
                        <option value="IT">Information Technology</option>
                        <option value="CS">Computer Science</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Mechanical">Mechanical</option>
                        <option value="Civil">Civil Engineering</option>
                        <option value="IPS">IPS</option>
                        <option value="UPSC">UPSC</option>
                        <option value="MPSC">MPSC</option>
                        <option value="General">General</option>
                    </select>
                    <i class="fa-solid fa-chevron-down" style="position: absolute; right: 1.2rem; top: 1.2rem; color: var(--text-secondary); pointer-events: none;"></i>
                </div>
                
                <button type="submit" class="btn btn-primary magnetic-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-bottom: 1.5rem; margin-top: 0.5rem;">
                    Create Account <i class="fa-solid fa-user-plus"></i>
                </button>
                
                <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                    Already have an account? <a href="login.php" style="color: var(--accent-cyan); text-decoration: none; font-weight: 500;">Sign in</a>
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

@media (max-width: 768px) {
    .auth-branding-panel { display: none !important; }
    .auth-form-panel { padding: 2rem !important; }
    .auth-name-row { flex-direction: column !important; gap: 0 !important; }
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

// Live password match check
const passField = document.getElementById('reg-password');
const confirmField = document.getElementById('reg-confirm-password');
const matchMsg = document.getElementById('password-match-msg');

function checkMatch() {
    if (!confirmField.value) { matchMsg.style.display = 'none'; return; }
    matchMsg.style.display = 'block';
    if (passField.value === confirmField.value) {
        matchMsg.textContent = '✓ Passwords match';
        matchMsg.style.background = 'rgba(0,255,136,0.1)';
        matchMsg.style.color = '#00ff88';
        matchMsg.style.border = '1px solid rgba(0,255,136,0.3)';
    } else {
        matchMsg.textContent = '✗ Passwords do not match';
        matchMsg.style.background = 'rgba(255,51,102,0.1)';
        matchMsg.style.color = '#ff6688';
        matchMsg.style.border = '1px solid rgba(255,51,102,0.3)';
    }
}
passField.addEventListener('input', checkMatch);
confirmField.addEventListener('input', checkMatch);

function validateRegister() {
    if (passField.value !== confirmField.value) {
        alert('Passwords do not match!');
        confirmField.focus();
        return false;
    }
    return true;
}
</script>

<?php include '../includes/footer.php'; ?>
