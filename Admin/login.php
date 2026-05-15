<?php
$level = '../';
$page_title = 'Admin Login';
include '../includes/config.php';

// If already logged in as admin, redirect
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password']   ?? '';

    if (!$email || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, fname, first_name, username, password, role, theme FROM users WHERE (email = ? OR username = ?) AND role = 'admin'");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = 'Invalid admin credentials.';
        } else {
            $user = $result->fetch_assoc();
            if (!password_verify($password, $user['password'])) {
                $error = 'Invalid admin credentials.';
            } else {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['username']   = $user['username'] ?: ($user['first_name'] ?: $user['fname']);
                $_SESSION['first_name'] = $user['first_name'] ?: $user['fname'];
                $_SESSION['role']       = 'admin';
                $_SESSION['theme']      = $user['theme'] ?? '';
                $conn->query("UPDATE users SET last_activity = NOW() WHERE id = " . $user['id']);
                header('Location: dashboard.php');
                exit();
            }
        }
    }
}

include '../includes/header.php';
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="glass-panel animate-fade-in-up" style="max-width: 440px; width: 100%; padding: 3rem; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, rgba(255,0,0,0.1), rgba(255,170,0,0.1)); border: 1px solid rgba(255,170,0,0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 0 30px rgba(255,170,0,0.2);">
                <i class="fa-solid fa-shield-halved" style="font-size: 2rem; color: #ffaa00;"></i>
            </div>
            <h2 style="font-size: 1.8rem; margin-bottom: 0.3rem;">Admin Access</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Restricted area — authorized personnel only</p>
        </div>

        <?php if($error): ?>
        <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.2rem;font-size:0.9rem;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <input type="text" id="admin-email" name="email" class="form-control" placeholder=" " required>
                <label for="admin-email" class="form-label">Admin Email</label>
            </div>
            <div class="form-group" style="position: relative;">
                <input type="password" id="admin-password" name="password" class="form-control" placeholder=" " required style="padding-right: 3rem;">
                <label for="admin-password" class="form-label">Password</label>
                <button type="button" onclick="togglePwd()" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:1rem;z-index:2;">
                    <i id="pwd-icon" class="fa-solid fa-eye"></i>
                </button>
            </div>
            <button type="submit" class="btn btn-primary magnetic-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #ffaa00, #ff6600);">
                <i class="fa-solid fa-right-to-bracket"></i> Access Admin Panel
            </button>
        </form>
        <p style="text-align: center; color: var(--text-secondary); font-size: 0.85rem;">
            <a href="../auth/login.php" style="color: var(--accent-cyan); text-decoration: none;">← Back to User Login</a>
        </p>
    </div>
</div>

<script>
function togglePwd() {
    const inp = document.getElementById('admin-password');
    const ico = document.getElementById('pwd-icon');
    if (inp.type === 'password') { inp.type = 'text'; ico.classList.replace('fa-eye','fa-eye-slash'); }
    else { inp.type = 'password'; ico.classList.replace('fa-eye-slash','fa-eye'); }
}
</script>

<?php include '../includes/footer.php'; ?>
