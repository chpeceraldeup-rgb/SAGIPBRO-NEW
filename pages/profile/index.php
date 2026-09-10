<?php
require_once '../../includes/auth_check.php';

$pageTitle = 'My Profile';
$pageDescription = 'Manage your SAGIPBRO administrator profile and security preferences.';
$basePath = '../../';
$isAdmin = true;
$activeAdmin = 'profile';
$profileName = trim((string) ($_SESSION['full_name'] ?? 'Maria Santos')) ?: 'Maria Santos';
$profileRole = ucfirst((string) ($_SESSION['role'] ?? 'Administrator'));
$profileInitials = implode('', array_map(static fn ($part) => strtoupper(substr($part, 0, 1)), array_slice(array_filter(explode(' ', $profileName)), 0, 2)));
$profileDashboard = ($_SESSION['role'] ?? '') === 'volunteer' ? 'volunteer.php' : 'admin.php';

include '../../includes/header.php';
?>
<div class="admin-shell">
    <?php include '../../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include '../../includes/navbar.php'; ?>
        <main class="admin-content" id="main-content">
            <header class="page-header">
                <div>
                    <nav aria-label="Breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../../dashboard/<?= htmlspecialchars($profileDashboard, ENT_QUOTES, 'UTF-8') ?>">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">My profile</li></ol></nav>
                    <h1>My profile</h1>
                    <p>Keep your account details current and review your recent security activity.</p>
                </div>
                <div class="page-actions"><span class="status-badge status-success"><i class="bi bi-shield-check" aria-hidden="true"></i> Account secure</span></div>
            </header>

            <div class="profile-layout">
                <aside class="dashboard-stack" aria-label="Profile summary">
                    <section class="data-card profile-card">
                        <div class="profile-avatar-large" aria-hidden="true"><?= htmlspecialchars($profileInitials ?: 'MS', ENT_QUOTES, 'UTF-8') ?></div>
                        <h2><?= htmlspecialchars($profileName, ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($profileRole, ENT_QUOTES, 'UTF-8') ?> · Barangay Binloc</p>
                        <span class="status-badge status-success mx-auto">Active account</span>
                        <div class="profile-details">
                            <div><span>User ID</span><strong>USR-001</strong></div>
                            <div><span>Member since</span><strong>Jan 2024</strong></div>
                            <div><span>Last sign-in</span><strong>Today, 8:12 AM</strong></div>
                            <div><span>Role</span><strong><?= htmlspecialchars($profileRole, ENT_QUOTES, 'UTF-8') ?></strong></div>
                        </div>
                    </section>

                    <section class="data-card">
                        <div class="data-card-header"><div><h2>Account health</h2><p>Security recommendations</p></div><span class="status-badge status-success">Good</span></div>
                        <div class="data-card-body">
                            <ul class="stock-warning-list">
                                <li><span class="warning-icon" style="color:#146c43;background:#e0f3e8"><i class="bi bi-check-lg" aria-hidden="true"></i></span><span><strong>Strong password</strong><small>Updated 43 days ago</small></span><span class="stock-count" style="color:#146c43">Done</span></li>
                                <li><span class="warning-icon" style="color:#146c43;background:#e0f3e8"><i class="bi bi-check-lg" aria-hidden="true"></i></span><span><strong>Verified email</strong><small>Recovery address confirmed</small></span><span class="stock-count" style="color:#146c43">Done</span></li>
                                <li><span class="warning-icon"><i class="bi bi-phone" aria-hidden="true"></i></span><span><strong>Recovery phone</strong><small>Review before Sep 30</small></span><span class="stock-count">Review</span></li>
                            </ul>
                        </div>
                    </section>
                </aside>

                <div class="dashboard-stack">
                    <section class="data-card" aria-labelledby="personalInfoHeading">
                        <div class="data-card-header"><div><h2 id="personalInfoHeading">Personal information</h2><p>Used for identification, alerts, and account recovery.</p></div><i class="bi bi-person-vcard text-success" aria-hidden="true"></i></div>
                        <div class="data-card-body">
                            <form action="index.php" method="post" data-demo-form data-toast-message="Profile information updated.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label" for="profileFullName">Full name <span class="required-mark">*</span></label><input class="form-control" id="profileFullName" name="full_name" value="<?= htmlspecialchars($profileName, ENT_QUOTES, 'UTF-8') ?>" required autocomplete="name"></div>
                                    <div class="col-md-6"><label class="form-label" for="profilePosition">Barangay position</label><input class="form-control" id="profilePosition" name="position" value="Disaster Response Coordinator"></div>
                                    <div class="col-md-6"><label class="form-label" for="profileEmail">Email address <span class="required-mark">*</span></label><input class="form-control" id="profileEmail" name="email" type="email" value="maria.santos@binloc.gov.ph" required autocomplete="email"></div>
                                    <div class="col-md-6"><label class="form-label" for="profileContact">Contact number <span class="required-mark">*</span></label><input class="form-control" id="profileContact" name="contact" type="tel" value="0917 555 0148" required autocomplete="tel"></div>
                                    <div class="col-md-6"><label class="form-label" for="profileUsername">Username</label><input class="form-control" id="profileUsername" value="maria.santos" disabled aria-describedby="usernameLockedHelp"><div class="form-text" id="usernameLockedHelp">Contact a system administrator to change your username.</div></div>
                                    <div class="col-md-6"><label class="form-label" for="profileLanguage">Interface language</label><select class="form-select" id="profileLanguage" name="language"><option selected>English</option><option>Filipino</option></select></div>
                                </div>
                                <div class="d-flex justify-content-end mt-4"><button class="btn btn-brand" type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i> Save profile</button></div>
                            </form>
                        </div>
                    </section>

                    <section class="data-card" aria-labelledby="notificationsHeading">
                        <div class="data-card-header"><div><h2 id="notificationsHeading">Notification preferences</h2><p>Choose which urgent operational changes reach you.</p></div><i class="bi bi-bell text-success" aria-hidden="true"></i></div>
                        <div class="data-card-body">
                            <form action="index.php" method="post" data-demo-form data-toast-message="Notification preferences saved.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="d-grid gap-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3"><div><label class="fw-semibold small" for="notifyStock">Low-stock warnings</label><p class="small mb-0">Notify me when a resource reaches its reorder threshold.</p></div><div class="form-check form-switch"><input class="form-check-input" id="notifyStock" name="notify_stock" type="checkbox" role="switch" checked></div></div>
                                    <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3"><div><label class="fw-semibold small" for="notifyCenters">Evacuation capacity alerts</label><p class="small mb-0">Notify me when a center passes 80% occupancy.</p></div><div class="form-check form-switch"><input class="form-check-input" id="notifyCenters" name="notify_centers" type="checkbox" role="switch" checked></div></div>
                                    <div class="d-flex justify-content-between align-items-start gap-3"><div><label class="fw-semibold small" for="notifyDigest">Daily operations digest</label><p class="small mb-0">Receive a summary of distributions and activity at 5:00 PM.</p></div><div class="form-check form-switch"><input class="form-check-input" id="notifyDigest" name="notify_digest" type="checkbox" role="switch"></div></div>
                                </div>
                                <div class="d-flex justify-content-end mt-4"><button class="btn btn-brand" type="submit">Save preferences</button></div>
                            </form>
                        </div>
                    </section>

                    <section class="data-card" aria-labelledby="passwordHeading">
                        <div class="data-card-header"><div><h2 id="passwordHeading">Change password</h2><p>Use at least eight characters and avoid passwords used elsewhere.</p></div><i class="bi bi-shield-lock text-success" aria-hidden="true"></i></div>
                        <div class="data-card-body">
                            <form action="index.php" method="post" data-demo-form data-toast-message="Password changed successfully.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                                <div class="row g-3">
                                    <div class="col-12"><label class="form-label" for="currentPassword">Current password <span class="required-mark">*</span></label><input class="form-control" id="currentPassword" name="current_password" type="password" required autocomplete="current-password"></div>
                                    <div class="col-md-6"><label class="form-label" for="newPassword">New password <span class="required-mark">*</span></label><input class="form-control" id="newPassword" name="new_password" type="password" required minlength="8" autocomplete="new-password"></div>
                                    <div class="col-md-6"><label class="form-label" for="confirmNewPassword">Confirm new password <span class="required-mark">*</span></label><input class="form-control" id="confirmNewPassword" name="confirm_new_password" type="password" required minlength="8" autocomplete="new-password"></div>
                                </div>
                                <div class="d-flex justify-content-end mt-4"><button class="btn btn-brand" type="submit"><i class="bi bi-key" aria-hidden="true"></i> Update password</button></div>
                            </form>
                        </div>
                    </section>

                    <section class="data-card" aria-labelledby="recentSecurityHeading">
                        <div class="data-card-header"><div><h2 id="recentSecurityHeading">Recent account activity</h2><p>Your latest sign-ins and security changes.</p></div><a href="../activity/index.php">View all logs <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
                        <div class="data-card-body">
                            <ol class="timeline">
                                <li class="timeline-item"><span class="timeline-dot"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></span><span><strong>Successful sign-in</strong><small>Barangay Hall workstation · 10.10.4.18</small></span><time datetime="2026-09-10T08:12:00+08:00">Today, 8:12 AM</time></li>
                                <li class="timeline-item"><span class="timeline-dot"><i class="bi bi-person-check" aria-hidden="true"></i></span><span><strong>Profile information updated</strong><small>Contact number verified</small></span><time datetime="2026-09-04T15:24:00+08:00">Sep 4</time></li>
                                <li class="timeline-item"><span class="timeline-dot"><i class="bi bi-key" aria-hidden="true"></i></span><span><strong>Password changed</strong><small>All other sessions were signed out</small></span><time datetime="2026-07-29T10:18:00+08:00">Jul 29</time></li>
                            </ol>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
