<?php
require_once __DIR__ . '/includes/public_service_helpers.php';
$statuses = ['Upcoming', 'Active', 'Completed', 'Cancelled'];
$filters = ['q' => publicInput('q'), 'status' => publicChoice('status', $statuses)];
$result = publicLoad(static fn(PDO $database): array => publicDistributions($database, $filters));
$pageTitle = 'Relief Distribution';
$pageDescription = 'Find published relief distribution schedules, locations, supplies and current status in Barangay Bonuan Binloc.';
$activePage = 'services';
$basePath = '';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main id="main-content">
    <?php publicServiceHero('Relief distribution', 'Review published schedules and active relief distributions, including locations, planned supplies, and recorded distribution totals.', 'distributions'); ?>
    <section class="section-space section-soft" aria-labelledby="distribution-directory-title">
        <div class="container">
            <div class="section-heading"><span class="section-kicker">Schedules and updates</span><h2 id="distribution-directory-title">Relief distribution information</h2><p>Active and upcoming events appear first. All dates and times use Philippine time (PHT / UTC+8). Planned supplies may change; confirm collection instructions with the barangay.</p></div>
            <?php publicDataNotice($result); ?>
            <?php if (!$result['error']): $directory = $result['data']; ?>
                <?php publicFilterForm('distributions.php', $filters, $statuses); ?>
                <p class="public-record-meta"><?= number_format($directory['total']) ?> distribution<?= $directory['total'] === 1 ? '' : 's' ?> found</p>
                <?php if (!$directory['rows']): publicEmpty('No distributions found', 'No public schedules or recorded distributions match these filters. Check again for updates or contact the barangay hall.'); else: ?>
                    <div class="public-directory-grid">
                        <?php foreach ($directory['rows'] as $distribution): ?>
                            <article class="public-info-card<?= $distribution['status'] === 'Active' ? ' public-info-card--available' : '' ?>">
                                <div class="public-card-header"><h2><?= publicEscape($distribution['title']) ?></h2><?php publicStatus($distribution['status']); ?></div>
                                <p><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= publicEscape($distribution['location']) ?></p>
                                <p class="public-record-meta"><strong><?= $distribution['status'] === 'Completed' ? 'Recorded date:' : 'Starts:' ?></strong> <?= publicEscape(publicDate($distribution['starts_at'])) ?><?php if (!$distribution['time_recorded']): ?><br>Time not recorded<?php endif; ?>
                                    <?php if ($distribution['ends_at']): ?><br><strong>Ends:</strong> <?= publicEscape(publicDate($distribution['ends_at'])) ?><?php endif; ?>
                                </p>
                                <?php if ($distribution['details']): ?><p class="public-record-details"><?= publicEscape($distribution['details']) ?></p><?php endif; ?>
                                <h3 class="h6">Relief supplies</h3>
                                <?php if (!$distribution['resources']): ?><p class="public-record-meta">Supply details have not been recorded.</p><?php else: ?>
                                    <ul class="public-record-list">
                                        <?php foreach ($distribution['resources'] as $resource): ?>
                                            <li><strong><?= publicEscape($resource['name']) ?></strong><br>
                                                <?php if ($resource['planned_quantity'] !== null): ?>Planned: <?= publicQuantity($resource['planned_quantity']) ?> <?= publicEscape($resource['unit']) ?><br><?php endif; ?>
                                                Distributed: <?= publicQuantity($resource['distributed_quantity']) ?> <?= publicEscape($resource['unit']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <p class="public-record-meta mb-0">Last recorded update <?= publicEscape(publicDate($distribution['updated_at'])) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
