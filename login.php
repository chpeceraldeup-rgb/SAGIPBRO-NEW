<?php

require_once __DIR__ . '/config/session.php';

$error = trim((string) ($_GET['error'] ?? ''));
$success = trim((string) ($_GET['success'] ?? ''));
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sign in | SAGIPBRO</title>
	<?php if (getenv('SAGIPBRO_RECAPTCHA_SITE_KEY')): ?><script src="https://www.google.com/recaptcha/api.js" async defer></script><?php endif; ?>
</head>
<body>
	<main>
		<h1>SAGIPBRO</h1>
		<?php if ($error !== ''): ?><p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
		<?php if ($success !== ''): ?><p role="status"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
		<form method="post" action="actions/auth/login.php" autocomplete="on">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
			<label>Username <input name="username" required maxlength="80" autocomplete="username"></label>
			<label>Password <input type="password" name="password" required autocomplete="current-password"></label>
			<?php if (getenv('SAGIPBRO_RECAPTCHA_SITE_KEY')): ?>
				<div class="g-recaptcha" data-sitekey="<?= htmlspecialchars(getenv('SAGIPBRO_RECAPTCHA_SITE_KEY'), ENT_QUOTES, 'UTF-8') ?>"></div>
			<?php else: ?>
				<label><input type="checkbox" name="not_robot" value="1" required> I'm not a robot</label>
			<?php endif; ?>
			<button type="submit">Sign in</button>
		</form>
		<p><a href="register.php">Create an account</a></p>
	</main>
</body>
</html>
