<?php
page_title('Sign In');
if (current_user() !== null) {
	header('Location: ' . home_url(), true, 302);
	exit;
}

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = trim((string)($_POST['email'] ?? ''));
	if (!csrf_check()) {
		$error = 'The form has expired. Reload the page and try again.';
	} elseif (user_signin($email, (string)($_POST['password'] ?? ''))) {
		header('Location: ' . home_url(), true, 303);
		exit;
	} else {
		$error = 'Wrong email or password.';
	}
}
?>
<div class="md:p-7 lg:p-9 p-4">
	<div class="d-flex flex-column gap-5" style="max-width: 24rem;">
		<h2 class="text-title">Sign In</h2>

		<?php if ($error !== ''): ?>
			<p class="fg-danger" role="alert"><?= e($error) ?></p>
		<?php endif; ?>

		<form method="post" class="d-flex flex-column gap-3">
			<?= csrf_field() ?>
			<div class="form-field">
				<label for="email" class="form-label">Email</label>
				<input class="form-control" type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
			</div>
			<div class="form-field">
				<label for="password" class="form-label">Password</label>
				<input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
			</div>
			<button type="submit" class="btn-outline theme-primary">Sign In</button>
		</form>

		<span>No account yet? <a href="<?= e(url('account', 'signup')) ?>">Sign Up</a></span>
	</div>
</div>
