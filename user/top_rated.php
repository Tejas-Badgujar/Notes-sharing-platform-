<?php
$level = '../';
$page_title = 'Top Rated Notes';
include '../includes/config.php';

$is_logged_in = isset($_SESSION['user_id']);

// Fetch all approved notes sorted by rating descending
$notes_q = $conn->query("
    SELECT n.id, n.title, n.field, n.semester, n.subject, n.downloads, n.avg_rating, n.tags,
           u.username, u.first_name, u.fname
    FROM notes n
    LEFT JOIN users u ON n.user_id = u.id
    WHERE n.status = 'approved'
    ORDER BY n.avg_rating DESC, n.downloads DESC
");
$all_notes = [];
if ($notes_q) while($row = $notes_q->fetch_assoc()) $all_notes[] = $row;

// For guests, limit to top 8
if (!$is_logged_in) {
    $all_notes = array_slice($all_notes, 0, 8);
}

include '../includes/header.php';
?>

<?php if ($is_logged_in): ?>
<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
<?php else: ?>
<?php include '../includes/navbar.php'; ?>
<main class="main-content">
    <div class="container">
<?php endif; ?>

        <div class="animate-fade-in-up" style="margin-bottom:2.5rem;">
            <div style="display:flex;align-items:center;gap:0.8rem;margin-bottom:0.5rem;">
                <div style="width:45px;height:45px;border-radius:12px;background:rgba(255,215,0,0.1);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#ffd700;">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>
                <h2 style="margin:0;">Top Rated Notes</h2>
            </div>
            <p style="color:var(--text-secondary);font-size:0.95rem;">
                The highest rated study materials, sorted by community ratings.
                <?php if (!$is_logged_in): ?>
                <span style="color:var(--accent-cyan);">Sign in to see all notes and download.</span>
                <?php endif; ?>
            </p>
        </div>

        <?php if (empty($all_notes)): ?>
        <div class="glass-panel animate-fade-in-up" style="padding:5rem;text-align:center;">
            <i class="fa-solid fa-ranking-star" style="font-size:3rem;color:rgba(255,255,255,0.1);margin-bottom:1rem;display:block;"></i>
            <h3 style="margin-bottom:0.5rem;">No Rated Notes Yet</h3>
            <p style="color:var(--text-secondary);">Be the first to 
                <a href="<?= $is_logged_in ? 'upload.php' : '../auth/register.php' ?>" style="color:var(--accent-cyan);">upload and rate!</a>
            </p>
        </div>
        <?php else: ?>

        <div class="notes-grid animate-fade-in-up delay-100">
            <?php foreach($all_notes as $i => $note): 
                $uploader = htmlspecialchars($note['username'] ?: ($note['first_name'] ?: ($note['fname'] ?: 'Anonymous')));
                $tags_arr = array_filter(array_map('trim', explode(',', $note['tags'] ?? '')));
                $rating = number_format((float)$note['avg_rating'], 1);
                $rank = $i + 1;
            ?>
            <div class="glass-card" style="position:relative;">
                <!-- Rank badge -->
                <?php if ($rank <= 3): ?>
                <div style="position:absolute;top:-10px;left:-10px;width:34px;height:34px;border-radius:50%;
                    background:<?= $rank===1 ? 'linear-gradient(135deg,#ffd700,#ffaa00)' : ($rank===2 ? 'linear-gradient(135deg,#c0c0c0,#888)' : 'linear-gradient(135deg,#cd7f32,#a0522d)') ?>;
                    display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:0.8rem;color:#000;
                    box-shadow:0 4px 12px rgba(0,0,0,0.3);z-index:20;">
                    #<?= $rank ?>
                </div>
                <?php endif; ?>

                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <span class="tag-pill"><i class="fa-solid fa-file-pdf" style="margin-right:3px;"></i>PDF</span>
                        <?php foreach(array_slice($tags_arr,0,2) as $tag): ?>
                        <span class="tag-pill"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div style="color:#ffd700;font-size:1rem;display:flex;align-items:center;gap:0.3rem;font-weight:700;">
                        <i class="fa-solid fa-star"></i> <?= $rating ?>
                    </div>
                </div>

                <h3 style="margin-bottom:0.3rem;font-size:1.1rem;line-height:1.4;"><?= htmlspecialchars($note['title']) ?></h3>
                <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:0.4rem;">
                    <?= htmlspecialchars($note['field'] ?: $note['subject']) ?> &bull; Semester <?= (int)($note['semester'] ?? 0) ?>
                </p>
                <p style="color:var(--text-secondary);font-size:0.78rem;margin-bottom:1rem;">
                    <i class="fa-solid fa-user" style="font-size:0.7rem;"></i> <?= $uploader ?>
                </p>

                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.06);">
                    <span style="color:var(--text-secondary);font-size:0.82rem;display:flex;align-items:center;gap:0.3rem;">
                        <i class="fa-solid fa-download" style="color:var(--accent-cyan);"></i>
                        <?= $note['downloads'] >= 1000 ? round($note['downloads']/1000,1).'k' : (int)$note['downloads'] ?>
                    </span>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="view_note.php?id=<?= $note['id'] ?>" class="btn" style="padding:0.5rem 1rem;font-size:0.82rem;border-radius:10px;text-align:center;background:rgba(0,243,255,0.08);border:1px solid rgba(0,243,255,0.2);color:var(--accent-cyan);text-decoration:none;">
                            <i class="fa-solid fa-eye"></i> View
                        </a>
                        <?php if($is_logged_in): ?>
                        <a href="download.php?id=<?= $note['id'] ?>" class="btn btn-primary" style="padding:0.5rem 1rem;font-size:0.82rem;border-radius:10px;text-align:center;text-decoration:none;">
                            <i class="fa-solid fa-download"></i> Download
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!$is_logged_in && count($all_notes) >= 8): ?>
        <!-- Guest CTA -->
        <div class="animate-fade-in-up" style="text-align:center;padding:3rem 2rem;margin-top:2rem;">
            <div class="glass-panel" style="padding:2.5rem;max-width:600px;margin:0 auto;border-color:rgba(0,243,255,0.15);">
                <h3 style="margin-bottom:0.8rem;"><span class="text-gradient">Want to See All Notes?</span></h3>
                <p style="color:var(--text-secondary);margin-bottom:1.5rem;">Create a free account to access the full library, download PDFs, and rate notes.</p>
                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                    <a href="../auth/register.php" class="btn btn-primary magnetic-btn" style="padding:0.8rem 1.8rem;">
                        <i class="fa-solid fa-user-plus"></i> Sign Up Free
                    </a>
                    <a href="../auth/login.php" class="btn btn-outline magnetic-btn" style="padding:0.8rem 1.8rem;">Sign In</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

<?php if ($is_logged_in): ?>
    </main>
</div>
<?php else: ?>
    </div>
</main>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
