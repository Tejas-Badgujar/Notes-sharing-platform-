<?php 
$level = '../'; 
$page_title = 'Profile Settings';

include '../includes/header.php'; 

// Fetch User Data from DB
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for demo
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($user_sql);
$user_data = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [
    'fname' => 'User', 'lname' => 'Not Found', 'email' => 'N/A', 
    'role' => 'user', 'branch' => 'N/A', 'created_at' => date('Y-m-d'),
    'current_streak' => 0, 'last_upload_date' => null
];

// Streak Logic
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$last_upload = $user_data['last_upload_date'];
$current_streak = $user_data['current_streak'];

// Check if streak is broken
if ($last_upload != $today && $last_upload != $yesterday && $last_upload != null) {
    $conn->query("UPDATE users SET current_streak = 0 WHERE id = $user_id");
    $current_streak = 0;
}
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content">
        
        <!-- Profile Header Section -->
        <div class="profile-header animate-fade-in-up">
            <div class="profile-banner" id="profile-banner-bg">
                <label for="banner-upload" class="banner-edit-overlay">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Banner
                </label>
                <input type="file" id="banner-upload" style="display: none;" accept="image/*">
            </div>
            
            <div class="profile-avatar-large user-avatar" id="main-avatar">
                <span id="avatar-initial"><?= strtoupper(substr($user_data['fname'], 0, 1)) ?></span>
                <img src="" id="avatar-img" class="avatar-preview-img">
                <label for="avatar-upload" class="avatar-edit-overlay">
                    <i class="fa-solid fa-camera"></i>
                </label>
            </div>
            <input type="file" id="avatar-upload" style="display: none;" accept="image/*">
        </div>

        <div style="display: flex; gap: 2rem; margin-top: 1rem;">
            
            <!-- Left Column: Details -->
            <div style="flex: 2; display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Personal Info Card -->
                <div class="glass-panel animate-fade-in-up delay-100" style="padding: 2.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
                        <div>
                            <h2 id="display-name"><?= $user_data['fname'] . ' ' . $user_data['lname'] ?></h2>
                            <p style="color: var(--text-secondary); margin-top: 0.5rem;"><i class="fa-solid fa-location-dot"></i> <span id="display-location">Mumbai, India</span> &bull; <span id="display-role"><?= ucfirst($user_data['role']) ?></span></p>
                        </div>
                        <button class="btn btn-outline" onclick="openModal('edit-profile-modal')"><i class="fa-solid fa-pen"></i> Edit Profile</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <p style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: var(--accent-cyan); margin-bottom: 0.5rem; font-weight: 700;">Email Address</p>
                            <p id="display-email"><?= $user_data['email'] ?></p>
                        </div>
                        <div>
                            <p style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: var(--accent-magenta); margin-bottom: 0.5rem; font-weight: 700;">Phone Number</p>
                            <p id="display-phone">+91 98765 43210</p>
                        </div>
                        <div>
                            <p style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: var(--accent-cyan); margin-bottom: 0.5rem; font-weight: 700;">Current Branch</p>
                            <p id="display-sem"><?= $user_data['branch'] ?></p>
                        </div>
                        <div>
                            <p style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; color: var(--accent-magenta); margin-bottom: 0.5rem; font-weight: 700;">Joined Date</p>
                            <p><?= date('d F Y', strtotime($user_data['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Security Card -->
                <div class="glass-panel animate-fade-in-up delay-200" style="padding: 2.5rem;">
                    <h3 style="margin-bottom: 2rem;">Account Security</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid var(--glass-border);">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <div style="width: 45px; height: 45px; border-radius: 12px; background: rgba(0,229,255,0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-cyan); font-size: 1.2rem;">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-weight: 600;">Two-Factor Authentication</p>
                                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary);">Add an extra layer of security to your account.</p>
                                </div>
                            </div>
                            <button class="btn btn-outline" id="tfa-btn" onclick="openModal('tfa-modal')">Enable</button>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid var(--glass-border);">
                            <div style="display: flex; gap: 1.5rem; align-items: center;">
                                <div style="width: 45px; height: 45px; border-radius: 12px; background: rgba(138,43,226,0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-violet); font-size: 1.2rem;">
                                    <i class="fa-solid fa-key"></i>
                                </div>
                                <div>
                                    <p style="margin: 0; font-weight: 600;">Change Password</p>
                                    <p style="margin: 0; font-size: 0.8rem; color: var(--text-secondary);" id="pass-last-changed">Last changed 3 months ago.</p>
                                </div>
                            </div>
                            <button class="btn btn-outline" onclick="openModal('password-modal')">Update</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Stats -->
            <div style="flex: 1; display: flex; flex-direction: column; gap: 2rem;">
                <div class="glass-panel animate-fade-in-up delay-100" style="padding: 2rem;">
                    <h3 style="margin-bottom: 1.5rem;">Learning Journey</h3>
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8rem;">
                                <span style="color: var(--text-secondary);">Note Master</span>
                                <span>80%</span>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden;">
                                <div style="width: 80%; height: 100%; background: linear-gradient(90deg, var(--accent-cyan), var(--accent-violet)); border-radius: 3px;"></div>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.8rem;">
                                <span style="color: var(--text-secondary);">Current Streak</span>
                                <span style="color: var(--accent-cyan); font-weight: bold;"><?= $current_streak ?> Days <i class="fa-solid fa-fire"></i></span>
                            </div>
                            <div style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden;">
                                <?php 
                                    $streak_width = min(($current_streak / 30) * 100, 100); // 30 days as a milestone
                                ?>
                                <div style="width: <?= $streak_width ?>%; height: 100%; background: linear-gradient(90deg, #ff8c00, #ff0000); border-radius: 3px; box-shadow: 0 0 10px #ff4500;"></div>
                            </div>
                            <p style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.5rem;">Upload daily to keep your fire alive!</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel animate-fade-in-up delay-200" style="padding: 2rem;">
                    <h3 style="margin-bottom: 1.5rem;">Recent Activity</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="padding-left: 1rem; border-left: 2px solid var(--accent-cyan);">
                            <p style="margin: 0; font-size: 0.85rem; font-weight: 600;">Downloaded Advanced OS Notes</p>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">2 hours ago</p>
                        </div>
                        <div style="padding-left: 1rem; border-left: 2px solid var(--accent-magenta);">
                            <p style="margin: 0; font-size: 0.85rem; font-weight: 600;">Uploaded Database Design PDF</p>
                            <p style="margin: 0; font-size: 0.75rem; color: var(--text-secondary);">Yesterday</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- MODALS -->

<!-- 1. Edit Profile Modal -->
<div class="modal-overlay" id="edit-profile-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Personal Details</h3>
            <i class="fa-solid fa-xmark modal-close" onclick="closeModal('edit-profile-modal')"></i>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <input type="text" class="form-control" id="edit-name" placeholder=" ">
                <label class="form-label">Full Name</label>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" id="edit-location" placeholder=" ">
                <label class="form-label">Location</label>
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <input type="email" class="form-control" id="edit-email" placeholder=" ">
            <label class="form-label">Email Address</label>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <input type="text" class="form-control" id="edit-phone" placeholder=" ">
            <label class="form-label">Phone Number</label>
        </div>
        <div class="form-group" style="margin-bottom: 2rem;">
            <input type="text" class="form-control" id="edit-sem" placeholder=" ">
            <label class="form-label">Semester & Branch</label>
        </div>
        <button class="btn btn-primary" style="width: 100%;" onclick="saveProfile()">Save Changes</button>
    </div>
</div>

<!-- 2. Two-Factor Modal -->
<div class="modal-overlay" id="tfa-modal">
    <div class="modal-content" style="text-align: center;">
        <div class="modal-header">
            <h3>Enable 2FA</h3>
            <i class="fa-solid fa-xmark modal-close" onclick="closeModal('tfa-modal')"></i>
        </div>
        <div id="tfa-step-1">
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)</p>
            <div style="background: #fff; padding: 1.5rem; width: 200px; height: 200px; margin: 0 auto 2rem; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=NotesSharingPlatform-2FA" alt="QR Code" style="width: 100%;">
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <input type="text" class="form-control" id="tfa-code" placeholder=" " style="text-align: center; letter-spacing: 5px; font-weight: 700;">
                <label class="form-label">Enter 6-digit Code</label>
            </div>
            <button class="btn btn-primary" style="width: 100%;" onclick="verifyTFA()">Verify & Enable</button>
        </div>
        <div id="tfa-success" style="display: none;">
            <div style="width: 80px; height: 80px; background: rgba(0,255,100,0.1); color: #00ff64; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 2rem;">
                <i class="fa-solid fa-check"></i>
            </div>
            <h4>2FA Enabled Successfully!</h4>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; margin-top: 1rem;">Your account is now protected with an extra layer of security.</p>
            <button class="btn btn-outline" style="width: 100%;" onclick="closeModal('tfa-modal')">Done</button>
        </div>
    </div>
</div>

<!-- 3. Password Modal -->
<div class="modal-overlay" id="password-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change Password</h3>
            <i class="fa-solid fa-xmark modal-close" onclick="closeModal('password-modal')"></i>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <input type="password" class="form-control" placeholder=" ">
            <label class="form-label">Current Password</label>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <input type="password" class="form-control" placeholder=" ">
            <label class="form-label">New Password</label>
        </div>
        <div class="form-group" style="margin-bottom: 2rem;">
            <input type="password" class="form-control" placeholder=" ">
            <label class="form-label">Confirm New Password</label>
        </div>
        <button class="btn btn-primary" style="width: 100%;" onclick="updatePassword()">Update Password</button>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Load from LocalStorage
    loadStoredData();

    // 2. Avatar Upload
    document.getElementById('avatar-upload').addEventListener('change', function(e) {
        handleFileUpload(e.target.files[0], 'user-profile-image', (base64) => {
            document.getElementById('avatar-img').src = base64;
            document.getElementById('avatar-img').style.display = 'block';
            document.getElementById('avatar-initial').style.display = 'none';
            if (window.syncGlobalUI) window.syncGlobalUI();
        });
    });

    // 3. Banner Upload
    document.getElementById('banner-upload').addEventListener('change', function(e) {
        handleFileUpload(e.target.files[0], 'user-profile-banner', (base64) => {
            document.getElementById('profile-banner-bg').style.backgroundImage = `url(${base64})`;
        });
    });
});

function handleFileUpload(file, key, callback) {
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const base64 = event.target.result;
            localStorage.setItem(key, base64);
            callback(base64);
            showToast('Media updated successfully!');
        };
        reader.readAsDataURL(file);
    }
}

function openModal(id) {
    const modal = document.getElementById(id);
    modal.style.display = 'flex';
    
    // Prefill edit modal
    if (id === 'edit-profile-modal') {
        document.getElementById('edit-name').value = document.getElementById('display-name').textContent;
        document.getElementById('edit-location').value = document.getElementById('display-location').textContent;
        document.getElementById('edit-email').value = document.getElementById('display-email').textContent;
        document.getElementById('edit-phone').value = document.getElementById('display-phone').textContent;
        document.getElementById('edit-sem').value = document.getElementById('display-sem').textContent;
    }
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    // Reset TFA state if closed
    if (id === 'tfa-modal') {
        document.getElementById('tfa-step-1').style.display = 'block';
        document.getElementById('tfa-success').style.display = 'none';
    }
}

function saveProfile() {
    const data = {
        name: document.getElementById('edit-name').value,
        location: document.getElementById('edit-location').value,
        email: document.getElementById('edit-email').value,
        phone: document.getElementById('edit-phone').value,
        sem: document.getElementById('edit-sem').value
    };

    localStorage.setItem('user-profile-data', JSON.stringify(data));
    loadStoredData();
    closeModal('edit-profile-modal');
    showToast('Profile details saved!');
}

function verifyTFA() {
    const code = document.getElementById('tfa-code').value;
    if (code.length === 6) {
        document.getElementById('tfa-step-1').style.display = 'none';
        document.getElementById('tfa-success').style.display = 'block';
        document.getElementById('tfa-btn').textContent = 'Active';
        document.getElementById('tfa-btn').classList.add('active');
        localStorage.setItem('notes-tfa-active', 'true');
    } else {
        alert('Please enter a valid 6-digit code.');
    }
}

function updatePassword() {
    closeModal('password-modal');
    showToast('Password updated successfully!');
    document.getElementById('pass-last-changed').textContent = 'Last changed Just now.';
}

function loadStoredData() {
    // Details
    const stored = localStorage.getItem('user-profile-data');
    if (stored) {
        const data = JSON.parse(stored);
        document.getElementById('display-name').textContent = data.name;
        document.getElementById('display-location').textContent = data.location;
        document.getElementById('display-email').textContent = data.email;
        document.getElementById('display-phone').textContent = data.phone;
        document.getElementById('display-sem').textContent = data.sem;
    }

    // Avatar
    const savedAvatar = localStorage.getItem('user-profile-image');
    if (savedAvatar) {
        document.getElementById('avatar-img').src = savedAvatar;
        document.getElementById('avatar-img').style.display = 'block';
        document.getElementById('avatar-initial').style.display = 'none';
    }

    // Banner
    const savedBanner = localStorage.getItem('user-profile-banner');
    if (savedBanner) {
        document.getElementById('profile-banner-bg').style.backgroundImage = `url(${savedBanner})`;
    }

    // TFA
    if (localStorage.getItem('notes-tfa-active') === 'true') {
        document.getElementById('tfa-btn').textContent = 'Active';
        document.getElementById('tfa-btn').style.color = '#00ff64';
        document.getElementById('tfa-btn').style.borderColor = '#00ff64';
    }
}

function showToast(msg) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 80px; right: 2rem; background: var(--accent-cyan); color: #000;
        padding: 0.8rem 1.5rem; border-radius: 12px; font-weight: 600; font-size: 0.9rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999; animation: slideIn 0.4s forwards;
    `;
    toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> ${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.4s forwards';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}
</script>
