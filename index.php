<?php 
$level = './'; 
$page_title = 'Explore Notes';
include 'includes/header.php'; 

// If user is logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: user/dashboard.php');
    exit();
}

// Guest view: show only top 6 best-rated approved notes from DB
$top_notes = [];
$q = $conn->query("
    SELECT n.id, n.title, n.field, n.semester, n.subject, n.downloads, n.avg_rating,
           u.username, u.first_name, u.fname
    FROM notes n
    LEFT JOIN users u ON n.user_id = u.id
    WHERE n.status = 'approved'
    ORDER BY n.avg_rating DESC, n.downloads DESC
    LIMIT 6
");
if ($q) while($r = $q->fetch_assoc()) $top_notes[] = $r;
?>

<?php include 'includes/navbar.php'; ?>

<main class="main-content">
    <div class="container">
        
        <!-- Hero Section -->
        <section style="text-align: center; margin-bottom: 4rem; padding-top: 2rem;">
            <h1 class="animate-fade-in-up text-gradient" style="font-size: 3.5rem; margin-bottom: 1rem;">Elevate Your Learning</h1>
            <p class="animate-fade-in-up delay-100" style="color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 2rem;">
                Discover, share, and collaborate on ultra-premium study materials tailored for top-tier academic success.
            </p>
            
            <?php include 'includes/search_bar.php'; ?>
        </section>

        <!-- Trending Notes Section — Real DB data -->
        <section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;" class="animate-fade-in-up">
                <h2>Top Rated Notes</h2>
                <a href="user/top_rated.php" style="color: var(--accent-cyan); text-decoration: none; font-size: 0.9rem;">View All <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            
            <?php if (empty($top_notes)): ?>
            <div class="glass-panel animate-fade-in-up" style="padding:4rem;text-align:center;">
                <i class="fa-solid fa-inbox" style="font-size:3rem;color:rgba(255,255,255,0.1);margin-bottom:1rem;display:block;"></i>
                <h3 style="margin-bottom:0.5rem;">No Notes Available Yet</h3>
                <p style="color:var(--text-secondary);">Be the first to contribute! <a href="auth/register.php" style="color:var(--accent-cyan);">Create an account</a> and upload your notes.</p>
            </div>
            <?php else: ?>
            <div class="notes-grid">
                <?php foreach($top_notes as $note):
                    $note_id = $note['id'];
                    $note_title = htmlspecialchars($note['title']);
                    $note_subject = htmlspecialchars($note['field'] ?: $note['subject']);
                    $note_rating = number_format((float)$note['avg_rating'], 1);
                    $note_downloads = $note['downloads'] >= 1000 ? round($note['downloads']/1000,1).'k' : (int)$note['downloads'];
                    include 'includes/note_card.php';
                endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Guest CTA Section -->
        <section class="animate-fade-in-up" style="text-align:center;padding:4rem 2rem;margin-top:3rem;">
            <div class="glass-panel" style="padding:3rem;max-width:700px;margin:0 auto;border-color:rgba(0,243,255,0.15);">
                <h2 style="margin-bottom:1rem;"><span class="text-gradient">Want Full Access?</span></h2>
                <p style="color:var(--text-secondary);margin-bottom:2rem;font-size:1.05rem;">Create a free account to browse all notes, download PDFs, upload your own materials, and rate notes from other students.</p>
                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                    <a href="auth/register.php" class="btn btn-primary magnetic-btn" style="padding:0.9rem 2rem;font-size:1.05rem;">
                        <i class="fa-solid fa-user-plus"></i> Create Free Account
                    </a>
                    <a href="auth/login.php" class="btn btn-outline magnetic-btn" style="padding:0.9rem 2rem;font-size:1.05rem;">
                        <i class="fa-solid fa-right-to-bracket"></i> Sign In
                    </a>
                </div>
            </div>
        </section>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
