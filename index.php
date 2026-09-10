<?php
$pageTitle = 'SAGIPBRO';
$pageDescription = 'Official disaster relief resource information for Bonuan Binloc, Dagupan City—relief supplies, evacuation centers, distributions, and emergency updates.';
$activePage = 'home';
$basePath = '';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<main id="main-content">
    <section class="home-hero" aria-labelledby="hero-title">
        <div class="container">
            <div class="hero-content">
                <div class="hero-chip"><span aria-hidden="true"></span> Serving Bonuan Binloc, Dagupan City</div>
                <h1 id="hero-title">SAGIPBRO</h1>
                <p class="hero-subtitle">Disaster Relief Resource Information System</p>
                <p class="hero-copy">One trusted place for residents and barangay responders to find relief supply availability, evacuation center information, and verified emergency updates when every minute matters.</p>
                <div class="hero-actions">
                    <a class="btn btn-white" href="resources.php"><i class="bi bi-box-seam" aria-hidden="true"></i> View resources</a>
                    <a class="btn btn-ghost-light" href="services.php#evacuation-centers"><i class="bi bi-buildings" aria-hidden="true"></i> View evacuation centers</a>
                </div>
            </div>
        </div>
        <div class="hero-trust" aria-label="System qualities">
            <div class="container hero-trust-row">
                <span><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Barangay-verified information</span>
                <span><i class="bi bi-phone" aria-hidden="true"></i> Mobile-ready access</span>
                <span><i class="bi bi-clock-history" aria-hidden="true"></i> Timely operational updates</span>
            </div>
        </div>
    </section>

    <section class="emergency-band" aria-label="Emergency announcement">
        <div class="container">
            <div class="emergency-band-inner">
                <span class="emergency-label"><i class="bi bi-megaphone-fill" aria-hidden="true"></i> Advisory</span>
                <div class="emergency-message">
                    <strong>Community preparedness reminder</strong>
                    <span>Keep your family go-bag ready, monitor official weather bulletins, and know your nearest evacuation route.</span>
                </div>
                <a class="emergency-link" href="services.php#announcements">Read guidance <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </section>

    <section class="section-space" aria-labelledby="quick-info-title">
        <div class="container">
            <div class="section-heading">
                <span class="section-kicker">Quick information</span>
                <h2 id="quick-info-title">What do you need today?</h2>
                <p>Find clear, current information before, during, and after an emergency.</p>
            </div>
            <div class="quick-grid">
                <a class="quick-card" href="resources.php">
                    <span class="quick-icon"><i class="bi bi-box2-heart" aria-hidden="true"></i></span>
                    <h3>Relief resources</h3>
                    <p>Check the current availability of food, water, hygiene supplies, medicine, and other essential goods.</p>
                    <span class="quick-link">Browse supplies <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                </a>
                <a class="quick-card" href="services.php#evacuation-centers">
                    <span class="quick-icon"><i class="bi bi-houses" aria-hidden="true"></i></span>
                    <h3>Evacuation centers</h3>
                    <p>Review center locations, operating status, capacity, and available accommodation before traveling.</p>
                    <span class="quick-link">Find a safe center <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                </a>
                <a class="quick-card" href="services.php#relief-distribution">
                    <span class="quick-icon"><i class="bi bi-truck" aria-hidden="true"></i></span>
                    <h3>Relief distribution</h3>
                    <p>Understand how organized relief is scheduled, recorded, and delivered fairly to affected households.</p>
                    <span class="quick-link">How distribution works <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                </a>
            </div>
        </div>
    </section>

    <section class="section-space section-soft" aria-labelledby="snapshot-title">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div class="section-heading mb-0">
                    <span class="section-kicker">Situation snapshot</span>
                    <h2 id="snapshot-title">Relief readiness at a glance</h2>
                    <p>A quick view of essential supplies and evacuation capacity.</p>
                </div>
                <span class="status-badge status-success">Updated today, 8:30 AM</span>
            </div>
            <div class="snapshot-wrap">
                <article class="surface-card">
                    <div class="surface-card-header">
                        <div>
                            <h3>Essential relief inventory</h3>
                            <p>Most-requested resources available for response operations</p>
                        </div>
                        <a class="btn btn-sm btn-brand-soft" href="resources.php">View all</a>
                    </div>
                    <div class="surface-card-body pt-0">
                        <ul class="resource-list">
                            <li>
                                <span class="resource-mini-icon"><i class="bi bi-basket2" aria-hidden="true"></i></span>
                                <span><strong>Family food packs</strong><small>Food supplies · per pack</small></span>
                                <span class="resource-quantity"><strong>486</strong><small class="status-badge status-success">In stock</small></span>
                            </li>
                            <li>
                                <span class="resource-mini-icon"><i class="bi bi-droplet" aria-hidden="true"></i></span>
                                <span><strong>Drinking water</strong><small>Water · 6-liter container</small></span>
                                <span class="resource-quantity"><strong>320</strong><small class="status-badge status-success">In stock</small></span>
                            </li>
                            <li>
                                <span class="resource-mini-icon"><i class="bi bi-bag-heart" aria-hidden="true"></i></span>
                                <span><strong>Hygiene kits</strong><small>Health &amp; sanitation · per kit</small></span>
                                <span class="resource-quantity"><strong>74</strong><small class="status-badge status-warning">Low stock</small></span>
                            </li>
                            <li>
                                <span class="resource-mini-icon"><i class="bi bi-moon-stars" aria-hidden="true"></i></span>
                                <span><strong>Sleeping mats</strong><small>Shelter supplies · per piece</small></span>
                                <span class="resource-quantity"><strong>118</strong><small class="status-badge status-success">In stock</small></span>
                            </li>
                        </ul>
                    </div>
                </article>
                <article class="center-feature">
                    <span class="feature-icon"><i class="bi bi-building-check" aria-hidden="true"></i></span>
                    <span class="status-badge status-success mb-3">Open</span>
                    <h2>Bonuan multipurpose evacuation facility</h2>
                    <p>Serving the One Bonuan community during hazard events, with coordinated barangay assistance.</p>
                    <div class="occupancy-line"><span>Current occupancy</span><strong>184 / 1,200</strong></div>
                    <div class="progress" role="progressbar" aria-label="Evacuation center occupancy" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: 15%"></div></div>
                    <a class="btn btn-ghost-light w-100 mt-4" href="services.php#evacuation-centers"><i class="bi bi-geo-alt" aria-hidden="true"></i> Center information</a>
                </article>
            </div>
            <p class="mt-3 mb-0 text-secondary" style="font-size:.72rem"><i class="bi bi-info-circle me-1" aria-hidden="true"></i> Demonstration inventory values are shown for the frontend design and will be replaced by connected database records.</p>
        </div>
    </section>

    <section class="trust-strip" aria-label="SAGIPBRO benefits">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-item"><i class="bi bi-database-check" aria-hidden="true"></i><span><strong>Organized records</strong><small>One source of operational data</small></span></div>
                <div class="trust-item"><i class="bi bi-shield-lock" aria-hidden="true"></i><span><strong>Role-based access</strong><small>Protected staff information</small></span></div>
                <div class="trust-item"><i class="bi bi-universal-access" aria-hidden="true"></i><span><strong>Easy to use</strong><small>Readable on every device</small></span></div>
                <div class="trust-item"><i class="bi bi-people" aria-hidden="true"></i><span><strong>Community first</strong><small>Built for residents and responders</small></span></div>
            </div>
        </div>
    </section>

    <section class="section-space" aria-labelledby="process-title">
        <div class="container">
            <div class="section-heading text-center">
                <span class="section-kicker">Coordinated response</span>
                <h2 id="process-title">From supply to family support</h2>
                <p>SAGIPBRO gives barangay officials a consistent way to prepare, respond, and report.</p>
            </div>
            <div class="process-grid">
                <article class="process-step"><h3>Record and monitor</h3><p>Relief stocks, center availability, residents, and volunteers are organized in one secure workspace.</p></article>
                <article class="process-step"><h3>Coordinate and distribute</h3><p>Teams can plan distributions, record recipients, and keep quantities accountable during operations.</p></article>
                <article class="process-step"><h3>Inform and improve</h3><p>Verified notices keep residents informed while reports help officials plan the next response.</p></article>
            </div>
        </div>
    </section>

    <section class="pb-5" aria-labelledby="prepared-title">
        <div class="container">
            <div class="cta-panel">
                <div>
                    <h2 id="prepared-title">Prepared communities respond better.</h2>
                    <p>Review available resources, save official emergency contacts, and talk with your household about where to go before an emergency begins.</p>
                </div>
                <div class="cta-actions">
                    <a class="btn btn-white" href="resources.php">Check resources</a>
                    <a class="btn btn-ghost-light" href="contact.php">Contact us</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
