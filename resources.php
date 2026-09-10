<?php
require_once __DIR__ . '/includes/public_service_helpers.php';
$statuses = ['Available', 'Low Stock', 'Out of Stock'];
$filters = ['q' => publicInput('q'), 'category' => publicInput('category'), 'status' => publicChoice('status', $statuses)];
$result = publicLoad(static fn(PDO $database): array => publicResources($database, $filters));
$pageTitle = 'Relief Resources';
$pageDescription = 'Check recorded relief supplies, quantities, categories, and stock availability in Barangay Bonuan Binloc.';
$activePage = 'resources';
$basePath = '';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main id="main-content">
    <?php publicServiceHero('Relief resources', 'Check available supplies and stock conditions. Contact the barangay hall to confirm eligibility and collection arrangements.', 'resources'); ?>
    <section class="section-space section-soft" aria-labelledby="resource-directory-title">
        <div class="container">
            <div class="section-heading"><span class="section-kicker">Relief supply directory</span><h2 id="resource-directory-title">Available relief supplies</h2><p>Quantities reflect the latest records. Low Stock means the quantity has reached the barangay’s recorded minimum stock level.</p></div>
            <?php publicDataNotice($result); ?>
            <?php if (!$result['error']): $directory = $result['data']; ?>
                <?php publicFilterForm('resources.php', $filters, $statuses, $directory['categories']); ?>
                <p class="public-record-meta"><?= number_format($directory['total']) ?> resource<?= $directory['total'] === 1 ? '' : 's' ?> found</p>
                <?php if (!$directory['rows']): publicEmpty('No resources found', 'No supplies match these filters, or no resources have been recorded yet. Clear the filters or contact the barangay hall.'); else: ?>
                    <div class="public-directory-grid">
                        <?php foreach ($directory['rows'] as $resource): ?>
                            <article class="public-info-card">
                                <div class="public-card-header"><h2><?= publicEscape($resource['name']) ?></h2><?php publicStatus($resource['availability']); ?></div>
                                <p class="public-record-meta"><i class="bi bi-tag" aria-hidden="true"></i> <?= publicEscape($resource['category']) ?></p>
                                <dl class="public-metrics public-metrics--single"><div><dt>Available quantity</dt><dd><?= publicQuantity($resource['stock']) ?> <small><?= publicEscape($resource['unit']) ?></small></dd></div></dl>
                                <p class="public-record-meta mb-0">Last updated <?= publicEscape(publicDate($resource['updated_at'])) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a class="btn btn-outline-brand mt-4" href="distributions.php">View relief distribution schedules <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
