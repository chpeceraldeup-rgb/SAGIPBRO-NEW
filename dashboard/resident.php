<?php
require_once __DIR__ . '/../config/session.php';
requireRole(['resident']);

$pageTitle = 'Resident portal';
$pageDescription = 'SAGIPBRO resident information portal.';
$basePath = '../';
$activePage = '';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>
<main id="main-content">
    <section class="page-hero" aria-labelledby="resident-title">
        <div class="container">
            <span class="hero-chip"><span aria-hidden="true"></span> Resident portal</span>
            <h1 id="resident-title">Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Resident', ENT_QUOTES, 'UTF-8') ?>.</h1>
            <p>Review trusted community guidance, available resources, and the household information linked to your account.</p>
        </div>
    </section>
    <section class="section-space section-soft">
        <div class="container">
            <?php require __DIR__ . '/../includes/alerts.php'; ?>
            <div class="quick-grid mb-4">
                <a class="quick-card" href="../resources.php"><span class="quick-icon"><i class="bi bi-box-seam"></i></span><h3>Available resources</h3><p>View the latest public relief supply information and stock status.</p><span class="quick-link">Browse resources <i class="bi bi-arrow-right"></i></span></a>
                <a class="quick-card" href="../evacuation-centers.php"><span class="quick-icon"><i class="bi bi-buildings"></i></span><h3>Evacuation information</h3><p>Review center capacity, availability, and preparedness information.</p><span class="quick-link">View centers <i class="bi bi-arrow-right"></i></span></a>
                <a class="quick-card" href="../contact.php"><span class="quick-icon"><i class="bi bi-telephone"></i></span><h3>Emergency contacts</h3><p>Save the verified Dagupan City response numbers before you need them.</p><span class="quick-link">Contact directory <i class="bi bi-arrow-right"></i></span></a>
            </div>
            <div class="contact-layout">
                <section class="surface-card" aria-labelledby="household-title">
                    <div class="surface-card-header"><div><h2 id="household-title">Household profile</h2><p>Information associated with your resident account</p></div><span class="status-badge status-success">Verified</span></div>
                    <div class="surface-card-body">
                        <dl class="row mb-0 small">
                            <dt class="col-sm-4 text-secondary mb-2">Household number</dt><dd class="col-sm-8 mb-2">BB-04-0182</dd>
                            <dt class="col-sm-4 text-secondary mb-2">Area</dt><dd class="col-sm-8 mb-2">Purok 4, Bonuan Binloc</dd>
                            <dt class="col-sm-4 text-secondary mb-2">Household members</dt><dd class="col-sm-8 mb-2">5 registered members</dd>
                            <dt class="col-sm-4 text-secondary">Last reviewed</dt><dd class="col-sm-8">August 28, 2026</dd>
                        </dl>
                    </div>
                    <div class="surface-card-footer"><a class="btn btn-sm btn-outline-brand" href="../contact.php">Request an information update</a></div>
                </section>
                <aside class="center-feature"><span class="feature-icon"><i class="bi bi-backpack"></i></span><h2>Is your family go-bag ready?</h2><p>Include drinking water, food, medicines, a flashlight, radio, clothing, hygiene supplies, and copies of important documents.</p><a class="btn btn-ghost-light w-100 mt-3" href="../services.php">Review preparedness services</a></aside>
            </div>
            <form class="mt-4 text-end" action="../actions/auth/logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-outline-brand" type="submit"><i class="bi bi-box-arrow-left"></i> Sign out</button></form>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
