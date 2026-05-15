<?php 
$level = '../'; 
$page_title = 'My Notes';
include '../includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$uid = (int)$_SESSION['user_id'];
$success = $_GET['success'] ?? '';

// Real DB query — prepared statement
$notes_stmt = $conn->prepare("SELECT id, title, field, semester, subject, status, downloads, avg_rating, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC");
$notes_stmt->bind_param("i", $uid);
$notes_stmt->execute();
$notes_q = $notes_stmt->get_result();
$my_notes = [];
if ($notes_q) while($r = $notes_q->fetch_assoc()) $my_notes[] = $r;

include '../includes/header.php'; 
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="animate-fade-in-up">My Uploaded Notes</h2>
            <a href="upload.php" class="btn btn-primary animate-fade-in-up delay-100 magnetic-btn">
                <i class="fa-solid fa-plus"></i> New Upload
            </a>
        </div>

        <?php if($success): ?>
        <div class="animate-fade-in-up" style="background:rgba(0,255,136,0.1);border:1px solid rgba(0,255,136,0.4);color:#00ff88;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:0.9rem;">
            <i class="fa-solid fa-check-circle"></i> Note uploaded successfully! It will appear here once approved by admin.
        </div>
        <?php endif; ?>

        <?php if (empty($my_notes)): ?>
        <!-- Empty state -->
        <div class="glass-panel animate-fade-in-up delay-200" style="padding:5rem;text-align:center;">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size:4rem;color:rgba(255,255,255,0.1);margin-bottom:1.5rem;display:block;"></i>
            <h3 style="margin-bottom:0.5rem;">No Notes Uploaded Yet</h3>
            <p style="color:var(--text-secondary);margin-bottom:2rem;">Share your first study material with the community!</p>
            <a href="upload.php" class="btn btn-primary magnetic-btn" style="padding:1rem 2rem;">
                <i class="fa-solid fa-plus"></i> Upload Your First Note
            </a>
        </div>
        <?php else: ?>
        <div class="notes-grid animate-fade-in-up delay-200">
            <?php foreach($my_notes as $note): 
                $status_color = $note['status']==='approved' ? '#00ff88' : ($note['status']==='pending' ? '#ffaa00' : '#ff3366');
                $status_label = ucfirst($note['status']);
            ?>
            <div class="glass-card" style="position:relative;">
                <div style="position:absolute;top:-10px;right:-10px;background:<?= $status_color ?>;color:#000;font-size:0.7rem;font-weight:bold;padding:0.2rem 0.5rem;border-radius:10px;box-shadow:0 0 10px <?= $status_color ?>44;z-index:20;"><?= $status_label ?></div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <div style="background:rgba(0,243,255,0.1);color:var(--accent-cyan);padding:0.3rem 0.8rem;border-radius:20px;font-size:0.8rem;border:1px solid rgba(0,243,255,0.2);">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </div>
                    <div style="color:#ffd700;font-size:0.9rem;display:flex;align-items:center;gap:0.3rem;">
                        <i class="fa-solid fa-star"></i> <?= number_format((float)$note['avg_rating'],1) ?>
                    </div>
                </div>
                <h3 style="margin-bottom:0.5rem;font-size:1.2rem;"><?= htmlspecialchars($note['title']) ?></h3>
                <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:1.5rem;"><?= htmlspecialchars($note['field'] ?? '') ?> &bull; Sem <?= (int)$note['semester'] ?></p>
                <div style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--glass-border);padding-top:1rem;">
                    <div style="color:var(--text-secondary);font-size:0.85rem;">
                        <i class="fa-solid fa-download"></i> <?= (int)$note['downloads'] ?> Downloads
                    </div>
                    <a href="view_note.php?id=<?= $note['id'] ?>" class="btn btn-primary" style="padding:0.5rem 1rem;font-size:0.9rem;text-decoration:none;">
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
