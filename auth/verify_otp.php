<?php
$level = '../';
$page_title = 'Verify Identity';
include '../includes/config.php';

$type = $_GET['type'] ?? 'register';
if ($type === 'register' && !isset($_SESSION['temp_reg_data'])) {
    header("Location: register.php"); exit;
}
if ($type === 'reset' && !isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php"); exit;
}

$error = $_GET['error'] ?? '';
$email_hint = $type === 'register' ? $_SESSION['temp_reg_data']['email'] : $_SESSION['reset_email'];

include '../includes/header.php';
?>

<div class="video-bg-container">
    <video autoplay muted loop playsinline id="bg-video">
        <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-technology-lines-flowing-21110-large.mp4" type="video/mp4">
    </video>
    <div class="video-overlay"></div>
</div>

<div class="main-content" style="display: flex; align-items: center; justify-content: center; min-height: 100vh; margin-top: 0;">
    <div class="container" style="max-width: 480px;">
        
        <div class="glass-panel animate-fade-in-up" style="padding: 3rem; border-radius: 30px; text-align: center;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(0,243,255,0.1); border: 1px solid var(--accent-cyan); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 0 20px rgba(0,243,255,0.2);">
                <i class="fa-solid fa-envelope-open-text" style="font-size: 1.8rem; color: var(--accent-cyan);"></i>
            </div>
            
            <h2 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Verify Your Identity</h2>
            <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 2rem;">
                We've sent a 6-digit verification code to <br><strong style="color:var(--text-primary);"><?= htmlspecialchars($email_hint) ?></strong>
            </p>

            <?php if($error === 'invalid'): ?>
              <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i> Incorrect verification code. Please try again.
              </div>
            <?php elseif($error === 'expired'): ?>
              <div style="background:rgba(255,170,0,0.1);border:1px solid rgba(255,170,0,0.4);color:#ffaa00;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:0.9rem;">
                <i class="fa-solid fa-clock-rotate-left"></i> Verification code expired. Please request a new one.
              </div>
            <?php endif; ?>

            <form action="process_otp.php" method="POST" id="otp-form">
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                <input type="hidden" name="otp" id="otp-combined">

                <div class="otp-inputs" style="display: flex; gap: 0.8rem; justify-content: center; margin-bottom: 2rem;">
                    <?php for($i=1; $i<=6; $i++): ?>
                    <input type="text" class="otp-box form-control" maxlength="1" required inputmode="numeric" pattern="[0-9]"
                           style="width: 50px; height: 60px; font-size: 1.8rem; text-align: center; padding: 0; border-radius: 12px; font-weight: 700;">
                    <?php endfor; ?>
                </div>
                
                <button type="submit" class="btn btn-primary magnetic-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 12px; margin-bottom: 1.5rem;">
                    Verify & Continue <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div style="color: var(--text-secondary); font-size: 0.9rem;">
                Didn't receive the code? <br>
                <button id="resend-btn" style="background:none; border:none; color: var(--accent-cyan); font-weight: 600; cursor: pointer; margin-top: 0.5rem; font-size: 0.9rem;" disabled>
                    Resend Code in <span id="timer">60</span>s
                </button>
            </div>
        </div>

    </div>
</div>

<script>
// Auto-tabbing for OTP boxes
const inputs = document.querySelectorAll('.otp-box');
const combined = document.getElementById('otp-combined');

inputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        // Only allow digits
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        if(e.target.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        updateCombined();
    });
    
    input.addEventListener('keydown', (e) => {
        if(e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
            inputs[index - 1].focus();
        }
    });

    // Handle paste
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const data = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
        if (data.length === 6) {
            inputs.forEach((inp, i) => { inp.value = data[i] || ''; });
            inputs[5].focus();
            updateCombined();
        }
    });
});

function updateCombined() {
    let val = '';
    inputs.forEach(i => val += i.value);
    combined.value = val;
}

// Timer logic
let timeLeft = 60;
const timerSpan = document.getElementById('timer');
const resendBtn = document.getElementById('resend-btn');

const countdown = setInterval(() => {
    timeLeft--;
    timerSpan.innerText = timeLeft;
    if(timeLeft <= 0) {
        clearInterval(countdown);
        resendBtn.disabled = false;
        resendBtn.innerHTML = "Resend Code Now";
    }
}, 1000);

// REAL resend via AJAX
resendBtn.addEventListener('click', (e) => {
    e.preventDefault();
    if(!resendBtn.disabled) {
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
        
        fetch('../ajax/resend_otp.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'type=<?= htmlspecialchars($type) ?>'
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                // Show success toast
                const toast = document.createElement('div');
                toast.className = 'glass-card animate-fade-in-up';
                toast.innerHTML = '<i class="fa-solid fa-check-circle" style="color:#00ff88;"></i> New code sent successfully!';
                toast.style.cssText = 'position:fixed;bottom:30px;right:30px;z-index:99999;padding:1rem 1.5rem;';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
                
                // Reset timer
                timeLeft = 60;
                resendBtn.innerHTML = 'Resend Code in <span id="timer">60</span>s';
                const newTimerSpan = document.getElementById('timer');
                const newTimer = setInterval(() => {
                    timeLeft--;
                    newTimerSpan.innerText = timeLeft;
                    if(timeLeft <= 0) {
                        clearInterval(newTimer);
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = "Resend Code Now";
                    }
                }, 1000);
            } else {
                resendBtn.disabled = false;
                resendBtn.innerHTML = "Resend Code Now";
                alert("Failed to send code. Please try again.");
            }
        })
        .catch(() => {
            resendBtn.disabled = false;
            resendBtn.innerHTML = "Resend Code Now";
        });
    }
});
</script>

<style>
body { overflow: hidden; }
.main-content { margin-top: 0 !important; }
.otp-box:focus { border-color: var(--accent-cyan); box-shadow: 0 0 15px rgba(0,243,255,0.2); background: rgba(0,243,255,0.05); }

@media (max-width: 480px) {
    .otp-inputs { gap: 0.4rem !important; }
    .otp-box { width: 42px !important; height: 50px !important; font-size: 1.4rem !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
