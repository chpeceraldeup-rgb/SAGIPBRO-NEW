<?php
$alertError = trim((string) ($_GET['error'] ?? ''));
$alertSuccess = trim((string) ($_GET['success'] ?? ($_GET['message'] ?? '')));
?>
<?php if ($alertError !== ''): ?>
    <div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
        <i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i>
        <div><strong>Something needs attention</strong><span><?= htmlspecialchars($alertError, ENT_QUOTES, 'UTF-8') ?></span></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if ($alertSuccess !== ''): ?>
    <div class="alert alert-success alert-dismissible fade show app-alert" role="status">
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <div><strong>Success</strong><span><?= htmlspecialchars($alertSuccess, ENT_QUOTES, 'UTF-8') ?></span></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
