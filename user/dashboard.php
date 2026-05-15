<?php 
$level = '../'; 
$page_title = 'User Dashboard';
include '../includes/config.php';

// Login guard
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}


// Anti-cache headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$uid = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'average';
$first_name = $_SESSION['first_name'] ?? $username;

// Real DB stats — prepared statements
$uploads_count = 0;
$downloads_count = 0;
$avg_rating = 0;
$saved_count = 0;

$r = $conn->prepare("SELECT COUNT(*) as c FROM notes WHERE user_id = ?");
$r->bind_param("i", $uid); $r->execute();
$uploads_count = $r->get_result()->fetch_assoc()['c'];

$r = $conn->prepare("SELECT COALESCE(SUM(downloads),0) as c FROM notes WHERE user_id = ?");
$r->bind_param("i", $uid); $r->execute();
$downloads_count = $r->get_result()->fetch_assoc()['c'];

$r = $conn->prepare("SELECT COALESCE(AVG(avg_rating),0) as c FROM notes WHERE user_id = ? AND avg_rating > 0");
$r->bind_param("i", $uid); $r->execute();
$avg_rating = round($r->get_result()->fetch_assoc()['c'], 1);

$r = $conn->prepare("SELECT COUNT(*) as c FROM saved_notes WHERE user_id = ?");
$r->bind_param("i", $uid); $r->execute();
$saved_count = $r->get_result()->fetch_assoc()['c'];

// Recent uploads
$r_recent = $conn->prepare("SELECT id, title, field, semester, status, downloads, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$r_recent->bind_param("i", $uid);
$r_recent->execute();
$recent = $r_recent->get_result();

// Welcome message
$welcome = isset($_GET['welcome']) ? true : false;

include '../includes/header.php'; 
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content" style="position:relative;overflow:hidden;">
        
        <!-- Animated Wave Background -->
        <div class="wave-bg-container">
            <svg class="wave-svg wave-1" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="var(--accent-cyan)" fill-opacity="0.06" d="M0,96L48,112C96,128,192,160,288,176C384,192,480,192,576,170.7C672,149,768,107,864,96C960,85,1056,107,1152,133.3C1248,160,1344,192,1392,208L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
            </svg>
            <svg class="wave-svg wave-2" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="var(--accent-violet)" fill-opacity="0.04" d="M0,160L48,170.7C96,181,192,203,288,197.3C384,192,480,160,576,154.7C672,149,768,171,864,186.7C960,203,1056,213,1152,197.3C1248,181,1344,139,1392,117.3L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
            </svg>
        </div>

        <?php if($welcome): ?>
        <div class="animate-fade-in-up" style="background:rgba(0,255,136,0.08);border:1px solid rgba(0,255,136,0.3);color:#00ff88;padding:1rem 1.5rem;border-radius:14px;margin-bottom:2rem;font-size:0.95rem;display:flex;align-items:center;gap:0.8rem;">
            <i class="fa-solid fa-party-horn" style="font-size:1.3rem;"></i>
            <span>Welcome to NotesPlatform, <strong><?= htmlspecialchars($first_name) ?></strong>! Start by uploading your first note or explore the library.</span>
        </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="dash-header animate-fade-in-up">
            <div>
                <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:0.3rem;">
                    <?= date('l, d M Y') ?>
                </p>
                <h1 style="font-size:2.2rem;margin-bottom:0.3rem;">
                    Welcome back, <span class="text-gradient"><?= htmlspecialchars($first_name) ?></span>
                </h1>
                <p style="color:var(--text-secondary);font-size:1rem;">Here's what's happening with your notes today.</p>
            </div>
            <div class="glass-panel animate-fade-in-up" style="padding: 0.5rem 1.2rem; border-radius: 50px; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-violet)); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1rem;">
                    <?= strtoupper(substr($username, 0, 1)) ?>
                </div>
                <span style="font-weight: 500;"><?= htmlspecialchars($username) ?></span>
            </div>
        </div>

        <!-- Stats Cards with Wave Borders -->
        <div class="dash-stats-grid animate-fade-in-up delay-100">
            
            <div class="dash-stat-card stat-cyan">
                <div class="stat-wave"></div>
                <div class="stat-icon" style="background:rgba(0,243,255,0.1);color:var(--accent-cyan);">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $uploads_count ?></h3>
                    <p>Notes Uploaded</p>
                </div>
            </div>

            <div class="dash-stat-card stat-violet">
                <div class="stat-wave"></div>
                <div class="stat-icon" style="background:rgba(138,43,226,0.1);color:var(--accent-violet);">
                    <i class="fa-solid fa-download"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($downloads_count) ?></h3>
                    <p>Total Downloads</p>
                </div>
            </div>

            <div class="dash-stat-card stat-magenta">
                <div class="stat-wave"></div>
                <div class="stat-icon" style="background:rgba(255,0,255,0.1);color:var(--accent-magenta);">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $avg_rating ?: '—' ?></h3>
                    <p>Average Rating</p>
                </div>
            </div>

            <div class="dash-stat-card stat-green">
                <div class="stat-wave"></div>
                <div class="stat-icon" style="background:rgba(0,255,136,0.1);color:#00ff88;">
                    <i class="fa-solid fa-bookmark"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $saved_count ?></h3>
                    <p>Saved Notes</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="animate-fade-in-up delay-200" style="display:flex;gap:1rem;margin-bottom:2.5rem;flex-wrap:wrap;">
            <a href="upload.php" class="dash-action-btn" style="--ac:var(--accent-cyan);">
                <i class="fa-solid fa-plus"></i> Upload Note
            </a>
            <a href="my_notes.php" class="dash-action-btn" style="--ac:var(--accent-violet);">
                <i class="fa-solid fa-book-open"></i> My Notes
            </a>
            <a href="top_rated.php" class="dash-action-btn" style="--ac:var(--accent-magenta);">
                <i class="fa-solid fa-ranking-star"></i> Top Rated
            </a>
            <a href="saved.php" class="dash-action-btn" style="--ac:#00ff88;">
                <i class="fa-solid fa-bookmark"></i> Saved
            </a>
        </div>

        <!-- Recent Uploads -->
        <div class="glass-panel animate-fade-in-up delay-200" style="padding: 2rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                <h3>Recent Uploads</h3>
                <a href="my_notes.php" style="color:var(--accent-cyan);text-decoration:none;font-size:0.85rem;">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            
            <?php if ($recent && $recent->num_rows > 0): ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 1rem; color: var(--text-secondary); font-weight: 500;">Note Title</th>
                    <th style="padding: 1rem; color: var(--text-secondary); font-weight: 500;">Field</th>
                    <th style="padding: 1rem; color: var(--text-secondary); font-weight: 500;">Status</th>
                    <th style="padding: 1rem; color: var(--text-secondary); font-weight: 500;">Downloads</th>
                    <th style="padding: 1rem; color: var(--text-secondary); font-weight: 500;"></th>
                </tr>
                <?php while($row = $recent->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition:background 0.3s;" onmouseover="this.style.background='rgba(0,243,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; font-weight: 500;"><?= htmlspecialchars($row['title']) ?></td>
                    <td style="padding: 1rem; color: var(--text-secondary);"><?= htmlspecialchars($row['field'] ?? '') ?></td>
                    <td style="padding: 1rem;">
                        <?php if($row['status']==='approved'): ?>
                        <span style="color: #00ff88; background: rgba(0,255,136,0.1); padding: 0.3rem 0.6rem; border-radius: 12px; font-size: 0.8rem;">Approved</span>
                        <?php elseif($row['status']==='pending'): ?>
                        <span style="color: #ffaa00; background: rgba(255,170,0,0.1); padding: 0.3rem 0.6rem; border-radius: 12px; font-size: 0.8rem;">Pending</span>
                        <?php else: ?>
                        <span style="color: #ff3366; background: rgba(255,51,102,0.1); padding: 0.3rem 0.6rem; border-radius: 12px; font-size: 0.8rem;">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 1rem;"><?= $row['status']==='approved' ? (int)$row['downloads'] : '—' ?></td>
                    <td style="padding: 1rem;">
                        <?php if($row['status']==='approved'): ?>
                        <a href="view_note.php?id=<?= $row['id'] ?>" style="color:var(--accent-cyan);text-decoration:none;font-size:0.85rem;"><i class="fa-solid fa-eye"></i> View</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <div style="text-align:center;padding:3rem;color:var(--text-secondary);">
                <i class="fa-solid fa-cloud-arrow-up" style="font-size:2.5rem;margin-bottom:1rem;display:block;opacity:0.3;"></i>
                <p>No uploads yet. <a href="upload.php" style="color:var(--accent-cyan);">Upload your first note!</a></p>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<style>
/* ── Dashboard Wave Background ── */
.wave-bg-container {
    position:absolute;top:0;left:0;right:0;height:350px;pointer-events:none;overflow:hidden;z-index:0;
}
.wave-svg {
    position:absolute;top:0;left:0;width:100%;height:350px;
}
.wave-1 { animation: waveSlide1 12s ease-in-out infinite; }
.wave-2 { animation: waveSlide2 15s ease-in-out infinite; }
@keyframes waveSlide1 { 0%,100%{transform:translateX(0)} 50%{transform:translateX(-30px)} }
@keyframes waveSlide2 { 0%,100%{transform:translateX(0)} 50%{transform:translateX(25px)} }

/* ── Dashboard Header ── */
.dash-header {
    display:flex;justify-content:space-between;align-items:center;margin-bottom:2.5rem;
    position:relative;z-index:1;
}

/* ── Stats Grid ── */
.dash-stats-grid {
    display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;margin-bottom:2.5rem;
    position:relative;z-index:1;
}
.dash-stat-card {
    background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:18px;
    padding:1.8rem;display:flex;align-items:center;gap:1.5rem;
    position:relative;overflow:hidden;transition:all 0.4s cubic-bezier(0.16,1,0.3,1);
    backdrop-filter:blur(16px);
}
.dash-stat-card:hover {
    transform:translateY(-4px);box-shadow:0 12px 30px rgba(0,0,0,0.3);
    border-color:rgba(0,243,255,0.2);
}
.dash-stat-card .stat-wave {
    position:absolute;bottom:-10px;left:0;right:0;height:50px;
    opacity:0.12;pointer-events:none;
}
.stat-cyan .stat-wave { background:linear-gradient(0deg,var(--accent-cyan),transparent); animation:waveFloat 4s ease-in-out infinite; }
.stat-violet .stat-wave { background:linear-gradient(0deg,var(--accent-violet),transparent); animation:waveFloat 5s ease-in-out infinite 0.5s; }
.stat-magenta .stat-wave { background:linear-gradient(0deg,var(--accent-magenta),transparent); animation:waveFloat 4.5s ease-in-out infinite 1s; }
.stat-green .stat-wave { background:linear-gradient(0deg,#00ff88,transparent); animation:waveFloat 5.5s ease-in-out infinite 1.5s; }

@keyframes waveFloat {
    0%,100%{transform:translateY(0) scaleY(1)} 50%{transform:translateY(-8px) scaleY(1.3)}
}

.stat-icon {
    width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;
    font-size:1.6rem;flex-shrink:0;transition:transform 0.3s;
}
.dash-stat-card:hover .stat-icon { transform:scale(1.1); }
.stat-info h3 { font-size:2rem;margin-bottom:0; }
.stat-info p { color:var(--text-secondary);margin:0;font-size:0.88rem; }

/* ── Quick Action Buttons ── */
.dash-action-btn {
    display:inline-flex;align-items:center;gap:0.6rem;padding:0.7rem 1.3rem;
    background:rgba(255,255,255,0.03);border:1px solid var(--glass-border);
    border-radius:12px;color:var(--text-secondary);text-decoration:none;
    font-size:0.9rem;font-weight:500;transition:all 0.3s ease;
    position:relative;z-index:1;
}
.dash-action-btn:hover {
    color:var(--ac);border-color:var(--ac);background:rgba(0,243,255,0.04);
    box-shadow:0 0 15px rgba(0,243,255,0.08);transform:translateY(-2px);
}
.dash-action-btn i { color:var(--ac);font-size:0.95rem; }
</style>

<?php include '../includes/footer.php'; ?>
