<?php 
$level = '../'; 
$page_title = 'All Users';
include '../includes/config.php';

// Admin auth guard
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

include '../includes/header.php'; 

// Fetch all users
$search = trim($_GET['q'] ?? '');
$where = "WHERE role = 'user'";
if ($search) {
    $esc = $conn->real_escape_string($search);
    $where .= " AND (username LIKE '%$esc%' OR first_name LIKE '%$esc%' OR fname LIKE '%$esc%' OR email LIKE '%$esc%')";
}
$users = $conn->query("SELECT u.*, 
    (SELECT COUNT(*) FROM notes WHERE user_id = u.id) as note_count,
    (SELECT COUNT(*) FROM notes WHERE user_id = u.id AND status='approved') as approved_count
    FROM users u $where ORDER BY u.created_at DESC");
?>

<div class="dashboard-layout">
    <?php include 'sidebar.php'; ?>

    <main class="dashboard-content">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <h2 class="animate-fade-in-up">User Management</h2>
            <div style="display:flex;gap:1rem;align-items:center;">
                <span style="color:var(--text-secondary);font-size:0.9rem;"><?= $users->num_rows ?> users</span>
            </div>
        </div>

        <!-- Search -->
        <div class="animate-fade-in-up" style="margin-bottom:2rem;">
            <form method="GET" style="display:flex;gap:1rem;max-width:500px;">
                <div class="form-group" style="flex:1;margin-bottom:0;">
                    <input type="text" name="q" class="form-control" placeholder="Search by name, username, email..." value="<?= htmlspecialchars($search) ?>" style="border-radius:50px;padding-left:2.5rem;">
                </div>
                <button type="submit" class="btn btn-primary" style="border-radius:50px;padding:0 1.5rem;">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
        </div>

        <div class="glass-panel animate-fade-in-up delay-100" style="padding:1.5rem;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;min-width:700px;">
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">User</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Email</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Branch</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Notes</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Joined</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Last Active</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;text-align:center;">Actions</th>
                </tr>
                <?php if ($users->num_rows > 0): ?>
                <?php while($u = $users->fetch_assoc()): 
                    $uname = $u['username'] ?: ($u['first_name'] ?: $u['fname']);
                    $initials = strtoupper(substr($uname, 0, 1));
                    $is_online = (strtotime($u['last_activity']) > (time() - 300));
                ?>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.3s;" onmouseover="this.style.background='rgba(0,243,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem;">
                        <div style="display:flex;align-items:center;gap:0.8rem;">
                            <div style="position:relative;">
                                <div style="width:35px;height:35px;border-radius:50%;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;">
                                    <?= $initials ?>
                                </div>
                                <?php if($is_online): ?>
                                <div style="position:absolute;bottom:-1px;right:-1px;width:10px;height:10px;background:#00ff88;border-radius:50%;border:2px solid var(--bg-deep);"></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div style="font-weight:500;"><?= htmlspecialchars($uname) ?></div>
                                <div style="font-size:0.75rem;color:var(--text-secondary);">ID: <?= $u['id'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem;color:var(--text-secondary);font-size:0.9rem;"><?= htmlspecialchars($u['email']) ?></td>
                    <td style="padding:1rem;"><?= htmlspecialchars($u['branch'] ?: '—') ?></td>
                    <td style="padding:1rem;">
                        <span style="color:var(--accent-cyan);"><?= $u['approved_count'] ?></span>
                        <span style="color:var(--text-secondary);font-size:0.8rem;">/ <?= $u['note_count'] ?></span>
                    </td>
                    <td style="padding:1rem;color:var(--text-secondary);font-size:0.85rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td style="padding:1rem;color:var(--text-secondary);font-size:0.85rem;"><?= $is_online ? '<span style="color:#00ff88;">Online</span>' : date('d M H:i', strtotime($u['last_activity'])) ?></td>
                    <td style="padding:1rem;text-align:center;">
                        <div style="display:flex;gap:0.5rem;justify-content:center;">
                            <a href="mailto:<?= htmlspecialchars($u['email']) ?>" class="admin-action-btn" title="Email" style="color:var(--accent-cyan);">
                                <i class="fa-solid fa-envelope"></i>
                            </a>
                            <button onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($uname)) ?>')" class="admin-action-btn" title="Delete" style="color:#ff3366;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-secondary);">No users found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </main>
</div>

<style>
.admin-action-btn {
    width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;
    border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);
    cursor:pointer;transition:all 0.3s;text-decoration:none;font-size:0.85rem;
}
.admin-action-btn:hover { background:rgba(255,255,255,0.1);transform:translateY(-2px); }
</style>

<script>
function deleteUser(id, name) {
    if (confirm('Are you sure you want to delete user "' + name + '"? All their notes will also be deleted.')) {
        fetch('delete_user.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) location.reload();
            else alert('Error: ' + (data.error || 'Unknown'));
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>
