<?php 
$level = '../'; 
$page_title = 'Notifications';
include '../includes/header.php'; 
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
            <div>
                <h2 class="animate-fade-in-up">Notifications</h2>
                <p class="animate-fade-in-up delay-100" style="color: var(--text-secondary); margin-top: 0.2rem;">Stay updated with your study network</p>
            </div>
            <button class="btn btn-outline animate-fade-in-up delay-100" style="font-size: 0.85rem;">
                <i class="fa-solid fa-check-double"></i> Mark all as read
            </button>
        </div>

        <div class="filter-chips animate-fade-in-up delay-100" style="margin-bottom: 2rem;" id="notif-filters">
            <div class="chip active" data-category="all">All</div>
            <div class="chip" data-category="system">System</div>
            <div class="chip" data-category="uploads">Uploads</div>
            <div class="chip" data-category="interactions">Interactions</div>
        </div>

        <div class="animate-fade-in-up delay-200" style="display: flex; flex-direction: column; gap: 1rem;" id="notif-container">
            
            <!-- Notif 1 -->
            <div class="glass-panel notif-item" data-category="uploads" style="padding: 1.5rem; display: flex; gap: 1.5rem; align-items: center; border-left: 4px solid var(--accent-cyan);">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(0, 243, 255, 0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--accent-cyan); flex-shrink: 0;">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h4 style="margin-bottom: 0.3rem;">Note Approved!</h4>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">10 mins ago</span>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Your upload <b>"Advanced Quantum Physics"</b> has been reviewed and approved by the moderation team.</p>
                </div>
                <div style="width: 10px; height: 10px; background: var(--accent-cyan); border-radius: 50%; box-shadow: 0 0 10px var(--accent-cyan);"></div>
            </div>

            <!-- Notif 2 -->
            <div class="glass-panel notif-item" data-category="interactions" style="padding: 1.5rem; display: flex; gap: 1.5rem; align-items: center; opacity: 0.8;">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(138, 43, 226, 0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--accent-violet); flex-shrink: 0;">
                    <i class="fa-solid fa-comment"></i>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h4 style="margin-bottom: 0.3rem;">New Comment</h4>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">2 hours ago</span>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;"><b>Rahul Sharma</b> commented on your note: <i>"This helped me clear my finals, thank you so much!"</i></p>
                </div>
            </div>

            <!-- Notif 3 -->
            <div class="glass-panel notif-item" data-category="system" style="padding: 1.5rem; display: flex; gap: 1.5rem; align-items: center; opacity: 0.8;">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255, 0, 255, 0.1); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--accent-magenta); flex-shrink: 0;">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <h4 style="margin-bottom: 0.3rem;">Achievement Unlocked</h4>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Yesterday</span>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Congratulations! You've reached the <b>"Helper Spirit"</b> tier for uploading 10+ approved notes.</p>
                </div>
            </div>

        </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chips = document.querySelectorAll('#notif-filters .chip');
    const items = document.querySelectorAll('.notif-item');
    const container = document.getElementById('notif-container');

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            // Update active chip
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');

            const category = chip.getAttribute('data-category');

            // Animate transition
            container.style.opacity = '0';
            container.style.transform = 'translateY(10px)';

            setTimeout(() => {
                items.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 300);
        });
    });
});
</script>

        <div style="text-align: center; margin-top: 3rem;">
            <button class="btn btn-outline" style="color: var(--text-secondary); border-color: var(--glass-border);">Load older notifications</button>
        </div>

    </main>
</div>

<?php include '../includes/footer.php'; ?>
