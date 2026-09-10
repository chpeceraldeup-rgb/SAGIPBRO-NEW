<?php
require_once __DIR__ . '/../config/session.php';
requireRole(['admin', 'official']);

$pageTitle = 'Dashboard';
$pageDescription = 'SAGIPBRO administration dashboard for Barangay Binloc disaster relief operations.';
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
            <?php require __DIR__ . '/../includes/alerts.php'; ?>

            <section class="dashboard-hero" aria-labelledby="dashboard-title">
                <div>
                    <h1 id="dashboard-title">Operations overview</h1>
                    <p>Monitor relief readiness, active centers, and the latest community response work across Bonuan Binloc.</p>
                </div>
                <div class="dashboard-date">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    <span><strong><?= htmlspecialchars(date('l, F j, Y'), ENT_QUOTES, 'UTF-8') ?></strong><span data-live-time><?= htmlspecialchars(date('g:i A'), ENT_QUOTES, 'UTF-8') ?></span></span>
                </div>
            </section>

            <section class="stat-grid" aria-label="Key operational totals">
                <article class="stat-card">
                    <div class="stat-card-top"><span class="stat-card-label">Total resources</span><span class="stat-card-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">2,846</strong>
                    <span class="stat-meta"><span class="trend-up"><i class="bi bi-arrow-up-short"></i> 8.4%</span> from last month</span>
                </article>
                <article class="stat-card warning">
                    <div class="stat-card-top"><span class="stat-card-label">Low-stock items</span><span class="stat-card-icon"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">6</strong>
                    <span class="stat-meta"><span class="trend-down">2 critical</span> require action</span>
                </article>
                <article class="stat-card info">
                    <div class="stat-card-top"><span class="stat-card-label">Evacuation centers</span><span class="stat-card-icon"><i class="bi bi-buildings" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">4</strong>
                    <span class="stat-meta"><span class="trend-up">3 open</span> · 1 on standby</span>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top"><span class="stat-card-label">Registered residents</span><span class="stat-card-icon"><i class="bi bi-people" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">11,326</strong>
                    <span class="stat-meta"><span class="trend-up"><i class="bi bi-check2"></i> 91%</span> profiles reviewed</span>
                </article>
                <article class="stat-card info">
                    <div class="stat-card-top"><span class="stat-card-label">Active volunteers</span><span class="stat-card-icon"><i class="bi bi-person-hearts" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">68</strong>
                    <span class="stat-meta"><span class="trend-up">42 available</span> for deployment</span>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top"><span class="stat-card-label">Recent distributions</span><span class="stat-card-icon"><i class="bi bi-truck" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">127</strong>
                    <span class="stat-meta"><span class="trend-up">1,032 packs</span> this month</span>
                </article>
            </section>

            <div class="dashboard-grid">
                <section class="data-card" aria-labelledby="activity-title">
                    <div class="data-card-header">
                        <div><h2 id="activity-title">Recent activity</h2><p>Latest updates from the operations team</p></div>
                        <a href="../pages/activity/index.php">View all activity <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table app-table">
                            <caption class="visually-hidden">Recent SAGIPBRO activity</caption>
                            <thead><tr><th scope="col">Activity</th><th scope="col">User</th><th scope="col">Module</th><th scope="col">Time</th></tr></thead>
                            <tbody>
                                <tr><td><div class="activity-cell"><span class="activity-icon"><i class="bi bi-box-arrow-in-down"></i></span><span><span class="table-primary-text">Added 120 family food packs</span><span class="table-secondary-text">Stock receipt #RCV-0924</span></span></div></td><td>Joel Mendoza</td><td><span class="status-badge status-success">Resources</span></td><td><time datetime="2026-09-10T09:42">12 min ago</time></td></tr>
                                <tr><td><div class="activity-cell"><span class="activity-icon info"><i class="bi bi-person-plus"></i></span><span><span class="table-primary-text">Registered 18 residents</span><span class="table-secondary-text">Purok 4 household intake</span></span></div></td><td>Maria Santos</td><td><span class="status-badge status-info">Residents</span></td><td><time datetime="2026-09-10T09:15">39 min ago</time></td></tr>
                                <tr><td><div class="activity-cell"><span class="activity-icon warning"><i class="bi bi-truck"></i></span><span><span class="table-primary-text">Completed relief distribution</span><span class="table-secondary-text">54 households · Purok 2</span></span></div></td><td>Anna Flores</td><td><span class="status-badge status-warning">Distribution</span></td><td><time datetime="2026-09-10T08:36">1 hr ago</time></td></tr>
                                <tr><td><div class="activity-cell"><span class="activity-icon"><i class="bi bi-megaphone"></i></span><span><span class="table-primary-text">Published preparedness advisory</span><span class="table-secondary-text">Visible on public website</span></span></div></td><td>Maria Santos</td><td><span class="status-badge status-success">Announcement</span></td><td><time datetime="2026-09-10T07:52">2 hrs ago</time></td></tr>
                                <tr><td><div class="activity-cell"><span class="activity-icon info"><i class="bi bi-building-check"></i></span><span><span class="table-primary-text">Updated center occupancy</span><span class="table-secondary-text">Bonuan multipurpose facility</span></span></div></td><td>Carlo Reyes</td><td><span class="status-badge status-info">Evacuation</span></td><td><time datetime="2026-09-09T17:30">Yesterday</time></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="dashboard-stack">
                    <section class="data-card warning-card" aria-labelledby="stock-warning-title">
                        <div class="data-card-header"><div><h2 id="stock-warning-title"><i class="bi bi-exclamation-triangle-fill me-1"></i> Low-stock warning</h2><p>6 items below their set threshold</p></div><a href="../pages/resources/index.php">Manage</a></div>
                        <div class="data-card-body">
                            <ul class="stock-warning-list">
                                <li><span class="warning-icon"><i class="bi bi-capsule"></i></span><span><strong>First-aid kits</strong><small>Threshold: 20 kits</small></span><span class="stock-count">8 left</span></li>
                                <li><span class="warning-icon"><i class="bi bi-bag-heart"></i></span><span><strong>Hygiene kits</strong><small>Threshold: 100 kits</small></span><span class="stock-count">74 left</span></li>
                                <li><span class="warning-icon"><i class="bi bi-lightning-charge"></i></span><span><strong>Flashlights</strong><small>Threshold: 35 pieces</small></span><span class="stock-count">12 left</span></li>
                            </ul>
                        </div>
                    </section>

                    <section class="data-card" aria-labelledby="announcement-title">
                        <div class="data-card-header"><div><h2 id="announcement-title">Recent announcements</h2><p>Public information updates</p></div><a href="../pages/announcements/index.php">View all</a></div>
                        <div class="data-card-body">
                            <ul class="announcement-list">
                                <li class="announcement-item urgent"><h3>Coastal weather advisory</h3><p>Residents are advised to monitor official weather bulletins and avoid unnecessary shoreline activity.</p><time datetime="2026-09-10">Today · 7:52 AM</time></li>
                                <li class="announcement-item"><h3>Go-bag preparedness reminder</h3><p>Review medicines, drinking water, flashlights, and important documents in your family emergency bag.</p><time datetime="2026-09-09">Yesterday · 3:20 PM</time></li>
                                <li class="announcement-item"><h3>Volunteer orientation schedule</h3><p>New response volunteers are invited to the barangay hall briefing this Saturday.</p><time datetime="2026-09-08">Sep 8 · 11:05 AM</time></li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>

            <section class="data-card" aria-labelledby="distribution-overview-title">
                <div class="data-card-header"><div><h2 id="distribution-overview-title">Distribution overview</h2><p>Items released during the past 30 days</p></div><a href="../pages/reports/index.php">Open report <i class="bi bi-arrow-right"></i></a></div>
                <div class="data-card-body distribution-bars">
                    <div class="distribution-bar-row"><strong>Food packs</strong><div class="bar-track"><div class="bar-fill" style="width:88%"></div></div><span>842 packs</span></div>
                    <div class="distribution-bar-row"><strong>Drinking water</strong><div class="bar-track"><div class="bar-fill" style="width:72%"></div></div><span>690 units</span></div>
                    <div class="distribution-bar-row"><strong>Hygiene kits</strong><div class="bar-track"><div class="bar-fill" style="width:46%"></div></div><span>441 kits</span></div>
                    <div class="distribution-bar-row"><strong>Sleeping mats</strong><div class="bar-track"><div class="bar-fill" style="width:31%"></div></div><span>296 pieces</span></div>
                </div>
            </section>
        </main>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
