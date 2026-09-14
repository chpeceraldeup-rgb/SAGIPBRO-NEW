<?php $basePath = $basePath ?? ''; $isAdmin = $isAdmin ?? false; $isAuthPage = $isAuthPage ?? false; ?>
<?php if ($isAdmin): ?>
<footer class="admin-footer">
    <span>&copy; <span data-current-year><?= date('Y') ?></span> Barangay Binloc. SAGIPBRO v1.0</span>
    <span class="d-none d-sm-inline">Secure disaster-relief operations console</span>
</footer>
<?php elseif ($isAuthPage): ?>
<footer class="auth-footer">
    <span>&copy; <span data-current-year><?= date('Y') ?></span> Barangay Binloc, Dagupan City</span>
    <span>Disaster Relief Resource Information System</span>
</footer>
<?php else: ?>
<footer class="public-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <a class="brand-lockup footer-brand" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>index.php">
                    <img src="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>assets/images/sagipbro-mark.svg" alt="" width="45" height="51">
                    <span><strong>SAGIPBRO</strong><small>Ready. Informed. Together.</small></span>
                </a>
                <p>Bonuan Binloc's central information system for relief resources, evacuation support, and timely community updates.</p>
                <span class="footer-status"><i class="bi bi-shield-check"></i> Information you can trust</span>
            </div>
            <div>
                <h2>Quick links</h2>
                <ul class="footer-links">
                    <li><a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>about.php">About SAGIPBRO</a></li>
                    <li><a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>services.php">Our services</a></li>
                    <li><a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>resources.php">Available resources</a></li>
                    <li><a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>contact.php">Contact the barangay</a></li>
                </ul>
            </div>
            <div>
                <h2>Contact</h2>
                <ul class="footer-contact">
                    <li><i class="bi bi-geo-alt"></i><span>Bonuan Binloc<br>Dagupan City, Pangasinan 2400</span></li>
                    <li><i class="bi bi-envelope"></i><span>Barangay email: <a href="mailto:barangaybonuabbinloc@gmail.com">barangaybonuabbinloc@gmail.com</a></span></li>
                    <li><i class="bi bi-telephone"></i><span>Barangay hall hotlines:<br><a href="tel:+639631743346">0963 174 3346</a> / <a href="tel:+639632173031">0963 217 3031</a></span></li>
                    <li><i class="bi bi-envelope"></i><span>City inquiries: <a href="mailto:dagupanlgu@gmail.com">dagupanlgu@gmail.com</a></span></li>
                    <li><i class="bi bi-telephone"></i><span>City CDRRMO: <a href="tel:+639684449598">0968 444 9598</a></span></li>
                </ul>
            </div>
            <div class="footer-emergency">
                <h2>Need urgent help?</h2>
                <p>For urgent disaster and medical response in Dagupan City, contact the CDRRMO immediately.</p>
                <a class="btn btn-light w-100" href="tel:+639684449598"><i class="bi bi-telephone-fill"></i> Call CDRRMO</a>
                <small>Available 24 hours a day</small>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <span data-current-year><?= date('Y') ?></span> Bonuan Binloc, Dagupan City</span>
            <span>Disaster Relief Resource Information System</span>
        </div>
    </div>
</footer>
<?php endif; ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="appToast" class="toast border-0 shadow" role="status" aria-live="polite" aria-atomic="true">
        <div class="toast-header"><span class="toast-icon"><i class="bi bi-check-lg"></i></span><strong class="me-auto">SAGIPBRO</strong><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button></div>
        <div class="toast-body">Action completed successfully.</div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>assets/js/script.js"></script>
<?php if ($isAdmin): ?><script src="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>assets/js/crud-view.js"></script><?php endif; ?>
<?php if ($isAdmin): ?><script src="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>assets/js/dashboard.js"></script><?php endif; ?>
</body>
</html>
