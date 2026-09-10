<?php
require_once __DIR__ . '/../config/session.php';
requireRole(['volunteer']);

$pageTitle = 'Volunteer dashboard';
$pageDescription = 'SAGIPBRO volunteer coordination dashboard.';
$basePath = '../';
$isAdmin = true;
$activeAdmin = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="admin-shell">
    <?php require __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php require __DIR__ . '/../includes/navbar.php'; ?>
        <main id="main-content" class="admin-content">
            <section class="dashboard-hero" aria-labelledby="volunteer-title">
                <div><h1 id="volunteer-title">Volunteer operations</h1><p>View today's assignments, record relief work, and stay informed about active barangay response operations.</p></div>
                <div class="dashboard-date"><i class="bi bi-calendar-check"></i><span><strong><?= htmlspecialchars(date('l, F j'), ENT_QUOTES, 'UTF-8') ?></strong><span data-live-time><?= htmlspecialchars(date('g:i A'), ENT_QUOTES, 'UTF-8') ?></span></span></div>
            </section>
            <section class="stat-grid" aria-label="Volunteer overview">
                <article class="stat-card"><div class="stat-card-top"><span class="stat-card-label">Today's assignment</span><span class="stat-card-icon"><i class="bi bi-clipboard2-check"></i></span></div><strong class="stat-value">2</strong><span class="stat-meta"><span class="trend-up">Next at 1:30 PM</span></span></article>
                <article class="stat-card info"><div class="stat-card-top"><span class="stat-card-label">Active centers</span><span class="stat-card-icon"><i class="bi bi-buildings"></i></span></div><strong class="stat-value">3</strong><span class="stat-meta">Across the One Bonuan area</span></article>
                <article class="stat-card"><div class="stat-card-top"><span class="stat-card-label">Households assisted</span><span class="stat-card-icon"><i class="bi bi-house-heart"></i></span></div><strong class="stat-value">54</strong><span class="stat-meta">During your current shift</span></article>
                <article class="stat-card warning"><div class="stat-card-top"><span class="stat-card-label">Shift hours</span><span class="stat-card-icon"><i class="bi bi-clock"></i></span></div><strong class="stat-value">4.5</strong><span class="stat-meta">Started at 7:30 AM</span></article>
            </section>
            <div class="dashboard-grid">
                <section class="data-card" aria-labelledby="assignments-title">
                    <div class="data-card-header"><div><h2 id="assignments-title">Today's assignments</h2><p>Thursday, September 10</p></div><span class="status-badge status-success">On duty</span></div>
                    <div class="table-responsive"><table class="table app-table"><caption class="visually-hidden">Volunteer assignments</caption><thead><tr><th>Time</th><th>Assignment</th><th>Location</th><th>Coordinator</th><th>Status</th></tr></thead><tbody>
                        <tr><td>8:00 AM</td><td><span class="table-primary-text">Relief pack preparation</span><span class="table-secondary-text">Packing team B</span></td><td>Barangay operations center</td><td>Joel Mendoza</td><td><span class="status-badge status-success">Completed</span></td></tr>
                        <tr><td>1:30 PM</td><td><span class="table-primary-text">Household distribution</span><span class="table-secondary-text">Purok 2 route</span></td><td>Palatong area</td><td>Anna Flores</td><td><span class="status-badge status-info">Upcoming</span></td></tr>
                        <tr><td>4:00 PM</td><td><span class="table-primary-text">Inventory count</span><span class="table-secondary-text">Medical and hygiene</span></td><td>Relief stockroom</td><td>Carlo Reyes</td><td><span class="status-badge status-neutral">Scheduled</span></td></tr>
                    </tbody></table></div>
                </section>
                <div class="dashboard-stack">
                    <section class="data-card" aria-labelledby="quick-action-title"><div class="data-card-header"><h2 id="quick-action-title">Quick actions</h2></div><div class="data-card-body d-grid gap-2"><a class="btn btn-brand justify-content-start" href="../pages/distribution/index.php"><i class="bi bi-plus-circle"></i> Record distribution</a><a class="btn btn-brand-soft justify-content-start" href="../evacuation-centers.php"><i class="bi bi-person-check"></i> View center information</a><a class="btn btn-outline-brand justify-content-start" href="../resources.php"><i class="bi bi-box-seam"></i> Check public resource view</a></div></section>
                    <section class="data-card warning-card" aria-labelledby="volunteer-advisory"><div class="data-card-header"><h2 id="volunteer-advisory"><i class="bi bi-megaphone-fill me-1"></i> Team advisory</h2></div><div class="data-card-body"><p class="mb-2" style="font-size:.72rem">Bring your volunteer ID and rain protection for the afternoon distribution route. Check in with the coordinator before deployment.</p><span class="status-badge status-warning">Updated 30 min ago</span></div></section>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
