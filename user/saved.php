<?php 
$level = '../'; 
$page_title = 'Saved Notes';
include '../includes/config.php';
include '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Count saved notes
$count_stmt = $conn->prepare("SELECT COUNT(*) as c FROM saved_notes WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$saved_count = $count_stmt->get_result()->fetch_assoc()['c'];

// Fetch saved notes with prepared statement
$stmt = $conn->prepare("
    SELECT n.id, n.title, n.subject, n.field, n.semester,
           n.downloads, n.avg_rating, n.tags, s.saved_at
    FROM saved_notes s
    INNER JOIN notes n ON s.note_id = n.id
    WHERE s.user_id = ? AND n.status = 'approved'
    ORDER BY s.saved_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$saved_notes = [];
while($row = $result->fetch_assoc()) {
    $saved_notes[] = $row;
}
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content">
        <?php include '../includes/back_button.php'; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 class="animate-fade-in-up" style="margin-bottom: 0.3rem;">Saved Notes</h2>
                <p class="animate-fade-in-up delay-100" style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">Your personal collection of bookmarked notes</p>
            </div>
            <div class="glass-panel animate-fade-in-up delay-100" style="padding: 0.6rem 1.2rem; border-radius: 50px; font-size: 0.9rem; color: var(--accent-cyan);">
                <i class="fa-solid fa-bookmark"></i> <span id="saved-total"><?= $saved_count ?></span> Saved
            </div>
        </div>

        <!-- Search inside saved -->
        <div class="glass-panel animate-fade-in-up delay-100" style="padding: 1rem 1.5rem; margin-bottom: 2rem; border-radius: 50px; display: flex; align-items: center; gap: 1rem;">
            <i class="fa-solid fa-magnifying-glass" style="color: var(--text-secondary);"></i>
            <input type="text" id="saved-search" class="form-control" placeholder="Search your saved notes..." style="border: none; background: transparent; padding: 0; box-shadow: none; flex: 1;">
        </div>

        <!-- Saved notes collection tabs -->
        <div class="filter-chips animate-fade-in-up delay-200" style="margin-bottom: 2rem;">
            <div class="chip active" data-filter="all">All</div>
            <div class="chip" data-filter="computer science">Computer Science</div>
            <div class="chip" data-filter="information technology">Information Tech</div>
            <div class="chip" data-filter="electronics">Electronics</div>
        </div>

        <?php if (empty($saved_notes)): ?>
        <!-- Empty State -->
        <div class="glass-panel animate-fade-in-up delay-200" style="padding: 5rem; text-align: center;">
            <i class="fa-solid fa-bookmark" style="font-size: 3rem; color: rgba(255,255,255,0.1); margin-bottom: 1rem; display: block;"></i>
            <h3 style="margin-bottom: 0.5rem;">No Saved Notes Yet</h3>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">Explore notes and click the bookmark icon to save them here.</p>
            <a href="explore.php" class="btn btn-primary magnetic-btn" style="padding: 1rem 2rem;">
                <i class="fa-solid fa-compass"></i> Explore Notes
            </a>
        </div>
        <?php else: ?>
        <!-- Saved Notes Grid -->
        <div class="notes-grid animate-fade-in-up delay-200">
            <?php foreach($saved_notes as $note): 
                $tags_arr = array_filter(array_map('trim', explode(',', $note['tags'] ?? '')));
            ?>
            <div class="glass-card animate-fade-in-up note-item" data-branch="<?= htmlspecialchars(strtolower($note['field'] ?? '')) ?>" data-id="<?= $note['id'] ?>">
                <!-- Top row: type badge + saved date + remove bookmark -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <div style="background: rgba(0, 243, 255, 0.1); color: var(--accent-cyan); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; border: 1px solid rgba(0, 243, 255, 0.2);">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </div>
                        <?php foreach(array_slice($tags_arr,0,2) as $tag): ?>
                        <span class="tag-pill"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.8rem;">
                        <span style="color: #ffd700; font-size: 0.9rem;"><i class="fa-solid fa-star"></i> <?= number_format((float)$note['avg_rating'], 1) ?></span>
                        <button class="unsave-btn" data-id="<?= $note['id'] ?>" title="Remove from saved"
                            style="background: none; border: none; color: var(--accent-cyan); cursor: pointer; font-size: 1rem; transition: color 0.2s; padding: 0;"
                            onmouseover="this.style.color='#ff3366'" onmouseout="this.style.color='var(--accent-cyan)'">
                            <i class="fa-solid fa-bookmark"></i>
                        </button>
                    </div>
                </div>

                <h3 style="margin-bottom: 0.5rem; font-size: 1.1rem; color: var(--text-primary);"><?= htmlspecialchars($note['title']) ?></h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($note['field'] ?: $note['subject']) ?></p>
                <p style="color: rgba(255,255,255,0.3); font-size: 0.78rem; margin-bottom: 1.2rem;"><i class="fa-regular fa-clock"></i> Saved on <?= date("d M Y", strtotime($note['saved_at'])) ?></p>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--glass-border); padding-top: 1rem;">
                    <div style="color: var(--text-secondary); font-size: 0.85rem;">
                        <i class="fa-solid fa-download"></i> <?= (int)$note['downloads'] ?> Downloads
                    </div>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="view_note.php?id=<?= $note['id'] ?>" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background:rgba(0,243,255,0.08);border:1px solid rgba(0,243,255,0.2);color:var(--accent-cyan);border-radius:10px;text-decoration:none;">
                            <i class="fa-solid fa-eye"></i> View
                        </a>
                        <a href="download.php?id=<?= $note['id'] ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem; border-radius:10px;text-decoration:none;">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Discover More CTA -->
        <div class="glass-panel animate-fade-in-up" style="padding:2rem; margin-top:3rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1.5rem; background:linear-gradient(135deg,rgba(0,243,255,0.05),rgba(138,43,226,0.08));">
            <div>
                <h3 style="margin:0 0 0.4rem;">Looking for more notes?</h3>
                <p style="color:var(--text-secondary); margin:0; font-size:0.9rem;">Browse premium study materials across all branches.</p>
            </div>
            <a href="explore.php" class="btn btn-primary magnetic-btn" style="padding:0.85rem 2rem; font-size:1rem; white-space:nowrap;">
                <i class="fa-solid fa-compass"></i> Explore All Notes
            </a>
        </div>

    </main>
</div>

<script>
// Saved page — live search + chip filtering + AJAX unsave
const savedSearch = document.getElementById('saved-search');
const savedGrid   = document.querySelector('.notes-grid');

function filterSavedNotes() {
    const query  = savedSearch ? savedSearch.value.trim().toLowerCase() : '';
    const active = document.querySelector('.filter-chips .chip.active');
    const branch = active ? active.getAttribute('data-filter') : 'all';

    const cards = savedGrid ? savedGrid.querySelectorAll('.note-item') : [];
    if (savedGrid) savedGrid.style.opacity = '0';

    setTimeout(() => {
        let visible = 0;
        cards.forEach(card => {
            const title  = card.querySelector('h3') ? card.querySelector('h3').textContent.toLowerCase() : '';
            const cardBr = card.getAttribute('data-branch') || '';
            const matchQ  = !query  || title.includes(query);
            const matchBr = branch === 'all' || cardBr.includes(branch);
            if (matchQ && matchBr) {
                card.style.display = 'block';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        if (savedGrid) savedGrid.style.opacity = '1';
    }, 260);
}

if (savedSearch) {
    savedSearch.addEventListener('input', filterSavedNotes);
}

// Chip filter wiring
document.querySelectorAll('.filter-chips .chip').forEach(chip => {
    chip.addEventListener('click', function() {
        document.querySelectorAll('.filter-chips .chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        filterSavedNotes();
    });
});

// AJAX unsave — remove from DB and animate card out
document.querySelectorAll('.unsave-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const noteId = this.getAttribute('data-id');
        const card = this.closest('.note-item');
        
        fetch('../ajax/save_note.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'note_id=' + noteId
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok && !data.saved) {
                // Animate card out
                card.style.transition = 'transform 0.4s cubic-bezier(0.16,1,0.3,1), opacity 0.35s ease';
                card.style.transform  = 'scale(0.85) translateY(-8px)';
                card.style.opacity    = '0';
                setTimeout(() => {
                    card.remove();
                    // Update counter
                    const counter = document.getElementById('saved-total');
                    if (counter) counter.textContent = Math.max(0, parseInt(counter.textContent) - 1);
                }, 400);
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
