<?php 
$level = '../'; 
$page_title = 'Admin Dashboard';
include '../includes/config.php';

// Admin auth guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include '../includes/header.php'; 

// Fetch Stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE status = 'approved'")->fetch_assoc()['count'];
$pending_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE status = 'pending'")->fetch_assoc()['count'];
$rejected_notes = $conn->query("SELECT COUNT(*) as count FROM notes WHERE status = 'rejected'")->fetch_assoc()['count'];
$total_downloads = $conn->query("SELECT COALESCE(SUM(downloads),0) as count FROM notes")->fetch_assoc()['count'];

// Fetch Online Users
$online_result = $conn->query("SELECT COUNT(*) as count FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
$online_count = $online_result->fetch_assoc()['count'];

// Recent registrations
$recent_users = $conn->query("SELECT id, first_name, fname, username, email, created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5");

// Recent uploads
$recent_notes = $conn->query("SELECT n.id, n.title, n.status, n.created_at, u.username, u.first_name, u.fname 
                               FROM notes n LEFT JOIN users u ON n.user_id = u.id 
                               ORDER BY n.created_at DESC LIMIT 8");
?>

<div class="dashboard-layout">
    <?php include 'sidebar.php'; ?>

    <main class="dashboard-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <h2 class="animate-fade-in-up">Admin Overview</h2>
            <div style="display:flex;gap:1rem;align-items:center;">
                <div class="glass-card" style="padding: 0.5rem 1rem; border-color: rgba(0,255,136,0.3);">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <div style="width:8px;height:8px;background:#00ff88;border-radius:50%;box-shadow:0 0 10px #00ff88;animation:pulse 2s infinite;"></div>
                        <span style="font-size:0.85rem;color:var(--text-secondary);">Online:</span>
                        <span style="color:#00ff88;font-weight:600;"><?= $online_count ?></span>
                    </div>
                </div>
                <div class="glass-card" style="padding: 0.5rem 1rem;">
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">Status:</span> <span style="color: #00ff88; font-weight: bold;">LIVE</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="glass-card animate-fade-in-up delay-100" style="text-align: center; padding: 2rem;">
                <i class="fa-solid fa-users" style="font-size:1.5rem;color:var(--accent-cyan);margin-bottom:0.8rem;display:block;"></i>
                <h3 style="font-size: 2.5rem; color: var(--accent-cyan); margin-bottom: 0;"><?= number_format($total_users) ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Total Users</p>
            </div>
            <div class="glass-card animate-fade-in-up delay-200" style="text-align: center; padding: 2rem;">
                <i class="fa-solid fa-file-lines" style="font-size:1.5rem;color:var(--accent-violet);margin-bottom:0.8rem;display:block;"></i>
                <h3 style="font-size: 2.5rem; color: var(--accent-violet); margin-bottom: 0;"><?= number_format($total_notes) ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Approved Notes</p>
            </div>
            <div class="glass-card animate-fade-in-up delay-300" style="text-align: center; padding: 2rem; border-color: rgba(255,170,0,0.3);">
                <i class="fa-solid fa-clock" style="font-size:1.5rem;color:#ffaa00;margin-bottom:0.8rem;display:block;"></i>
                <h3 style="font-size: 2.5rem; color: #ffaa00; margin-bottom: 0;"><?= number_format($pending_notes) ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Pending</p>
            </div>
            <div class="glass-card animate-fade-in-up" style="text-align: center; padding: 2rem; border-color: rgba(255,51,102,0.3);">
                <i class="fa-solid fa-xmark" style="font-size:1.5rem;color:#ff3366;margin-bottom:0.8rem;display:block;"></i>
                <h3 style="font-size: 2.5rem; color: #ff3366; margin-bottom: 0;"><?= number_format($rejected_notes) ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Rejected</p>
            </div>
            <div class="glass-card animate-fade-in-up" style="text-align: center; padding: 2rem; border-color: rgba(0,255,136,0.3);">
                <i class="fa-solid fa-download" style="font-size:1.5rem;color:#00ff88;margin-bottom:0.8rem;display:block;"></i>
                <h3 style="font-size: 2.5rem; color: #00ff88; margin-bottom: 0;"><?= number_format($total_downloads) ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Downloads</p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;" class="admin-two-col">
            <!-- Recent Uploads -->
            <div class="glass-panel animate-fade-in-up delay-200" style="padding: 2rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                    <h3>Recent Uploads</h3>
                    <a href="notes.php" style="color:var(--accent-cyan);text-decoration:none;font-size:0.85rem;">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <?php if ($recent_notes && $recent_notes->num_rows > 0): ?>
                <div style="display:flex;flex-direction:column;gap:0.8rem;">
                    <?php while($rn = $recent_notes->fetch_assoc()): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.8rem;background:rgba(255,255,255,0.02);border-radius:10px;">
                        <div>
                            <div style="font-weight:500;font-size:0.9rem;"><?= htmlspecialchars($rn['title']) ?></div>
                            <div style="font-size:0.75rem;color:var(--text-secondary);">by <?= htmlspecialchars($rn['username'] ?: ($rn['first_name'] ?: $rn['fname'])) ?></div>
                        </div>
                        <?php if($rn['status']==='pending'): ?>
                        <span style="color:#ffaa00;background:rgba(255,170,0,0.1);padding:0.2rem 0.6rem;border-radius:8px;font-size:0.75rem;">Pending</span>
                        <?php elseif($rn['status']==='approved'): ?>
                        <span style="color:#00ff88;background:rgba(0,255,136,0.1);padding:0.2rem 0.6rem;border-radius:8px;font-size:0.75rem;">Approved</span>
                        <?php else: ?>
                        <span style="color:#ff3366;background:rgba(255,51,102,0.1);padding:0.2rem 0.6rem;border-radius:8px;font-size:0.75rem;">Rejected</span>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p style="color:var(--text-secondary);text-align:center;padding:2rem;">No uploads yet.</p>
                <?php endif; ?>
            </div>

            <!-- Recent Users -->
            <div class="glass-panel animate-fade-in-up delay-300" style="padding: 2rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                    <h3>Recent Users</h3>
                    <a href="users.php" style="color:var(--accent-cyan);text-decoration:none;font-size:0.85rem;">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <?php if ($recent_users && $recent_users->num_rows > 0): ?>
                <div style="display:flex;flex-direction:column;gap:0.8rem;">
                    <?php while($ru = $recent_users->fetch_assoc()): ?>
                    <div style="display:flex;align-items:center;gap:0.8rem;padding:0.8rem;background:rgba(255,255,255,0.02);border-radius:10px;">
                        <div style="width:35px;height:35px;border-radius:50%;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;flex-shrink:0;">
                            <?= strtoupper(substr($ru['username'] ?: ($ru['first_name'] ?: $ru['fname']), 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight:500;font-size:0.9rem;"><?= htmlspecialchars($ru['username'] ?: ($ru['first_name'] ?: $ru['fname'])) ?></div>
                            <div style="font-size:0.75rem;color:var(--text-secondary);"><?= htmlspecialchars($ru['email']) ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p style="color:var(--text-secondary);text-align:center;padding:2rem;">No users yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<style>
@keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:0.6} }
@media (max-width: 768px) {
    .admin-two-col { grid-template-columns: 1fr !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
