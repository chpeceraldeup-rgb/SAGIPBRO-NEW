<?php

require_once __DIR__ . '/config/session.php';

$error = trim((string) ($_GET['error'] ?? ''));
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register | SAGIPBRO</title>
</head>
<body>
	<main>
		<h1>Create your SAGIPBRO account</h1>
		<?php if ($error !== ''): ?><p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
		<form method="post" action="actions/auth/register.php" autocomplete="on">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
			<label>Full name <input name="full_name" required maxlength="150" autocomplete="name"></label>
			<label>Username <input name="username" required maxlength="80" autocomplete="username"></label>
			<label>Password <input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
			<label>Confirm password <input type="password" name="confirm_password" required minlength="8" autocomplete="new-password"></label>
			<button type="submit">Register</button>
		</form>
		<p><a href="login.php">Back to sign in</a></p>
	</main>
</body>
</html>
