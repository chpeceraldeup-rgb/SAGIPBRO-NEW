<?php
require_once '../../includes/auth_check.php';

$pageTitle = 'Residents';
$pageDescription = 'Manage resident and household records for Barangay Binloc.';
$basePath = '../../';
$isAdmin = true;
$activeAdmin = 'residents';

$residents = [
    ['BIN-0241', 'Ana Mae Ramos', 'AM', 'HH-2026-041', 'Purok 1, Riverside', '34', '0917 624 1842', 'Pregnant', 'Active'],
    ['BIN-0238', 'Roberto de Vera', 'RD', 'HH-2026-038', 'Purok 3, Mabini St.', '67', '0928 114 6720', 'Senior citizen', 'Active'],
    ['BIN-0234', 'Liza Manalo', 'LM', 'HH-2026-034', 'Purok 2, Bonuan Rd.', '42', '0995 302 8011', 'None', 'Active'],
    ['BIN-0229', 'Joel Santiago', 'JS', 'HH-2026-029', 'Purok 5, Coastal Area', '29', '0916 443 0921', 'PWD', 'Active'],
    ['BIN-0224', 'Marites Aquino', 'MA', 'HH-2026-024', 'Purok 4, Narra St.', '51', '0920 861 3390', 'Solo parent', 'Active'],
    ['BIN-0219', 'Carlo Mendoza', 'CM', 'HH-2026-019', 'Purok 6, Sitio Centro', '25', '0908 576 1412', 'None', 'Inactive'],
];

include '../../includes/header.php';
?>
<div class="admin-shell">
    <?php include '../../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include '../../includes/navbar.php'; ?>
        <main class="admin-content" id="main-content">
            <header class="page-header">
                <div>
                    <nav aria-label="Breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../dashboard/admin.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Residents</li>
                        </ol>
                    </nav>
                    <h1>Resident records</h1>
                    <p>Maintain reliable household information for relief assessment and response planning.</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline-brand" type="button" data-export="#residentsTable" data-export-name="resident-directory">
                        <i class="bi bi-download" aria-hidden="true"></i> Export list
                    </button>
                    <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#addResidentModal">
                        <i class="bi bi-person-plus-fill" aria-hidden="true"></i> Add resident
                    </button>
                </div>
            </header>

            <section class="stat-grid" aria-label="Resident overview">
                <article class="stat-card">
                    <div class="stat-card-top"><span class="stat-card-label">Registered residents</span><span class="stat-card-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">1,284</strong>
                    <span class="stat-meta"><span class="trend-up">+18</span> this month</span>
                </article>
                <article class="stat-card info">
                    <div class="stat-card-top"><span class="stat-card-label">Households</span><span class="stat-card-icon"><i class="bi bi-house-door-fill" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">342</strong>
                    <span class="stat-meta">3.8 residents per household</span>
                </article>
                <article class="stat-card warning">
                    <div class="stat-card-top"><span class="stat-card-label">Priority residents</span><span class="stat-card-icon"><i class="bi bi-heart-pulse-fill" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">176</strong>
                    <span class="stat-meta">Seniors, PWDs and high-risk groups</span>
                </article>
                <article class="stat-card">
                    <div class="stat-card-top"><span class="stat-card-label">Records verified</span><span class="stat-card-icon"><i class="bi bi-patch-check-fill" aria-hidden="true"></i></span></div>
                    <strong class="stat-value">96%</strong>
                    <span class="stat-meta">Updated within the last 12 months</span>
                </article>
            </section>

            <section aria-labelledby="residentDirectoryHeading">
                <div class="filter-toolbar">
                    <div class="search-field">
                        <label for="residentSearch">Search residents</label>
                        <div class="input-icon">
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <input class="form-control" id="residentSearch" type="search" placeholder="Name, household ID or address" autocomplete="off" data-table-search="#residentsTable">
                        </div>
                    </div>
                    <div class="filter-field">
                        <label for="residentStatusFilter">Record status</label>
                        <select class="form-select" id="residentStatusFilter" data-filter-select="#residentsTable" data-filter-field="status">
                            <option value="">All statuses</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <span class="filter-results" aria-live="polite">Showing 6 recent records</span>
                </div>

                <div class="data-card">
                    <div class="data-card-header">
                        <div>
                            <h2 id="residentDirectoryHeading">Resident directory</h2>
                            <p>Household and vulnerability information used during emergency operations.</p>
                        </div>
                        <span class="status-badge status-success">Data current</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table app-table align-middle" id="residentsTable">
                            <caption class="visually-hidden">Barangay Binloc resident directory</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Resident</th>
                                    <th scope="col">Household</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Age</th>
                                    <th scope="col">Contact</th>
                                    <th scope="col">Priority group</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($residents as $resident): ?>
                                    <tr data-status="<?= htmlspecialchars($resident[8], ENT_QUOTES, 'UTF-8') ?>">
                                        <td>
                                            <span class="table-avatar" aria-hidden="true"><?= htmlspecialchars($resident[2], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="d-inline-block align-middle">
                                                <span class="table-primary-text"><?= htmlspecialchars($resident[1], ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="table-secondary-text"><?= htmlspecialchars($resident[0], ENT_QUOTES, 'UTF-8') ?></span>
                                            </span>
                                        </td>
                                        <td><span class="table-primary-text"><?= htmlspecialchars($resident[3], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td><?= htmlspecialchars($resident[4], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($resident[5], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($resident[6], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><span class="status-badge <?= $resident[7] === 'None' ? 'status-neutral' : 'status-warning' ?>"><?= htmlspecialchars($resident[7], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td><span class="status-badge <?= $resident[8] === 'Active' ? 'status-success' : 'status-neutral' ?>"><?= htmlspecialchars($resident[8], ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td class="text-end">
                                            <div class="table-actions" role="group" aria-label="Actions for <?= htmlspecialchars($resident[1], ENT_QUOTES, 'UTF-8') ?>">
                                                <button class="btn btn-light btn-icon" type="button" title="View resident" aria-label="View <?= htmlspecialchars($resident[1], ENT_QUOTES, 'UTF-8') ?>" data-bs-toggle="modal" data-bs-target="#viewResidentModal"><i class="bi bi-eye" aria-hidden="true"></i></button>
                                                <button class="btn btn-light btn-icon" type="button" title="Edit resident" aria-label="Edit <?= htmlspecialchars($resident[1], ENT_QUOTES, 'UTF-8') ?>" data-bs-toggle="modal" data-bs-target="#editResidentModal"><i class="bi bi-pencil" aria-hidden="true"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="data-card-footer record-summary">
                        <span>Showing 1–6 of 1,284 residents</span>
                        <nav aria-label="Resident table pages">
                            <ul class="pagination">
                                <li class="page-item disabled"><button class="page-link" type="button" disabled aria-label="Previous page"><i class="bi bi-chevron-left" aria-hidden="true"></i></button></li>
                                <li class="page-item active"><button class="page-link" type="button" aria-current="page">1</button></li>
                                <li class="page-item"><button class="page-link" type="button" aria-label="Page 2">2</button></li>
                                <li class="page-item"><button class="page-link" type="button" aria-label="Next page"><i class="bi bi-chevron-right" aria-hidden="true"></i></button></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </section>
        </main>
        <?php include '../../includes/footer.php'; ?>
    </div>
</div>

<div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="index.php" method="post" data-demo-form data-toast-message="Resident record added successfully.">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="modal-header">
                    <div><h2 class="modal-title" id="addResidentModalLabel">Add resident</h2><p class="mb-0 mt-1 small text-body-secondary">Create a resident and household profile for relief planning.</p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="residentFirstName">First name <span class="required-mark">*</span></label><input class="form-control" id="residentFirstName" name="first_name" required autocomplete="given-name"></div>
                        <div class="col-md-6"><label class="form-label" for="residentLastName">Last name <span class="required-mark">*</span></label><input class="form-control" id="residentLastName" name="last_name" required autocomplete="family-name"></div>
                        <div class="col-md-4"><label class="form-label" for="residentBirthDate">Birth date</label><input class="form-control" id="residentBirthDate" name="birth_date" type="date"></div>
                        <div class="col-md-4"><label class="form-label" for="residentSex">Sex</label><select class="form-select" id="residentSex" name="sex"><option>Female</option><option>Male</option><option>Prefer not to say</option></select></div>
                        <div class="col-md-4"><label class="form-label" for="residentContact">Contact number</label><input class="form-control" id="residentContact" name="contact" type="tel" autocomplete="tel" placeholder="09XX XXX XXXX"></div>
                        <div class="col-md-6"><label class="form-label" for="residentHousehold">Household ID <span class="required-mark">*</span></label><input class="form-control" id="residentHousehold" name="household_id" required placeholder="HH-2026-000"></div>
                        <div class="col-md-6"><label class="form-label" for="residentPriority">Priority group</label><select class="form-select" id="residentPriority" name="priority_group"><option>None</option><option>Senior citizen</option><option>PWD</option><option>Pregnant</option><option>Solo parent</option><option>Child under five</option></select></div>
                        <div class="col-12"><label class="form-label" for="residentAddress">Complete address <span class="required-mark">*</span></label><textarea class="form-control" id="residentAddress" name="address" rows="3" required autocomplete="street-address"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-brand"><i class="bi bi-check-lg" aria-hidden="true"></i> Save resident</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewResidentModal" tabindex="-1" aria-labelledby="viewResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h2 class="modal-title" id="viewResidentModalLabel">Resident profile</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-4"><span class="profile-avatar-large m-0" aria-hidden="true">AR</span><div><h3 class="h5 mb-1">Ana Mae Ramos</h3><span class="status-badge status-success">Active record</span></div></div>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-body-secondary">Resident ID</dt><dd class="col-7">BIN-0241</dd>
                    <dt class="col-5 text-body-secondary">Household</dt><dd class="col-7">HH-2026-041</dd>
                    <dt class="col-5 text-body-secondary">Address</dt><dd class="col-7">Purok 1, Riverside</dd>
                    <dt class="col-5 text-body-secondary">Contact</dt><dd class="col-7">0917 624 1842</dd>
                    <dt class="col-5 text-body-secondary">Priority group</dt><dd class="col-7">Pregnant</dd>
                    <dt class="col-5 text-body-secondary">Last verified</dt><dd class="col-7 mb-0">September 4, 2026</dd>
                </dl>
            </div>
            <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Close</button><button class="btn btn-brand" type="button" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editResidentModal"><i class="bi bi-pencil" aria-hidden="true"></i> Edit record</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="editResidentModal" tabindex="-1" aria-labelledby="editResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="index.php" method="post" data-demo-form data-toast-message="Resident changes saved.">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="modal-header"><h2 class="modal-title" id="editResidentModalLabel">Edit resident record</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <p class="modal-intro">Update the selected resident's basic contact and classification information.</p>
                    <div class="mb-3"><label class="form-label" for="editResidentName">Full name</label><input class="form-control" id="editResidentName" name="full_name" value="Ana Mae Ramos" required></div>
                    <div class="mb-3"><label class="form-label" for="editResidentContact">Contact number</label><input class="form-control" id="editResidentContact" name="contact" value="0917 624 1842" type="tel"></div>
                    <div class="mb-3"><label class="form-label" for="editResidentPriority">Priority group</label><select class="form-select" id="editResidentPriority" name="priority_group"><option>None</option><option>Senior citizen</option><option>PWD</option><option selected>Pregnant</option><option>Solo parent</option></select></div>
                    <div><label class="form-label" for="editResidentStatus">Record status</label><select class="form-select" id="editResidentStatus" name="status"><option selected>Active</option><option>Inactive</option></select></div>
                </div>
                <div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-brand" type="submit">Save changes</button></div>
            </form>
        </div>
    </div>
</div>
