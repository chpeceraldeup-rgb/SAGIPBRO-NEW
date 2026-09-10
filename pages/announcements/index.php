<?php
require_once '../../includes/auth_check.php';
requireRole(['admin', 'official']);

$pageTitle = 'Announcements';
$pageDescription = 'Create, publish, and maintain trusted community advisories through SAGIPBRO.';
$basePath = '../../';
$isAdmin = true;
$activeAdmin = 'announcements';

$announcements = [
    ['id' => 'ANN-0261', 'title' => 'Orange Rainfall Warning: Stay Alert', 'content' => 'Moderate to heavy rainfall may affect Dagupan this afternoon. Monitor official updates and prepare essential medicines and documents.', 'category' => 'Emergency', 'audience' => 'All residents', 'status' => 'Published', 'date' => 'Sep 10, 2026', 'time' => '9:15 AM', 'author' => 'Maria L. Santos'],
    ['id' => 'ANN-0260', 'title' => 'Relief Distribution Schedule — Zones 1 to 3', 'content' => 'Registered households may claim food packs at the Barangay Hall beginning 1:00 PM. Bring a household reference or valid identification.', 'category' => 'Distribution', 'audience' => 'Zones 1–3', 'status' => 'Published', 'date' => 'Sep 10, 2026', 'time' => '8:00 AM', 'author' => 'Ana L. Mendoza'],
    ['id' => 'ANN-0259', 'title' => 'Temporary Water Supply Interruption', 'content' => 'Potable-water service may be interrupted in Sitio Korea while emergency line repairs are completed. Water stations are being prepared.', 'category' => 'Advisory', 'audience' => 'Sitio Korea', 'status' => 'Draft', 'date' => 'Sep 10, 2026', 'time' => '7:35 AM', 'author' => 'Joel P. Garcia'],
    ['id' => 'ANN-0258', 'title' => 'One Bonuan Center Capacity Update', 'content' => 'The One Bonuan Multi-Purpose Center remains open and has available family spaces. Please register at the reception desk on arrival.', 'category' => 'Evacuation', 'audience' => 'Evacuees', 'status' => 'Published', 'date' => 'Sep 9, 2026', 'time' => '6:20 PM', 'author' => 'Maria L. Santos'],
    ['id' => 'ANN-0257', 'title' => 'Volunteer Operations Briefing', 'content' => 'Accredited volunteers assigned to packing and distribution are requested to report to the operations desk for the 6:30 AM briefing.', 'category' => 'Operations', 'audience' => 'Volunteers', 'status' => 'Draft', 'date' => 'Sep 9, 2026', 'time' => '4:42 PM', 'author' => 'Roberto S. Cruz'],
    ['id' => 'ANN-0256', 'title' => 'Floodwater Monitoring Continues', 'content' => 'Monitoring teams are checking low-lying roads and drainage channels. Avoid entering moving floodwater and report blocked access routes.', 'category' => 'Emergency', 'audience' => 'All residents', 'status' => 'Published', 'date' => 'Sep 9, 2026', 'time' => '2:10 PM', 'author' => 'Maria L. Santos'],
    ['id' => 'ANN-0255', 'title' => 'Palatong Road Access Advisory', 'content' => 'Earlier road-clearing work has been completed. This advisory was archived after normal access resumed.', 'category' => 'Advisory', 'audience' => 'Motorists', 'status' => 'Archived', 'date' => 'Sep 8, 2026', 'time' => '5:30 PM', 'author' => 'Joel P. Garcia'],
];

$statusClasses = ['Published' => 'status-success', 'Draft' => 'status-warning', 'Archived' => 'status-neutral'];
$categoryIcons = ['Emergency' => 'bi-exclamation-triangle-fill', 'Distribution' => 'bi-box-seam-fill', 'Advisory' => 'bi-info-circle-fill', 'Evacuation' => 'bi-buildings-fill', 'Operations' => 'bi-people-fill'];

include '../../includes/header.php';
?>

<div class="admin-shell">
    <?php include '../../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include '../../includes/navbar.php'; ?>

        <main id="main-content" class="admin-content">
            <header class="page-header">
                <div>
                    <nav aria-label="Breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= $basePath ?>dashboard/admin.php">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Announcements</li></ol></nav>
                    <h1>Announcements</h1>
                    <p>Prepare clear, verified advisories and control what information is visible to the community.</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline-brand" type="button" data-bs-toggle="modal" data-bs-target="#previewGuidelinesModal"><i class="bi bi-journal-check" aria-hidden="true"></i> Publishing guide</button>
                    <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal"><i class="bi bi-plus-lg" aria-hidden="true"></i> Create announcement</button>
                </div>
            </header>

            <section class="filter-toolbar" aria-label="Announcement table filters">
                <div class="search-field">
                    <label for="announcementSearch">Search announcements</label>
                    <div class="input-group"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search" aria-hidden="true"></i></span><input class="form-control border-start-0" id="announcementSearch" type="search" placeholder="Search title, content, or author" autocomplete="off" data-table-search="#announcementsTable"></div>
                </div>
                <div class="filter-field">
                    <label for="announcementCategory">Category</label>
                    <select class="form-select" id="announcementCategory" data-filter-select="#announcementsTable" data-filter-field="category"><option value="">All categories</option><option>Emergency</option><option>Distribution</option><option>Advisory</option><option>Evacuation</option><option>Operations</option></select>
                </div>
                <div class="filter-field">
                    <label for="announcementStatus">Status</label>
                    <select class="form-select" id="announcementStatus" data-filter-select="#announcementsTable" data-filter-field="status"><option value="">All statuses</option><option>Published</option><option>Draft</option><option>Archived</option></select>
                </div>
                <div class="filter-field">
                    <label for="announcementAudience">Audience</label>
                    <select class="form-select" id="announcementAudience" data-filter-select="#announcementsTable" data-filter-field="audience"><option value="">All audiences</option><option>All residents</option><option>Zones 1–3</option><option>Sitio Korea</option><option>Evacuees</option><option>Volunteers</option><option>Motorists</option></select>
                </div>
                <span class="filter-results" aria-live="polite" data-filter-results><?= count($announcements) ?> announcements</span>
            </section>

            <section class="data-card" aria-labelledby="announcementRegisterHeading">
                <div class="data-card-header">
                    <div><h2 id="announcementRegisterHeading">Community announcement register</h2><p>Published notices appear on the public information feed.</p></div>
                    <span class="status-badge status-info">4 currently published</span>
                </div>
                <div class="table-responsive">
                    <table class="table app-table align-middle" id="announcementsTable" data-table>
                        <thead><tr><th scope="col">Title and content</th><th scope="col">Category</th><th scope="col">Audience</th><th scope="col">Status</th><th scope="col">Date</th><th scope="col">Author</th><th scope="col" class="text-end">Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($announcements as $announcement): ?>
                                <tr data-row data-category="<?= htmlspecialchars($announcement['category'], ENT_QUOTES, 'UTF-8') ?>" data-status="<?= htmlspecialchars($announcement['status'], ENT_QUOTES, 'UTF-8') ?>" data-audience="<?= htmlspecialchars($announcement['audience'], ENT_QUOTES, 'UTF-8') ?>">
                                    <td style="min-width: 280px; max-width: 420px;"><span class="table-primary-text"><?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?></span><span class="table-secondary-text text-truncate" style="max-width: 390px;"><?= htmlspecialchars($announcement['content'], ENT_QUOTES, 'UTF-8') ?></span><span class="table-secondary-text"><?= htmlspecialchars($announcement['id'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td><span class="d-inline-flex align-items-center gap-2"><i class="bi <?= htmlspecialchars($categoryIcons[$announcement['category']] ?? 'bi-megaphone-fill', ENT_QUOTES, 'UTF-8') ?> text-success" aria-hidden="true"></i><?= htmlspecialchars($announcement['category'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td><?= htmlspecialchars($announcement['audience'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><span class="status-badge <?= $statusClasses[$announcement['status']] ?>"><?= htmlspecialchars($announcement['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td><span class="table-primary-text"><?= htmlspecialchars($announcement['date'], ENT_QUOTES, 'UTF-8') ?></span><span class="table-secondary-text"><?= htmlspecialchars($announcement['time'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td><?= htmlspecialchars($announcement['author'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="text-end"><div class="table-actions" role="group" aria-label="Actions for <?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>">
                                        <button class="btn btn-light btn-icon" type="button" title="View" aria-label="View <?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>" data-bs-toggle="modal" data-bs-target="#viewAnnouncementModal" data-record-id="<?= htmlspecialchars($announcement['id'], ENT_QUOTES, 'UTF-8') ?>" data-record-name="<?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-eye" aria-hidden="true"></i></button>
                                        <button class="btn btn-light btn-icon" type="button" title="Edit" aria-label="Edit <?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal" data-record-id="<?= htmlspecialchars($announcement['id'], ENT_QUOTES, 'UTF-8') ?>" data-record-name="<?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                                        <button class="btn btn-light btn-icon text-danger" type="button" title="Archive" aria-label="Archive <?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>" data-bs-toggle="modal" data-bs-target="#deleteAnnouncementModal" data-record-id="<?= htmlspecialchars($announcement['id'], ENT_QUOTES, 'UTF-8') ?>" data-record-name="<?= htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-archive" aria-hidden="true"></i></button>
                                    </div></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="data-card-footer record-summary"><span>Showing 1–<?= count($announcements) ?> of 21 announcements</span><nav aria-label="Announcement pagination"><ul class="pagination pagination-sm"><li class="page-item disabled"><button class="page-link" type="button" aria-label="Previous page"><i class="bi bi-chevron-left"></i></button></li><li class="page-item active" aria-current="page"><button class="page-link" type="button">1</button></li><li class="page-item"><button class="page-link" type="button">2</button></li><li class="page-item"><button class="page-link" type="button">3</button></li><li class="page-item"><button class="page-link" type="button" aria-label="Next page"><i class="bi bi-chevron-right"></i></button></li></ul></nav></div>
            </section>

            <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><form data-demo-form data-demo-message="Announcement saved in this preview.">
                    <div class="modal-header"><h2 class="modal-title" id="addAnnouncementTitle">Create announcement</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body"><p class="modal-intro">Use plain language, state the affected area, and include only confirmed instructions.</p><div class="row g-3">
                        <div class="col-12"><label class="form-label" for="addAnnouncementHeading">Title</label><input class="form-control" id="addAnnouncementHeading" name="title" maxlength="180" placeholder="Clear and specific announcement title" required></div>
                        <div class="col-12"><label class="form-label" for="addAnnouncementContent">Content</label><textarea class="form-control" id="addAnnouncementContent" name="content" rows="6" placeholder="What happened, who is affected, what residents should do, and where updates will be posted" required></textarea><div class="form-text">Avoid all caps except for short, urgent labels.</div></div>
                        <div class="col-md-4"><label class="form-label" for="addAnnouncementCategory">Category</label><select class="form-select" id="addAnnouncementCategory" name="category" required><option value="" selected disabled>Select category</option><option>Emergency</option><option>Distribution</option><option>Advisory</option><option>Evacuation</option><option>Operations</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="addAnnouncementAudience">Audience</label><select class="form-select" id="addAnnouncementAudience" name="audience" required><option>All residents</option><option>Selected zones</option><option>Evacuees</option><option>Volunteers</option><option>Barangay officials</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="addAnnouncementStatus">Status</label><select class="form-select" id="addAnnouncementStatus" name="status"><option>Draft</option><option>Published</option></select></div>
                        <div class="col-md-6"><label class="form-label" for="addAnnouncementDate">Publish date and time</label><input class="form-control" id="addAnnouncementDate" name="published_at" type="datetime-local" value="2026-09-10T11:30"></div>
                        <div class="col-md-6"><label class="form-label" for="addAnnouncementAuthor">Author</label><input class="form-control" id="addAnnouncementAuthor" name="author" value="<?= htmlspecialchars($_SESSION['full_name'] ?? 'Maria Santos', ENT_QUOTES, 'UTF-8') ?>" readonly></div>
                    </div><div class="info-callout mt-3"><i class="bi bi-shield-check"></i><div><strong>Verification reminder</strong><span>Emergency announcements should be approved by the incident lead before publication.</span></div></div></div>
                    <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-outline-brand" type="submit" name="intent" value="draft"><i class="bi bi-file-earmark"></i> Save draft</button><button class="btn btn-brand" type="submit" name="intent" value="publish"><i class="bi bi-send"></i> Publish</button></div>
                </form></div></div>
            </div>

            <div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-labelledby="viewAnnouncementTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title" id="viewAnnouncementTitle">Announcement preview</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body"><div class="d-flex flex-wrap align-items-center gap-2 mb-3"><span class="status-badge status-danger">Emergency</span><span class="status-badge status-success">Published</span></div><h3 class="h4">Orange Rainfall Warning: Stay Alert</h3><p>Moderate to heavy rainfall may affect Dagupan this afternoon. Monitor official updates and prepare essential medicines and documents.</p><div class="info-callout mb-3"><i class="bi bi-info-circle"></i><div><strong>Safety instruction</strong><span>Keep mobile phones charged and follow evacuation instructions issued through official barangay channels.</span></div></div><dl class="row small mb-0"><dt class="col-sm-4">Audience</dt><dd class="col-sm-8">All residents</dd><dt class="col-sm-4">Published</dt><dd class="col-sm-8">September 10, 2026 at 9:15 AM</dd><dt class="col-sm-4">Author</dt><dd class="col-sm-8">Maria L. Santos</dd><dt class="col-sm-4">Reference</dt><dd class="col-sm-8">ANN-0261</dd></dl></div>
                    <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button><button class="btn btn-outline-brand" type="button" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal"><i class="bi bi-pencil"></i> Edit announcement</button></div>
                </div></div>
            </div>

            <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><form data-demo-form data-demo-message="Announcement changes saved in this preview.">
                    <div class="modal-header"><h2 class="modal-title" id="editAnnouncementTitle">Edit announcement</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body"><p class="modal-intro">Editing a published announcement will create a new activity-log entry.</p><div class="row g-3">
                        <div class="col-12"><label class="form-label" for="editAnnouncementHeading">Title</label><input class="form-control" id="editAnnouncementHeading" name="title" value="Orange Rainfall Warning: Stay Alert" maxlength="180" required></div>
                        <div class="col-12"><label class="form-label" for="editAnnouncementContent">Content</label><textarea class="form-control" id="editAnnouncementContent" name="content" rows="6" required>Moderate to heavy rainfall may affect Dagupan this afternoon. Monitor official updates and prepare essential medicines and documents.</textarea></div>
                        <div class="col-md-4"><label class="form-label" for="editAnnouncementCategory">Category</label><select class="form-select" id="editAnnouncementCategory" name="category"><option selected>Emergency</option><option>Distribution</option><option>Advisory</option><option>Evacuation</option><option>Operations</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="editAnnouncementAudience">Audience</label><select class="form-select" id="editAnnouncementAudience" name="audience"><option selected>All residents</option><option>Selected zones</option><option>Evacuees</option><option>Volunteers</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="editAnnouncementStatus">Status</label><select class="form-select" id="editAnnouncementStatus" name="status"><option>Draft</option><option selected>Published</option></select></div>
                        <div class="col-md-6"><label class="form-label" for="editAnnouncementDate">Publish date and time</label><input class="form-control" id="editAnnouncementDate" name="published_at" type="datetime-local" value="2026-09-10T09:15"></div>
                        <div class="col-md-6"><label class="form-label" for="editAnnouncementRevision">Revision note</label><input class="form-control" id="editAnnouncementRevision" name="revision_note" placeholder="Brief reason for this update"></div>
                    </div></div>
                    <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-brand" type="submit"><i class="bi bi-check-lg"></i> Save changes</button></div>
                </form></div></div>
            </div>

            <div class="modal fade" id="deleteAnnouncementModal" tabindex="-1" aria-labelledby="deleteAnnouncementTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form data-demo-form data-demo-message="Announcement archived in this preview.">
                    <div class="modal-header"><h2 class="modal-title" id="deleteAnnouncementTitle">Archive announcement?</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body"><p class="mb-2">Archive <strong>Orange Rainfall Warning: Stay Alert</strong>?</p><p class="small text-muted mb-0">It will be removed from the public feed but retained in the announcement register and activity log.</p></div>
                    <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Keep published</button><button class="btn btn-danger" type="submit" data-confirm-action="Announcement archived."><i class="bi bi-archive"></i> Archive</button></div>
                </form></div></div>
            </div>

            <div class="modal fade" id="previewGuidelinesModal" tabindex="-1" aria-labelledby="previewGuidelinesTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title" id="previewGuidelinesTitle">Safe publishing checklist</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body"><ul class="list-group list-group-flush small"><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Confirm the source and approval authority.</li><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Name the affected area and effective time.</li><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Give one clear action residents should take.</li><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Include a verified hotline for urgent concerns.</li><li class="list-group-item px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Archive the notice when it is no longer current.</li></ul></div>
                    <div class="modal-footer"><button class="btn btn-brand" type="button" data-bs-dismiss="modal">Understood</button></div>
                </div></div>
            </div>
        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
