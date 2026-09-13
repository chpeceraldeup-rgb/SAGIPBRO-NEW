<?php
require_once '../../includes/auth_check.php';
requireRole(['admin', 'official']);

$pageTitle = 'Reports';
$pageDescription = 'Generate operational reports for Barangay Binloc disaster response.';
$basePath = '../../';
$isAdmin = true;
$activeAdmin = 'reports';

$reports = [];

include '../../includes/header.php';
?>
<div class="admin-shell">
    <?php include '../../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include '../../includes/navbar.php'; ?>
        <main class="admin-content" id="main-content">
            <header class="page-header">
                <div>
                    <nav aria-label="Breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../../dashboard/admin.php">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Reports</li></ol></nav>
                    <h1>Reports center</h1>
                    <p>Turn current relief information into clear, printable operational summaries.</p>
                </div>
                <div class="page-actions"><button class="btn btn-outline-brand" type="button" data-print="#reportCatalog"><i class="bi bi-printer" aria-hidden="true"></i> Print overview</button><button class="btn btn-brand" type="button" data-export="#reportCatalog" data-export-name="sagipbro-report-overview"><i class="bi bi-file-earmark-arrow-down" aria-hidden="true"></i> Export overview</button></div>
            </header>

            <section class="stat-grid" aria-label="Reporting overview">
                <article class="stat-card"><div class="stat-card-top"><span class="stat-card-label">Reports generated</span><span class="stat-card-icon"><i class="bi bi-file-earmark-bar-graph-fill" aria-hidden="true"></i></span></div><strong class="stat-value">0</strong><span class="stat-meta">No reports generated yet</span></article>
                <article class="stat-card info"><div class="stat-card-top"><span class="stat-card-label">Records summarized</span><span class="stat-card-icon"><i class="bi bi-database-fill-check" aria-hidden="true"></i></span></div><strong class="stat-value">0</strong><span class="stat-meta">No records summarized yet</span></article>
                <article class="stat-card"><div class="stat-card-top"><span class="stat-card-label">Last data sync</span><span class="stat-card-icon"><i class="bi bi-arrow-repeat" aria-hidden="true"></i></span></div><strong class="stat-value">0</strong><span class="stat-meta">No sync recorded yet</span></article>
                <article class="stat-card warning"><div class="stat-card-top"><span class="stat-card-label">Scheduled reports</span><span class="stat-card-icon"><i class="bi bi-calendar-check-fill" aria-hidden="true"></i></span></div><strong class="stat-value">0</strong><span class="stat-meta">No scheduled reports yet</span></article>
            </section>

            <form class="filter-toolbar" action="index.php" method="get" data-demo-form data-toast-message="Report period applied.">
                <div class="filter-field"><label for="reportDateFrom">From</label><input class="form-control" id="reportDateFrom" name="from" type="date" value="2026-09-01"></div>
                <div class="filter-field"><label for="reportDateTo">To</label><input class="form-control" id="reportDateTo" name="to" type="date" value="2026-09-10"></div>
                <div class="filter-field"><label for="reportArea">Category</label><select class="form-select" id="reportArea" name="coverage"><option>All categories</option><option>Japan</option><option>China</option><option>America</option><option>Palatong</option><option>Bliss</option><option>Korea</option><option>Russia</option></select></div>
                <button class="btn btn-brand" type="submit"><i class="bi bi-funnel" aria-hidden="true"></i> Apply period</button>
                <span class="filter-results">Reporting period: Sep 1–10, 2026</span>
            </form>

            <section id="reportCatalog" aria-labelledby="reportCatalogHeading">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3"><div><h2 class="h5 mb-1" id="reportCatalogHeading">Operational report types</h2><p class="small mb-0">Choose a report to preview, print, or export as a CSV file.</p></div><span class="status-badge status-success">Data ready</span></div>
                <div class="report-grid">
                    <?php foreach ($reports as $report): ?>
                        <article class="report-card" id="<?= htmlspecialchars($report[0], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="report-card-top"><span class="report-icon"><i class="bi <?= htmlspecialchars($report[2], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i></span><span class="report-frequency"><?= htmlspecialchars($report[4], ENT_QUOTES, 'UTF-8') ?></span></div>
                            <h2><?= htmlspecialchars($report[1], ENT_QUOTES, 'UTF-8') ?></h2>
                            <p><?= htmlspecialchars($report[3], ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="report-meta"><span><?= htmlspecialchars($report[5], ENT_QUOTES, 'UTF-8') ?></span><span><?= htmlspecialchars($report[6], ENT_QUOTES, 'UTF-8') ?></span></div>
                            <div class="report-actions">
                                <button class="btn btn-brand-soft flex-grow-1" type="button" data-bs-toggle="modal" data-bs-target="#reportPreviewModal"><i class="bi bi-eye" aria-hidden="true"></i> Preview</button>
                                <button class="btn btn-light btn-icon" type="button" title="Print <?= htmlspecialchars(strtolower($report[1]), ENT_QUOTES, 'UTF-8') ?>" aria-label="Print <?= htmlspecialchars($report[1], ENT_QUOTES, 'UTF-8') ?>" data-print="#<?= htmlspecialchars($report[0], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-printer" aria-hidden="true"></i></button>
                                <button class="btn btn-light btn-icon" type="button" title="Export <?= htmlspecialchars(strtolower($report[1]), ENT_QUOTES, 'UTF-8') ?>" aria-label="Export <?= htmlspecialchars($report[1], ENT_QUOTES, 'UTF-8') ?>" data-export="#<?= htmlspecialchars($report[0], ENT_QUOTES, 'UTF-8') ?>" data-export-name="<?= htmlspecialchars($report[0], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-download" aria-hidden="true"></i></button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <article class="report-card">
                        <div class="report-card-top"><span class="report-icon"><i class="bi bi-file-earmark-plus" aria-hidden="true"></i></span><span class="report-frequency">Custom</span></div>
                        <h2>Custom operations summary</h2>
                        <p>Combine selected data points into a briefing prepared for barangay leadership.</p>
                        <div class="report-meta"><span>Choose fields</span><span>On demand</span></div>
                        <div class="report-actions"><button class="btn btn-outline-brand flex-grow-1" type="button" data-bs-toggle="modal" data-bs-target="#customReportModal"><i class="bi bi-sliders" aria-hidden="true"></i> Configure report</button></div>
                    </article>
                </div>
            </section>

            <section class="data-card mt-3" aria-labelledby="recentReportsHeading">
                <div class="data-card-header"><div><h2 id="recentReportsHeading">Recently generated</h2><p>Ready-to-use reports prepared by authorized personnel.</p></div><span class="status-badge status-info">0 files</span></div>
                <div class="table-responsive"><table class="table app-table" id="recentReportsTable"><caption class="visually-hidden">Recently generated SAGIPBRO reports</caption><thead><tr><th scope="col">Report</th><th scope="col">Period</th><th scope="col">Generated by</th><th scope="col">Generated</th><th scope="col">Format</th><th scope="col" class="text-end">Actions</th></tr></thead><tbody><tr><td colspan="6" class="text-center text-body-secondary py-4">No reports generated yet.</td></tr></tbody></table></div>
            </section>
        </main>
    </div>
</div>

<div class="modal fade" id="reportPreviewModal" tabindex="-1" aria-labelledby="reportPreviewModalLabel" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><div><h2 class="modal-title" id="reportPreviewModalLabel">Operational report preview</h2><p class="mb-0 mt-1 small text-body-secondary">Barangay Binloc · September 1–10, 2026</p></div><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body" id="reportPreviewContent">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4"><div><span class="eyebrow mb-2">SAGIPBRO report</span><h3 class="h4 mb-1">Resource Stock Position</h3><p class="small mb-0">No report generated yet</p></div><span class="status-badge status-neutral">No data</span></div>
        <div class="metric-split border rounded-3 mb-4"><div><strong>0</strong><span>Total available units</span></div><div><strong>0</strong><span>Resource categories</span></div><div><strong>0</strong><span>Low-stock items</span></div></div>
        <div class="table-responsive"><table class="table app-table"><caption class="visually-hidden">Resource stock report preview</caption><thead><tr><th scope="col">Resource</th><th scope="col">Category</th><th scope="col">Available</th><th scope="col">Threshold</th><th scope="col">Status</th></tr></thead><tbody><tr><td>Family Food Pack</td><td>Food</td><td>120 packs</td><td>40</td><td><span class="status-badge status-success">Available</span></td></tr><tr><td>Drinking Water</td><td>Water</td><td>18 cases</td><td>25</td><td><span class="status-badge status-warning">Low stock</span></td></tr><tr><td>Hygiene Kit</td><td>Sanitation</td><td>64 kits</td><td>20</td><td><span class="status-badge status-success">Available</span></td></tr></tbody></table></div>
    </div>
    <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button><button class="btn btn-outline-brand" type="button" data-print="#reportPreviewContent"><i class="bi bi-printer" aria-hidden="true"></i> Print</button><button class="btn btn-brand" type="button" data-export="#reportPreviewContent" data-export-name="resource-stock-report"><i class="bi bi-download" aria-hidden="true"></i> Export</button></div>
</div></div></div>

<div class="modal fade" id="customReportModal" tabindex="-1" aria-labelledby="customReportModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
    <form action="index.php" method="post" data-demo-form data-toast-message="Custom report generated and added to recent reports.">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="modal-header"><div><h2 class="modal-title" id="customReportModalLabel">Configure custom report</h2><p class="mb-0 mt-1 small text-body-secondary">Select the operational sections required for your briefing.</p></div><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body"><fieldset><legend class="form-label mb-2">Include sections</legend><div class="row g-2"><div class="col-md-6"><div class="form-check"><input class="form-check-input" id="includeResources" name="sections[]" value="resources" type="checkbox" checked><label class="form-check-label" for="includeResources">Resource inventory</label></div></div><div class="col-md-6"><div class="form-check"><input class="form-check-input" id="includeDistributions" name="sections[]" value="distributions" type="checkbox" checked><label class="form-check-label" for="includeDistributions">Relief distributions</label></div></div><div class="col-md-6"><div class="form-check"><input class="form-check-input" id="includeEvacuation" name="sections[]" value="evacuation" type="checkbox"><label class="form-check-label" for="includeEvacuation">Evacuation centers</label></div></div><div class="col-md-6"><div class="form-check"><input class="form-check-input" id="includePeople" name="sections[]" value="people" type="checkbox"><label class="form-check-label" for="includePeople">Residents and volunteers</label></div></div></div></fieldset><hr><div class="row g-3"><div class="col-md-6"><label class="form-label" for="customReportFormat">Output format</label><select class="form-select" id="customReportFormat" name="format"><option>PDF document</option><option>CSV spreadsheet</option><option>Print-ready view</option></select></div><div class="col-md-6"><label class="form-label" for="customReportTitle">Report title</label><input class="form-control" id="customReportTitle" name="title" value="Barangay Operations Brief" required></div></div></div>
        <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-brand" type="submit"><i class="bi bi-file-earmark-check" aria-hidden="true"></i> Generate report</button></div>
    </form>
</div></div></div>
<?php include '../../includes/footer.php'; ?>
