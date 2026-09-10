<?php
require_once '../../includes/auth_check.php';

$pageTitle = 'Activity Logs';
$pageDescription = 'Review auditable activity across the SAGIPBRO operations console.';
$basePath = '../../';
$isAdmin = true;
$activeAdmin = 'activity';

$logs = [];

include '../../includes/header.php';
?>
<div class="admin-shell">
    <?php include '../../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include '../../includes/navbar.php'; ?>
        <main class="admin-content" id="main-content">
            <header class="page-header">
                <div>
                    <nav aria-label="Breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../../dashboard/admin.php">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Activity logs</li></ol></nav>
                    <h1>Activity logs</h1>
                    <p>Review a traceable history of important access and operational events.</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline-brand" type="button" data-print="#activityLogsTable"><i class="bi bi-printer" aria-hidden="true"></i> Print</button>
                    <button class="btn btn-brand" type="button" data-export="#activityLogsTable" data-export-name="sagipbro-activity-log"><i class="bi bi-download" aria-hidden="true"></i> Export log</button>
                </div>
            </header>

            <section class="stat-grid" aria-label="Activity log overview">
                <article class="stat-card"><div class="stat-card-top"><span class="stat-card-label">Events today</span><span class="stat-card-icon"><i class="bi bi-activity" aria-hidden="true"></i></span></div><strong class="stat-value">146</strong><span class="stat-meta"><span class="trend-up">+12%</span> from yesterday</span></article>
                <article class="stat-card info"><div class="stat-card-top"><span class="stat-card-label">Active users</span><span class="stat-card-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span></div><strong class="stat-value">23</strong><span class="stat-meta">Within the last 24 hours</span></article>
                <article class="stat-card warning"><div class="stat-card-top"><span class="stat-card-label">Warnings</span><span class="stat-card-icon"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i></span></div><strong class="stat-value">3</strong><span class="stat-meta">Require administrator review</span></article>
                <article class="stat-card danger"><div class="stat-card-top"><span class="stat-card-label">Blocked attempts</span><span class="stat-card-icon"><i class="bi bi-shield-x" aria-hidden="true"></i></span></div><strong class="stat-value">1</strong><span class="stat-meta">Automatically contained</span></article>
            </section>

            <div class="info-callout mb-3" role="note"><i class="bi bi-shield-check" aria-hidden="true"></i><div><strong>Audit records are read-only</strong><span>Logs help Barangay Binloc account for system changes. Times are shown in Philippine Standard Time (UTC+8).</span></div></div>

            <section aria-labelledby="activityHistoryHeading">
                <div class="filter-toolbar">
                    <div class="search-field"><label for="activitySearch">Search activity</label><div class="input-icon"><i class="bi bi-search" aria-hidden="true"></i><input class="form-control" id="activitySearch" type="search" placeholder="User, event, module, record or IP" autocomplete="off" data-table-search="#activityLogsTable"></div></div>
                    <div class="filter-field"><label for="activityTypeFilter">Event type</label><select class="form-select" id="activityTypeFilter" data-filter-select="#activityLogsTable" data-filter-field="action"><option value="">All events</option><option value="Created">Created</option><option value="Updated">Updated</option><option value="Published">Published</option><option value="Signed in">Signed in</option><option value="Exported">Exported</option><option value="Warning">Warning</option><option value="Blocked">Blocked</option></select></div>
                    <span class="filter-results" aria-live="polite">Showing 8 latest events</span>
                </div>

                <div class="data-card">
                    <div class="data-card-header"><div><h2 id="activityHistoryHeading">System activity history</h2><p>Security, data changes, announcements, distributions, and report events.</p></div><span class="status-badge status-success">Logging active</span></div>
                    <div class="table-responsive">
                        <table class="table app-table" id="activityLogsTable">
                            <caption class="visually-hidden">Recent SAGIPBRO system activity</caption>
                            <thead><tr><th scope="col">Event</th><th scope="col">User</th><th scope="col">Module</th><th scope="col">Details</th><th scope="col">Source</th><th scope="col">Date &amp; time</th><th scope="col" class="text-end">Review</th></tr></thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <?php $badgeClass = $log[9] === 'danger' ? 'status-danger' : ($log[9] === 'warning' ? 'status-warning' : ($log[9] === 'info' ? 'status-info' : 'status-success')); ?>
                                    <tr data-action="<?= htmlspecialchars($log[3], ENT_QUOTES, 'UTF-8') ?>">
                                        <td><div class="activity-cell"><span class="activity-icon <?= $log[9] === 'warning' ? 'warning' : ($log[9] === 'info' ? 'info' : '') ?>"><i class="bi <?= htmlspecialchars($log[10], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i></span><span><span class="table-primary-text"><?= htmlspecialchars($log[3], ENT_QUOTES, 'UTF-8') ?></span><span class="table-secondary-text"><?= htmlspecialchars($log[0], ENT_QUOTES, 'UTF-8') ?></span></span></div></td>
                                        <td><span class="table-avatar" aria-hidden="true"><?= htmlspecialchars($log[2], ENT_QUOTES, 'UTF-8') ?></span><span class="table-primary-text d-inline"><?= htmlspecialchars($log[1], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td><span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($log[4], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td><?= htmlspecialchars($log[5], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><code class="small text-body-secondary"><?= htmlspecialchars($log[6], ENT_QUOTES, 'UTF-8') ?></code></td>
                                        <td><span class="table-primary-text"><?= htmlspecialchars($log[7], ENT_QUOTES, 'UTF-8') ?></span><span class="table-secondary-text"><?= htmlspecialchars($log[8], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td class="text-end"><button class="btn btn-light btn-icon" type="button" title="Review event" aria-label="Review activity <?= htmlspecialchars($log[0], ENT_QUOTES, 'UTF-8') ?>" data-bs-toggle="modal" data-bs-target="#activityDetailModal"><i class="bi bi-eye" aria-hidden="true"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="data-card-footer record-summary"><span>Showing 1–8 of 4,918 retained events</span><span>Retention period: 24 months</span></div>
                </div>
            </section>
        </main>
        <?php include '../../includes/footer.php'; ?>
    </div>
</div>

<div class="modal fade" id="activityDetailModal" tabindex="-1" aria-labelledby="activityDetailModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><div><h2 class="modal-title" id="activityDetailModalLabel">Activity event details</h2><p class="mb-0 mt-1 small text-body-secondary">Immutable audit entry LOG-9842</p></div><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body">
        <div class="d-flex align-items-start gap-3 mb-4"><span class="activity-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span><div><h3 class="h6 mb-1">Resource stock updated</h3><span class="status-badge status-success">Successful</span></div></div>
        <dl class="row small mb-3"><dt class="col-sm-4 text-body-secondary">Performed by</dt><dd class="col-sm-8">Maria Santos (Administrator)</dd><dt class="col-sm-4 text-body-secondary">Date and time</dt><dd class="col-sm-8">September 10, 2026 at 8:42:16 AM PHT</dd><dt class="col-sm-4 text-body-secondary">Module</dt><dd class="col-sm-8">Resources</dd><dt class="col-sm-4 text-body-secondary">Affected record</dt><dd class="col-sm-8">RES-102 — Family Food Pack</dd><dt class="col-sm-4 text-body-secondary">Source address</dt><dd class="col-sm-8"><code>10.10.4.18</code></dd><dt class="col-sm-4 text-body-secondary">Session reference</dt><dd class="col-sm-8 mb-0"><code>SES-6F82…A190</code></dd></dl>
        <div class="rounded-3 border p-3 bg-body-tertiary"><strong class="d-block small mb-1">Change summary</strong><span class="small text-body-secondary">Available stock changed from 138 to 120 packs after reconciliation with distribution batch DST-2026-0910-03.</span></div>
    </div>
    <div class="modal-footer"><button class="btn btn-brand" type="button" data-bs-dismiss="modal">Done</button></div>
</div></div></div>
