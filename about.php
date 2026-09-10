<?php
$pageTitle = 'About SAGIPBRO';
$pageDescription = 'Learn how SAGIPBRO supports clear, organized disaster-relief information for Barangay Bonuan Binloc, Dagupan City.';
$activePage = 'about';
$basePath = '';

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main id="main-content">
    <section class="page-hero about-hero" aria-labelledby="about-page-title">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About</li>
                </ol>
            </nav>
            <span class="hero-chip"><span aria-hidden="true"></span> About the system</span>
            <h1 id="about-page-title">Preparedness built around people and reliable information.</h1>
            <p>SAGIPBRO gives Barangay Bonuan Binloc a clear, accessible place for disaster-relief resources, evacuation information, distributions, and community advisories.</p>
        </div>
    </section>

    <section class="section-space" aria-labelledby="community-profile-title">
        <div class="container">
            <div class="section-heading text-center">
                <span class="section-kicker">Community profile</span>
                <h2 id="community-profile-title">Designed for Bonuan Binloc</h2>
                <p>The system is shaped around the information needs of an urban, coastal barangay in Dagupan City.</p>
            </div>

            <div class="stat-band" aria-label="Bonuan Binloc community facts">
                <div class="public-stat">
                    <strong>11,326</strong>
                    <span>Population recorded in the 2024 POPCEN</span>
                </div>
                <div class="public-stat">
                    <strong>Urban</strong>
                    <span>Barangay classification</span>
                </div>
                <div class="public-stat">
                    <strong>Coastal</strong>
                    <span>Community setting</span>
                </div>
                <div class="public-stat">
                    <strong>0105518007</strong>
                    <span>Philippine Standard Geographic Code</span>
                </div>
            </div>
            <p class="mt-3 mb-0 text-center small text-secondary">Community facts: <a href="https://psa.gov.ph/classification/psgc/brgydetail/0105518007" target="_blank" rel="noopener noreferrer">Philippine Statistics Authority barangay profile <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a></p>
        </div>
    </section>

    <section class="section-space section-soft" aria-labelledby="mission-title">
        <div class="container">
            <div class="mission-panel">
                <div class="mission-visual">
                    <blockquote>
                        “Clear information helps neighbors, volunteers, and officials make safer decisions together.”
                        <small>SAGIPBRO community-first principle</small>
                    </blockquote>
                </div>
                <div>
                    <span class="section-kicker">Purpose and mission</span>
                    <h2 id="mission-title">One dependable view of relief operations</h2>
                    <p>SAGIPBRO is intended to organize essential disaster-relief information so it is easier to understand, update, and use during preparedness and response activities.</p>
                    <p>Its mission is to support timely, transparent, and coordinated decisions while keeping public information readable for every member of the community.</p>
                    <ul class="check-list">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Present relief supply availability in a simple, consistent format.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Make evacuation-center information easier to locate and compare.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Support orderly documentation of relief distribution activities.</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Share important advisories through a clear public information channel.</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space" aria-labelledby="objectives-title">
        <div class="container">
            <div class="section-heading">
                <span class="section-kicker">System objectives</span>
                <h2 id="objectives-title">Better visibility from preparedness to recovery</h2>
                <p>Each part of SAGIPBRO is designed to make essential information easier to find and routine coordination easier to follow.</p>
            </div>

            <div class="icon-card-grid">
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-box-seam" aria-hidden="true"></i></div>
                    <h3>Organize resources</h3>
                    <p>Group supplies by category, quantity, unit, and stock condition for a clearer inventory view.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-buildings" aria-hidden="true"></i></div>
                    <h3>Clarify safe locations</h3>
                    <p>Present evacuation-center location, capacity, occupancy, and availability in one place.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-truck" aria-hidden="true"></i></div>
                    <h3>Document distributions</h3>
                    <p>Keep an understandable record of what was distributed, to whom, where, and when.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-megaphone" aria-hidden="true"></i></div>
                    <h3>Communicate clearly</h3>
                    <p>Give urgent announcements and community notices a visible, readable home.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-people" aria-hidden="true"></i></div>
                    <h3>Support coordination</h3>
                    <p>Help authorized officials and volunteers work from the same organized information.</p>
                </article>
                <article class="icon-card">
                    <div class="icon-box"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></div>
                    <h3>Improve reporting</h3>
                    <p>Turn operational records into practical summaries for review and planning.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="pb-5" aria-labelledby="about-cta-title">
        <div class="container">
            <div class="cta-panel">
                <div>
                    <h2 id="about-cta-title">Explore community relief information</h2>
                    <p>Review the sample resource view or learn how each SAGIPBRO service supports preparedness and response.</p>
                </div>
                <div class="cta-actions">
                    <a class="btn btn-white" href="resources.php"><i class="bi bi-box-seam" aria-hidden="true"></i> View resources</a>
                    <a class="btn btn-ghost-light" href="services.php">Our services</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
