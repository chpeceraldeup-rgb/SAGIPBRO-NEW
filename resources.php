<?php
$pageTitle = 'Relief Resources';
$pageDescription = 'Browse a sample presentation of relief-supply information for the SAGIPBRO public interface.';
$activePage = 'resources';
$basePath = '';

$resources = [
    ['name' => 'Family food packs', 'category' => 'Food', 'quantity' => 84, 'unit' => 'packs', 'status' => 'In stock', 'tone' => 'success', 'icon' => 'bi-basket2'],
    ['name' => 'Rice', 'category' => 'Food', 'quantity' => 320, 'unit' => 'kilograms', 'status' => 'In stock', 'tone' => 'success', 'icon' => 'bi-bag-heart'],
    ['name' => 'Bottled drinking water', 'category' => 'Water', 'quantity' => 180, 'unit' => 'bottles', 'status' => 'In stock', 'tone' => 'success', 'icon' => 'bi-droplet'],
    ['name' => 'Hygiene kits', 'category' => 'Hygiene', 'quantity' => 28, 'unit' => 'kits', 'status' => 'Low stock', 'tone' => 'warning', 'icon' => 'bi-handbag'],
    ['name' => 'Baby care kits', 'category' => 'Hygiene', 'quantity' => 12, 'unit' => 'kits', 'status' => 'Low stock', 'tone' => 'warning', 'icon' => 'bi-heart'],
    ['name' => 'First-aid kits', 'category' => 'Medical', 'quantity' => 8, 'unit' => 'kits', 'status' => 'Low stock', 'tone' => 'warning', 'icon' => 'bi-bandaid'],
    ['name' => 'Face masks', 'category' => 'Medical', 'quantity' => 600, 'unit' => 'pieces', 'status' => 'In stock', 'tone' => 'success', 'icon' => 'bi-shield-plus'],
    ['name' => 'Sleeping mats', 'category' => 'Shelter', 'quantity' => 46, 'unit' => 'pieces', 'status' => 'In stock', 'tone' => 'success', 'icon' => 'bi-grid'],
    ['name' => 'Blankets', 'category' => 'Shelter', 'quantity' => 0, 'unit' => 'pieces', 'status' => 'Out of stock', 'tone' => 'danger', 'icon' => 'bi-layers'],
];

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main id="main-content">
    <section class="page-hero" aria-labelledby="resources-page-title">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Resources</li>
                </ol>
            </nav>
            <span class="hero-chip"><span aria-hidden="true"></span> Relief supply directory</span>
            <h1 id="resources-page-title">Find resource information quickly.</h1>
            <p>Search and filter the resource-card experience designed for SAGIPBRO. The quantities below are illustrative sample data, not live barangay inventory.</p>
        </div>
    </section>

    <section class="section-space section-soft" aria-labelledby="resource-directory-title">
        <div class="container">
            <div class="section-heading">
                <span class="section-kicker">Sample snapshot</span>
                <h2 id="resource-directory-title">Relief supply overview</h2>
                <p>Preview how public resource names, categories, quantities, units, and stock conditions are presented.</p>
            </div>

            <div class="alert alert-warning app-alert mb-4" role="note">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Interface preview only</strong>
                    <span>Sample snapshot dated 10 September 2026. Quantities are illustrative and do not represent current barangay stock.</span>
                </div>
                <span class="status-badge status-neutral">Sample data</span>
            </div>

            <div class="filter-panel" role="search" aria-label="Filter sample relief resources">
                <div class="filter-search">
                    <label for="resourceSearch">Search supplies</label>
                    <div class="input-icon">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input class="form-control" id="resourceSearch" type="search" placeholder="Search by resource name..." autocomplete="off" aria-controls="resourceGrid" data-resource-search>
                    </div>
                </div>
                <div class="filter-field">
                    <label for="resourceCategory">Category</label>
                    <select class="form-select" id="resourceCategory" aria-controls="resourceGrid" data-resource-filter>
                        <option value="all">All categories</option>
                        <option value="food">Food</option>
                        <option value="water">Water</option>
                        <option value="hygiene">Hygiene</option>
                        <option value="medical">Medical</option>
                        <option value="shelter">Shelter</option>
                    </select>
                </div>
                <p class="mb-2 ms-auto small text-secondary" aria-live="polite"><strong data-resource-count><?= count($resources) ?></strong> sample resources shown</p>
            </div>

            <div class="public-resource-grid" id="resourceGrid">
                <?php foreach ($resources as $resource): ?>
                    <article
                        class="resource-card"
                        data-resource-card
                        data-resource-name="<?= htmlspecialchars(strtolower($resource['name']), ENT_QUOTES, 'UTF-8') ?>"
                        data-resource-category="<?= htmlspecialchars(strtolower($resource['category']), ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <div class="resource-card-top">
                            <div>
                                <div class="resource-mini-icon"><i class="bi <?= htmlspecialchars($resource['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i></div>
                                <h2><?= htmlspecialchars($resource['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <span class="category"><?= htmlspecialchars($resource['category'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <span class="status-badge status-<?= htmlspecialchars($resource['tone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($resource['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="resource-stock">
                            <div>
                                <small>Sample available quantity</small>
                                <strong><?= number_format($resource['quantity']) ?></strong>
                            </div>
                            <small><?= htmlspecialchars($resource['unit'], ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="empty-state surface-card mt-3" data-resource-empty hidden aria-live="polite">
                <i class="bi bi-search" aria-hidden="true"></i>
                <h3>No sample resources match</h3>
                <p>Try another search term or choose a different category.</p>
            </div>
        </div>
    </section>

    <section class="section-space" aria-labelledby="resource-guidance-title">
        <div class="container">
            <div class="icon-card-grid">
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-check-circle" aria-hidden="true"></i></div>
                    <h2 id="resource-guidance-title">Confirm availability</h2>
                    <p>Relief availability can change. Contact the appropriate response channel before relying on a displayed quantity.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-person-check" aria-hidden="true"></i></div>
                    <h2>Follow distribution guidance</h2>
                    <p>Distribution schedules and eligibility instructions should come from an authorized announcement or official.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></div>
                    <h2>Use 911 for emergencies</h2>
                    <p>For an immediate threat to life or safety, call the national emergency hotline instead of using a website form.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="pb-5" aria-labelledby="resources-cta-title">
        <div class="container">
            <div class="cta-panel">
                <div>
                    <h2 id="resources-cta-title">Need confirmed assistance information?</h2>
                    <p>Use the verified Dagupan City disaster-response contact details listed on the contact page.</p>
                </div>
                <div class="cta-actions">
                    <a class="btn btn-white" href="contact.php"><i class="bi bi-telephone" aria-hidden="true"></i> View contact details</a>
                    <a class="btn btn-ghost-light" href="tel:911">Call 911</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
