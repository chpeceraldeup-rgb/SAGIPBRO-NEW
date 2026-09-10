<?php
require_once __DIR__ . '/config/session.php';
$error = trim((string) ($_GET['error'] ?? ''));
$pageTitle = 'Create account';
$pageDescription = 'Create a SAGIPBRO user account.';
$basePath = '';
$useLoginStyles = true;
$isAuthPage = true;
require __DIR__ . '/includes/header.php';
?>
<main class="auth-main register-main" id="main-content">
    <section class="auth-visual" aria-label="SAGIPBRO account security">
        <a class="auth-brand brand-lockup" href="index.php"><img src="assets/images/sagipbro-mark.svg" alt="" width="49" height="55"><span><strong>SAGIPBRO</strong><small>Disaster relief information system</small></span></a>
        <div class="auth-visual-copy"><span class="auth-chip"><i class="bi bi-person-check"></i> Account setup</span><h1>Join a coordinated community response.</h1><p>Create a secure account for access appropriate to your assigned barangay role.</p></div>
        <div class="auth-visual-status"><i class="bi bi-lock-fill" aria-hidden="true"></i><span><strong>Protected access</strong><small>Your credentials are securely processed</small></span></div>
    </section>
    <section class="auth-form-side" aria-labelledby="register-title">
        <div class="auth-mobile-brand"><a class="brand-lockup" href="index.php"><img src="assets/images/sagipbro-mark.svg" alt="" width="42" height="48"><span><strong>SAGIPBRO</strong><small>Barangay Binloc</small></span></a></div>
        <div class="auth-card auth-card-wide">
            <a class="auth-back" href="login.php"><i class="bi bi-arrow-left"></i> Back to sign in</a>
            <div class="auth-heading"><span class="eyebrow">New account</span><h2 id="register-title">Create your account</h2><p>Use your official details. Passwords must contain at least eight characters.</p></div>
            <?php if ($error !== ''): ?><div class="alert alert-danger app-alert" role="alert"><i class="bi bi-exclamation-circle-fill"></i><div><strong>Registration unsuccessful</strong><span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span></div></div><?php endif; ?>
            <form method="post" action="actions/auth/register.php" autocomplete="on">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="mb-3"><label class="form-label" for="full_name">Full name</label><div class="auth-input"><i class="bi bi-person-badge"></i><input class="form-control" id="full_name" name="full_name" required maxlength="150" autocomplete="name" placeholder="Juan Dela Cruz"></div></div>
                <div class="mb-3"><label class="form-label" for="new_username">Username</label><div class="auth-input"><i class="bi bi-at"></i><input class="form-control" id="new_username" name="username" required maxlength="80" autocomplete="username" placeholder="Choose a username"></div></div>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><label class="form-label" for="new_password">Password</label><div class="auth-input"><i class="bi bi-lock"></i><input class="form-control" id="new_password" type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="At least 8 characters"></div></div>
                    <div class="col-sm-6"><label class="form-label" for="confirm_password">Confirm password</label><div class="auth-input"><i class="bi bi-shield-lock"></i><input class="form-control" id="confirm_password" type="password" name="confirm_password" required minlength="8" autocomplete="new-password" placeholder="Repeat password"></div></div>
                </div>
                <button class="btn btn-brand w-100 auth-submit" type="submit">Create account <i class="bi bi-arrow-right"></i></button>
            </form>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
