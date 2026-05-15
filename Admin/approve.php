<?php 
$level = '../'; 
$page_title = 'Pending Approvals';
include '../includes/config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit(); }
include '../includes/header.php'; 

$sql = "SELECT n.*, u.fname, u.lname, u.first_name, u.username, u.email, u.branch, u.created_at as user_joined 
        FROM notes n JOIN users u ON n.user_id = u.id WHERE n.status = 'pending' ORDER BY n.created_at DESC";
$result = $conn->query($sql);
$online_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetch_assoc()['c'];
?>
<div class="dashboard-layout">
    <?php include 'sidebar.php'; ?>
    <main class="dashboard-content">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <h2 class="animate-fade-in-up">Pending Approvals</h2>
            <div class="glass-card animate-fade-in-up" style="padding:0.5rem 1rem;display:flex;align-items:center;gap:0.5rem;">
                <div style="width:8px;height:8px;background:#00ff88;border-radius:50%;box-shadow:0 0 8px #00ff88;"></div>
                <span style="font-size:0.85rem;"><?= $online_count ?> Online</span>
            </div>
        </div>
        <div class="glass-panel animate-fade-in-up delay-100" style="padding:1.5rem;min-height:300px;overflow-x:auto;">
            <?php if ($result->num_rows > 0): ?>
            <table style="width:100%;border-collapse:collapse;text-align:left;min-width:600px;">
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Uploader</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Note Title</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Subject</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;text-align:center;">Actions</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): 
                    $uname = $row['username'] ?: ($row['first_name'] ?: $row['fname']);
                ?>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.3s;" onmouseover="this.style.background='rgba(0,243,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem;">
                        <div style="display:flex;align-items:center;gap:0.8rem;">
                            <div style="width:35px;height:35px;border-radius:50%;background:linear-gradient(135deg,var(--accent-cyan),var(--accent-violet));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;">
                                <?= strtoupper(substr($uname, 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:500;"><?= htmlspecialchars($uname) ?></div>
                                <div style="font-size:0.75rem;color:var(--text-secondary);"><?= htmlspecialchars($row['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem;font-weight:500;"><?= htmlspecialchars($row['title']) ?></td>
                    <td style="padding:1rem;color:var(--text-secondary);"><?= htmlspecialchars($row['subject']) ?></td>
                    <td style="padding:1rem;text-align:center;">
                        <div style="display:flex;gap:0.4rem;justify-content:center;">
                            <a href="<?= $level . $row['file_path'] ?>" target="_blank" class="admin-action-btn" title="View PDF" style="color:var(--accent-cyan);"><i class="fa-solid fa-eye"></i></a>
                            <button onclick="updateStatus(<?= $row['id'] ?>,'approved')" class="admin-action-btn" title="Approve" style="color:#00ff88;"><i class="fa-solid fa-check"></i></button>
                            <button onclick="updateStatus(<?= $row['id'] ?>,'rejected')" class="admin-action-btn" title="Reject" style="color:#ff3366;"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <div style="text-align:center;padding:4rem;color:var(--text-secondary);">
                <i class="fa-solid fa-folder-open" style="font-size:3rem;margin-bottom:1rem;opacity:0.5;"></i>
                <p>No pending notes for approval.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<style>
.admin-action-btn{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);cursor:pointer;transition:all 0.3s;text-decoration:none;font-size:0.85rem;}
.admin-action-btn:hover{background:rgba(255,255,255,0.1);transform:translateY(-2px);}
</style>
<script>
function updateStatus(id,status){
    if(confirm('Set this note to '+status+'?')){
        fetch('handle_note.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&status='+status})
        .then(r=>r.text()).then(d=>{if(d.includes('success'))location.reload();else alert('Error: '+d);});
    }
}
</script>
<?php include '../includes/footer.php'; ?>
