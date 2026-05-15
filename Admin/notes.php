<?php 
$level = '../'; 
$page_title = 'All Notes';
include '../includes/config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit(); }
include '../includes/header.php'; 

$filter = $_GET['status'] ?? 'all';
$where = "";
if ($filter === 'pending') $where = "WHERE n.status='pending'";
elseif ($filter === 'approved') $where = "WHERE n.status='approved'";
elseif ($filter === 'rejected') $where = "WHERE n.status='rejected'";

$notes = $conn->query("SELECT n.*, u.username, u.first_name, u.fname, u.email 
    FROM notes n LEFT JOIN users u ON n.user_id = u.id $where ORDER BY n.created_at DESC");
?>
<div class="dashboard-layout">
    <?php include 'sidebar.php'; ?>
    <main class="dashboard-content">
        <h2 class="animate-fade-in-up" style="margin-bottom:2rem;">All Notes</h2>

        <div class="filter-chips animate-fade-in-up" style="margin-bottom:2rem;">
            <a href="notes.php" class="chip <?= $filter==='all'?'active':'' ?>" style="text-decoration:none;">All</a>
            <a href="notes.php?status=pending" class="chip <?= $filter==='pending'?'active':'' ?>" style="text-decoration:none;">Pending</a>
            <a href="notes.php?status=approved" class="chip <?= $filter==='approved'?'active':'' ?>" style="text-decoration:none;">Approved</a>
            <a href="notes.php?status=rejected" class="chip <?= $filter==='rejected'?'active':'' ?>" style="text-decoration:none;">Rejected</a>
        </div>

        <div class="glass-panel animate-fade-in-up delay-100" style="padding:1.5rem;overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;text-align:left;min-width:800px;">
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Title</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Uploader</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Field</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Status</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Downloads</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;">Rating</th>
                    <th style="padding:1rem;color:var(--text-secondary);font-weight:500;text-align:center;">Actions</th>
                </tr>
                <?php if($notes && $notes->num_rows > 0): while($n = $notes->fetch_assoc()):
                    $uname = $n['username'] ?: ($n['first_name'] ?: ($n['fname'] ?: 'Unknown'));
                ?>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);transition:background 0.3s;" onmouseover="this.style.background='rgba(0,243,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem;font-weight:500;"><?= htmlspecialchars($n['title']) ?></td>
                    <td style="padding:1rem;color:var(--text-secondary);font-size:0.9rem;"><?= htmlspecialchars($uname) ?></td>
                    <td style="padding:1rem;font-size:0.9rem;"><?= htmlspecialchars($n['field'] ?: $n['subject']) ?></td>
                    <td style="padding:1rem;">
                        <?php if($n['status']==='approved'): ?>
                        <span style="color:#00ff88;background:rgba(0,255,136,0.1);padding:0.3rem 0.6rem;border-radius:12px;font-size:0.8rem;">Approved</span>
                        <?php elseif($n['status']==='pending'): ?>
                        <span style="color:#ffaa00;background:rgba(255,170,0,0.1);padding:0.3rem 0.6rem;border-radius:12px;font-size:0.8rem;">Pending</span>
                        <?php else: ?>
                        <span style="color:#ff3366;background:rgba(255,51,102,0.1);padding:0.3rem 0.6rem;border-radius:12px;font-size:0.8rem;">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:1rem;"><?= (int)$n['downloads'] ?></td>
                    <td style="padding:1rem;color:#ffd700;"><?= number_format((float)$n['avg_rating'],1) ?></td>
                    <td style="padding:1rem;text-align:center;">
                        <div style="display:flex;gap:0.4rem;justify-content:center;">
                            <a href="<?= $level . $n['file_path'] ?>" target="_blank" class="admin-action-btn" title="View" style="color:var(--accent-cyan);"><i class="fa-solid fa-eye"></i></a>
                            <?php if($n['status']!=='approved'): ?>
                            <button onclick="updateNote(<?= $n['id'] ?>,'approved')" class="admin-action-btn" title="Approve" style="color:#00ff88;"><i class="fa-solid fa-check"></i></button>
                            <?php endif; ?>
                            <?php if($n['status']!=='rejected'): ?>
                            <button onclick="updateNote(<?= $n['id'] ?>,'rejected')" class="admin-action-btn" title="Reject" style="color:#ff3366;"><i class="fa-solid fa-xmark"></i></button>
                            <?php endif; ?>
                            <button onclick="deleteNote(<?= $n['id'] ?>)" class="admin-action-btn" title="Delete" style="color:#ff3366;"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-secondary);">No notes found.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </main>
</div>
<style>
.admin-action-btn{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);cursor:pointer;transition:all 0.3s;text-decoration:none;font-size:0.85rem;}
.admin-action-btn:hover{background:rgba(255,255,255,0.1);transform:translateY(-2px);}
</style>
<script>
function updateNote(id,status){
    if(confirm('Set note #'+id+' to '+status+'?')){
        fetch('handle_note.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&status='+status})
        .then(r=>r.text()).then(d=>{if(d.includes('success'))location.reload();else alert('Error: '+d);});
    }
}
function deleteNote(id){
    if(confirm('Permanently delete note #'+id+'?')){
        fetch('handle_note.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id+'&status=delete'})
        .then(r=>r.text()).then(d=>{if(d.includes('success'))location.reload();else alert('Error: '+d);});
    }
}
</script>
<?php include '../includes/footer.php'; ?>
