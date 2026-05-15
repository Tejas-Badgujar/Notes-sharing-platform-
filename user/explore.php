<?php
$level = '../';
$page_title = 'Explore Notes';
include '../includes/config.php';
// No forced login — page is public; only download requires login
$is_logged_in = isset($_SESSION['user_id']);
$display_name = $is_logged_in ? ($_SESSION['username'] ?? $_SESSION['first_name'] ?? 'User') : null;
$avatar_char  = $display_name ? strtoupper(substr($display_name,0,1)) : 'G';

// Pull ONLY approved notes from real DB — using prepared statements
$filter_field  = $_GET['field']  ?? '';
$filter_branch = $_GET['branch'] ?? '';
$search_q      = trim($_GET['q'] ?? '');

$sql = "SELECT n.id, n.title, n.field, n.semester, n.subject, n.downloads, n.avg_rating, n.tags, n.created_at,
               u.username, u.first_name
        FROM notes n
        LEFT JOIN users u ON n.user_id = u.id
        WHERE n.status = 'approved'";
$params = [];
$types  = '';

if ($filter_field) {
    $sql .= " AND n.field = ?";
    $params[] = $filter_field;
    $types .= 's';
}
if ($filter_branch) {
    $sql .= " AND n.field = ?";
    $params[] = $filter_branch;
    $types .= 's';
}
if ($search_q) {
    $sql .= " AND (n.title LIKE ? OR n.subject LIKE ? OR n.tags LIKE ?)";
    $like = '%' . $search_q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}

$sql .= " ORDER BY n.downloads DESC";
if (!$is_logged_in) $sql .= " LIMIT 10";

$stmt_notes = $conn->prepare($sql);
if ($types) {
    $stmt_notes->bind_param($types, ...$params);
}
$stmt_notes->execute();
$notes_q = $stmt_notes->get_result();
$all_notes = [];
if ($notes_q) while($row=$notes_q->fetch_assoc()) $all_notes[]=$row;
if (!$is_logged_in && count($all_notes) >= 10) $guest_limit_reached = true;

// Load user's saved note IDs for bookmark state
$saved_ids = [];
if ($is_logged_in) {
    $sv = $conn->prepare("SELECT note_id FROM saved_notes WHERE user_id = ?");
    $sv->bind_param("i", $_SESSION['user_id']);
    $sv->execute();
    $svr = $sv->get_result();
    while ($s = $svr->fetch_assoc()) $saved_ids[] = (int)$s['note_id'];
}
include '../includes/header.php';
?>

<!-- Floating ambient orbs -->
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- Logged-in Navbar (no Login / Sign-up) -->
<nav class="navbar-user" id="explore-navbar">
    <div class="container">
        <a href="../user/dashboard.php" class="logo">
            <i class="fa-solid fa-layer-group"></i>
            <span>Notes</span>Platform
        </a>

    <div style="display:flex;align-items:center;gap:1.5rem;">
            <a href="explore.php" style="color:var(--accent-cyan);font-weight:600;text-decoration:none;font-size:0.95rem;display:flex;align-items:center;gap:0.4rem;"><i class="fa-solid fa-compass"></i> Explore</a>
            <?php if($is_logged_in): ?>
            <a href="saved.php" style="color:var(--text-secondary);font-weight:500;text-decoration:none;font-size:0.95rem;transition:color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'"><i class="fa-solid fa-bookmark"></i> Saved</a>
            <a href="dashboard.php" style="color:var(--text-secondary);font-weight:500;text-decoration:none;font-size:0.95rem;transition:color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.6)'"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="dashboard.php" class="user-pill">
                <div class="user-avatar"><?= $avatar_char ?></div>
                <span style="font-size:0.9rem;font-weight:500;"><?= htmlspecialchars($display_name) ?></span>
                <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;color:var(--text-secondary);"></i>
            </a>
            <?php else: ?>
            <a href="../auth/login.php" style="color:var(--text-secondary);font-weight:500;text-decoration:none;font-size:0.95rem;">Login</a>
            <a href="../auth/register.php" class="btn btn-primary" style="padding:0.4rem 1rem;font-size:0.9rem;">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="page-enter" style="min-height:100vh; padding-bottom:4rem;">

    <!-- Hero -->
    <section class="explore-hero container">
        <div class="animate-fade-in-up" style="display:inline-flex; align-items:center; gap:0.5rem; background:rgba(0,243,255,0.08); border:1px solid rgba(0,243,255,0.2); border-radius:99px; padding:0.35rem 1rem; font-size:0.82rem; color:var(--accent-cyan); margin-bottom:1.5rem; letter-spacing:0.04em;">
            <i class="fa-solid fa-bolt-lightning"></i> Your Notes Library
        </div>
        <h1 class="animate-fade-in-up delay-100">
            Discover <span class="text-gradient">Premium</span> Study Materials
        </h1>
        <p class="subtitle animate-fade-in-up delay-200">
            Browse, filter, and download top-rated notes shared by students across all branches and semesters.
        </p>
<?php
        $sql = "SELECT 
            (SELECT COUNT(*) FROM users) AS total_users,
            (SELECT COUNT(*) FROM notes WHERE status = 'approved') AS total_notes,
            (SELECT COALESCE(SUM(downloads), 0) FROM notes WHERE status = 'approved') AS total_downloads";

$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->error);
}

$data = $result->fetch_assoc();

$total_users = $data['total_users'];
$total_notes = $data['total_notes'];
$total_downloads = $data['total_downloads'];
?>
        <!-- Quick stats -->
        <div class="explore-stats-bar animate-fade-in-up delay-300">
            <div class="stat-item">
                <div class="value" data-counter="<?= $total_notes ?>">0</div>
                <div class="label">Total Notes</div>
            </div>
            <div style="width:1px; background:var(--glass-border); height:40px; align-self:center;"></div>
            <div class="stat-item">
                <div class="value" data-counter="<?= $total_users ?>">0</div>
                <div class="label">Contributors</div>
            </div>
            <div style="width:1px; background:var(--glass-border); height:40px; align-self:center;"></div>
            <div class="stat-item">
                <div class="value" data-counter="<?= $total_downloads ?>">0</div>
                <div class="label">Downloads</div>
            </div>
        </div>

        <!-- Hero Search -->
        <div class="hero-search animate-fade-in-up delay-300" style="max-width:680px;">
            <input type="text" id="explore-search" class="form-control" placeholder="Search notes, subjects, tags…" autocomplete="off">
            <i class="fa-solid fa-magnifying-glass"></i>
            <div id="explore-dropdown" class="search-dropdown">
                <div class="results" style="padding:0.5rem 0;"></div>
            </div>
        </div>
    </section>

    <!-- Filter strip -->
    <div class="container">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:2rem;">
            <div class="filter-chips" style="margin:0;" id="branch-chips">
                <div class="chip active" data-filter="all">All Branches</div>
                <div class="chip" data-filter="cs">Computer Science</div>
                <div class="chip" data-filter="it">Information Tech</div>
                <div class="chip" data-filter="ec">Electronics</div>
                <div class="chip" data-filter="mech">Mechanical</div>
            </div>
            <div style="display:flex; align-items:center; gap:0.8rem;">
                <span style="color:var(--text-secondary); font-size:0.85rem;">Sort by:</span>
                <select id="sort-select" class="form-control" style="width:auto; padding:0.5rem 1rem; border-radius:99px; font-size:0.85rem; cursor:pointer;">
                    <option value="popular">Most Popular</option>
                    <option value="rating">Highest Rated</option>
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="az">A → Z</option>
                    <option value="za">Z → A</option>
                </select>
            </div>
        </div>



        <!-- Results count -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <p id="results-count" style="color:var(--text-secondary); font-size:0.9rem;">
                Showing <span id="count-num" style="color:var(--accent-cyan); font-weight:600;"><?= count($all_notes) ?></span> notes
            </p>
            <div style="display:flex; gap:0.6rem;">
                <button id="view-grid" title="Grid view" style="width:36px;height:36px;border-radius:8px;background:rgba(0,243,255,0.1);border:1px solid rgba(0,243,255,0.3);color:var(--accent-cyan);cursor:pointer;">
                    <i class="fa-solid fa-grip"></i>
                </button>
                <button id="view-list" title="List view" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:var(--text-secondary);cursor:pointer;">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Notes Grid (real DB data) -->
        <div class="notes-grid" id="explore-grid">
            <?php if(empty($all_notes)): ?>
            <div style="grid-column:1/-1;text-align:center;padding:5rem;color:var(--text-secondary);">
                <i class="fa-solid fa-inbox" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:0.3;"></i>
                <p>No approved notes yet. Be the first to <a href="<?= $is_logged_in ? 'upload.php' : '../auth/register.php' ?>" style="color:var(--accent-cyan);">upload!</a></p>
            </div>
            <?php else: foreach($all_notes as $i=>$note):
                $delay_cls = 'd'.(($i%7)+1);
                $uploader  = htmlspecialchars($note['username'] ?: $note['first_name'] ?: 'Anonymous');
                $tags_arr  = array_filter(array_map('trim', explode(',', $note['tags'] ?? '')));
                $pop       = min(100, $note['downloads'] > 0 ? min(100, round(log($note['downloads']+1)/log(6000)*100)) : 0);
                $is_saved  = in_array((int)$note['id'], $saved_ids);
            ?>
            <div class="explore-card reveal <?= $delay_cls ?>"
                 data-branch="<?= htmlspecialchars($note['field'] ?? '') ?>"
                 data-sem="<?= (int)($note['semester']??0) ?>"
                 data-downloads="<?= (int)$note['downloads'] ?>"
                 data-rating="<?= (float)$note['avg_rating'] ?>"
                 data-date="<?= htmlspecialchars($note['created_at'] ?? '') ?>"
                 data-title="<?= htmlspecialchars($note['title']) ?>">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.1rem;">
                    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                        <span class="tag-pill"><i class="fa-solid fa-file-pdf" style="margin-right:3px;"></i>PDF</span>
                        <?php foreach(array_slice($tags_arr,0,2) as $tag): ?>
                        <span class="tag-pill"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <button class="save-btn <?= $is_saved ? 'saved' : '' ?>" data-id="<?= $note['id'] ?>" title="<?= $is_saved ? 'Unsave note' : 'Save note' ?>"
                        style="background:none;border:none;color:<?= $is_saved ? 'var(--accent-cyan)' : 'rgba(255,255,255,0.3)' ?>;cursor:pointer;font-size:1.1rem;transition:all 0.3s;padding:0;">
                        <i class="<?= $is_saved ? 'fa-solid' : 'fa-regular' ?> fa-bookmark"></i>
                    </button>
                </div>
                <h3 style="font-size:1.08rem;margin-bottom:0.3rem;line-height:1.4;"><?= htmlspecialchars($note['title']) ?></h3>
                <p style="color:var(--text-secondary);font-size:0.85rem;margin-bottom:1rem;">
                    <?= htmlspecialchars($note['field'] ?: $note['subject']) ?>
                </p>
                <div class="popularity-bar"><div class="popularity-fill" data-width="<?= $pop ?>"></div></div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1.2rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.06);">
                    <div style="display:flex;gap:1.2rem;">
                        <span style="color:var(--text-secondary);font-size:0.82rem;display:flex;align-items:center;gap:0.3rem;">
                            <i class="fa-solid fa-download" style="color:var(--accent-cyan);"></i>
                            <?= $note['downloads'] >= 1000 ? round($note['downloads']/1000,1).'k' : (int)$note['downloads'] ?>
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.3rem;color:#ffd700;font-size:0.9rem;font-weight:600;">
                        <i class="fa-solid fa-star"></i> <?= number_format((float)$note['avg_rating'],1) ?>
                    </div>
                </div>
                <!-- Action buttons -->
                <div style="margin-top:1rem;display:flex;gap:0.7rem;">
                    <a href="view_note.php?id=<?= $note['id'] ?>" class="btn" style="flex:1;padding:0.6rem;font-size:0.85rem;border-radius:10px;text-align:center;background:rgba(0,243,255,0.08);border:1px solid rgba(0,243,255,0.2);color:var(--accent-cyan);text-decoration:none;">
                        <i class="fa-solid fa-eye"></i> View
                    </a>
                    <?php if($is_logged_in): ?>
                    <a href="download.php?id=<?= $note['id'] ?>" class="btn btn-primary" style="flex:1;padding:0.6rem;font-size:0.85rem;border-radius:10px;text-align:center;text-decoration:none;">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                    <?php else: ?>
                    <button onclick="showLoginModal()" class="btn btn-primary" style="flex:1;padding:0.6rem;font-size:0.85rem;border-radius:10px;">
                        <i class="fa-solid fa-download"></i> Download
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <?php if(isset($guest_limit_reached) && $guest_limit_reached): ?>
        <div class="glass-panel animate-fade-in-up" style="margin-top:3rem; padding:3rem; text-align:center; border:1px dashed var(--accent-cyan);">
            <div style="font-size:2rem; margin-bottom:1rem;">🚀</div>
            <h3 style="margin-bottom:0.5rem;">Unlock 8,000+ More Notes</h3>
            <p style="color:var(--text-secondary); margin-bottom:2rem; max-width:500px; margin-left:auto; margin-right:auto;">
                You're viewing a limited selection of notes. Create a free account to access our full library, download materials, and save your favorites!
            </p>
            <a href="../auth/register.php" class="btn btn-primary" style="padding:0.8rem 2rem; border-radius:12px;">Get Full Access — It's Free</a>
        </div>
        <?php endif; ?>

        <!-- Empty state (hidden by default) -->
        <div id="empty-state" style="display:none; text-align:center; padding:5rem 2rem;">
            <i class="fa-solid fa-magnifying-glass" style="font-size:3rem; color:rgba(255,255,255,0.1); margin-bottom:1rem;"></i>
            <h3 style="color:var(--text-secondary); font-weight:400;">No notes found for this filter.</h3>
            <p style="color:rgba(255,255,255,0.3); margin-top:0.5rem;">Try a different branch or semester.</p>
        </div>

    </div>
</main>

<!-- Login Gate Modal (shown when guest tries to download) -->
<div id="login-gate-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.8);backdrop-filter:blur(8px);z-index:999999;align-items:center;justify-content:center;">
  <div class="glass-panel" style="max-width:420px;width:90%;padding:2.5rem;border-radius:24px;text-align:center;animation:fadeInUp 0.4s ease;">
    <div style="font-size:2.5rem;margin-bottom:1rem;">🔒</div>
    <h2 style="margin-bottom:0.5rem;">Login Required</h2>
    <p style="color:var(--text-secondary);margin-bottom:2rem;">You need to sign in to download notes. It's free and takes only a moment!</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="../auth/login.php" class="btn btn-primary magnetic-btn" style="padding:0.8rem 1.8rem;">Sign In</a>
      <a href="../auth/register.php" class="btn" style="padding:0.8rem 1.8rem;background:rgba(255,255,255,0.06);border:1px solid var(--glass-border);border-radius:12px;">Create Account</a>
    </div>
    <button onclick="document.getElementById('login-gate-modal').style.display='none'"
            style="margin-top:1.5rem;background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:0.85rem;">✕ Close</button>
  </div>
</div>

<script>
function showLoginModal(){
  document.getElementById('login-gate-modal').style.display='flex';
}
document.getElementById('login-gate-modal').addEventListener('click',function(e){
  if(e.target===this) this.style.display='none';
});

/* ============================================================
   EXPLORE PAGE SCRIPTS — Full functionality
   ============================================================ */

// 1. Navbar scroll solidify
const navbar = document.getElementById('explore-navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 40);
}, { passive: true });

// 2. Animated counters
document.querySelectorAll('[data-counter]').forEach(el => {
    const target = +el.getAttribute('data-counter');
    const step   = target / (1400 / 16);
    let curr = 0;
    const timer = setInterval(() => {
        curr += step;
        if (curr >= target) { curr = target; clearInterval(timer); }
        el.textContent = Math.floor(curr).toLocaleString();
    }, 16);
});

// 3. IntersectionObserver — scroll-reveal + populate popularity bars
const revealEls = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            const fill = entry.target.querySelector('.popularity-fill');
            if (fill) {
                setTimeout(() => {
                    fill.style.width = fill.getAttribute('data-width') + '%';
                }, 200);
            }
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
revealEls.forEach(el => revealObserver.observe(el));

// 4. 3D Tilt effect on explore cards
document.querySelectorAll('.explore-card').forEach(card => {
    card.addEventListener('mousemove', e => {
        const r  = card.getBoundingClientRect();
        const x  = ((e.clientX - r.left) / r.width  - 0.5) * 18;
        const y  = ((e.clientY - r.top)  / r.height - 0.5) * -18;
        card.style.transform = `perspective(900px) rotateX(${y}deg) rotateY(${x}deg) translateZ(6px)`;
        card.style.transition = 'transform 0.08s ease';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(900px) rotateX(0) rotateY(0) translateZ(0)';
        card.style.transition = 'transform 0.6s cubic-bezier(0.16,1,0.3,1)';
    });
});

// 5. Save bookmark — AJAX toggle to DB
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `@keyframes rippleAnim{to{transform:scale(3);opacity:0;}}`;
document.head.appendChild(rippleStyle);

document.querySelectorAll('.save-btn').forEach(btn => {
    btn.addEventListener('click', e => {
        e.stopPropagation();
        const noteId = btn.getAttribute('data-id');
        const icon = btn.querySelector('i');
        
        <?php if($is_logged_in): ?>
        // AJAX save/unsave
        fetch('../ajax/save_note.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'note_id=' + noteId
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                if (data.saved) {
                    icon.className = 'fa-solid fa-bookmark';
                    btn.style.color = 'var(--accent-cyan)';
                    btn.title = 'Unsave note';
                } else {
                    icon.className = 'fa-regular fa-bookmark';
                    btn.style.color = 'rgba(255,255,255,0.3)';
                    btn.title = 'Save note';
                }
                // Ripple animation
                const ripple = document.createElement('span');
                ripple.style.cssText = `position:absolute;width:30px;height:30px;background:rgba(0,243,255,0.3);border-radius:50%;transform:scale(0);animation:rippleAnim 0.5s ease forwards;pointer-events:none;top:50%;left:50%;margin:-15px;`;
                btn.style.position = 'relative';
                btn.appendChild(ripple);
                setTimeout(() => ripple.remove(), 500);
            }
        });
        <?php else: ?>
        showLoginModal();
        <?php endif; ?>
    });
});

// 6. Filter logic — Branch + Search
const grid        = document.getElementById('explore-grid');
const emptyState  = document.getElementById('empty-state');
const countNum    = document.getElementById('count-num');
let activeBranch  = 'all';
let searchQuery   = '';

function applyFilters() {
    const cards = grid.querySelectorAll('.explore-card');
    let visible  = 0;
    grid.style.opacity = '0';

    setTimeout(() => {
        cards.forEach(card => {
            const branch  = card.getAttribute('data-branch').toLowerCase();
            const title   = card.querySelector('h3').textContent.toLowerCase();
            const tags    = [...card.querySelectorAll('.tag-pill')].map(t => t.textContent.toLowerCase()).join(' ');

            const matchBranch = activeBranch === 'all' || branch.toLowerCase().includes(activeBranch.toLowerCase());
            const matchSearch = searchQuery  === ''    || title.includes(searchQuery) || tags.includes(searchQuery);

            if (matchBranch && matchSearch) {
                card.style.display = '';
                card.classList.remove('visible');
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    card.classList.add('visible');
                    const fill = card.querySelector('.popularity-fill');
                    if (fill) fill.style.width = fill.getAttribute('data-width') + '%';
                }));
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        countNum.textContent = visible;
        emptyState.style.display = visible === 0 ? 'block' : 'none';
        grid.style.opacity = '1';
    }, 280);
}

// Branch chips
document.querySelectorAll('#branch-chips .chip').forEach(chip => {
    chip.addEventListener('click', function() {
        document.querySelectorAll('#branch-chips .chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        activeBranch = this.getAttribute('data-filter');
        applyFilters();
    });
});

// 7. AJAX Live search with clickable suggestions
const searchInput    = document.getElementById('explore-search');
const searchDropdown = document.getElementById('explore-dropdown');
let searchTimer;

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    searchQuery = this.value.trim().toLowerCase();

    if (searchQuery.length > 0) {
        searchDropdown.classList.add('active');
        // Debounced AJAX search
        searchTimer = setTimeout(() => {
            fetch('../ajax/live_search_ui.php?q=' + encodeURIComponent(searchQuery))
                .then(r => r.json())
                .then(data => {
                    const rc = searchDropdown.querySelector('.results');
                    if (data.length > 0) {
                        rc.innerHTML = data.map(item => `
                            <a href="view_note.php?id=${item.id}" style="display:block;padding:12px 16px;border-bottom:1px solid var(--glass-border);cursor:pointer;transition:background 0.2s;text-decoration:none;color:inherit;"
                               onmouseover="this.style.background='rgba(0,243,255,0.06)'" onmouseout="this.style.background='transparent'">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <div style="color:var(--text-primary);font-size:0.92rem;font-weight:500;">${item.title}</div>
                                        <div style="font-size:0.78rem;color:var(--text-secondary);margin-top:2px;">${item.field || item.subject || ''}</div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:0.8rem;flex-shrink:0;">
                                        <span style="color:#ffd700;font-size:0.8rem;"><i class="fa-solid fa-star"></i> ${item.rating}</span>
                                    </div>
                                </div>
                            </a>
                        `).join('');
                    } else {
                        rc.innerHTML = `<div style="padding:16px;color:var(--text-secondary);font-size:0.85rem;text-align:center;">No results for "<b>${searchQuery}</b>"</div>`;
                    }
                })
                .catch(() => {});
            // Also filter grid
            applyFilters();
        }, 250);
    } else {
        searchDropdown.classList.remove('active');
        applyFilters();
    }
});

searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        searchDropdown.classList.remove('active');
        applyFilters();
    }
});

document.addEventListener('click', e => {
    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
        searchDropdown.classList.remove('active');
    }
});

// Pre-fill search from URL query param
const urlQ = new URLSearchParams(window.location.search).get('q');
if (urlQ) {
    searchInput.value = urlQ;
    searchQuery = urlQ.toLowerCase();
    setTimeout(applyFilters, 500);
}

// 8. Sort — Real client-side sorting of cards
document.getElementById('sort-select').addEventListener('change', function() {
    const sortBy = this.value;
    const cards = [...grid.querySelectorAll('.explore-card')];
    
    grid.style.opacity = '0';
    grid.style.transition = 'opacity 0.3s ease';
    
    setTimeout(() => {
        cards.sort((a, b) => {
            switch(sortBy) {
                case 'popular':
                    return (+b.dataset.downloads) - (+a.dataset.downloads);
                case 'rating':
                    return (+b.dataset.rating) - (+a.dataset.rating);
                case 'newest':
                    return (b.dataset.date || '').localeCompare(a.dataset.date || '');
                case 'oldest':
                    return (a.dataset.date || '').localeCompare(b.dataset.date || '');
                case 'az':
                    return (a.dataset.title || '').localeCompare(b.dataset.title || '');
                case 'za':
                    return (b.dataset.title || '').localeCompare(a.dataset.title || '');
                default:
                    return 0;
            }
        });
        
        // Re-append sorted cards with staggered animation
        cards.forEach((card, i) => {
            grid.appendChild(card);
            card.classList.remove('visible');
            setTimeout(() => {
                card.classList.add('visible');
                const fill = card.querySelector('.popularity-fill');
                if (fill) fill.style.width = fill.getAttribute('data-width') + '%';
            }, 50 * i);
        });
        
        grid.style.opacity = '1';
    }, 300);
});

// 9. Grid / List view toggle
const gridBtn = document.getElementById('view-grid');
const listBtn = document.getElementById('view-list');

function setActiveViewBtn(active, inactive) {
    active.style.background = 'rgba(0,243,255,0.1)';
    active.style.borderColor = 'rgba(0,243,255,0.3)';
    active.style.color = 'var(--accent-cyan)';
    inactive.style.background = 'rgba(255,255,255,0.04)';
    inactive.style.borderColor = 'rgba(255,255,255,0.1)';
    inactive.style.color = 'var(--text-secondary)';
}

listBtn.addEventListener('click', function() {
    grid.classList.add('list-view');
    setActiveViewBtn(this, gridBtn);
});
gridBtn.addEventListener('click', function() {
    grid.classList.remove('list-view');
    setActiveViewBtn(this, listBtn);
});
</script>

<?php include '../includes/footer.php'; ?>
