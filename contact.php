<?php
$pageTitle = 'Contact';
$pageDescription = 'Find Barangay Bonuan Binloc hall hotlines, email, and Dagupan City emergency coordination details, and preview the SAGIPBRO contact form.';
$activePage = 'contact';
$basePath = '';
$contactMessageSent = false;
$contactMessageError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/connection.php';
    try {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $sitio = trim((string) ($_POST['sitio'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
            throw new InvalidArgumentException('Please complete all required fields.');
        }
        $stmt = sagipbroDatabase()->prepare('INSERT INTO contact_messages (name, email, phone, sitio, subject, message) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $phone ?: null, $sitio ?: null, $subject, $message]);
        $contactMessageSent = true;
    } catch (Throwable $e) {
        $contactMessageError = $e instanceof InvalidArgumentException ? $e->getMessage() : 'Message could not be sent. Please try again.';
        error_log('Contact message failed (' . get_class($e) . ').');
    }
}

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main id="main-content">
    <section class="page-hero photo-hero contact-hero" aria-labelledby="contact-page-title">
        <div class="container">
            <nav aria-label="Breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                </ol>
            </nav>
            <span class="hero-chip"><span aria-hidden="true"></span> Contact and assistance</span>
            <h1 id="contact-page-title">Reach the right help, without guesswork.</h1>
            <p>Find the barangay address, hall hotlines, email, and clearly labeled Dagupan City contact channels. For immediate threats to life or safety, call 911.</p>
        </div>
    </section>

    <section class="section-space section-soft" aria-labelledby="contact-details-title">
        <div class="container">
            <div class="contact-layout">
                <aside class="contact-panel" aria-labelledby="contact-details-title">
                    <div class="contact-panel-body">
                        <span class="eyebrow text-white">Contact details</span>
                        <h2 id="contact-details-title">Barangay Bonuan Binloc</h2>
                        <p>Use these details to choose the most appropriate channel for your concern.</p>
                        <ul class="contact-list">
                            <li>
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                <div>
                                    <strong>Barangay address</strong>
                                    <span>Bonuan Binloc, Dagupan City, Pangasinan 2400</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-telephone" aria-hidden="true"></i>
                                <div>
                                    <strong>Barangay hall hotlines</strong>
                                    <a href="tel:+639631743346">0963 174 3346</a><br>
                                    <a href="tel:+639632173031">0963 217 3031</a>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                                <div>
                                    <strong>Barangay email</strong>
                                    <a href="mailto:barangaybonuabbinloc@gmail.com">barangaybonuabbinloc@gmail.com</a>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-telephone" aria-hidden="true"></i>
                                <div>
                                    <strong>Dagupan City CDRRMO mobile</strong>
                                    <a href="tel:+639684449598">0968-444-9598</a>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-telephone-forward" aria-hidden="true"></i>
                                <div>
                                    <strong>Dagupan City CDRRMO landline</strong>
                                    <a href="tel:+63755400363">(075) 540-0363</a>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                                <div>
                                    <strong>Dagupan City general email</strong>
                                    <a href="mailto:dagupanlgu@gmail.com">dagupanlgu@gmail.com</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="contact-hours">
                        <strong>Barangay and city contacts</strong>
                        <span>Use the barangay hall hotlines and email for local inquiries. Dagupan City contact channels are listed separately for citywide concerns and disaster coordination.</span>
                        <a class="d-inline-block mt-2 text-white small" href="https://www.dagupan.gov.ph/directories/" target="_blank" rel="noopener noreferrer">View the official city directory <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                    </div>
                </aside>

                <div class="form-card">
                    <h2>Send a message</h2>
                    <p>Send a message to the barangay administration team.</p>

                    <div class="alert <?= $contactMessageSent ? 'alert-success' : ($contactMessageError ? 'alert-danger' : 'alert-info') ?> app-alert" id="contactFormNotice" role="status">
                        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                        <div>
                            <strong><?= $contactMessageSent ? 'Message sent' : ($contactMessageError ?: 'Send a message') ?></strong>
                            <span><?= $contactMessageSent ? 'Your message was sent to the admin inbox.' : 'Messages are reviewed by authorized administrators.' ?></span>
                        </div>
                    </div>

                    <form action="contact.php" method="post" aria-describedby="contactFormNotice">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="contactName">Full name <span class="required-mark" aria-hidden="true">*</span></label>
                                <input class="form-control" id="contactName" name="name" type="text" autocomplete="name" maxlength="120" required aria-required="true">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contactEmail">Email address <span class="required-mark" aria-hidden="true">*</span></label>
                                <input class="form-control" id="contactEmail" name="email" type="email" autocomplete="email" maxlength="160" required aria-required="true">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contactPhone">Contact number</label>
                                <input class="form-control" id="contactPhone" name="phone" type="tel" autocomplete="tel" maxlength="30" inputmode="tel" placeholder="09XX XXX XXXX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contactSitio">Sitio</label>
                                <input class="form-control" id="contactSitio" name="sitio" type="text" maxlength="100" placeholder="Enter your sitio">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contactSubject">Subject <span class="required-mark" aria-hidden="true">*</span></label>
                                <select class="form-select" id="contactSubject" name="subject" required aria-required="true">
                                    <option value="" selected disabled>Select a subject</option>
                                    <option value="resource">Relief resource inquiry</option>
                                    <option value="evacuation">Evacuation information</option>
                                    <option value="distribution">Relief distribution</option>
                                    <option value="announcement">Announcement clarification</option>
                                    <option value="other">Other concern</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="contactMessage">Message <span class="required-mark" aria-hidden="true">*</span></label>
                                <textarea class="form-control" id="contactMessage" name="message" rows="5" maxlength="1500" required aria-required="true" placeholder="Describe your question or concern."></textarea>
                                <div class="form-text">Do not include passwords, financial details, or other sensitive personal information.</div>
                            </div>
                            <div class="col-12 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                <small class="text-secondary"><span class="required-mark" aria-hidden="true">*</span> Required fields</small>
                                <button class="btn btn-brand" type="submit"><i class="bi bi-send" aria-hidden="true"></i> Preview submission</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space" aria-labelledby="urgent-channels-title">
        <div class="container">
            <div class="section-heading text-center">
                <span class="section-kicker">Choose a channel</span>
                <h2 id="urgent-channels-title">When every minute matters</h2>
                <p>Match the type of concern with the contact channel intended to handle it.</p>
            </div>
            <div class="hotline-grid">
                <article class="hotline-card">
                    <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                    <strong>Life-threatening emergency</strong>
                    <a href="tel:911">Call 911</a>
                </article>
                <article class="hotline-card">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    <strong>Disaster coordination</strong>
                    <a href="tel:+639684449598">CDRRMO: 0968-444-9598</a>
                </article>
                <article class="hotline-card">
                    <i class="bi bi-telephone" aria-hidden="true"></i>
                    <strong>City landline</strong>
                    <a href="tel:+63755400363">(075) 540-0363</a>
                </article>
                <article class="hotline-card">
                    <i class="bi bi-envelope" aria-hidden="true"></i>
                    <strong>General city inquiry</strong>
                    <a href="mailto:dagupanlgu@gmail.com">dagupanlgu@gmail.com</a>
                </article>
            </div>
        </div>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
