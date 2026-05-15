<?php 
$level      = '../'; 
$page_title = 'View Note';
include '../includes/config.php';
include '../includes/header.php';

$note_id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_guest  = !isset($_SESSION['user_id']);

// ── Fetch real note from DB ──
if ($note_id) {
    $stmt = $conn->prepare("
        SELECT n.id, n.title, n.field, n.subject, n.description, n.tags,
               n.downloads, n.avg_rating, n.file_path, n.status, n.created_at,
               u.username, u.first_name, u.fname
        FROM notes n
        LEFT JOIN users u ON n.user_id = u.id
        WHERE n.id = ? AND n.status = 'approved'
    ");
    $stmt->bind_param("i", $note_id);
    $stmt->execute();
    $note = $stmt->get_result()->fetch_assoc();
} else {
    $note = null;
}

if (!$note) {
    echo '<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:3rem;">
            <div>
                <i class="fa-solid fa-file-circle-xmark" style="font-size:4rem;color:var(--accent-magenta);margin-bottom:1rem;"></i>
                <h2>Note Not Available</h2>
                <p style="color:var(--text-secondary);">This note may not be approved yet or does not exist.</p>
                <a href="explore.php" class="btn btn-primary" style="margin-top:1.5rem;">Browse Notes</a>
            </div>
          </div>';
    include '../includes/footer.php';
    exit();
}

$uploader   = htmlspecialchars($note['username'] ?: ($note['first_name'] ?: ($note['fname'] ?: 'Anonymous')));
$tags_arr   = array_filter(array_map('trim', explode(',', $note['tags'] ?? '')));
$pdf_url    = $level . ltrim($note['file_path'], '/');
$upload_date = date('d M Y', strtotime($note['created_at']));

// Fetch user's existing rating if logged in
$user_rating = 0;
$total_ratings = 0;
if (!$is_guest) {
    $r = $conn->prepare("SELECT rating FROM note_ratings WHERE note_id = ? AND user_id = ?");
    $r->bind_param("ii", $note_id, $_SESSION['user_id']);
    $r->execute();
    $rr = $r->get_result()->fetch_assoc();
    if ($rr) $user_rating = (int)$rr['rating'];
}
$tr = $conn->prepare("SELECT COUNT(*) as c FROM note_ratings WHERE note_id = ?");
$tr->bind_param("i", $note_id);
$tr->execute();
$tr_result = $tr->get_result();
if ($tr_result) $total_ratings = $tr_result->fetch_assoc()['c'];
?>

<?php if ($is_guest): ?>
<!-- ── Guest Page-Lock Modal ── -->
<div id="page-lock-modal" style="display:none;position:fixed;inset:0;z-index:9999999;align-items:center;justify-content:center;">
  <div style="position:absolute;inset:0;background:rgba(5,5,15,0.72);backdrop-filter:blur(14px);"></div>
  <div class="glass-panel" id="page-lock-card"
       style="position:relative;max-width:460px;width:92%;padding:2.8rem 2.5rem;border-radius:28px;text-align:center;
              animation:lockCardIn 0.55s cubic-bezier(0.16,1,0.3,1) forwards;overflow:hidden;">
    <div style="position:absolute;inset:0;pointer-events:none;overflow:hidden;border-radius:28px;">
      <div id="lock-ripple" style="position:absolute;width:120%;padding-bottom:120%;top:50%;left:50%;transform:translate(-50%,-50%) scale(0);
                                    background:radial-gradient(circle,rgba(0,243,255,0.18) 0%,transparent 65%);
                                    border-radius:50%;animation:lockRippleAnim 1.4s ease-out forwards;"></div>
    </div>
    <div style="font-size:3.5rem;margin-bottom:1rem;animation:lockIconBounce 0.7s 0.3s ease both;">
        <i class="fa-solid fa-lock" style="color:var(--accent-cyan);"></i>
    </div>
    <h2 style="margin-bottom:0.5rem;font-size:1.5rem;">Full Access Required</h2>
    <p style="color:var(--text-secondary);margin-bottom:2rem;line-height:1.7;font-size:0.95rem;">
      Log in or create a free account to read all pages and download the PDF.
    </p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="../auth/login.php" class="btn btn-primary magnetic-btn" style="padding:0.9rem 2rem;font-size:1rem;">
        <i class="fa-solid fa-right-to-bracket"></i> Sign In
      </a>
      <a href="../auth/register.php" class="btn" style="padding:0.9rem 2rem;font-size:1rem;background:rgba(255,255,255,0.07);border:1px solid var(--glass-border);border-radius:12px;">
        Create Free Account
      </a>
    </div>
    <button onclick="hideLockModal()" style="margin-top:1.5rem;background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:0.85rem;">
      <i class="fa-solid fa-xmark"></i> Continue with preview only
    </button>
  </div>
</div>
<style>
@keyframes lockCardIn    { from{opacity:0;transform:scale(0.85) translateY(30px)} to{opacity:1;transform:scale(1) translateY(0)} }
@keyframes lockRippleAnim{ from{transform:translate(-50%,-50%) scale(0);opacity:1} to{transform:translate(-50%,-50%) scale(1.4);opacity:0} }
@keyframes lockIconBounce{ 0%{transform:translateY(20px);opacity:0} 60%{transform:translateY(-8px)} 100%{transform:translateY(0);opacity:1} }
</style>
<?php endif; ?>

<!-- ── Layout ── -->
<div class="dashboard-layout">
  <?php if (!$is_guest): ?>
    <?php include '../includes/sidebar.php'; ?>
  <?php else: ?>
    <!-- Guest minimal top bar -->
    <nav style="position:fixed;top:0;left:0;right:0;z-index:100;padding:0.8rem 2rem;background:rgba(5,5,15,0.85);backdrop-filter:blur(16px);border-bottom:1px solid var(--glass-border);display:flex;justify-content:space-between;align-items:center;">
      <a href="../index.php" class="logo" style="font-size:1.4rem;"><i class="fa-solid fa-layer-group"></i><span>Notes</span>Platform</a>
      <div style="display:flex;gap:1rem;">
        <a href="../index.php" class="btn" style="background:rgba(255,255,255,0.06);border:1px solid var(--glass-border);border-radius:10px;padding:0.5rem 1.2rem;font-size:0.9rem;">Explore</a>
        <a href="../auth/login.php" class="btn btn-primary" style="padding:0.5rem 1.2rem;font-size:0.9rem;">Sign In</a>
      </div>
    </nav>
    <div style="height:60px;"></div>
  <?php endif; ?>

  <main class="dashboard-content">

    <!-- Breadcrumbs -->
    <div style="margin-bottom:2rem;font-size:0.9rem;color:var(--text-secondary);" class="animate-fade-in-up">
      <a href="<?= $is_guest ? '../index.php' : 'explore.php' ?>" style="color:var(--text-secondary);text-decoration:none;">Explore</a> /
      <span style="color:var(--accent-cyan);"><?= htmlspecialchars($note['title']) ?></span>
    </div>

    <div class="view-note-grid" style="display:grid;grid-template-columns:2fr 1fr;gap:2rem;">

      <!-- Left: PDF + Info -->
      <div>
        <div class="glass-panel animate-fade-in-up" style="margin-bottom:2rem;padding:1.5rem;">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
            <div>
              <h2 style="margin-bottom:0.5rem;"><?= htmlspecialchars($note['title']) ?></h2>
              <p style="color:var(--text-secondary);">
                <i class="fa-solid fa-user"></i> By <?= $uploader ?>
                &bull; <i class="fa-regular fa-calendar"></i> <?= $upload_date ?>
              </p>
            </div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
              <?php if (!$is_guest): ?>
              <a href="download.php?id=<?= $note_id ?>" class="btn btn-primary" style="padding:0.7rem 1.5rem;">
                <i class="fa-solid fa-download"></i> Download PDF
              </a>
              <?php else: ?>
              <button onclick="showLockModal()" class="btn btn-primary" style="padding:0.7rem 1.5rem;">
                <i class="fa-solid fa-download"></i> Download PDF
              </button>
              <?php endif; ?>
            </div>
          </div>

          <!-- Native PDF Viewer using iframe -->
          <div id="pdf-viewer-wrap" style="position:relative;border-radius:16px;overflow:hidden;border:1px solid var(--glass-border);background:var(--bg-navy);">
            <?php if (!$is_guest): ?>
            <!-- Full PDF in browser's built-in viewer -->
            <iframe src="<?= htmlspecialchars($pdf_url) ?>#toolbar=1&navpanes=0" 
                    style="width:100%;height:700px;border:none;border-radius:16px;background:#fff;"
                    title="PDF Viewer"></iframe>
            <?php else: ?>
            <!-- Guest: show limited preview with overlay -->
            <iframe src="<?= htmlspecialchars($pdf_url) ?>#toolbar=0&navpanes=0&page=1" 
                    style="width:100%;height:500px;border:none;border-radius:16px;background:#fff;"
                    title="PDF Preview"></iframe>
            <div style="position:absolute;bottom:0;left:0;right:0;height:60%;background:linear-gradient(to bottom,transparent,rgba(5,5,15,0.95));pointer-events:none;"></div>
            <div style="position:absolute;bottom:2rem;left:0;right:0;text-align:center;z-index:10;">
                <button onclick="showLockModal()" class="btn btn-primary magnetic-btn" style="padding:0.8rem 2rem;">
                    <i class="fa-solid fa-lock"></i> Sign in to view full document
                </button>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Description -->
        <div class="glass-panel animate-fade-in-up delay-100" style="padding:2rem;margin-bottom:2rem;">
          <h3 style="margin-bottom:1rem;">Description</h3>
          <p style="color:var(--text-secondary);line-height:1.8;"><?= htmlspecialchars($note['description'] ?: 'No description provided.') ?></p>
          <?php if ($tags_arr): ?>
          <div style="display:flex;gap:0.6rem;flex-wrap:wrap;margin-top:1.5rem;">
            <?php foreach($tags_arr as $tag): ?>
            <span class="chip">#<?= htmlspecialchars($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Right: Stats + Rating -->
      <div>
        <!-- Quick Stats -->
        <div class="glass-panel animate-fade-in-up" style="padding:1.5rem;margin-bottom:2rem;">
          <h3 style="margin-bottom:1.5rem;">Quick Stats</h3>
          <div style="display:flex;flex-direction:column;gap:1.2rem;font-size:0.9rem;">
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-secondary);">Field</span>
              <span><?= htmlspecialchars($note['field'] ?: '—') ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-secondary);">Subject</span>
              <span><?= htmlspecialchars($note['subject'] ?: '—') ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-secondary);">Downloads</span>
              <span><?= number_format((int)$note['downloads']) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;">
              <span style="color:var(--text-secondary);">Rating</span>
              <span style="color:#ffd700;"><i class="fa-solid fa-star"></i> <?= number_format((float)$note['avg_rating'],1) ?> <small style="color:var(--text-secondary);">(<?= $total_ratings ?>)</small></span>
            </div>
          </div>
        </div>

        <!-- Rating Widget (logged-in users only) -->
        <?php if (!$is_guest): ?>
        <div class="glass-panel animate-fade-in-up delay-100" style="padding:1.8rem;margin-bottom:2rem;text-align:center;">
          <h3 style="margin-bottom:0.5rem;">Rate this Note</h3>
          <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:1.2rem;">Help others by sharing your rating</p>
          
          <div id="star-rating" style="display:flex;justify-content:center;gap:0.4rem;margin-bottom:1rem;">
            <?php for($s=1;$s<=5;$s++): ?>
            <button class="star-btn" data-star="<?= $s ?>" onclick="rateNote(<?= $s ?>)"
                    style="background:none;border:none;cursor:pointer;font-size:2rem;color:<?= $s <= $user_rating ? '#ffd700' : 'rgba(255,255,255,0.15)' ?>;transition:all 0.2s;padding:0.2rem;">
              <i class="fa-solid fa-star"></i>
            </button>
            <?php endfor; ?>
          </div>
          
          <p id="rating-msg" style="color:var(--text-secondary);font-size:0.85rem;">
            <?= $user_rating ? "You rated this $user_rating/5" : "Click a star to rate" ?>
          </p>
          <div id="avg-display" style="margin-top:0.8rem;font-size:0.9rem;">
            Average: <span id="avg-val" style="color:#ffd700;font-weight:600;"><?= number_format((float)$note['avg_rating'],1) ?></span>
            <span style="color:var(--text-secondary);"> / 5</span>
            <span style="color:var(--text-secondary);font-size:0.8rem;"> (<span id="total-val"><?= $total_ratings ?></span> ratings)</span>
          </div>
        </div>
        <?php endif; ?>

        <!-- Download CTA -->
        <?php if (!$is_guest): ?>
        <div class="glass-panel animate-fade-in-up delay-200" style="padding:1.5rem;text-align:center;">
            <a href="download.php?id=<?= $note_id ?>" class="btn btn-primary magnetic-btn" style="width:100%;padding:1rem;font-size:1.05rem;">
                <i class="fa-solid fa-download"></i> Download PDF
            </a>
            <p style="color:var(--text-secondary);font-size:0.8rem;margin-top:0.8rem;"><?= number_format((int)$note['downloads']) ?> downloads so far</p>
        </div>
        <?php endif; ?>

        <?php if ($is_guest): ?>
        <div class="glass-panel animate-fade-in-up delay-100" style="padding:1.8rem;text-align:center;border-color:rgba(0,243,255,0.25);">
          <div style="font-size:2.5rem;margin-bottom:0.8rem;"><i class="fa-solid fa-book-open" style="color:var(--accent-cyan);"></i></div>
          <h3 style="margin-bottom:0.5rem;">Want Full Access?</h3>
          <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:1.5rem;">Log in to read every page and download any PDF for free.</p>
          <a href="../auth/login.php" class="btn btn-primary" style="width:100%;margin-bottom:0.8rem;display:block;">Sign In</a>
          <a href="../auth/register.php" style="color:var(--accent-cyan);font-size:0.9rem;text-decoration:none;">Create free account →</a>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<?php if (!$is_guest): ?>
<script>
// Star rating functionality
const starBtns = document.querySelectorAll('.star-btn');

// Hover effect
starBtns.forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        const s = parseInt(this.dataset.star);
        starBtns.forEach(b => {
            b.style.color = parseInt(b.dataset.star) <= s ? '#ffd700' : 'rgba(255,255,255,0.15)';
            b.style.transform = parseInt(b.dataset.star) <= s ? 'scale(1.2)' : 'scale(1)';
        });
    });
});

document.getElementById('star-rating').addEventListener('mouseleave', () => {
    const current = document.getElementById('star-rating').dataset.current || 0;
    starBtns.forEach(b => {
        b.style.color = parseInt(b.dataset.star) <= current ? '#ffd700' : 'rgba(255,255,255,0.15)';
        b.style.transform = 'scale(1)';
    });
});

// Set initial state
document.getElementById('star-rating').dataset.current = <?= $user_rating ?>;

function rateNote(stars) {
    fetch('../ajax/rate_note.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'note_id=<?= $note_id ?>&rating=' + stars
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            document.getElementById('star-rating').dataset.current = stars;
            starBtns.forEach(b => {
                b.style.color = parseInt(b.dataset.star) <= stars ? '#ffd700' : 'rgba(255,255,255,0.15)';
            });
            document.getElementById('rating-msg').textContent = 'You rated this ' + stars + '/5';
            document.getElementById('avg-val').textContent = data.avg_rating.toFixed(1);
            document.getElementById('total-val').textContent = data.total_ratings;
            
            // Success animation
            document.getElementById('rating-msg').style.color = '#00ff88';
            setTimeout(() => document.getElementById('rating-msg').style.color = 'var(--text-secondary)', 2000);
        }
    });
}
</script>
<?php endif; ?>

<script>
/* ── Lock modal helpers ── */
function showLockModal() {
  const modal = document.getElementById('page-lock-modal');
  if (!modal) return;
  modal.style.display = 'flex';
}
function hideLockModal() {
  const modal = document.getElementById('page-lock-modal');
  if (modal) {
    modal.style.opacity = '0';
    modal.style.transition = 'opacity 0.4s';
    setTimeout(() => { modal.style.display = 'none'; modal.style.opacity = ''; modal.style.transition = ''; }, 400);
  }
}
</script>

<style>
@media (max-width: 768px) {
    .view-note-grid { grid-template-columns: 1fr !important; }
    iframe { height: 400px !important; }
}
</style>

<?php include '../includes/footer.php'; ?>
