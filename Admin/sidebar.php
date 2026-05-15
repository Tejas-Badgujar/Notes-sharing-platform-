<?php
/**
 * Admin Sidebar Include — used across all admin pages
 */
$level = isset($level) ? $level : '../';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar animate-fade-in-up">
    <a href="../index.php" class="logo">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Admin</span>Panel
    </a>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i> Overview
            </a>
        </li>
        <li>
            <a href="approve.php" class="<?= $current_page == 'approve.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-check-double"></i> Approvals
            </a>
        </li>
        <li>
            <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> Users
            </a>
        </li>
        <li>
            <a href="notes.php" class="<?= $current_page == 'notes.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-lines"></i> All Notes
            </a>
        </li>
        <li style="margin-top: auto;">
            <a href="../auth/logout.php" class="text-secondary">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</aside>
